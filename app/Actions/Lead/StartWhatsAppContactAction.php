<?php

namespace App\Actions\Lead;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\LeadStatusHistory;
use App\Models\MessageTemplate;
use App\Models\User;

class StartWhatsAppContactAction
{
    public function __construct(
        protected CreateLeadFromSearchResultAction $createLeadAction
    ) {}

    public function execute(User $user, array $data): array
    {
        $lead = null;

        if (!empty($data['lead_id'])) {
            $lead = Lead::forUser($user->id)->findOrFail($data['lead_id']);
        } else {
            $lead = $this->createLeadAction->execute($user, $data, LeadStatus::CONTATO_INICIADO);
        }

        // Determinar mensagem a ser usada
        $message = trim($data['custom_message'] ?? '');

        if (empty($message) && !empty($data['template_id'])) {
            $template = MessageTemplate::where('user_id', $user->id)->find($data['template_id']);
            if ($template) {
                $message = $template->parseForLead($lead);
            }
        }

        if (empty($message)) {
            $message = "Olá {$lead->name}, tudo bem? Vi sua empresa no LeadSpect e gostaria de apresentar uma oportunidade para o seu negócio.";
        }

        // Atualizar status do Lead para 'contato_iniciado'
        $oldStatus = $lead->status;
        $lead->status = LeadStatus::CONTATO_INICIADO;
        $lead->save();

        // Registrar no Histórico de Status
        LeadStatusHistory::create([
            'lead_id' => $lead->id,
            'user_id' => $user->id,
            'old_status' => $oldStatus?->value ?? null,
            'new_status' => LeadStatus::CONTATO_INICIADO->value,
            'message_used' => $message,
            'notes' => 'Contato iniciado via WhatsApp',
        ]);

        // Formatar número de WhatsApp para link wa.me
        $phone = preg_replace('/[^0-9]/', '', $lead->whatsapp ?: $lead->phone ?: '');
        if (!empty($phone)) {
            // Se já tiver DDI (ex: 5511999998888 ou 55...), mantém
            if (str_starts_with($phone, '55') && strlen($phone) >= 12) {
                // ok
            } elseif (strlen($phone) === 10 || strlen($phone) === 11) {
                $phone = '55' . $phone;
            } elseif (strlen($phone) === 8 || strlen($phone) === 9) {
                $phone = '5511' . $phone; // Fallback para DDD 11 se digitado sem DDD
            }
        }

        $whatsappUrl = "https://wa.me/{$phone}?text=" . urlencode($message);

        return [
            'lead' => $lead,
            'message' => $message,
            'whatsapp_url' => $whatsappUrl,
        ];
    }
}
