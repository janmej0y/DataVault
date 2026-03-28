<?php

namespace App\Http\Controllers;

use App\Repositories\BusinessRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DuplicateController extends Controller
{
    public function index(Request $request, BusinessRepository $businessRepository)
    {
        $filters = $request->only(['search', 'city', 'category', 'area']);

        return view('duplicates.index', [
            'duplicateGroups' => $businessRepository->duplicateGroups($filters),
            'filters' => $filters,
            'filterOptions' => $businessRepository->filterOptions(),
        ]);
    }

    public function export(Request $request, BusinessRepository $businessRepository): StreamedResponse
    {
        $records = $businessRepository
            ->duplicateExportQuery($request->only(['search', 'city', 'category', 'area']))
            ->get();

        return response()->streamDownload(function () use ($records) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Group Key',
                'ID',
                'Business Name',
                'Area',
                'City',
                'Mobile No',
                'Category',
                'Address',
                'Duplicate',
            ]);

            foreach ($records as $record) {
                fputcsv($handle, [
                    $record->duplicate_group,
                    $record->id,
                    $record->business_name,
                    $record->area,
                    $record->city,
                    $record->mobile_no,
                    $record->category,
                    $record->address,
                    $record->is_duplicate ? 'Yes' : 'No',
                ]);
            }

            fclose($handle);
        }, 'duplicate-records.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
