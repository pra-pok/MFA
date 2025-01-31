@extends('admin.layouts.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $_panel }}</li>
            </ol>
        </nav>

        <div class="card">
            <h5 class="card-header">{{ $_panel }}</h5>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    @include('admin.includes.buttons.button-create')

                    <div class="ml-auto">
                        @include('admin.includes.buttons.button_display_trash')
                    </div>
                </div>
                @include('admin.includes.flash_message')
                <div class="text-nowrap">
                    <table id="datatable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>SN</th>
                                <th width="170">Name</th>
                                <th>Slug</th>
                                <th>Rank</th>
                                <th>ISO Code</th>
                                <th>Currency</th>
                                <th>Status</th>
                                <th>Modified By/At</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $('#datatable').DataTable({
            ajax: {
                url: '{{ route($_base_route . '.index') }}',
                dataSrc: '',
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                error: function(xhr, status, error) {
                    console.error("Error loading data: " + error);
                    alert("Failed to load data. Please check your console for details.");
                }
            },
            columns: [{
                    data: null
                },
                {
                    data: 'name'
                },
                {
                    data: 'slug'
                },
                {
                    data: 'rank'
                },
                {
                    data: 'iso_code'
                },
                {
                    data: 'currency'
                },
                {
                    data: 'status'
                },
                {
                    data: 'createds.username'
                },
            ],
            rowCallback: function(row, data, index) {
                const statusBadge = data.status === 1 ?
                    '<span class="badge bg-label-success me-1">Active</span>' :
                    '<span class="badge bg-label-danger">De-Active</span>';
                const editUrl = `{{ url('admin/country/${data.id}/edit') }}`;
                const showUrl = `{{ url('admin/country/${data.id}/show') }}`;
                const deleteUrl = `{{ url('admin/country/${data.id}') }}`;
                const modifiedByName = data.updatedBy && data.updatedBy.username ?
                    data.updatedBy.username :
                    (data.createds && data.createds.username ? data.createds.username : 'Unknown');

                const modifiedDate = data.updated_at ? new Date(data.updated_at).toLocaleString() : (data
                    .created_at ? new Date(data.created_at).toLocaleString() : '');
                const rowContent = `
            <td>${index + 1}</td>
            <td>
                <a href="${showUrl}">  ${data.name} </a>

                <div class="dropdown" style="margin-left: 251px; margin-top: -22px;">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="${editUrl}">
                            <i class="bx bx-edit-alt me-1"></i> Edit
                        </a>
                        <form action="${deleteUrl}" method="POST" onsubmit="return confirm('Are you sure?');">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="dropdown-item">
                                <i class="bx bx-trash me-1"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </td>
            <td>${data.slug}</td>
            <td>${data.rank}</td>
            <td>${data.iso_code}</td>
            <td>${data.currency}</td>
            <td>${statusBadge}</td>
            <td>
                ${modifiedByName}<br>
                ${modifiedDate}
            </td>
        `;
                $(row).html(rowContent);
            },
            pageLength: 10,
            lengthMenu: [10, 25, 50, 75, 100, 150],
            responsive: true
        });
    </script>
@endsection
