<div class="row">
    @foreach($data['record']->organizationPages as $item)
        <div class="col-md-12">
            <div class="card mb-5">
                <div class="card-body">
                    <h5 class="card-title">{{$item->page->title ?? ''}}</h5>
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
