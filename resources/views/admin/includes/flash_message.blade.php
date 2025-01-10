<script>

    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
    @if(Session::has('alert-' . $msg))
    var type = "{{ Session::get('alert-type', $msg) }}";
    switch(type){
        case 'info':
            toastr.info("{{ Session::get('alert-' . $msg) }}");
            break;

        case 'warning':
            toastr.warning("{{ Session::get('alert-' . $msg) }}");
            break;

        case 'success':
            toastr.success("{{ Session::get('alert-' . $msg) }}");
            break;

        case 'error':
            toastr.error("{{ Session::get('alert-' . $msg) }}");
            break;
    }
    @endif
    @endforeach
</script>
<div>
    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('alert-' . $msg))
            <div class="alert alert-{{ $msg }} alert-dismissible">
{{--                <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>--}}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                {{ Session::get('alert-' . $msg) }}
            </div>
        @endif
    @endforeach
</div>
