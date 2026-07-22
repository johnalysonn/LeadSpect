<?php

namespace App\Http\Controllers;

use App\Actions\Lead\CreateLeadFromSearchResultAction;
use App\Actions\Lead\EnrichLeadAction;
use App\Actions\Lead\ExportLeadsAction;
use App\Actions\Lead\StartWhatsAppContactAction;
use App\Actions\Lead\UpdateLeadStatusAction;
use App\Enums\LeadStatus;
use App\Http\Requests\StoreLeadRequest;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\MessageTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LeadController extends Controller
{
    /**
     * Display Kanban Pipeline and Lead List.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Lead::class);
        $user = $request->user();

        $allLeads = Lead::forUser($user->id)
            ->with(['statusHistories', 'notes', 'tags'])
            ->latest()
            ->get();

        // Agrupar leads pelos 9 status do Enum para a visão Kanban
        $leadsByStatus = [];
        foreach (LeadStatus::cases() as $status) {
            $leadsByStatus[$status->value] = $allLeads->filter(fn($l) => $l->status === $status)->values();
        }

        $templates = MessageTemplate::where('user_id', $user->id)->get();

        return view('leads.index', compact('allLeads', 'leadsByStatus', 'templates'));
    }

    /**
     * Store a lead from search or manual addition.
     */
    public function store(StoreLeadRequest $request, CreateLeadFromSearchResultAction $action): JsonResponse
    {
        Gate::authorize('create', Lead::class);

        $lead = $action->execute($request->user(), $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Lead adicionado aos seus contatos!',
            'lead' => $lead,
        ]);
    }

    /**
     * Update lead status (Kanban Drag and Drop).
     */
    public function updateStatus(Request $request, Lead $lead, UpdateLeadStatusAction $action): JsonResponse
    {
        Gate::authorize('update', $lead);

        $request->validate([
            'status' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $updatedLead = $action->execute($request->user(), $lead, $request->input('status'), $request->input('notes'));

        return response()->json([
            'success' => true,
            'lead' => $updatedLead,
        ]);
    }

    /**
     * Initiate WhatsApp contact, update status to 'contato_iniciado' and generate wa.me URL.
     */
    public function whatsapp(Request $request, StartWhatsAppContactAction $action): JsonResponse
    {
        $result = $action->execute($request->user(), $request->all());

        return response()->json([
            'success' => true,
            'lead' => $result['lead'],
            'message' => $result['message'],
            'whatsapp_url' => $result['whatsapp_url'],
        ]);
    }

    /**
     * Perform lazy website enrichment for a lead.
     */
    public function enrich(Lead $lead, EnrichLeadAction $action): JsonResponse
    {
        Gate::authorize('update', $lead);

        $enrichedLead = $action->execute($lead);

        return response()->json([
            'success' => true,
            'lead' => $enrichedLead,
        ]);
    }

    /**
     * Toggle favorite status.
     */
    public function toggleFavorite(Lead $lead): JsonResponse
    {
        Gate::authorize('update', $lead);

        $lead->is_favorite = !$lead->is_favorite;
        $lead->save();

        return response()->json([
            'success' => true,
            'is_favorite' => $lead->is_favorite,
        ]);
    }

    /**
     * Add a note to a lead.
     */
    public function addNote(Request $request, Lead $lead): JsonResponse
    {
        Gate::authorize('update', $lead);

        $request->validate([
            'content' => ['required', 'string'],
            'follow_up_at' => ['nullable', 'date'],
        ]);

        $note = LeadNote::create([
            'lead_id' => $lead->id,
            'user_id' => $request->user()->id,
            'content' => $request->input('content'),
            'follow_up_at' => $request->input('follow_up_at'),
        ]);

        return response()->json([
            'success' => true,
            'note' => $note,
        ]);
    }

    /**
     * Export leads as CSV download.
     */
    public function export(Request $request, ExportLeadsAction $action): Response
    {
        $csvContent = $action->execute($request->user(), $request->query('status'));

        $filename = 'leadspect_leads_' . date('Y-m-d_H-i') . '.csv';

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Delete a lead.
     */
    public function destroy(Lead $lead): JsonResponse|RedirectResponse
    {
        Gate::authorize('delete', $lead);

        $lead->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('leads.index')->with('status', 'Lead removido com sucesso!');
    }
}
