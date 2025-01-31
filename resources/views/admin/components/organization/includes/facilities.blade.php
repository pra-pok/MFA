<form action="{{ route('organization_facilities.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="organization_id" value="{{ $data['record']->id ?? '' }}"/>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        @foreach($data['faculty'] as $facility)
                            <div class="col-md-2 col-lg-3 mb-3">
                                {{--                                                <input type="hidden" name="facility_id[{{ $facility->id }}]" value="0">--}}
                                <input type="checkbox" name="facility_id[{{ $facility->id }}]" value="{{ $facility->id }}"
                                       class="form-check-input checkbox"  {{ in_array($facility->id, $data['Facilities']) ? 'checked' : '' }}/>
                                <i class="{{ $facility->icon }}"></i>
                                {{ $facility->title }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
