<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Device;
use App\Models\Environment;
use Illuminate\Http\Request;
use App\Jobs\GenerateReportJob;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('reports.index', compact('reports'));
    }

    public function generateForm()
    {
        $devices = Device::where('user_id', auth()->id())->get();
        $environments = Environment::where('user_id', auth()->id())->get();

        return view('reports.generate', compact('devices', 'environments'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:consumption,cost,efficiency,comparative,custom',
            'period_type' => 'required|in:daily,weekly,monthly,yearly,custom',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start',
            'format' => 'required|in:pdf,excel,csv',
            'devices' => 'nullable|array',
            'environments' => 'nullable|array',
        ]);

        $report = Report::create([
            'user_id' => auth()->id(),
            'status' => 'pending',
            'filters' => [
                'devices' => $request->devices,
                'environments' => $request->environments,
            ],
            ...$validated,
        ]);

        // Executa o job **sincronamente** (sem fila)
        $job = new GenerateReportJob($report);
        $job->handle();

        return redirect()->route('reports.index')
            ->with('success', 'Relatório gerado com sucesso!');
    }
   public function destroy(Report $report)
    {

        // Apagar o arquivo do storage se existir
        if ($report->file_path && Storage::exists($report->file_path)) {
            Storage::delete($report->file_path);
        }

        $report->delete();

        return redirect()->route('reports.index')
            ->with('success', 'Relatório excluído com sucesso!');
    }
    public function download(Report $report)
    {
        $this->authorize('view', $report);

        if (!$report->file_path || $report->status !== 'completed') {
            return back()->with('error', 'Relatório não disponível para download');
        }

        return response()->download(storage_path('app/' . $report->file_path));
    }
}
