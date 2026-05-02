<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SalesRep</title>
    @vite(['resources/css/app.css'])
</head>
<body>
<header class="app-header">
    <h1>SalesRep</h1>
    @auth
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="btn btn-outline" type="submit">Logout</button>
    </form>
    @endauth
</header>

<main class="app-content">@yield('content')</main>

@auth
<nav class="bottom-nav">
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
    <a href="{{ route('pharmacies.index') }}" class="{{ request()->routeIs('pharmacies.*') ? 'active' : '' }}">Pharmacies</a>
</nav>
@endauth
</body>
</html>
