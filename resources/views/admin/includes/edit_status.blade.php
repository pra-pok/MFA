<div class="mt-3">
    <label for="status" class="form-label">Status</label>

    <input
        name="status"
        class="form-check-input"
        type="radio"
        value="1"
        id="activeStatus"
        {{ isset($data['record']->status) && $data['record']->status == 1 ? 'checked' : '' }}
    />
    <label class="form-check-label" for="activeStatus"> Active </label>

    <input
        name="status"
        class="form-check-input"
        type="radio"
        value="0"
        id="deactiveStatus"
        {{ isset($data['record']->status) && $data['record']->status == 0 ? 'checked' : '' }}
    />
    <label class="form-check-label" for="deactiveStatus"> In-Active </label>
</div>
