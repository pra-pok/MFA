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
                            <div class="text-truncate">Add Team</div>
                        </a>
                    </li>
                @endcan
                @can('team__read')
                    <li class="menu-item">
                        <a href="{{ route('team.index') }}" class="menu-link">
                            <div class="text-truncate">List Team</div>
                        </a>
                    </li>
                @endcan
            </ul>
        </li>
    @endcan

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
                            <div class="text-truncate">Add Role</div>
                        </a>
                    </li>
                @endcan
                @can('role__read')
                    <li class="menu-item">
                        <a href="{{ route('role.index') }}" class="menu-link">
                            <div class="text-truncate">List Role</div>
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
                            <div class="text-truncate">Add User</div>
                        </a>
                    </li>
                @endcan
                @can('user__read')
                    <li class="menu-item">
                        <a href="{{ route('user.index') }}" class="menu-link">
                            <div class="text-truncate">List User</div>
                        </a>
                    </li>
                @endcan
            </ul>
        </li>
    @endcan

    <!-- Modules -->
    <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Modules</span>
    </li>

    @canAny(['article__read', 'article__create'])
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-news"></i>
                <div class="text-truncate">Articles</div>
            </a>
            <ul class="menu-sub">
                @can('article__create')
                    <li class="menu-item">
                        <a href="{{ route('article.create') }}" class="menu-link">
                            <div class="text-truncate">Add Article</div>
                        </a>
                    </li>
                @endcan
                @can('article__read')
                    <li class="menu-item">
                        <a href="{{ route('article.index') }}" class="menu-link">
                            <div class="text-truncate">List Article</div>
                        </a>
                    </li>
                @endcan
            </ul>
        </li>
    @endcan
</ul>
