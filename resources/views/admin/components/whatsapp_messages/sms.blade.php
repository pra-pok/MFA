@php
    $vendors = DB::table('sms_api_tokens')->pluck('vendor', 'vendor');
@endphp<h5 class="card-header text-center bg-primary text-white py-3">Send Sms</h5>
<form action="{{ route('admin.sms.sendSms') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <!-- Vendor Selection (Static across all tabs) -->
    <div class="form-element">
        <div class="row gy-3">
            <div class="col-md-3">
                <h5 class="form-element-title">Choose Vendor</h5>
            </div>
            <div class="col-md-9">
                <div class="form-inner">
                    <select class="form-select shadow-sm" name="vendor" id="vendor">
                        <option value="">Select Vendor</option>
                        @foreach ($vendors as $vendor => $vendor_name)
                            <option value="{{ $vendor }}">{{ $vendor_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

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
                            <a class="nav-link active" data-bs-toggle="tab" href="#multipleSms" role="tab"
                                aria-selected="true">
                                <i class="bi bi-person-fill"></i>Multiple SMS
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#groupSms" role="tab"
                                aria-selected="false" tabindex="-1">
                                <i class="bi bi-people-fill"></i> Group SMS
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#importSmsFile" role="tab"
                                aria-selected="false" tabindex="-1">
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
        <div class="tab-pane fade show active" id="multipleSms" role="tabpanel">
            <div class="form-element mt-3">
                <div class="row gy-3">
                    <div class="col-md-3">
                        <h5 class="form-element-title">Recipient Phone</h5>
                    </div>
                    <div class="col-md-9">
                        <div class="form-inner">
                            <select name="recipients[]" id="sms" class="form-control select2-multiple"
                                multiple="multiple" placeholder="Enter phone numbers">
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Group Audience Tab -->
        <div class="tab-pane fade" id="groupSms" role="tabpanel">
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
        <div class="tab-pane fade" id="importSmsFile" role="tabpanel">
            <div class="form-element">
                <div class="row gy-3">
                    <div class="col-md-3">
                        <h5 class="form-element-title">Import File</h5>
                    </div>
                    <div class="col-md-9">
                        <div class="form-inner">
                            <input type="file" name="contacts_file" id="file"
                                class="form-control shadow-sm" />
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