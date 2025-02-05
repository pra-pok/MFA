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
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#basicModal">
                        <i class="icon-base bx bx-plus icon-sm"></i>
                    </button>

                    <div class="ml-auto">
                        @include('admin.includes.buttons.button_display_trash')
                    </div>
                </div>
                @include('admin.includes.flash_message')
                <div class="table-responsive text-nowrap">
                    <table id="datatable" class=" table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center" width="7%" >SN</th>
                                <th>Title</th>
                                <th>Icon</th>
                                <th class="text-center">Rank</th>
                                <th class="text-center">Status</th>
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
    <div class="mt-4">
        <!-- Button trigger modal -->
        <!-- Modal -->
        <div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                @include('admin.components.facilities.create')
            </div>
        </div>
    </div>
    <div class="mt-4">
        <!-- Button trigger modal -->
        <!-- Modal -->
        <div class="modal fade" id="edit-basic" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                @include('admin.components.facilities.edit')
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $('#datatable').DataTable({
            ajax: {
                url: '{{ route($_base_route . '.index') }}',
                dataSrc: 'data',
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
                    data: 'title'
                },
                {
                    data: 'icon'
                },
                {
                    data: 'rank'
                },
                {
                    data: 'status'
                },
                {
                    data: 'createds.username'
                },
            ],
            rowCallback: function(row, data, index) {
                const pageInfo = $('#datatable').DataTable().page.info();
                const pageIndex = pageInfo.page;
                const pageLength = pageInfo.length;

                const serialNumber = (pageIndex * pageLength) + (index + 1);
                const statusBadge = data.status === 1 ?
                    '<span class="badge bg-label-success me-1">Active</span>' :
                    '<span class="badge bg-label-danger">De-Active</span>';

                const deleteUrl = `{{ url('facilities/${data.id}') }}`;
                const modifiedByName = data.updatedBy && data.updatedBy.username ?
                    data.updatedBy.username :
                    (data.createds && data.createds.username ? data.createds.username : 'Unknown');
                const modifiedDate = data.updated_at ? new Date(data.updated_at).toLocaleString() : (data
                    .created_at ? new Date(data.created_at).toLocaleString() : '');
                const rowContent = `
            <td class="text-center">${serialNumber}</td>
            <td class="position-relative">
            ${data.title}
                <div class="dropdown d-inline-block">
                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow position-absolute top-50 end-0 translate-middle-y" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded fs-5 d-none"></i>
                        </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="javascript:void(0)" onclick="editCategory(${data.id})">
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
            <td>${data.icon}</td>
            <td class="text-center">${data.rank}</td>
            <td class="text-center">${statusBadge}</td>
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

        function editCategory(id) {
            $.ajax({
                url: `/facilities/${id}/edit`,
                type: 'GET',
                success: function(response) {
                    //console.log(response);
                    const formInstance = $("#editForm");
                    formInstance.find("input[name='title']").val(response.data.title);
                    formInstance.find("input[name='rank']").val(response.data.rank);
                    formInstance.find("input[name='icon']").val(response.data.icon);
                    if (response.data.status == 1) {
                        formInstance.find("input[name='status'][value='1']").prop('checked', true);
                    } else {
                        formInstance.find("input[name='status'][value='0']").prop('checked', true);
                    }
                    $('#editForm').attr('action', `/facilities/${id}`);

                    $('#edit-basic').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error("Error loading edit data: " + error);
                    alert("Failed to load data for editing.");
                }
            });

        }
        $('#basicModal').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
        });
    </script>
    @include('admin.includes.javascript.display_none')
@endsection
