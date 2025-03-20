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
                    @include('admin.includes.buttons.button-create')

                    <div class="ml-auto">
                        @include('admin.includes.buttons.button_display_trash')
                    </div>
                </div>
                <div class=" text-nowrap table-responsive">
                    <table id="datatable" class=" table table-bordered">
                        <thead>
                            <tr>
                                <th width="6%">SN</th>
                                <th>Driver</th>
                                <th>Encryption</th>
                                <th>Address</th>
                                <th>Name</th>
                                <th>Host</th>
                                <th>Port</th>
                                <th>Username</th>
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
            columns: [{data: null},
                {data: 'Driver'},
                {data: 'Encryption'},
                {data: 'Address'},
                {data: 'Name'},
                {data: 'Host'},
                {data: 'Port'},
                {data: 'Username'},
                {data: 'createds.username'},

            ],
            rowCallback: function(row, data, index) {
                console.log(data);
                const editUrl = `{{ url('organization-email-config/${data.id}/edit') }}`;
                const showUrl = `{{ url('organization-email-config-show/${data.id}') }}`;
                const deleteUrl = `{{ url('organization-email-config/${data.id}') }}`;
                const mail_driver = data.mail_driver ? `${data.mail_driver}` : '';
                const mail_encryption = data.mail_encryption ? `${data.mail_encryption}` : '';
                const mail_from_address = data.mail_from_address ? `${data.mail_from_address}` : '';
                const mail_from_name = data.mail_from_name ? `${data.mail_from_name}` : '';
                const mail_host = data.mail_host ? `${data.mail_host}` : '';
                const mail_port = data.mail_port ? `${data.mail_port}` : '';
                const username = data.mail_username ? `${data.mail_username}` : '';
                const modifiedByName = data.updated_by  ? data.updated_by : (data.created_by  ? data.data.created_by : 'Unknown');
                const modifiedDate = data.updated_at ? new Date(data.updated_at).toLocaleString() : (data.created_at ? new Date(data.created_at).toLocaleString() : '');
                const rowContent = `
            <td>${index + 1}</td>
            <td class="position-relative"> <a href="${showUrl}">  ${mail_driver}  </a>
                <div class="dropdown d-inline-block">
                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow position-absolute top-50 end-0 translate-middle-y" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded fs-5 d-none"></i>
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
            <td>
              <div style="white-space:pre-wrap;">${mail_encryption}</div>
            </td>
              <td>
              <div style="white-space:pre-wrap;">${mail_from_address}</div>
            </td>
             <td>
              <div style="white-space:pre-wrap;">${mail_from_name}</div>
            </td>
              <td>
              <div style="white-space:pre-wrap;">${mail_host}</div>
            </td>
              <td>
              <div style="white-space:pre-wrap;">${mail_port}</div>
            </td>
               <td>
              <div style="white-space:pre-wrap;">${username}</div>
            </td>
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
    @include('admin.includes.javascript.display_none')
@endsection
