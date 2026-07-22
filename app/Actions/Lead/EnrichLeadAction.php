<?php

namespace App\Actions\Lead;

use App\Models\Lead;
use App\Services\Enrichment\WebsiteEnrichmentService;

class EnrichLeadAction
{
    public function __construct(
        protected WebsiteEnrichmentService $enrichmentService
    ) {}

    public function execute(Lead $lead): Lead
    {
        return $this->enrichmentService->enrich($lead);
    }
}
