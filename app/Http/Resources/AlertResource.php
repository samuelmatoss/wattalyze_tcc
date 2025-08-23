<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AlertResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'severity' => $this->severity,
            'title' => $this->title,
            'message' => $this->message,
            'is_read' => (bool) $this->is_read,
            'is_resolved' => (bool) $this->is_resolved,
            'created_at' => $this->created_at->toIso8601String(),
            'resolved_at' => $this->resolved_at?->toIso8601String(),
            
            // Dados calculados
            'duration' => $this->resolved_at 
                ? $this->resolved_at->diffInMinutes($this->created_at)
                : now()->diffInMinutes($this->created_at),
                
            'priority_score' => $this->calculatePriority(),
            
            // Relacionamentos
            'device' => $this->whenLoaded('device', function () {
                return [
                    'id' => $this->device->id,
                    'name' => $this->device->name,
                ];
            }),
            
            'environment' => $this->whenLoaded('environment', function () {
                return [
                    'id' => $this->environment->id,
                    'name' => $this->environment->name,
                ];
            }),
            
            // Links
            'actions' => [
                'mark_read' => route('api.alerts.mark-read', $this->id),
                'resolve' => route('api.alerts.resolve', $this->id),
            ],
        ];
    }
    
    protected function calculatePriority()
    {
        $severityWeights = [
            'low' => 1,
            'medium' => 2,
            'high' => 3,
            'critical' => 4,
        ];
        
        $ageHours = now()->diffInHours($this->created_at);
        
        return $severityWeights[$this->severity] * (1 + $ageHours/24);
    }
}