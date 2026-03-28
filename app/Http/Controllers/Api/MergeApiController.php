<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MergeBusinessesRequest;
use App\Services\BusinessMergeService;
use Illuminate\Http\JsonResponse;

class MergeApiController extends Controller
{
    public function store(
        MergeBusinessesRequest $request,
        BusinessMergeService $businessMergeService
    ): JsonResponse {
        $business = $businessMergeService->merge(
            $request->validated('business_ids'),
            $request->validated('master_id')
        );

        return response()->json([
            'message' => 'Records merged successfully.',
            'business' => $business,
        ]);
    }
}
