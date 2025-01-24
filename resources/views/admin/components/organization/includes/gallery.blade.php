<form action="{{ route('organization_gallery.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="organization_id" value="{{ $data['record']->id }} " />
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
        <tbody>
        @if(isset($data['record']) && $data['record']->organizationGalleries()->count() > 0)
            @foreach ($data['record']->organizationGalleries as $index => $gallery)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <input type="hidden" name="id[{{ $index }}]" value="{{ $gallery->id }}" />
                        <select class="form-select required" name="gallery_category_id[{{ $index }}]" aria-label="Select Category">
                            <option selected disabled>Select Category Name</option>
                            @foreach ($data['gallery'] as $key => $value)
                                <option value="{{ $key }}" {{ $gallery->gallery_category_id == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input name="type[{{ $index }}]" class="form-check-input" type="radio" value="1" id="imageType-{{ $index }}" {{ $gallery->type == 1 ? 'checked' : '' }} />
                        <label class="form-check-label" for="imageType-{{ $index }}"> Image </label>
                        <br><br>
                        <input name="type[{{ $index }}]" class="form-check-input" type="radio" value="0" id="videoType-{{ $index }}" {{ $gallery->type == 0 ? 'checked' : '' }} />
                        <label class="form-check-label" for="videoType-{{ $index }}"> Video </label>
                    </td>
                    <td>
                        <input type="text" name="caption[{{ $index }}]" class="form-control" value="{{ $gallery->caption }}" />
                    </td>
                    <td>
                        <input type="number" name="rank[{{ $index }}]" class="form-control" min="0" max="100" value="{{ $gallery->rank }}" />
                    </td>
                    <td>
                        <input type="text" name="media[{{ $index }}]" class="form-control media-text" style="display: {{ $gallery->type == 0 ? 'block' : 'none' }};" value="{{ $gallery->media }}" />
                        <input type="file" name="media_file[{{ $index }}]" class="form-control media-file" style="display: {{ $gallery->type == 1 ? 'block' : 'none' }};" multiple />
                        @if ($gallery->type == 1 && $gallery->media)
                            <img src="{{ asset('images/organization-gallery/' . $gallery->media) }}" alt="Gallery Media" class="img-thumbnail" style="width: 100px; height: 100px; margin-top: 10px;">
                        @elseif ($gallery->type == 0)

                        @else
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png" alt="No media available" class="img-thumbnail" style="width: 100px; height: 100px; margin-top: 10px;">
                        @endif
                    </td>
                    <td>
                        <button type="button" class="btn btn-primary add-row">
                            <i class="icon-base bx bx-plus icon-sm"></i>
                        </button><br><br>
                        <button type="button" class="btn btn-danger remove-row">
                            <i class="icon-base bx bx-trash icon-sm"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td>1</td>
                <td>
                    <input type="hidden" name="id[0]" value="" />
                    <select class="form-select required" name="gallery_category_id[0]" aria-label="Select Category">
                        <option selected disabled>Select Category Name</option>
                        @foreach ($data['gallery'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input name="type[0]" class="form-check-input" type="radio" value="1" id="imageType-0" checked />
                    <label class="form-check-label" for="imageType-0"> Image </label>
                    <br><br>
                    <input name="type[0]" class="form-check-input" type="radio" value="0" id="videoType-0" />
                    <label class="form-check-label" for="videoType-0"> Video </label>
                </td>
                <td>
                    <input type="text" name="caption[0]" class="form-control" />
                </td>
                <td>
                    <input type="number" name="rank[0]" class="form-control" min="0" max="100" />
                </td>
                <td>
                    <input type="text" name="media[0]" class="form-control media-text" style="display: none;" />
                    <input type="file" name="media_file[0]" class="form-control media-file" multiple />
                </td>
                <td>
                    <button type="button" class="btn btn-primary add-row">
                        <i class="icon-base bx bx-plus icon-sm"></i>
                    </button>
                    <button type="button" class="btn btn-danger remove-row">
                        <i class="icon-base bx bx-trash icon-sm"></i>
                    </button>
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</form>
