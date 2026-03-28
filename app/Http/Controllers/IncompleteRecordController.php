<?php

namespace App\Http\Controllers;

use App\Repositories\BusinessRepository;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IncompleteRecordController extends Controller
{
    public function index(Request $request, BusinessRepository $businessRepository): View
    {
        $filters = $request->only(['search', 'city', 'category', 'area']);

        return view('incomplete.index', [
            'businesses' => $businessRepository->incompletePaginate($filters),
            'filters' => $filters,
            'filterOptions' => $businessRepository->filterOptions(),
        ]);
    }
}
