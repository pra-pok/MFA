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
            <div class="card-body">
                <div>
                    <table class="table table-bordered">
                        <tr>
                            <th>Mail Driver</th>
                            <td>{{ $data['record']->mail_driver }}</td>
                        </tr>
                        <tr>
                            <th>Mail Host</th>
                            <td>{{ $data['record']->mail_host }}</td>
                        </tr>

                        <tr>
                            <th>Mail Port</th>
                            <td>{{ $data['record']->mail_port }}</td>

                        </tr>
                        <tr>
                            <th>Mail Username</th>
                            <td>{{ $data['record']->mail_username }}</td>

                        </tr>
                        <tr>
                            <th>Mail Password</th>
                            <td>{{ $data['record']->mail_password }}</td>

                        </tr>
                        <tr>
                            <th>Mail Encryption</th>
                            <td>{{ $data['record']->mail_encryption }}</td>

                        </tr>
                        <tr>
                            <th>Mail Address</th>
                            <td>{{ $data['record']->mail_from_address }}</td>

                        </tr>
                        <tr>
                            <th>Mail From Name</th>
                            <td>{{ $data['record']->mail_from_name }}</td>

                        </tr>


                        <tr>
                            <th>Created By</th>
                            <td>{{ $data['record']->createds->username }}</td>
                        </tr>
                        @if ($data['record']->updated_by != null)
                            <tr>
                                <th>Updated By</th>
                                <td>{{ $data['record']->updatedBy->username }}</td>
                            </tr>
                        @endif
                        <tr>
                            <th>Created At</th>
                            <td>{{ $data['record']->created_at }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $data['record']->updated_at }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
