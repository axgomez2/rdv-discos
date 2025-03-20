@props(['user'])

<div class="nav-item dropdown">
    <a href="#" class="nav-link d-flex align-items-center text-reset p-0" data-bs-toggle="dropdown" aria-expanded="false">
        <div class="d-none d-xl-block ps-2">
            <div>{{ $user->name }}</div>
            <div class="mt-1 small text-muted">{{ $user->email }}</div>
        </div>
    </a>
    <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="#">Meus Favoritos</a></li>
        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Perfil</a></li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item">Logout</button>
            </form>
        </li>
    </ul>
</div>
