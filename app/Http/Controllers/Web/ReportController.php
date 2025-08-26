<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Device;
use App\Models\Environment;
use Illuminate\Http\Request;
use App\Jobs\GenerateReportJob;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    /**
     * Get all reports for authenticated user
     */
    public function index(): JsonResponse
    {
        $reports = Report::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return response()->json(['reports' => $reports]);
    }

    /**
     * Get form data for generating a new report
     */
    public function generateForm(): JsonResponse
    {
        $devices = Device::where('user_id', auth()->id())->get();
        $environments = Environment::where('user_id', auth()->id())->get();

        return response()->json([
            'devices' => $devices,
            'environments' => $environments
        ]);
    }

    /**
     * Generate a new report
     */
    public function generate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:consumption,cost,efficiency,comparative,custom',
            'period_type' => 'required|in:daily,weekly,monthly,yearly,custom',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start',
            'format' => 'required|in:pdf,excel,csv',
            'devices' => 'nullable|array',
            'environments' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $report = Report::create([
            'user_id' => auth()->id(),
            'status' => 'pending',
            'filters' => [
                'devices' => $request->devices,
                'environments' => $request->environments,
            ],
            ...$validated,
        ]);

        // Dispatch the report generation job (async)
        GenerateReportJob::dispatch($report);

        return response()->json([
            'message' => 'Relatório está sendo gerado!',
            'report_id' => $report->id
        ], 202);
    }

    /**
     * Delete a report
     */
    public function destroy(Report $report): JsonResponse
    {
        // Check authorization
        if ($report->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Delete the file from storage if it exists
        if ($report->file_path && Storage::exists($report->file_path)) {
            Storage::delete($report->file_path);
        }

        $report->delete();

        return response()->json(['message' => 'Relatório excluído com sucesso!']);
    }

    /**
     * Download a report
     */
    public function download(Report $report): JsonResponse
    {
        // Check authorization
        if ($report->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!$report->file_path || $report->status !== 'completed') {
            return response()->json(['error' => 'Relatório não disponível para download'], 404);
        }

        // For API, return a URL to download the file
        $url = Storage::url($report->file_path);
        
        return response()->json([
            'download_url' => $url,
            'expires_at' => now()->addHours(1)->toISOString() // URL expiration time
        ]);
    }

    /**
     * Get report status
     */
    public function status(Report $report): JsonResponse
    {
        // Check authorization
        if ($report->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'status' => $report->status,
            'progress' => $report->progress ?? 0,
            'file_path' => $report->file_path,
            'created_at' => $report->created_at,
            'updated_at' => $report->updated_at
        ]);
    }
}