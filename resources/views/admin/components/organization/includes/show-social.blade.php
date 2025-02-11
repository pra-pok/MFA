<table id="datatable" class="table table-bordered">
    <thead>
    <tr>
        <th>Name</th>
        <th>Link</th>
        <th>Modified By/At</th>

    </tr>
    </thead>
    <tbody class="table-border-bottom-0">
    @foreach($data['record']->socialMediaLinks as $item)
        <tr>
            <td>
                {{$item->name}}
            </td>
            <td>
               <a href="{{$item->url}}" target="_blank">
                   {{$item->url ?? ''}}
               </a>
            </td>
            <td>
               {{$item->createds->username}} <br>
                {{$item->updated_at}}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
