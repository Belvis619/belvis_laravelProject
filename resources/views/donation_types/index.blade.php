@extends('layouts.app2')
@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">
  @include('partials.alerts')

  <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Donation Tracking System</h2>

  <div class="bg-white dark:bg-gray-800 text-black dark:text-white p-4 rounded shadow mb-4">
    <form action="{{ route('donation-types.store') }}" method="POST" class="flex gap-2">
      @csrf
      <input name="name" placeholder="Type name" class="border p-2 rounded flex-1 bg-white dark:bg-gray-700 text-black dark:text-white placeholder-gray-400" />
      <input name="description" placeholder="Short description" class="border p-2 rounded flex-1 bg-white dark:bg-gray-700 text-black dark:text-white placeholder-gray-400" />
      <button class="bg-green-600 text-white px-4 py-2 rounded">Add Type</button>
    </form>
  </div>

  <div x-data="typeModal()" x-cloak class="bg-white dark:bg-gray-800 text-black dark:text-white p-4 rounded shadow">
    <table class="w-full">
      <thead>
        <tr>
          <th class="p-2 text-left text-gray-700 dark:text-gray-200">Type</th>
          <th class="p-2 text-left text-gray-700 dark:text-gray-200">Description</th>
          <th class="p-2 text-left text-gray-700 dark:text-gray-200"># of Donations</th>
          <th class="p-2 text-left text-gray-700 dark:text-gray-200">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($types as $t)
          <tr class="border-t">
            <td class="p-2 text-gray-900 dark:text-gray-100">{{ $t->name }}</td>
            <td class="p-2 text-gray-700 dark:text-gray-300">{{ $t->description }}</td>
            <td class="p-2 text-gray-700 dark:text-gray-300">{{ $t->donations_count }} donations</td>
            <td class="p-2">
              <button @click="openEdit({!! htmlspecialchars(json_encode(['id'=>$t->id,'name'=>$t->name,'description'=>$t->description]), ENT_QUOTES,'UTF-8') !!})" class="text-blue-600 mr-3">Edit</button>

              <form action="{{ route('donation-types.destroy', $t) }}" method="POST" class="inline" onsubmit="return confirm('Delete type?')">
                @csrf @method('DELETE')
                <button class="text-red-600">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="4" class="p-4 text-center text-gray-600 dark:text-gray-400">No donation types yet.</td></tr>
        @endforelse
      </tbody>
    </table>

    {{-- Edit Modal --}}
    <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="absolute inset-0 bg-black/50" @click="close()"></div>
      <div class="bg-white dark:bg-gray-800 text-black dark:text-white max-w-lg w-full rounded shadow-lg p-6 z-10">
        <header class="flex justify-between items-center mb-4">
          <h2 class="text-lg font-semibold">Edit Donation Type</h2>
          <button @click="close()" class="text-gray-600">✕</button>
        </header>

        <form :action="updateUrl()" method="POST">
          @csrf
          <input type="hidden" name="_method" value="PUT">
          <div class="grid gap-3">
            <input name="name" x-model="form.name" required class="border p-2 rounded" />
            <input name="description" x-model="form.description" class="border p-2 rounded" />
          </div>

          <div class="mt-4 flex justify-end gap-2">
            <button type="button" @click="close()" class="px-4 py-2 rounded border">Cancel</button>
            <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white">Save</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>

<script>
function typeModal(){
  return {
    open:false,
    form:{id:'',name:'',description:''},
    openEdit(data){
      this.form = { id:data.id||'', name:data.name||'', description:data.description||'' };
      this.open = true;
    },
    close(){ this.open=false; },
    updateUrl(){ return `/donation-types/${this.form.id}`; }
  }
}
</script>
@endsection