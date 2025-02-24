<div class="row">
    @foreach($data['record']->organizationMembers as $item)
        <div class="col-md-12"> <!-- Each card takes 1/4th of the row -->
            <div class="card mb-5">
                <div class="card-body">
                    <h5 class="card-title">{{$item->organizationGroup->title ?? ''}}</h5>
                    <img src="{{ url('/file/organization_member/'. $item->photo) }}" class="img-thumbnail" width="200px" height="5=200px"
                         alt="{{$item->name ?? ''}}">
                    <p><strong>Name:</strong> {{$item->name ?? ''}}</p>
                    <p><strong>Rank:</strong> {{$item->rank ?? ''}}</p>
                    <p><strong>Designation:</strong> {{$item->designation ?? ''}}</p>
                    <p><strong>Bio:</strong> {!! $item->bio !!}</p>
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
