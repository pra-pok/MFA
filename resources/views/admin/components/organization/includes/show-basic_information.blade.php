<table class="table table-bordered">
    <tr>
        <th>ID</th>
        <td>{{$data['record']->id}}</td>
    </tr>
    <tr>
        <th>Country</th>
        <td>{{ $data['record']->country->name ?? '' }}</td>
    </tr>
    <tr>
        <th>Administrative Area </th>
        <td>{{ $data['record']->administrativeArea->name ?? '' }}</td>
    </tr>
    <tr>
        <th>Type</th>
        <td>{{ $data['record']->type }}</td>
    </tr>
    <tr>
        <th>College/School Name</th>
        <td>{{$data['record']->name}}</td>
    </tr>
    <tr>
        <th>Slug</th>
        <td>{{$data['record']->slug}}</td>
    </tr>
    <tr>
        <th>Address</th>
        <td>{{$data['record']->address}}</td>
    </tr>
    <tr>
        <th>Email</th>
        <td>
            {{$data['record']->email}}
        </td>
    </tr>
    <tr>
        <th>Phone</th>
        <td>{{ $data['record']->phone }}</td>
    </tr>
    <tr>
        <th>Website</th>
        <td>{{ $data['record']->website }}</td>
    </tr>
    <tr>
        <th>Established Year</th>
        <td>{{ $data['record']->established_year }}</td>
    </tr>
    <tr>
        <th>Logo</th>
        <td>
            @if($data['record']->logo != null)
                <img src="{{ asset('images/organization/' . $data['record']->logo) }}" alt="Logo" class="img-thumbnail" width="200"/>
            @else
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png" alt="Logo" class="img-thumbnail" width="200"/>
            @endif
        </td>
    </tr>

    <tr>
        <th>Banner Image</th>
        <td>
            @if($data['record']->banner_image != null)
                <img src="{{ asset('images/organization/banner/' . $data['record']->banner_image) }}" alt="Banner" class="img-thumbnail" width="200"/>
            @else
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png" alt="Banner" class="img-thumbnail" width="200"/>
            @endif
        </td>
    </tr>
    <tr>
        <th>Description</th>
        <td>{!! $data['record']->description !!}</td>
    </tr>
    <tr>
        <th>Search Keyword</th>
        <td>{{$data['record']->search_keywords}}</td>
    </tr>
    <tr>
        <th>Meta Title</th>
        <td>{{$data['record']->meta_title}}</td>
    </tr>
    <tr>
        <th>Meta Description</th>
        <td>{{$data['record']->meta_description}}</td>
    </tr>
    <tr>
        <th>Meta Keyword</th>
        <td>{{$data['record']->meta_keywords}}</td>
    </tr>
    <tr>
        <th>Status</th>
        <td>@include('admin.includes.buttons.display_status',['status' => $data['record']->status])</td>
    </tr>
    <tr>
        <th>Created By</th>
        <td>{{$data['record']->createds->name}}</td>
    </tr>
    @if($data['record']->updated_by != null)
        <tr>
            <th>Updated By</th>
            <td>{{$data['record']->updatedBy->name}}</td>
        </tr>
    @endif
    <tr>
        <th>Created At</th>
        <td>{{$data['record']->created_at}}</td>
    </tr>
    <tr>
        <th>Updated At</th>
        <td>{{$data['record']->updated_at}}</td>
    </tr>
</table>
