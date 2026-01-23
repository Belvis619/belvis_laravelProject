@extends('layouts.app2')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-6">

    {{-- Success / Error / Validation Alerts --}}
    @include('partials.alerts')

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-black dark:text-white">Trash - Deleted Donations</h1>
        <a href="{{ route('donations.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Back to Dashboard
        </a>
    </div>

    {{-- Search and Filter Section --}}
    <div class="p-4 bg-white dark:bg-gray-800 rounded shadow mb-6 text-black dark:text-white">
        <form method="GET" action="{{ route('donations.trash') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium mb-1">Search</label>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}"
                    placeholder="Search by donor name or items..." 
                    class="w-full border rounded p-2 dark:bg-gray-700 dark:text-white">
            </div>

            <div class="min-w-[200px]">
                <label class="block text-sm font-medium mb-1">Filter by Type</label>
                <select name="type_filter" class="w-full border rounded p-2 dark:bg-gray-700 dark:text-white">
                    <option value="">All Types</option>
                    @foreach($types as $t)
                        <option value="{{ $t->id }}" {{ $type_filter == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Search</button>
                <a href="{{ route('donations.trash') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Clear</a>
            </div>
        </form>
    </div>

    {{-- Trash Table --}}
    <div class="p-4 bg-white dark:bg-gray-800 rounded shadow text-black dark:text-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="border-b">
                    <tr>
                        <th class="p-2">Photo</th>
                        <th class="p-2">Donor</th>
                        <th class="p-2">Type</th>
                        <th class="p-2">Amount / Items</th>
                        <th class="p-2">Date</th>
                        <th class="p-2">Status</th>
                        <th class="p-2">Deleted At</th>
                        <th class="p-2">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($donations as $d)
                        <tr class="border-b">
                            <td class="p-2">
                                @if($d->photo)
                                    <img src="{{ asset('storage/' . $d->photo) }}" alt="{{ $d->donor_name }}" class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gray-400 dark:bg-gray-600 flex items-center justify-center text-white font-semibold text-sm">
                                        {{ $d->initials() }}
                                    </div>
                                @endif
                            </td>
                            <td class="p-2">{{ $d->donor_name }}</td>
                            <td class="p-2">{{ $d->type?->name ?? 'N/A' }}</td>

                            <td class="p-2">
                                @if($d->amount)
                                    ₱{{ number_format($d->amount,2) }}
                                @else
                                    {{ $d->items }}
                                @endif
                            </td>

                            <td class="p-2">
                                {{ \Carbon\Carbon::parse($d->donation_date)->format('Y-m-d') }}
                            </td>

                            <td class="p-2">{{ ucfirst($d->status) }}</td>

                            <td class="p-2 text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($d->deleted_at)->format('Y-m-d H:i') }}
                            </td>

                            <td class="p-2 space-x-2">
                                <form action="{{ route('donations.restore', $d->id) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Restore this donation?')">
                                    @csrf
                                    @method('PUT')
                                    <button class="text-green-600 hover:underline">Restore</button>
                                </form>

                                <form action="{{ route('donations.force-delete', $d->id) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Permanently delete this donation? This action cannot be undone!')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:underline">Delete Permanently</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-4 text-center text-gray-500">Trash is empty.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
