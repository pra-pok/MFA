<form action="{{route('organization_gallery.store')}}" method="POST"  enctype="multipart/form-data">
    @csrf
    <input  type="hidden" name="organization_id" value=" "  />
    <table id="datatable" class="table table-bordered">
        <thead>
        <tr>
            <th>SN</th>
            <th>Select Category</th>
            <th width="150">Type</th>
            <th>Caption</th>
            <th>Rank</th>
            <th>Media</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody class="table-border-bottom-0">
        <tr>
            <td>1</td>
            <td>
                <select class="form-select required" id="gallery_category_id" name="gallery_category_id"
                        aria-label="Select Category">
                    <option selected disabled>Select Category Name</option>
                    @foreach ($data['gallery'] as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input
                    name="type"
                    class="form-check-input"
                    type="radio"
                    value="1"
                    id="imageType-1"
                    checked
                />
                <label class="form-check-label" for="imageType-1"> Image </label>
                <br><br>
                <input
                    name="type"
                    class="form-check-input"
                    type="radio"
                    value="0"
                    id="videoType-1"
                />
                <label class="form-check-label" for="videoType-1"> Video </label>
            </td>
            <td>
                <input type="text" name="caption" class="form-control"/>
            </td>
            <td>
                <input type="text" name="rank" class="form-control"/>
            </td>
            <td>
                <input type="text" name="media" class="form-control media-text" style="display: none;"/>
                <input type="file" name="media_file[]" class="form-control media-file" multiple/>
            </td>
            <td>
                <a href="#" class="btn btn-primary add-row">
                    <i class="icon-base bx bx-plus icon-sm"></i>
                </a><br><br>
                <a href="#" class="btn btn-danger remove-row">
                    <i class="icon-base bx bx-trash icon-sm"></i>
                </a>
            </td>
        </tr>
        </tbody>
    </table>
    <div class="d-flex justify-content-between mt-3">
        <button type="submit" class="btn btn-secondary" id="nextBtn">Next</button>
    </div>
</form>

