{{-- gallery.blade.php --}}
<form action="{{ route('organization_gallery.update', $data['record']->id) }}" method="POST"
      enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="organization_id" value="{{ $data['record']->id ?? '' }}"/>
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
        @if(isset($data['record']) && $data['record']->organizationGalleries()->count() > 0)
            @foreach ($data['record']->organizationGalleries as $index => $gallery)
                {{--        @foreach($data['record']->organizationGalleries as $index => $gallery)--}}
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <select class="form-select required" name="gallery_category_id[{{ $index }}]"
                                aria-label="Select Category">
                            <option selected disabled>Select Category Name</option>
                            @foreach ($data['gallery'] as $key => $value)
                                <option
                                    value="{{ $key }}" {{ $gallery->gallery_category_id == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input name="type[{{ $index }}]" class="form-check-input" type="radio" value="1"
                               id="imageType-{{ $index }}" {{ $gallery->type == 1 ? 'checked' : '' }} />
                        <label class="form-check-label" for="imageType-{{ $index }}"> Image </label>
                        <br><br>
                        <input name="type[{{ $index }}]" class="form-check-input" type="radio" value="0"
                               id="videoType-{{ $index }}" {{ $gallery->type == 0 ? 'checked' : '' }} />
                        <label class="form-check-label" for="videoType-{{ $index }}"> Video </label>
                    </td>
                    <td>
                        <input type="text" name="caption[{{ $index }}]" class="form-control"
                               value="{{ $gallery->caption }}"/>
                    </td>
                    <td>
                        <input type="number" name="rank[{{ $index }}]" class="form-control" min="0" max="100"
                               value="{{ $gallery->rank }}"/>
                    </td>
                    <td>
                        <input type="text" name="media[{{ $index }}]" class="form-control media-text"
                               style="display: {{ $gallery->type == 0 ? 'block' : 'none' }};"
                               value="{{ $gallery->media }}"/>
                        <input type="file" name="media_file[{{ $index }}]" class="form-control media-file"
                               style="display: {{ $gallery->type == 1 ? 'block' : 'none' }};"/>
                        @if ($gallery->type == 1 && $gallery->media)
                            <img src="{{ asset('images/organization-gallery/' . $gallery->media) }}" alt="Gallery Media"
                                 class="img-thumbnail" style="width: 100px; height: 100px; margin-top: 10px;">
                        @elseif ($gallery->type == 0)
                            <span class="text-muted"></span>
                        @else
                            <img
                                src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                                alt="No media available" class="img-thumbnail clickable-image"
                                style="width: 100px; height: 100px; margin-top: 10px;">
                        @endif
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
            @endforeach
        @else
            <tr>
                <td>1</td>
                <td>
                    <select class="form-select required" name="gallery_category_id[]" aria-label="Select Category">
                        <option selected disabled>Select Category Name</option>
                        @foreach ($data['gallery'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input name="type[]" class="form-check-input" type="radio" value="1" id="imageType-1" checked/>
                    <label class="form-check-label" for="imageType-1"> Image </label>
                    <br><br>
                    <input name="type[]" class="form-check-input" type="radio" value="0" id="videoType-1"/>
                    <label class="form-check-label" for="videoType-1"> Video </label>
                </td>
                <td>
                    <input type="text" name="caption[]" class="form-control"/>
                </td>
                <td>
                    <input type="number" name="rank[]" class="form-control" min="0" max="100"/>
                </td>
                <td>
                    <input type="text" name="media[]" class="form-control media-text" style="display: none;"/>
                    <input type="file" name="media_file[]" class="form-control media-file"/>
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
        @endif
        </tbody>
    </table>
</form>


