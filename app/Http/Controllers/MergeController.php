<?php

namespace App\Http\Controllers;

use App\Http\Requests\MergeBusinessesRequest;
use App\Repositories\BusinessRepository;
use App\Services\BusinessMergeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MergeController extends Controller
{
    public function index(Request $request, BusinessRepository $businessRepository): View
    {
        $filters = $request->only(['search', 'city', 'category', 'area']);

        return view('merges.index', [
            'duplicateGroups' => $businessRepository->duplicateGroups($filters),
            'filters' => $filters,
            'filterOptions' => $businessRepository->filterOptions(),
        ]);
    }

    public function store(
        MergeBusinessesRequest $request,
        BusinessMergeService $businessMergeService
    ): RedirectResponse {
        $masterRecord = $businessMergeService->merge(
            $request->validated('business_ids'),
            $request->validated('master_id')
        );

        return redirect()
            ->route('merges.index')
            ->with('status', "Records were merged successfully into #{$masterRecord->id}.");
    }
}
