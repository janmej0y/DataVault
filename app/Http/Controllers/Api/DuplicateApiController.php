<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\BusinessRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DuplicateApiController extends Controller
{
    public function index(Request $request, BusinessRepository $businessRepository): JsonResponse
    {
        $groups = $businessRepository->duplicateGroups(
            $request->only(['search', 'city', 'category', 'area']),
            max((int) $request->integer('per_page', 10), 1)
        );

        return response()->json($groups);
    }
}
