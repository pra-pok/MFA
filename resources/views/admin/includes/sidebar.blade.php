<ul class="menu-inner py-1">
    <li class="menu-item">
        <a href="{{ route('dashboard') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-home-smile"></i>
            <div class="text-truncate">Dashboard</div>
        </a>
    </li>
    <!-- Dashboard -->

    <!-- User Management -->
    <li class="menu-header small text-uppercase">
        <span class="menu-header-text">User Management</span>
    </li>

    {{-- Enable Team Management only for Super Admin Team --}}
    @if (auth()->user()->team_id == 1)
        @canAny(['team__read', 'team__create'])
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-store"></i>
                    <div class="text-truncate">Teams</div>
                </a>
                <ul class="menu-sub">
                    @can('team__create')
                        <li class="menu-item">
                            <a href="{{ route('team.create') }}" class="menu-link">
                                <div class="text-truncate">Add</div>
                            </a>
                        </li>
                    @endcan
                    @can('team__read')
                        <li class="menu-item">
                            <a href="{{ route('team.index') }}" class="menu-link">
                                <div class="text-truncate">View</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan
    @endif

    @canAny(['role__read', 'role__create'])
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-lock-open-alt"></i>
                <div class="text-truncate">Roles</div>
            </a>
            <ul class="menu-sub">
                @can('role__create')
                    <li class="menu-item">
                        <a href="{{ route('role.create') }}" class="menu-link">
                            <div class="text-truncate">Add</div>
                        </a>
                    </li>
                @endcan
                @can('role__read')
                    <li class="menu-item">
                        <a href="{{ route('role.index') }}" class="menu-link">
                            <div class="text-truncate">View</div>
                        </a>
                    </li>
                @endcan
            </ul>
        </li>
    @endcan

    @canAny(['user__read', 'user__create'])
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div class="text-truncate">Users</div>
            </a>
            <ul class="menu-sub">
                @can('user__create')
                    <li class="menu-item">
                        <a href="{{ route('user.create') }}" class="menu-link">
                            <div class="text-truncate">Add</div>
                        </a>
                    </li>
                @endcan
                @can('user__read')
                    <li class="menu-item">
                        <a href="{{ route('user.index') }}" class="menu-link">
                            <div class="text-truncate">View</div>
                        </a>
                    </li>
                @endcan
            </ul>
        </li>
    @endcan
    <li class="menu-item">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-home-alt-2"></i>
            <div class="text-truncate">College Account</div>
        </a>
        <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('organization-signup.create') }}" class="menu-link">
                        <div class="text-truncate">Add</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('organization-signup.index') }}" class="menu-link">
                        <div class="text-truncate">View</div>
                    </a>
                </li>
        </ul>
    </li>
    {{-- Disable Modules for Super Admin Team --}}
    @if (auth()->user()->team_id != 1)
        <!-- Modules -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Modules</span>
        </li>
    @endif
    <li class="menu-item ">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-home"></i>
            <div class="text-truncate" data-i18n="Form Elements">College/School</div>
        </a>
        <ul class="menu-sub">
            <li class="menu-item ">
                <a href="{{route('organization.create')}}" class="menu-link">
                    <div class="text-truncate" data-i18n="Basic Inputs">Add</div>
                </a>
            </li>
            <li class="menu-item ">
                <a href="{{route('organization.index')}}" class="menu-link">
                    <div class="text-truncate" data-i18n="Basic Inputs">View</div>
                </a>
            </li>
        </ul>
    </li>
    <li class="menu-item">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-globe"></i>
            <div class="text-truncate" data-i18n="Basic Inputs">Address</div>
        </a>
        <ul class="menu-sub">
            <li class="menu-item ">
                <a href="{{route('admin.country.index')}}" class="menu-link">
                    <div class="text-truncate" data-i18n="Basic Inputs">Country</div>
                </a>
            </li>
            <li class="menu-item ">
                <a href="{{route('admin.administrative_area.index')}}" class="menu-link">

                    <div class="text-truncate" data-i18n="Form Elements">Administrative Area</div>
                </a>
            </li>
            <li class="menu-item ">
                <a href="{{route('locality.index')}}" class="menu-link">

                    <div class="text-truncate" data-i18n="Form Elements">Locality</div>
                </a>
            </li>
        </ul>
    </li>
    <li class="menu-item ">
        <a href="{{route('menu.index')}}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-menu"></i>
            <div class="text-truncate" data-i18n="Basic Inputs">Menu</div>
        </a>
    </li>
    <li class="menu-item ">
        <a href="{{route('catalog.index')}}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-no-entry"></i>
            <div class="text-truncate" data-i18n="Basic Inputs">Catalog</div>
        </a>
    </li>
    <li class="menu-item ">
        <a href="{{route('admin.university.index')}}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-building-house"></i>
            <div class="text-truncate" data-i18n="Basic Inputs">University</div>
        </a>
    </li>

    <li class="menu-item ">
        <a href="{{route('admin.stream.index')}}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-street-view"></i>
            <div class="text-truncate" data-i18n="Form Elements">Stream/Discipline</div>
        </a>
    </li>

    <li class="menu-item ">
        <a href="{{route('admin.level.index')}}" class="menu-link ">
            <i class="menu-icon tf-icons bx bx-lira"></i>
            <div class="text-truncate" data-i18n="Form Elements">Level</div>
        </a>

    </li>

    <li class="menu-item ">
        <a href="{{route('admin.course.index')}}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-book"></i>
            <div class="text-truncate" data-i18n="Form Elements">Course</div>
        </a>
    </li>
{{--    <li class="menu-item ">--}}
{{--        <a href="{{route('admin.administrative_area.index')}}" class="menu-link">--}}
{{--            <i class="menu-icon tf-icons bx bx-area"></i>--}}
{{--            <div class="text-truncate" data-i18n="Form Elements">Administrative Area</div>--}}
{{--        </a>--}}
{{--    </li>--}}
    <li class="menu-item ">
        <a href="{{route('admin.gallery_category.index')}}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-images"></i>
            <div class="text-truncate" data-i18n="Form Elements">Gallery Category</div>
        </a>
    </li>
    <li class="menu-item ">
        <a href="{{route('page-category.index')}}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-first-page"></i>
            <div class="text-truncate" data-i18n="Form Elements">Page Category</div>
        </a>
    </li>
    <li class="menu-item ">
        <a href="{{route('facilities.index')}}" class="menu-link">
            <i class="menu-icon tf-icons bx bxs-face"></i>
            <div class="text-truncate" data-i18n="Form Elements">Facilities</div>
        </a>
    </li>
    <li class="menu-item ">
        <a href="{{route('admin.news_event.index')}}" class="menu-link">
            <i class="menu-icon tf-icons bx bxs-news"></i>
            <div class="text-truncate" data-i18n="Form Elements">News & Event</div>
        </a>
    </li>
    <li class="menu-item ">
        <a href="{{route('organization-group.index')}}" class="menu-link">
            <i class="menu-icon tf-icons bx bxs-group"></i>
            <div class="text-truncate" data-i18n="Form Elements">Organization Group</div>
        </a>
    </li>
    <li class="menu-item">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bxl-whatsapp"></i>
            <div class="text-truncate" data-i18n="Basic Inputs">Whatsapp</div>
        </a>
        <ul class="menu-sub">
            <li class="menu-item ">
                <a href="{{route('admin.whatsapp-messages.create')}}" class="menu-link">
                    <div class="text-truncate" data-i18n="Basic Inputs">Send Messages</div>
                </a>
            </li>
            <li class="menu-item ">
                <a href="{{route('admin.whatsapp-messages.index')}}" class="menu-link">

                    <div class="text-truncate" data-i18n="Form Elements">History</div>
                </a>
            </li>
        </ul>
    </li>
    <li class="menu-item ">
        <a href="{{route('referral-source.index')}}" class="menu-link">
            <i class="menu-icon tf-icons bx bxs-report"></i>
            <div class="text-truncate" data-i18n="Form Elements">Referral Source</div>
        </a>
    </li>
    <li class="menu-item ">
        <a href="{{route('contactus.index')}}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-support"></i>
            <div class="text-truncate" data-i18n="Form Elements">Support Us</div>
        </a>
    </li>
    <li class="menu-item ">
        <a href="{{route('students.index')}}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-user"></i>
            <div class="text-truncate" data-i18n="Form Elements">Student</div>
        </a>
    </li>
</ul>
