<div class="accordion" id="accordionPanelsStayOpenExample">
    @foreach($data['record']->organizationCourses as $index => $item)
        @php $uniqueId = 'collapse-' . $index; @endphp
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading-{{$index}}">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#{{$uniqueId}}"
                        aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" aria-controls="{{$uniqueId}}">
                    {{$item->course->title ?? ' '}}
                </button>
            </h2>
            <div id="{{$uniqueId}}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" aria-labelledby="heading-{{$index}}">
                <div class="accordion-body">
{{--                    <h5 class="card-title">{{$item->course->title ?? ''}}</h5>--}}
                    <p><strong>University:</strong> {{$item->university->title ?? ''}}</p>
                    <p><strong>Min Fee Range:</strong> {{$item->start_fee}} to {{$item->end_fee}}</p>
                    <p><strong>Description:</strong> {!! $item->description !!}</p>
                    <p>
                        <strong>Status:</strong>
                        @include('admin.includes.buttons.display_status',['status' => $data['record']->status])
                    </p>
                    <p><strong>Modified By:</strong> {{$item->createds->username ?? ''}} at {{$item->updated_at}}</p>
                </div>
            </div>
        </div>
    @endforeach
</div>
