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
            @include('admin.includes.buttons.button-back')
            <div class="card-body" >
                <div >
                    <table class="table table-bordered">
                        <tr>
                            <th>ID</th>
                            <td>{{$data['record']->id}}</td>
                        </tr>
                        <tr>
                            <th>University Name</th>
                            <td>{{$data['record']->title}}</td>
                        </tr>
                        <tr>
                            <th>Slug</th>
                            <td>{{$data['record']->slug}}</td>
                        </tr>
                        <tr>
                            <th>Rank</th>
                            <td>{{$data['record']->rank}}</td>
                        </tr>
                        <tr>
                            <th>Types</th>
                            <td>{{$data['record']->types}}</td>
                        </tr>
                        <tr>
                            <th>Logo</th>
                            <td>
                                @if(!empty($data['record']->logo))
                                    <a href="{{asset('images/' . $folder . '/' . $data['record']->logo)}}" target="_blank">
                                        <img src="{{asset('images/' . $folder . '/' . $data['record']->logo)}}" alt="{{$data['record']->title}}" width="150px" />
                                    </a>
                                @else
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png" alt="{{$data['record']->title}}" width="200px" />
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>{!! $data['record']->description !!}</td>
                        </tr>
                        <tr>
                            <th>Meta Title</th>
                            <td>{{$data['record']->meta_title}}</td>
                        </tr>
                        <tr>
                            <th>Meta Description</th>
                            <td>{{$data['record']->meta_description}}</td>
                        </tr>
                        <tr>
                            <th>Meta Keyword</th>
                            <td>{{$data['record']->meta_keywords}}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>@include('admin.includes.buttons.display_status',['status' => $data['record']->status])</td>
                        </tr>
                        <tr>
                            <th>Created By</th>
                            <td>{{$data['record']->createdBy->name}}</td>
                        </tr>
                        @if($data['record']->updated_by != null)
                            <tr>
                                <th>Updated By</th>
                                <td>{{$data['record']->updatedBy->name}}</td>
                            </tr>
                        @endif
                        <tr>
                            <th>Created At</th>
                            <td>{{$data['record']->created_at}}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{$data['record']->updated_at}}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
