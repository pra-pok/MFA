@if ($errors->has($key))
    <span class="text-danger">{{ $errors->first($key) }}</span>
@endif
