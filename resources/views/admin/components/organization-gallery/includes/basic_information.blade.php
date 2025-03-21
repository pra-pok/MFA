<form  action="{{route('organization.store')}}" method="POST" enctype="multipart/form-data">
    @csrf
    <input  type="hidden" name="organization_id" value=" "  />
    <div class="mb-3">
        <label for="administrative_area_id" class="form-label">Select Administrative Area Name</label>
        <select class="form-select required" id="administrative_area_id" name="administrative_area_id"
                aria-label="Select Administrative Area Name">
            <option selected disabled>Select Administrative Area Name</option>
            @foreach ($data['area'] as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="type" class="form-label">Select Type</label>
        <select class="form-select required" id="type" name="type" aria-label="Select Type">
            <option selected disabled>Select Type</option>
            @foreach ($data['type'] as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="name" class="form-label">College/School Name</label>
        <input type="text" name="name" class="form-control required" id="name"
               placeholder="Enter The College/School Name"/>
    </div>

    <div class="mb-3">
        <label for="slug" class="form-label">Slug</label>
        <input type="text" name="slug" class="form-control required" id="slug" placeholder="Slug"/>
    </div>

    <div class="mb-3">
        <label for="address" class="form-label">Address</label>
        <input type="text" name="address" class="form-control " id="address" placeholder="Enter The Address"/>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" class="form-control " id="email" placeholder="Enter The Email"/>
    </div>

    <div class="mb-3">
        <label for="phone" class="form-label">Phone Number</label>
        <input type="number" name="phone" class="form-control " id="phone" placeholder="Enter Phone Number"/>
    </div>

    <div class="mb-3">
        <label for="website" class="form-label">Website</label>
        <input type="text" name="website" class="form-control " id="website" placeholder="Enter The Website"/>
    </div>

    <div class="mb-3">
        <label for="logo" class="form-label">Logo</label>
        <input type="file" name="logo_file" class="form-control " id="logo"/>
    </div>

    <div class="mb-3">
        <label for="banner" class="form-label">Banner Image</label>
        <input type="file" name="banner_file" class="form-control " id="banner"/>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control " name="description" id="des" rows="6"></textarea>
    </div>

    <div class="mb-3">
        <label for="established_year" class="form-label ">Established Year</label>
        <input type="text" name="established_year" class="form-control " id="established_year"
               placeholder="Enter Established Year"/>
    </div>

    <div class="mb-3">
        <label for="search_keywords" class="form-label">Search Keywords</label>
        <textarea class="form-control" name="search_keywords" id="search_keywords" rows="3"></textarea>

    </div>

    @include('admin.includes.create_meta')
    @include('admin.includes.create_status')
    <div class="d-flex justify-content-between mt-3">
        <button type="submit" class="btn btn-secondary" id="nextBtn" >Next</button>
    </div>
</form>
