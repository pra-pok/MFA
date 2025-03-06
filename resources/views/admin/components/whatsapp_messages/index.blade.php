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
                <div class="text-nowrap table-responsive">
                    <table id="datatable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>SN</th>
                                <th>Recipient Phone</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Sent At</th>
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
            columns: [
                {
                    data: null
                },
                {
                    data: 'recipient_phone'
                },
                {
                    data: 'message'
                },
                {
                    data: 'status',
                    render: function(data, type, row) {
                        switch (data) {
                            case 'pending':
                                return '<span class="badge bg-warning">Pending</span>';
                            case 'sent':
                                return '<span class="badge bg-success">Sent</span>';
                            case 'failed':
                                return '<span class="badge bg-danger">Failed</span>';
                            default:
                                return '<span class="badge bg-secondary">Unknown</span>';
                        }
                    }
                },
                {
                    data: 'created_at',
                    render: function(data) {
                        return new Date(data).toLocaleString();
                    }
                },
             
            ],
            rowCallback: function(row, data, index) {
                $('td:eq(0)', row).html(index + 1);
            },
            pageLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            responsive: true
        });
    </script>
    @include('admin.includes.javascript.display_none')
@endsection