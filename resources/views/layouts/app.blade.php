<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'SalesRep') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<header class="top-header">
    <h1>SalesRep</h1>
    @auth
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn btn-light" type="submit">Logout</button>
        </form>
    @endauth
</header>

<main class="app-content">
    @if (session('status'))
        <p class="alert-success">{{ session('status') }}</p>
    @endif
    @yield('content')
</main>

@auth
<nav class="bottom-nav">
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
    <a href="{{ route('pharmacies.index') }}" class="{{ request()->routeIs('pharmacies.*') ? 'active' : '' }}">Pharmacies</a>
</nav>
@endauth
</body>
</html>
