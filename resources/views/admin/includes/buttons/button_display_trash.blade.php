@if(Route::has($_base_route . '.trash'))
    <div style="margin-left: 1080px; margin-top: -50px;">
        <a href="{{route($_base_route . '.trash')}}" class="btn btn-danger btn-xs"><i class="bx bx-trash me-1">&nbsp;Trash {{$_panel}}</i></a>
    </div>
@endif <br>

