<ul class="nav">
    <li class="nav-item sidebar-actions">
        <span class="nav-link fw-bold">GENERAL</span>
    </li>
    <li class="nav-item @ifroute('admin') active @endifroute">
        <a class="nav-link" href="{{ route('admin') }}">
            <span class="menu-title">Dashboard</span>
            <i class="mdi mdi-home menu-icon"></i>
        </a>
    </li>
    <li class="nav-item @ifroute('countries.index') active @endifroute">
        <a class="nav-link" href="{{ route('countries.index') }}">
            <span class="menu-title">Countries</span>
            <i class="mdi mdi-earth menu-icon"></i>
        </a>
    </li>
    <li class="nav-item @ifroute('clubs.index') active @endifroute">
        <a class="nav-link" href="{{ route('clubs.index') }}">
            <span class="menu-title">Clubs</span>
            <i class="mdi mdi-shield menu-icon"></i>
        </a>
    </li>
    <li class="nav-item @ifroute('competitions.index') active @endifroute">
        <a class="nav-link" href="{{ route('competitions.index') }}">
            <span class="menu-title">Competitions</span>
            <i class="mdi mdi-trophy menu-icon"></i>
        </a>
    </li>
</ul>
