<div class="row">
    @foreach($data['record']->organizationCourses as $item)
        <div class="col-md-12"> <!-- Each card takes 1/4th of the row -->
            <div class="card mb-5">
                <div class="card-body">
                    <h5 class="card-title">{{$item->course->title ?? ''}}</h5>
                    <p><strong>Min Fee Range:</strong> {{$item->start_fee}} to {{$item->end_fee}}</p>
                    <p><strong>Description:</strong> {!! $item->description !!}</p>
                    <p>
                        <strong>Status:</strong>
                        @include('admin.includes.buttons.display_status',['status' => $data['record']->status])
                    </p>
                    <p><strong>Modified By:</strong> {{$item->createds->username}} at {{$item->updated_at}}</p>
                </div>
            </div>
        </div>
    @endforeach
</div>
