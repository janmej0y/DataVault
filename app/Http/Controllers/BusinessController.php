<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkDeleteBusinessesRequest;
use App\Models\Business;
use App\Repositories\BusinessRepository;
use App\Services\DuplicateDetectionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BusinessController extends Controller
{
    public function index(Request $request, BusinessRepository $businessRepository)
    {
        $filters = $request->only(['search', 'city', 'category', 'area']);

        return view('businesses.index', [
            'businesses' => $businessRepository->paginate($filters),
            'filters' => $filters,
            'filterOptions' => $businessRepository->filterOptions(),
        ]);
    }

    public function export(Request $request, BusinessRepository $businessRepository): StreamedResponse
    {
        $records = $businessRepository
            ->filteredQuery($request->only(['search', 'city', 'category', 'area']))
            ->get();

        return $this->streamCsv($records->all(), 'businesses-export.csv');
    }

    public function bulkDelete(
        BulkDeleteBusinessesRequest $request,
        DuplicateDetectionService $duplicateDetectionService
    ): RedirectResponse {
        Business::query()
            ->whereIn('id', $request->validated('ids'))
            ->delete();

        $duplicateDetectionService->refreshFlags();

        return back()->with('status', 'Selected records were deleted successfully.');
    }

    protected function streamCsv(array $rows, string $fileName): StreamedResponse
    {
        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID',
                'Business Name',
                'Area',
                'City',
                'Mobile No',
                'Category',
                'Sub Category',
                'Address',
                'Duplicate',
            ]);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->id,
                    $row->business_name,
                    $row->area,
                    $row->city,
                    $row->mobile_no,
                    $row->category,
                    $row->sub_category,
                    $row->address,
                    $row->is_duplicate ? 'Yes' : 'No',
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
