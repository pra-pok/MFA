@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">Student</li>
            </ol>
        </nav>
        <div class="card">
            <h5 class="card-header">Student</h5>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#basicModal">
                        <i class="icon-base bx bx-plus icon-sm"></i>
                    </button>

                    <div class="ml-auto">

                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                    <table id="datatable" class=" table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center" width="7%" >SN</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th class="text-center">Phone</th>
                                <th class="text-center">Address</th>
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
                @include('admin.components.students.create')
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $('#datatable').DataTable({
            ajax: {
                url: '{{ route( 'students.index') }}',
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
                    data: 'name'
                },
                {
                    data: 'email'
                },
                {
                    data: 'phone'
                },
                {
                    data: 'address'
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
                const modifiedByName = data.updatedBy && data.updatedBy.username ?
                    data.updatedBy.username :
                    (data.createds && data.createds.username ? data.createds.username : 'Unknown');
                const modifiedDate = data.updated_at ? new Date(data.updated_at).toLocaleString() : (data
                    .created_at ? new Date(data.created_at).toLocaleString() : '');
                const rowContent = `
            <td class="text-center">${serialNumber}</td>
            <td class="position-relative">
               ${data.name}
               <div class="dropdown d-inline-block">
                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow position-absolute top-50 end-0 translate-middle-y" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded fs-5 d-none"></i>
                        </button>
                </div>
            </td>
            <td>${data.slug}</td>
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
        $(document).ready(function() {
            $('#name').on('input', function() {
                var name = $(this).val();
                var slug = name.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
                $('#slug').val(slug);
            });
        });
        $('#basicModal').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
        });
    </script>
    @include('admin.includes.javascript.display_none')
@endsection
