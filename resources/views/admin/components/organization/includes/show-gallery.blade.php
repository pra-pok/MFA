<table id="datatable" class="table table-bordered">
    <thead>
    <tr>
        <th>ID</th>
        <th>Gallery Category</th>
        <th>Caption</th>
        <th>Rank</th>
        <th>Type</th>
        <th>Media</th>
        <th>Modified By/At</th>

    </tr>
    </thead>
    <tbody class="table-border-bottom-0">
    @foreach($data['record']->organizationGalleries as $item)
        <tr>
            <td>{{$item->id}}</td>
            <td>
                {{$item->galleryCategory->name ?? ''}}
            </td>
            <td>
                {{$item->caption}}
            </td>
            <td>{{$item->rank}}</td>
            <td>
                @if($item->type == 1)
                    Image
                @elseif($item->type == 0)
                    Video
                @else
                    {{$item->type}}
                @endif
            </td>
            <td>
                @if($item->type == 1)
                    <img src="{{ asset('images/organization-gallery/' . $item->media) }}" alt="{{$item->caption}}" style="width: 100px; height: 100px;">
                @elseif($item->type == 0)
                    <a href=" {{$item->media}}" target="_blank">
                        {{$item->media}}
                    </a>
                @else
                    {{$item->type}}
                @endif
            </td>
            <td>
                {{$item->createds->username}} <br>
                {{$item->updated_at}}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
