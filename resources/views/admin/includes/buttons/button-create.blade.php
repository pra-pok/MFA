@if(Route::has($_base_route . '.create'))

        <a href="{{ route($_base_route . '.create') }}" class="btn create-new btn-primary" aria-label="Create {{ $_panel }}">
            <i class="icon-base bx bx-plus icon-sm"></i> Add
        </a>

@endif <br>

