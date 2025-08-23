@component('mail::message')
# {{ $alert->title }}

{{ $alert->message }}

**Dispositivo:** {{ $alert->device?->name ?? 'N/A' }}  
**Valor atual:** {{ $alert->actual_value }} kWh  
**Limite:** {{ $alert->threshold_value }}  

@component('mail::button', ['url' => config('app.url') . '/alerts'])
Ver Alertas
@endcomponent

Obrigado,  
{{ config('app.name') }}
@endcomponent
