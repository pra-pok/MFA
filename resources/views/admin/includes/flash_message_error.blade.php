{{-- error-message --}}

@if($errors->any())
    <div>
        @foreach ($errors->all() as $error)
{{--             @php dd($error) @endphp--}}
            <div class="alert alert-block alert-danger">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                {{ $error }}
            </div>
        @endforeach
    </div>
@endif
