@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $_panel }}</li>
            </ol>
        </nav>

        <div class="card">
            <h5 class="card-header">{{ $_panel }}</h5>
            @include('admin.includes.buttons.button-back')
            @include('admin.includes.flash_message')
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table id="datatable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>SN</th>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Rank</th>
                                <th>Iso Code</th>
                                <th>Currency</th>
                                <th>Deleted_By</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach ($data['records'] as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->slug }}</td>
                                    <td>{{ $item->rank }}</td>
                                    <td>{{ $item->iso_code }}</td>
                                    <td>{{ $item->currency }}</td>
                                    <td>{{ $item->deleted_at }}</td>
                                    <td>
                                        @include('admin.includes.buttons.display_status', [
                                            'status' => $item->status,
                                        ])
                                    </td>
                                    <td>
                                        <a href="{{ route($_base_route . '.restore', $item->id) }}"
                                            class="btn btn-warning btn-sm"><i class="fas fa-recycle"></i> Restore</a>
                                        <form action="{{ route($_base_route . '.force_delete', $item->id) }}"
                                            method="POST">
                                            <input type="hidden" name="_method" value="DELETE">
                                            @csrf
                                            <input type="submit" value="Delete" class="btn btn-danger btn-sm">
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $('#datatable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
            });
        });
    </script>
@endsection
