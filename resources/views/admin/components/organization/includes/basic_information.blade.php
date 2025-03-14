{{--basic_information.blade.php--}}
<form action="{{route('organization.store')}}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="organization_id" value="{{ old('organization_id', $organization_id ?? '') }}"/>

    <div class="row">
        <div class="col-md-2">
            <label for="country_id" class="form-label"> Country </label>
            <select class="form-select select-country" id="country_id" name="country_id"
                    aria-label="Country">
                <option selected disabled>Country</option>
                @foreach ($data['country'] as $key => $value)
                    <option value="{{ $key }}" @if ($key == 1) selected @endif>{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="administrative_area_id" class="form-label"> Province/State </label>
            <select class="form-select" id="parent_id" aria-label="Province/State">
                <option value="">None</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="district_id" class="form-label">District</label>
            <select class="form-select district" id="district_id" aria-label="District">
                <option value="">None</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="locality_id" class="form-label">Municipality</label>
            <select class="form-select locality" id="locality_id" name="locality_id" aria-label="Municipality">
                <option value="">None</option>
            </select>
        </div>
        <div class="col-md-2 mb-3">
            <label for="type" class="form-label">Type</label>
            <select class="form-select select-type" id="type" name="type" aria-label="Type">
                <option selected disabled>Type</option>
                @foreach ($data['type'] as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-md-9 mb-3">
            <label for="name" class="form-label">College/School Name</label>
            <input type="text" name="name" class="form-control required" id="name"
                   placeholder="Enter The College/School Name"/>
        </div>
        <div class="col-md-3 mb-3">
            <label for="short_name" class="form-label">College/School Short Name</label>
            <input type="text" name="short_name" class="form-control" id="short_name"
                   placeholder="Enter The College/School Short Name"/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" name="slug" class="form-control required" id="slug" placeholder="Slug"/>
        </div>
        <div class="col-md-6 mb-3">
            <label for="established_year" class="form-label ">Established Year</label>
            <input type="text" name="established_year" class="form-control " id="established_year"
                   placeholder="Enter Established Year"/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <label for="address" class="form-label">Address</label>
            <input type="text" name="address" class="form-control " id="address" placeholder="Enter The Address"/>
        </div>

        <div class="col-md-6 mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control " id="email" placeholder="Enter The Email"/>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="text" name="phone" class="form-control" id="phone" placeholder="Enter Phone Number"/>
        </div>
        <div class="col-md-6 mb-3">
            <label for="website" class="form-label">Website</label>
            <input type="text" name="website" class="form-control" id="website" placeholder="Enter The Website"/>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <label for="logo" class="form-label">Logo</label>
            <input type="file" name="logo_file" class="form-control " id="logo"/>
        </div>

        <div class="col-md-6 mb-3">
            <label for="banner" class="form-label">Banner Image</label>
            <input type="file" name="banner_file" class="form-control " id="banner"/>
        </div>
    </div>
    <div class="mb-3">
        <label for="google_map" class="form-label">Google Map</label>
        <input type="text" name="google_map" class="form-control " id="google_map"/>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control editor" name="description"></textarea>
    </div>
    <div class="mb-3">
        <label for="search_keywords" class="form-label">Search Keywords</label>
        <textarea class="form-control" name="search_keywords" id="search_keywords" rows="3"></textarea>
    </div>
    @include('admin.includes.create_meta')
    <br>
    <div class="row">
        <label for="search_keywords" class="form-label">Catalog</label>
        @foreach($data['catalog'] as $id => $title)
            <div class="col-auto mb-2">
                <div class="form-check">
                    <input type="checkbox" name="catalog_id[]" value="{{ $id }}" class="form-check-input checkbox"/>
                    <label class="form-check-label">{{ $title ?? ''}}</label>
                </div>
            </div>
        @endforeach
    </div>
    <br>
    @include('admin.includes.create_status')

</form>
