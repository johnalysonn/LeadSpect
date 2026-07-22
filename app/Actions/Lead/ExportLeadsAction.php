<?php

namespace App\Actions\Lead;

use App\Models\Lead;
use App\Models\User;

class ExportLeadsAction
{
    public function execute(User $user, ?string $statusFilter = null): string
    {
        $query = Lead::forUser($user->id);

        if (!empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        $leads = $query->latest()->get();

        $output = fopen('php://temp', 'r+');

        // Header CSV (com UTF-8 BOM para Excel)
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($output, [
            'ID', 'Nome da Empresa', 'Categoria', 'Endereço', 'Cidade', 'Bairro',
            'Telefone', 'WhatsApp', 'E-mail', 'Website', 'Instagram', 'Status', 'Data de Cadastro'
        ], ';');

        foreach ($leads as $lead) {
            fputcsv($output, [
                $lead->id,
                $lead->name,
                $lead->category,
                $lead->address,
                $lead->city,
                $lead->neighborhood,
                $lead->phone,
                $lead->whatsapp,
                $lead->email,
                $lead->website,
                $lead->instagram,
                $lead->status->label(),
                $lead->created_at->format('d/m/Y H:i'),
            ], ';');
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent;
    }
}
