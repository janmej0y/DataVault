<?php

namespace App\Http\Controllers;

use App\Repositories\BusinessRepository;
use App\Repositories\ImportLogRepository;
use App\Services\BusinessReportService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(
        BusinessReportService $reportService,
        BusinessRepository $businessRepository,
        ImportLogRepository $importLogRepository
    ): View {
        return view('dashboard', [
            'summary' => $reportService->summary(),
            'cityCounts' => $reportService->cityCounts(),
            'categoryCityCounts' => $reportService->categoryCityCounts(),
            'categoryAreaCounts' => $reportService->categoryAreaCounts(),
            'topCategories' => $reportService->topCategories(),
            'recentImports' => $importLogRepository->latest(),
            'duplicateGroups' => $businessRepository->duplicateGroups(perPage: 3),
        ]);
    }
}
