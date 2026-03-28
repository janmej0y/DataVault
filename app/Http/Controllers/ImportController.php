<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportBusinessesRequest;
use App\Repositories\ImportLogRepository;
use App\Services\BusinessImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class ImportController extends Controller
{
    public function index(ImportLogRepository $importLogRepository): View
    {
        return view('imports.index', [
            'importLogs' => $importLogRepository->paginate(),
        ]);
    }

    public function store(
        ImportBusinessesRequest $request,
        BusinessImportService $businessImportService
    ): JsonResponse|RedirectResponse {
        try {
            $importLog = $businessImportService->import(
                $request->file('upload_file'),
                $request->string('google_drive_url')->toString() ?: null
            );
        } catch (Throwable $throwable) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $throwable->getMessage(),
                ], 422);
            }

            return back()
                ->withInput()
                ->withErrors(['upload_file' => $throwable->getMessage()]);
        }

        $message = "Import completed. {$importLog->inserted_rows} rows were saved.";

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'redirect' => route('imports.index'),
            ]);
        }

        return redirect()
            ->route('imports.index')
            ->with('status', $message);
    }
}
