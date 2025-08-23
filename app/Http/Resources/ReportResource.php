<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'period_type' => $this->period_type,
            'period_start' => $this->period_start->format('Y-m-d'),
            'period_end' => $this->period_end->format('Y-m-d'),
            'status' => $this->status,
            'format' => $this->format,
            'created_at' => $this->created_at->toIso8601String(),
            
            // Dados calculados
            'size' => $this->file_path ? filesize(storage_path('app/'.$this->file_path)) : 0,
            'download_url' => $this->when(
                $this->status === 'completed' && $this->file_path,
                route('api.reports.download', $this->id)
            ),
            
            // Estatísticas resumidas
            'summary' => $this->getReportSummary(),
            
            // Links
            'self' => route('api.reports.show', $this->id),
        ];
    }
    
    protected function getReportSummary()
    {
        if (!$this->data || !is_array($this->data)) {
            return null;
        }
        
        return [
            'total_consumption' => $this->data['total_consumption'] ?? null,
            'average_power' => $this->data['average_power'] ?? null,
            'peak_power' => $this->data['peak_power'] ?? null,
            'total_cost' => $this->data['total_cost'] ?? null,
            'carbon_footprint' => $this->data['carbon_footprint'] ?? null,
        ];
    }
}