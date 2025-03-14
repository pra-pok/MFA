{{--edit-basic_information.blade.php--}}
<form action="{{route('organization.update', $data['record']->id)}}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="organization_id" value="{{ $data['record']->id ?? 'default_value' }}"/>
    <div class="row">
        <div class="col-md-2">
            <label for="country_id" class="form-label"> Country </label>
            <select class="form-select select-country required" id="country_id" name="country_id"
                    aria-label="Country">
                <option selected disabled>Country</option>
                @foreach ($data['country'] as $key => $value)
                    <option value="{{ $key }}" {{ $data['record']->country_id === $key ? 'selected' : '' }} >
                        {{ $value }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="administrative_area_id" class="form-label"> Province/State </label>
            <select data-id="{{ $data['record']->locality->administrativeArea->parent->id ?? '' }}" class="form-select required" id="parent_id" aria-label="Province/State">
                <option value="">None</option>
            </select>
        </div>
        <div class="col-md-2">
            <label for="district_id" class="form-label">District</label>
            <select data-id="{{ $data['record']->locality->administrativeArea->id ?? '' }}" class="form-select district required" id="district_id"
                    aria-label="District">
                <option value="">None</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="locality_id" class="form-label">Municipality</label>
            <select data-id="{{ $data['record']->locality->id ?? '' }}" class="form-select locality required" id="locality_id" name="locality_id"
                    aria-label="Municipality">
                <option value="">None</option>
            </select>
        </div>
        <div class="col-md-3 mb-3">
            <label for="type" class="form-label"> Type</label>
            <select class="form-select select-type required" id="type" name="type" aria-label="Type">
                <option selected disabled> Type</option>
                @foreach ($data['type'] as $key => $value)
                    <option value="{{ $key }}"
                        {{ $data['record']->type === $key ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-md-9 mb-3">
            <label for="name" class="form-label">College/School Name</label>
            <input type="text" name="name" class="form-control required" id="name"
                   value="{{$data['record']->name}}"/>
        </div>
        <div class="col-md-3 mb-3">
            <label for="short_name" class="form-label">College/School Short Name</label>
            <input type="text" name="short_name" class="form-control" id="short_name"
                   value="{{$data['record']->short_name}}"/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" name="slug" class="form-control required" id="slug" placeholder="Slug" value="{{$data['record']->slug}}"/>
        </div>
        <div class="col-md-6 mb-3">
            <label for="established_year" class="form-label ">Established Year</label>
            <input type="text" name="established_year" class="form-control" id="established_year"
                   value="{{$data['record']->established_year}}"/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <label for="address" class="form-label">Address</label>
            <input type="text" name="address" class="form-control" id="address" value="{{$data['record']->address}}"/>
        </div>
        <div class="col-md-6 mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control " id="email" value="{{$data['record']->email}}"/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="text" name="phone" class="form-control " id="phone" value="{{$data['record']->phone}}"/>
        </div>
        <div class="col-md-6 mb-3">
            <label for="website" class="form-label">Website</label>
            <input type="text" name="website" class="form-control " id="website" value="{{$data['record']->website}}"/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-4">
            <label for="logo" class="form-label">Logo</label>
            <input type="file" name="logo_file" class="form-control" id="logo" />
            <div class="mt-3">
                @if($data['record']->logo)
                    <img src="{{ url('/file/' . $folder . '/' . $data['record']->logo) }}"
                         alt="logo" class="img-thumbnail clickable-image"
                         style="width: 100px; height: 100px;"
                         data-bs-toggle="modal" data-bs-target="#imageModal"
                         data-bs-image="{{ url('/file/' . $folder . '/' . $data['record']->logo) }}">
                @else
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                         alt="No logo available" class="img-thumbnail clickable-image"
                         style="width: 100px; height: 100px;"
                         data-bs-toggle="modal" data-bs-target="#imageModal"
                         data-bs-image="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png">
                @endif
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <label for="banner" class="form-label">Banner Image</label>
            <input type="file" name="banner_file" class="form-control" id="banner" />
            <div class="mt-3">
                @if($data['record']->banner_image)
                    <img src="{{ url('/file/organization_banner/' . $data['record']->banner_image) }}"

                         alt="banner" class="img-thumbnail clickable-image"
                         style="width: 100px; height: 100px;"
                         data-bs-toggle="modal" data-bs-target="#imageModal"
                         data-bs-image="{{ url('/file/organization_banner/' . $data['record']->banner_image) }}">
                @else
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                         alt="No banner available" class="img-thumbnail clickable-image"
                         style="width: 100px; height: 100px;"
                         data-bs-toggle="modal" data-bs-target="#imageModal"
                         data-bs-image="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png">
                @endif
            </div>
        </div>
    </div>
    <div class="mb-3">
        <label for="google_map" class="form-label">Google Map</label>
        <input type="text" name="google_map" class="form-control " id="google_map" value="{{ $data['record']->google_map }}"/>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control editor" name="description"  >{{$data['record']->description}}</textarea>
    </div>
    <div class="mb-3">
        <label for="search_keywords" class="form-label">Search Keywords</label>
        <textarea class="form-control" name="search_keywords" id="search_keywords" rows="3">{{$data['record']->search_keywords}}</textarea>
    </div>
    @include('admin.includes.edit_meta')
    <div class="row">
        <label for="search_keywords" class="form-label">Catalog</label>
        @foreach($data['catalog'] as $id => $title)
            <div class="col-auto mb-2">
                <div class="form-check">
                    <input type="checkbox" name="catalog_id[]" value="{{ $id }}" class="form-check-input checkbox"
                        {{ in_array($id, $data['selectedCatalogIds']->toArray()) ? 'checked' : '' }}
                    />
                    <label class="form-check-label">{{ $title ?? ''}}</label>
                </div>
            </div>
        @endforeach
    </div>
    <br>
    @include('admin.includes.edit_status')
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <img id="fullSizeImage" src="" alt="Full Size" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</form>




