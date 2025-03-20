<h5 class="card-header text-center bg-primary text-white py-3">Create Email</h5>
<form action="{{ route('organizationemail.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <!-- Tabs -->
    <div class="form-header">
        <div class="row gy-4 align-items-center">
            <div class="col-xxl-2 col-xl-3">
                <h4 class="card-title">Choose Audience</h4>
            </div>
            <div class="mt-3">
                <label for="organization_id" class="form-label">College/School</label>
                <select name="organization_signup_id[]" id="organization_signup_id"
                    class="form-control required select2-ajax"></select>

            </div>
            <div class="col-xxl-10 col-xl-9">
                <div class="form-tab">
                    <ul class="nav" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#customemaillist" role="tab"
                                aria-selected="true">
                                <i class="bi bi-person-fill"></i> Custom Email List
                            </a>
                        </li>

                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#importFilelist" role="tab"
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
        <div class="tab-pane fade show active" id="customemaillist" role="tabpanel">
            <div class="form-element">
                <div class="row gy-3">
                    <div class="col-md-3">
                        <h5 class="form-element-title">Recipient Email Address</h5>
                    </div>
                    <div class="col-md-9 col-12">
                        <div class="form-inner">
                            <select name="user_emails[]" id="recipient_email" class="form-control select2-multiple"
                                multiple="multiple" placeholder="Enter Email Address">
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Import File Tab -->
        <div class="tab-pane fade" id="importFilelist" role="tabpanel">
            <div class="form-element">
                <div class="row gy-3">
                    <div class="col-md-3">
                        <h5 class="form-element-title">Import File</h5>
                    </div>
                    <div class="col-md-9">
                        <div class="form-inner">
                            <input type="file" name="user_emails_file" id="file"
                                class="form-control shadow-sm" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label for="message" class="form-label">Subject</label>
        <input class="form-control" type="text" placeholder="Enter the Subject" name="subject">
    </div>
    <!-- Message Section -->
    <div class="mb-3">
        <label for="message" class="form-label">Message</label>
        <textarea class="form-control editor" name="message" rows="3"></textarea>
    </div>
    <div class="mt-3 text-center">
        <button type="submit" class="btn btn-primary btn-lg px-5">Send Email</button>
    </div>
</form>
