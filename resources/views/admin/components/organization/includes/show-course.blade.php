<table id="datatable" class="table table-bordered">
    <thead>
    <tr>
        <th>ID</th>
        <th>Course</th>
        <th>Min Fee Range</th>
        <th>Max Fee Range</th>
        <th>Description</th>
        <th>Modified By/At</th>

    </tr>
    </thead>
    <tbody class="table-border-bottom-0">
    @foreach($data['record']->organizationCourses as $item)
        <tr>
            <td>{{$item->id}}</td>
            <td>
                {{$item->course->title}}
            </td>
            <td>
                {{$item->start_fee}}
            </td>
            <td>
                {{$item->end_fee}}
            </td>
            <td>
                {!! $item->description !!}
            </td>
            <td>
                {{$item->createds->username}} <br>
                {{$item->updated_at}}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

