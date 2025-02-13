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
                @include('admin.includes.flash_message')
                <div class=" text-nowrap table-responsive">
                    <table id="datatable" class=" table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center" width="7%">SN</th>
                                <th>Country Name</th>
                                <th>Name</th>
                                <th class="text-center">Rank</th>
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
                        data: 'state.parent.country.name'
                    },
                    {
                        data: 'name',
                        data: 'state.name'
                    },
                    {
                        data: 'rank'
                    },
                    {
                        data: 'createds.name'
                    },

                ],
                columnDefs: [
                    {
                       targets: '_all',
                       visible: true
                    }
                ],

                rowCallback: function(row, data, index) {
                    const pageInfo = $('#datatable').DataTable().page.info();
                    const pageIndex = pageInfo.page;
                    const pageLength = pageInfo.length;

                    const serialNumber = (pageIndex * pageLength) + (index + 1);
                    const formattedDate = data.created_at ? new Date(data.created_at).toLocaleString() :
                        '';

                    const rowContent = `
                     <td class="text-center" >${serialNumber}</td>
                     <td>${data.state?.parent?.country?.name || ' '}</td>
                    <td class="position-relative">
                    ${data.name}
                      <br>  ${data.state?.name}
                    </td>
                    <td class="text-center">${data.rank}</td>
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
    </script>
    @include('admin.includes.javascript.display_none')
@endsection
