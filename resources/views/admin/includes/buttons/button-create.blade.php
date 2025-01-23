@if(Route::has($_base_route . '.create'))
    <div style="margin-left: 15px;">
        <a href="{{ route($_base_route . '.create') }}" class="btn create-new btn-primary" aria-label="Create {{ $_panel }}">
            <i class="icon-base bx bx-plus icon-sm"></i> Add
        </a>
    </div>
@endif <br>

