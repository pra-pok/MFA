<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    @foreach($data['record']->organizationfacilities as $facility)
                        <div class="col-md-2 col-lg-3 mb-7">
                            <i class="{{ $facility->facility->icon }}"></i>
                            {{ $facility->facility->title }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

