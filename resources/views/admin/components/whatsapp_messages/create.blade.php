@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row g-6">
            <div class="col-md-12">
                <div class="card shadow-lg">
                    <h5 class="card-header text-center bg-primary text-white py-3">Create WhatsApp Message</h5>
                    @include('admin.includes.flash_message_error')
                    <div class="card-body">
                        <form action="{{ route('admin.whatsapp-messages.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <!-- Tabs -->
                            <div class="form-header">
                                <div class="row gy-4 align-items-center">
                                    <div class="col-xxl-2 col-xl-3">
                                        <h4 class="card-title">Choose Audience</h4>
                                    </div>
                                    <div class="col-xxl-10 col-xl-9">
                                        <div class="form-tab">
                                            <ul class="nav" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link active" data-bs-toggle="tab" href="#singleAudience" role="tab" aria-selected="true">
                                                        <i class="bi bi-person-fill"></i> Single Audience
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link" data-bs-toggle="tab" href="#groupAudience" role="tab" aria-selected="false" tabindex="-1">
                                                        <i class="bi bi-people-fill"></i> Group Audience
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link" data-bs-toggle="tab" href="#importFile" role="tab" aria-selected="false" tabindex="-1">
                                                        <i class="bi bi-file-earmark-plus-fill"></i> Import File
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabs Content -->
                            <div class="tab-content">
                                <!-- Single Audience Tab -->
                                <div class="tab-pane fade show active" id="singleAudience" role="tabpanel">
                                    <div class="form-element">
                                        <div class="row gy-3">
                                            <div class="col-md-3">
                                                <h5 class="form-element-title">Recipient Phone</h5>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="form-inner">
                                                    <select name="recipient_phone[]" id="recipient_phone" class="form-control select2-multiple" multiple="multiple" placeholder="Enter phone numbers">
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Group Audience Tab -->
                                <div class="tab-pane fade" id="groupAudience" role="tabpanel">
                                    <div class="form-element">
                                        <div class="row gy-3">
                                            <div class="col-md-3">
                                                <h5 class="form-element-title">Choose Group</h5>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="form-inner">
                                                    <select class="form-select shadow-sm" name="group_audience[]" multiple>
                                                        <option value="">Choose Groups</option>
                                                        <!-- Options will go here -->
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Import File Tab -->
                                <div class="tab-pane fade" id="importFile" role="tabpanel">
                                    <div class="form-element">
                                        <div class="row gy-3">
                                            <div class="col-md-3">
                                                <h5 class="form-element-title">Import File</h5>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="form-inner">
                                                    <input type="file" name="contacts" accept=".csv,.xlsx" id="file" class="form-control shadow-sm" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Message Section -->
                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control editor" name="message" rows="3"></textarea>
                            </div>
                            <div class="mt-3 text-center">
                                <button type="submit" class="btn btn-primary btn-lg px-5">Send Message</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
@include('admin.components.whatsapp_messages.script')
@endsection
