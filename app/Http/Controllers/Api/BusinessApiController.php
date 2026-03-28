<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\BusinessRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessApiController extends Controller
{
    public function index(Request $request, BusinessRepository $businessRepository): JsonResponse
    {
        $businesses = $businessRepository->paginate(
            $request->only(['search', 'city', 'category', 'area']),
            max((int) $request->integer('per_page', 15), 1)
        );

        return response()->json($businesses);
    }
}
