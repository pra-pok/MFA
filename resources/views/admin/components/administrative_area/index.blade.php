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
{{--                @include('admin.includes.flash_message')--}}
                <div class=" text-nowrap table-responsive">
                    <table id="datatable" class=" table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center" width="7%">SN</th>
                                <th>Country Name</th>
                                <th>Administrative Area Name</th>
                                <th>Slug</th>
                                <th class="text-center">Rank</th>
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

    <div class="mt-4">

        <div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                @include('admin.components.administrative_area.create')
            </div>
        </div>
    </div>

    <div class="mt-4">
        <div class="modal fade" id="edit-basic" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                @include('admin.components.administrative_area.edit')
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
                        data: 'country.name'
                    },
                    {
                        data: 'name',
                        data: 'parent.name'
                    },
                    {
                        data: 'slug'
                    },
                    {
                        data: 'rank'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'createds.name'
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

                    //const editUrl = `{{ url('admin/administrative_area/${data.id}/edit') }}`;
                    const deleteUrl = `{{ url('admin/administrative_area/${data.id}') }}`;
                    const countryName = data.country?.name || (data.parent?.country?.name || '');
                    const rowContent = `
                     <td class="text-center" >${serialNumber}</td>
                     <td>${countryName}</td>
                    <td class="position-relative">
                    ${data.name}
                       <br> <span style="font-size: 13px;"> ${data.parent?.name || ''} </span>
                     <div class="dropdown d-inline-block">
                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow position-absolute top-50 end-0 translate-middle-y " data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded fs-5 d-none"></i>
                        </button>
                        <div class="dropdown-menu">
                           <a class="dropdown-item" href="javascript:void(0)" onclick="editCategory(${data.id})">
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
                    <td class="text-center">${data.rank}</td>
                    <td>${statusBadge}</td>
                    <td>
                        ${data.createds?.name || 'Null'}<br>
                        ${formattedDate}
                    </td>
                   `;
                    $(row).html(rowContent);
                },
                pageLength: 10,
                lengthMenu: [10, 25, 50, 75, 100, 150],
                responsive: true
            });

            $('#name').on('input', function() {
                var name = $(this).val();
                var slug = name.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
                $('#slug').val(slug);
            });

        });

        function loadAdministrativeAreas(countryId, selectedAreaId = null) {
            $("#parent_id").html('<option value="">None</option>');
            if (countryId) {
                $.ajax({
                    url: '/admin/administrative_area/get-parents-by-country',
                    type: "GET",
                    data: {
                        id: countryId,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        if (result.area_parents) {
                            $.each(result.area_parents, function(key, value) {
                                $("#parent_id").append(
                                    `<option value="${key}" ${selectedAreaId == key ? 'selected' : ''}>${value}</option>`
                                );
                            });
                        }
                    },
                    error: function() {
                        console.error('Error fetching administrative areas.');
                    }
                });
            }
        }

        function editCategory(id) {
            $.ajax({
                url: `/admin/administrative_area/${id}/edit`,
                type: 'GET',
                success: function(response) {
                    console.log(response);
                    const formInstance = $("#editForm");
                    formInstance.find("select[name='country_id']").val(response.data.country_id)
                        .change();
                    const selectedParentId = response.data.parent_id;
                    loadAdministrativeAreas(response.data.country_id, selectedParentId);
                    formInstance.find("input[name='name']").val(response.data.name);
                    formInstance.find("input[name='slug']").val(response.data.slug);
                    formInstance.find("input[name='rank']").val(response.data.rank);

                    if (response.data.status == 1) {
                        formInstance.find("input[name='status'][value='1']").prop('checked', true);
                    } else {
                        formInstance.find("input[name='status'][value='0']").prop('checked', true);
                    }

                    formInstance.find("input[name='name']").on('input', function() {
                        var name = $(this).val();
                        var slug = name.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g,
                            '');
                        formInstance.find("input[name='slug']").val(slug);
                    });
                    $('#editForm').attr('action', `/admin/administrative_area/${id}`);

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
    <script>
        $(document).ready(function() {


            $('#country_id').change(function() {
                var idcountry = this.value;
                $("#parent_id").html('<option value="">None</option>');
                if (idcountry) {
                    $.ajax({
                        url: '/admin/administrative_area/get-parents-by-country',
                        type: "GET",
                        data: {
                            id: idcountry,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(result) {
                            if (result.parents) {
                                $.each(result.parents, function(key, value) {
                                    $("#parent_id").append('<option value="' + key +
                                        '">' + value + '</option>');
                                });

                            }
                        }
                    });
                }
            });


        });
    </script>
    @include('admin.includes.javascript.display_none')
@endsection
