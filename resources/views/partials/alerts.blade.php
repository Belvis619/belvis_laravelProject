@if ($errors->any())
  <div class="mb-4 p-3 rounded bg-red-50 border border-red-200 text-red-800">
    <strong>Whoops — there were some problems:</strong>
    <ul class="mt-2 list-disc list-inside">
      @foreach ($errors->all() as $err)
        <li>{{ $err }}</li>
      @endforeach
    </ul>
  </div>
@endif

@if (session('success'))
  <div class="mb-4 p-3 rounded bg-green-50 border border-green-200 text-green-800">
    {{ session('success') }}
  </div>
@endif

@if (session('error'))
  <div class="mb-4 p-3 rounded bg-yellow-50 border border-yellow-200 text-yellow-800">
    {{ session('error') }}
  </div>
@endif