{{--edit-basic_information.blade.php--}}
<form action="{{route('organization.update', $data['record']->id)}}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="organization_id" value="{{ $data['record']->id ?? 'default_value' }}"/>
    <div class="row">
        <div class="col-md-4">
            <label for="country_id" class="form-label">Select Country </label>
            <select class="form-select required" id="country_id" name="country_id"
                    aria-label="Select">
                <option selected disabled>Select</option>
                @foreach ($data['country'] as $key => $value)
                    <option value="{{ $key }}" {{ $data['record']->country_id === $key ? 'selected' : '' }} >
                        {{ $value }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label for="administrative_area_id" class="form-label">Select Administrative Area </label>
            <select class="form-select required" id="parent_id" name="administrative_area_id"
                    aria-label="Select">
                <option value="">None</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label for="type" class="form-label">Select Type</label>
            <select class="form-select required" id="type" name="type" aria-label="Select Type">
                <option selected disabled>Select Type</option>
                @foreach ($data['type'] as $key => $value)
                    <option value="{{ $key }}"
                        {{ $data['record']->type === $key ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>


    <div class="mb-3">
        <label for="name" class="form-label">College/School Name</label>
        <input type="text" name="name" class="form-control required" id="name"
               value="{{$data['record']->name}}"/>
    </div>
    <div class="row">
        <div class="col-md-6">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" name="slug" class="form-control required" id="slug" placeholder="Slug" value="{{$data['record']->slug}}"/>
        </div>
        <div class="col-md-6 mb-3">
            <label for="established_year" class="form-label ">Established Year</label>
            <input type="text" name="established_year" class="form-control " id="established_year"
                   value="{{$data['record']->established_year}}"/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <label for="address" class="form-label">Address</label>
            <input type="text" name="address" class="form-control " id="address" value="{{$data['record']->address}}"/>
        </div>

        <div class="col-md-6 mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control " id="email" value="{{$data['record']->email}}"/>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="number" name="phone" class="form-control " id="phone" value="{{$data['record']->phone}}"/>
        </div>
        <div class="col-md-6 mb-3">
            <label for="website" class="form-label">Website</label>
            <input type="text" name="website" class="form-control " id="website" value="{{$data['record']->website}}"/>
        </div>
    </div>

    <div class="row">
        <!-- Logo Upload Section -->
        <div class="col-md-6 mb-4">
            <label for="logo" class="form-label">Logo</label>
            <input type="file" name="logo_file" class="form-control" id="logo" />
            <div class="mt-3">
                @if($data['record']->logo)
                    <img src="{{ asset('images/organization/' . $data['record']->logo) }}"
                         alt="logo" class="img-thumbnail clickable-image"
                         style="width: 100px; height: 100px;"
                         data-bs-toggle="modal" data-bs-target="#imageModal"
                         data-bs-image="{{ asset('images/organization/' . $data['record']->logo) }}">
                @else
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                         alt="No logo available" class="img-thumbnail clickable-image"
                         style="width: 100px; height: 100px;"
                         data-bs-toggle="modal" data-bs-target="#imageModal"
                         data-bs-image="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png">
                @endif
            </div>
        </div>

        <!-- Banner Image Upload Section -->
        <div class="col-md-6 mb-4">
            <label for="banner" class="form-label">Banner Image</label>
            <input type="file" name="banner_file" class="form-control" id="banner" />
            <div class="mt-3">
                @if($data['record']->banner_image)
                    <img src="{{ asset('images/organization/banner/' . $data['record']->banner_image) }}"
                         alt="banner" class="img-thumbnail clickable-image"
                         style="width: 100px; height: 100px;"
                         data-bs-toggle="modal" data-bs-target="#imageModal"
                         data-bs-image="{{ asset('images/organization/banner/' . $data['record']->banner_image) }}">
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
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control editor" name="description"  >{{$data['record']->description}}</textarea>
    </div>

    <div class="mb-3">
        <label for="search_keywords" class="form-label">Search Keywords</label>
        <textarea class="form-control" name="search_keywords" id="search_keywords" rows="3">{{$data['record']->search_keywords}}</textarea>

    </div>

    @include('admin.includes.edit_meta')
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




