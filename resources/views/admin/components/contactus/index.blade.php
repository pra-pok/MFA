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
                    <div class="ml-auto">
                        @include('admin.includes.buttons.button_display_trash')
                    </div>
                </div>
                <div class=" text-nowrap table-responsive">
                    <table id="datatable" class=" table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center" width="7%">SN</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0" id="area">
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
                ajax: {
                    url: '{{ route($_base_route . '.index') }}',
                    dataSrc: '',
                    type: "GET",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                },
                columns: [{
                        data: null
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'phone'
                    },
                    {
                        data: 'message'
                    },
                ],
                columnDefs: [{
                    targets: '_all',
                    visible: true
                }],
                rowCallback: function(row, data, index) {
                    const pageInfo = $('#datatable').DataTable().page.info();
                    const pageIndex = pageInfo.page;
                    const pageLength = pageInfo.length;

                    const serialNumber = (pageIndex * pageLength) + (index + 1);
                    const formattedDate = data.created_at ? new Date(data.created_at).toLocaleString() :
                        '';
                    const statusBadge = data.status === 1 ?
                        '<span class="badge bg-label-success me-1">Active</span>' :
                        '<span class="badge bg-label-danger">De-Active</span>';
                    const deleteUrl = `{{ url('contactus/${data.id}') }}`;
                    const rowContent = `
                     <td class="text-center" >${serialNumber}</td>
                    <td class="position-relative">
                    ${data.name}
                     <div class="dropdown d-inline-block">
                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow position-absolute top-50 end-0 translate-middle-y " data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded fs-5 d-none"></i>
                        </button>
                        <div class="dropdown-menu">
                        <form action="${deleteUrl}" method="POST" onsubmit="return confirm('Are you sure?');">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                            <button type="submit" class="dropdown-item">
                              <i class="bx bx-trash me-1"></i> Delete
                            </button>
                        </form>
                     </div>
                   </div>
                    </td>
                    <td>${data.email}</td>
                    <td class="text-center">${data.phone}</td>
                    <td>${data.message}</td>
                   `;
                    $(row).html(rowContent);
                },
                pageLength: 10,
                lengthMenu: [10, 25, 50, 75, 100, 150],
                responsive: true
            });

        });
    </script>
    @include('admin.includes.javascript.display_none')
@endsection
