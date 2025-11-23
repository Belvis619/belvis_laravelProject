<!doctype html>
<html>
<head>
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Donation Tracker</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
  <div class="container mx-auto p-4">
    <header><h1 class="text-xl font-semibold">Donation Tracking System</h1></header>
    @if(session('success'))<div class="p-2 bg-green-100">{{ session('success') }}</div>@endif
    @yield('content')
  </div>
</body>
</html>