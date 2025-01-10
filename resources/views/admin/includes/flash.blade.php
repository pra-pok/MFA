{{-- flash-message --}}
<br>
<div id="flash" class="flash-message">
    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('alert-' . $msg))
            <div class="alert alert-{{ $msg }} alert-dismissible">
                <<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                {{ Session::get('alert-' . $msg) }}
            </div>
        @endif
    @endforeach
</div>

{{-- error-message --}}

<div id="flash">
    @foreach ($errors->all() as $error)
        <div class="alert alert-block alert-danger">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            {{ $error }}
        </div>
    @endforeach
</div>

@section('js')
    @parent

    <script type="text/javascript">
        $(document).ready( function() {
            $('#flash').delay(1000).fadeOut();
        });
    </script>
@endsection

