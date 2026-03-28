<?php

namespace App\Http\Controllers;

use App\Services\BusinessReportService;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(BusinessReportService $businessReportService): View
    {
        return view('reports.index', [
            'summary' => $businessReportService->summary(),
            'cityCounts' => $businessReportService->cityCounts(15),
            'categoryCityCounts' => $businessReportService->categoryCityCounts(15),
            'categoryAreaCounts' => $businessReportService->categoryAreaCounts(15),
            'topCategories' => $businessReportService->topCategories(12),
        ]);
    }
}
