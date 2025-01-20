@extends('admin.layouts.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">{{$_panel}}</li>
            </ol>
        </nav>

        <div class="card">
            <h5 class="card-header">{{$_panel}}</h5>
            <div style="margin-left: 15px;">
                <button
                    type="button"
                    class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#basicModal">
                    Create {{$_panel}}
                </button>
            </div>
            @include('admin.includes.buttons.button_display_trash')
            @include('admin.includes.flash_message')
            <div class="card-body">
                <div class=" text-nowrap">
                    <table id="datatable" class=" table table-bordered">
                        <thead>
                        <tr>
                            <th>SN</th>
                            <th>Country Name</th>
                            <th>Administrative Area Name</th>
                            <th>Slug</th>
                            <th>Rank</th>
                            <th>Status</th>
                            <th>Modified By/At</th>
                        </tr>
                        </thead>
                        <tbody class="table-border-bottom-0" id="area">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{--    createModal--}}
    <div class="mt-4">
        <!-- Button trigger modal -->
        <!-- Modal -->
        <div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
               @include('admin.components.administrative_area.create')
            </div>
        </div>
    </div>

@endsection
@section('js')
    <script>
        $(document).ready(function () {
            $('#datatable').DataTable({
                ajax: {
                    url: '{{ route($_base_route . '.index') }}',
                    dataSrc: '',
                    type: "GET",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                },
                columns: [
                    {data: null},
                    {data: 'country.name'},
                    {data: 'name'},
                    {data: 'slug'},
                    {data: 'rank'},
                    {data: 'status'},
                    {data: 'createds.name'},

                ],
                columnDefs: [
                    {
                        targets: '_all',
                        visible: true
                    }
                ],
                rowCallback: function (row, data, index) {
                    const formattedDate = data.created_at ? new Date(data.created_at).toLocaleString() : '';
                    const statusBadge = data.status === 1
                        ? '<span class="badge bg-label-primary me-1">Active</span>'
                        : '<span class="badge bg-label-danger">De-Active</span>';

                    const editUrl = `{{ url('admin/administrative_area/${data.id}/edit') }}`;
                    const deleteUrl = `{{ url('admin/administrative_area/${data.id}') }}`;
                    // Custom row format
                    const rowContent = `
                    <td>${index + 1}</td>
                    <td>${data.country?.name || 'No Country'}</td>

                    <td>${data.name}
                        <br> <span style="font-size: 13px;"> ${data.parent?.name || 'No Administrative Area'} </span>
                     <div class="dropdown" style="margin-left: 251px; margin-top: -22px;" >
                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="${editUrl}" >
                            <i class="bx bx-edit-alt me-1"></i> Edit
                        </a>
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
                    <td>${data.slug}</td>
                    <td>${data.rank}</td>
                    <td>${statusBadge}</td>
                    <td>
                        ${data.createds?.name || 'Null'}<br>
                        ${formattedDate}
                    </td>
`;
                    // Replace the content of the row
                    $(row).html(rowContent);
                },
                pageLength: 5,
                lengthMenu: [5, 10, 25, 50],
                responsive: true
            });
        });

    </script>
    <script>
        $(document).ready(function () {
            $('#name').on('input', function () {
                var name = $(this).val();
                var slug = name.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
                $('#slug').val(slug);
            });

            $('#country_id').change(function () {
                var idcountry = this.value;
                $("#parent_id").html('<option value="">None</option>'); // Reset the parent dropdown
                if (idcountry) {
                    $.ajax({
                        url: '/admin/administrative_area/get-parents-by-country', // Replace with your route URL
                        type: "GET",
                        data: {
                            id: idcountry,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function (result) {
                            if (result.parents) {
                                $.each(result.parents, function (key, value) {
                                    $("#parent_id").append('<option value="' + key + '">' + value + '</option>');
                                });
                            }
                        }
                    });
                }
            });
        });
    </script>

@endsection
