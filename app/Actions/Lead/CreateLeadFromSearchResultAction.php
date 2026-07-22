<?php

namespace App\Actions\Lead;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\LeadStatusHistory;
use App\Models\User;

class CreateLeadFromSearchResultAction
{
    public function execute(User $user, array $data, LeadStatus $initialStatus = LeadStatus::A_PROSPECTAR): Lead
    {
        $osmId = $data['osm_id'] ?? null;
        $name = $data['name'] ?? 'Empresa Sem Nome';

        $attributes = [
            'user_id' => $user->id,
            'name' => $name,
        ];

        if (!empty($osmId)) {
            $attributes['osm_id'] = $osmId;
        } else {
            $attributes['address'] = $data['address'] ?? null;
        }

        $lead = Lead::where('user_id', $user->id)
            ->where(function ($q) use ($osmId, $name) {
                if ($osmId) {
                    $q->where('osm_id', $osmId);
                } else {
                    $q->where('name', $name);
                }
            })->first();

        if (!$lead) {
            $lead = Lead::create([
                'user_id' => $user->id,
                'osm_id' => $osmId,
                'name' => $name,
                'category' => $data['category'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'neighborhood' => $data['neighborhood'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'latitude' => isset($data['latitude']) ? (float) $data['latitude'] : null,
                'longitude' => isset($data['longitude']) ? (float) $data['longitude'] : null,
                'phone' => $data['phone'] ?? null,
                'whatsapp' => $data['whatsapp'] ?? null,
                'email' => $data['email'] ?? null,
                'website' => $data['website'] ?? null,
                'instagram' => $data['instagram'] ?? null,
                'facebook' => $data['facebook'] ?? null,
                'linkedin' => $data['linkedin'] ?? null,
                'rating' => isset($data['rating']) ? (float) $data['rating'] : null,
                'review_count' => isset($data['review_count']) ? (int) $data['review_count'] : 0,
                'status' => $initialStatus,
                'is_open_now' => $data['is_open_now'] ?? null,
                'opening_hours' => $data['opening_hours'] ?? null,
            ]);

            LeadStatusHistory::create([
                'lead_id' => $lead->id,
                'user_id' => $user->id,
                'old_status' => null,
                'new_status' => $initialStatus->value,
                'notes' => 'Lead adicionado a partir da busca geográfica',
            ]);
        }

        return $lead;
    }
}
