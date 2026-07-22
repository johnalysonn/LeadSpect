<?php

namespace App\Services\Enrichment;

use App\Models\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebsiteEnrichmentService
{
    /**
     * Perform lazy website enrichment to discover WhatsApp, phone, social links, and email.
     */
    public function enrich(Lead $lead): Lead
    {
        if (empty($lead->website)) {
            return $lead;
        }

        $url = $lead->website;
        if (!preg_match('#^https?://#i', $url)) {
            $url = 'https://' . $url;
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) LeadSpectBot/1.0',
            ])->timeout(6)->get($url);

            if ($response->successful()) {
                $html = $response->body();

                // 1. WhatsApp & Telefone
                if (preg_match_all('#(?:https?://)?(?:api\.whatsapp\.com/send\?phone=|wa\.me/|wa\.link/)([0-9]{10,13})#i', $html, $waMatches)) {
                    $whatsappFound = $waMatches[1][0] ?? null;
                    if ($whatsappFound && !$lead->whatsapp) {
                        $lead->whatsapp = $whatsappFound;
                    }
                }

                if (preg_match_all('#(?:\+?55\s*)?(?:\(?\d{2}\)?\s*)?(?:9\d{4}[-\s]?\d{4}|\d{4}[-\s]?\d{4})#', $html, $phoneMatches)) {
                    $phoneFound = trim($phoneMatches[0][0] ?? '');
                    if ($phoneFound && !$lead->phone) {
                        $lead->phone = $phoneFound;
                    }
                }

                // 2. Email
                if (preg_match_all('#[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}#', $html, $emailMatches)) {
                    $emails = array_filter($emailMatches[0], fn($e) => !preg_match('#\.(png|jpg|jpeg|gif|svg|webp)$#i', $e));
                    if (!empty($emails) && !$lead->email) {
                        $lead->email = reset($emails);
                    }
                }

                // 3. Instagram
                if (preg_match_all('#https?://(?:www\.)?instagram\.com/([a-zA-Z0-9_.-]+)/?#i', $html, $instaMatches)) {
                    $instaHandle = $instaMatches[1][0] ?? null;
                    if ($instaHandle && !in_array(strtolower($instaHandle), ['p', 'reel', 'stories']) && !$lead->instagram) {
                        $lead->instagram = "https://instagram.com/{$instaHandle}";
                    }
                }

                // 4. Facebook
                if (preg_match_all('#https?://(?:www\.)?facebook\.com/([a-zA-Z0-9_.-]+)/?#i', $html, $fbMatches)) {
                    $fbHandle = $fbMatches[1][0] ?? null;
                    if ($fbHandle && !$lead->facebook) {
                        $lead->facebook = "https://facebook.com/{$fbHandle}";
                    }
                }

                // 5. LinkedIn
                if (preg_match_all('#https?://(?:www\.)?linkedin\.com/(?:company|in)/([a-zA-Z0-9_.-]+)/?#i', $html, $liMatches)) {
                    $liHandle = $liMatches[1][0] ?? null;
                    if ($liHandle && !$lead->linkedin) {
                        $lead->linkedin = "https://linkedin.com/company/{$liHandle}";
                    }
                }

                $lead->enriched_at = now();
                $lead->save();
            }
        } catch (\Throwable $e) {
            Log::warning("Enriquecimento de site falhou para lead #{$lead->id} ({$url}): " . $e->getMessage());
        }

        return $lead;
    }
}
