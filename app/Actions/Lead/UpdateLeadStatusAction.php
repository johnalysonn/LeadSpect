<?php

namespace App\Actions\Lead;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\LeadStatusHistory;
use App\Models\User;

class UpdateLeadStatusAction
{
    public function execute(User $user, Lead $lead, string|LeadStatus $newStatus, ?string $notes = null): Lead
    {
        $targetStatus = is_string($newStatus) ? LeadStatus::from($newStatus) : $newStatus;
        $oldStatus = $lead->status;

        if ($oldStatus === $targetStatus) {
            return $lead;
        }

        $lead->status = $targetStatus;
        $lead->save();

        LeadStatusHistory::create([
            'lead_id' => $lead->id,
            'user_id' => $user->id,
            'old_status' => $oldStatus?->value,
            'new_status' => $targetStatus->value,
            'notes' => $notes ?: "Status alterado para {$targetStatus->label()}",
        ]);

        return $lead;
    }
}
