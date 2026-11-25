@if ($vacancy->is_paused)
    <span class="badge text-bg-warning">Pausada</span>
@else
    <span class="badge text-bg-success">Aberta</span>
@endif
