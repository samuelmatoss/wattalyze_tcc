<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Jobs\GenerateReportJob;

class ReportController extends Controller
{
    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:consumption,cost,efficiency,comparative,custom',
            'period_type' => 'required|in:daily,weekly,monthly,yearly,custom',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start',
            'format' => 'required|in:json,pdf,excel,csv',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $report = Report::create([
            'user_id' => auth()->id(),
            'status' => 'pending',
            ...$validator->validated()
        ]);

        // Enfileirar geração do relatório
        GenerateReportJob::dispatch($report);

        return response()->json([
            'message' => 'Relatório sendo gerado em segundo plano',
            'report_id' => $report->id
        ], 202);
    }

    public function index()
    {
        $reports = Report::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($reports);
    }

    public function download($id)
    {
        $report = Report::where('user_id', auth()->id())
            ->findOrFail($id);
            
        if ($report->status !== 'completed' || !$report->file_path) {
            return response()->json(['error' => 'Relatório não disponível'], 404);
        }

        return response()->download(storage_path('app/' . $report->file_path));
    }
}