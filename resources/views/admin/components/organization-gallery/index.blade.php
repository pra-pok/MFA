@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">{{$_panel}}</li>
            </ol>
        </nav>

        <div class="card">
            <h5 class="card-header">{{$_panel}}</h5>
            @include('admin.includes.buttons.button-create')
            @include('admin.includes.buttons.button_display_trash')
            <div class="card-body" >
                <div class="table-responsive text-nowrap">
                    <table id="datatable" class=" table table-bordered">
                        <thead>
                            <tr>
                                <th>SN</th>
                                <th>Stream Name</th>
                                <th>Level Name</th>
                                <th>Title</th>
                                <th>Slug</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
    {{--                        @foreach ($data['rows'] as $item)--}}
    {{--                            <tr>--}}
    {{--                                <td>{{ $loop->iteration }}</td>--}}
    {{--                                <td>{{ $item->title }}</td>--}}
    {{--                                <td>{{ $item->slug }}</td>--}}
    {{--                                <td>{{ $item->rank }}</td>--}}
    {{--                                <td>{{ $item->createdBy->name }}</td>--}}
    {{--                                <td>--}}
    {{--                                    @include('admin.includes.buttons.display_status',['status' => $item->status])--}}
    {{--                                </td>--}}
    {{--                                <td>--}}
    {{--                                    @include('admin.includes.buttons.button-edit',['edit' => $item->id])--}}
    {{--                                    @include('admin.includes.buttons.button-trash',['trash' => $item->id])--}}
    {{--                                </td>--}}
    {{--                            </tr>--}}
    {{--                        @endforeach--}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
<script>
    $(document).ready(function () {
        $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route($_base_route . '.getData') }}",
            columns: [
            {
                data: null,
                name: 'id',
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
                { data: 'stream', name: 'stream' },
                { data: 'level', name: 'level' },
                { data: 'title', name: 'title' },
                { data: 'slug', name: 'slug' },

                {
                    data: 'status',
                    name: 'status',
                    render: function (data, type, row) {
                        return data === 1
                            ? '<span class="badge bg-label-primary me-1">Active</span>'
                            : '<span class="badge bg-label-danger">De-Active</span>';
                    }
                },
            { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endsection
