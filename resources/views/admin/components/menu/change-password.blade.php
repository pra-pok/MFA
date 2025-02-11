<form id="resetPasswordForm" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel1">Reset Password</h5>
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id" id="id" value="">
            <div class="mt-3 form-password-toggle">
                <label for="title" class="form-label">New Password</label>
                <div class="input-group input-group-merge">
                    <input
                        type="password"
                        name="password"
                        class="form-control required"
                        id="password" autocomplete="current-password"/>
                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                    <x-input-error :messages="$errors->get('password')" class="mt-2"/>
                </div>
            </div>
            <div class="mt-3 form-password-toggle">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <div class="input-group input-group-merge">
                    <input
                        type="password"
                        name="password_confirmation"
                        class="form-control required"
                        id="password_confirmation" autocomplete="current-password"/>
                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2"/>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                        style="margin: 10px;">
                    Close
                </button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</form>
