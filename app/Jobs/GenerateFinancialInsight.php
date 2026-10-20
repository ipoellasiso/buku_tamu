<?php

namespace App\Jobs;

use App\Models\FinancialReport;
use App\Services\FinancialAnalysisService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateFinancialInsight implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;

    public function __construct(public FinancialReport $report) {}

    public function handle(FinancialAnalysisService $service): void
    {
        $service->generateInsight($this->report);
    }
}
