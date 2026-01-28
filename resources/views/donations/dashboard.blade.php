@extends('layouts.app2')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-6">

    {{-- Success / Error / Validation Alerts --}}
    @include('partials.alerts')

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="p-4 bg-white dark:bg-gray-800 rounded shadow text-black dark:text-white">
            <h3 class="font-semibold text-sm">Total Donations</h3>
            <p class="text-2xl font-bold mt-2">{{ $totalDonations }}</p>
        </div>

        <div class="p-4 bg-white dark:bg-gray-800 rounded shadow text-black dark:text-white">
            <h3 class="font-semibold text-sm">Total Amount (cash)</h3>
            <p class="text-2xl font-bold mt-2">₱{{ number_format($totalAmount,2) }}</p>
        </div>

        <div class="p-4 bg-white dark:bg-gray-800 rounded shadow text-black dark:text-white">
            <h3 class="font-semibold text-sm">Donation Types</h3>
            <p class="text-2xl font-bold mt-2">{{ $totalTypes }}</p>
        </div>
    </div>

    {{-- Search and Filter Section --}}
    <div class="p-4 bg-white dark:bg-gray-800 rounded shadow mb-6 text-black dark:text-white">
        <form method="GET" action="{{ route('donations.index') }}" class="flex flex-wrap gap-3 items-end">
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
                <a href="{{ route('donations.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Clear</a>
            </div>
        </form>
    </div>

    {{-- Add Donation Form --}}
    <div class="p-4 bg-white dark:bg-gray-800 rounded shadow mb-6 text-black dark:text-white">
        <h2 class="text-lg font-bold mb-4">Add New Donation</h2>
        <form action="{{ route('donations.store') }}" method="POST" enctype="multipart/form-data" class="grid md:grid-cols-4 gap-3">
            @csrf

            <input name="donor_name" required class="border rounded p-2 dark:bg-gray-700 dark:text-white" placeholder="Donor name">

            <select name="donation_type_id" class="border rounded p-2 dark:bg-gray-700 dark:text-white">
                <option value="">-- Type (optional) --</option>
                @foreach($types as $t)
                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                @endforeach
            </select>

            <input name="amount" type="number" step="0.01" placeholder="Amount (leave blank for items)" class="border rounded p-2 dark:bg-gray-700 dark:text-white">

            <input type="date" name="donation_date" required class="border rounded p-2 dark:bg-gray-700 dark:text-white">

            <input name="items" placeholder="Items description" class="border rounded p-2 dark:bg-gray-700 dark:text-white md:col-span-2">

            <select name="status" required class="border rounded p-2 dark:bg-gray-700 dark:text-white">
                <option value="received">Received</option>
                <option value="pending">Pending</option>
                <option value="distributed">Distributed</option>
            </select>

            <div class="md:col-span-4">
                <label class="block text-sm font-medium mb-1">Photo (JPG/PNG, max 2MB)</label>
                <input type="file" name="photo" accept="image/jpeg,image/jpg,image/png" class="border rounded p-2 dark:bg-gray-700 dark:text-white w-full">
            </div>

            <button class="bg-blue-600 text-white p-2 rounded md:col-span-4 hover:bg-blue-700">Add Donation</button>
        </form>
    </div>

    {{-- Donations Table --}}
    <div x-data="donationModal()" x-cloak class="p-4 bg-white dark:bg-gray-800 rounded shadow text-black dark:text-white">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold">Donations</h2>
            <form method="GET" action="{{ route('donations.export') }}" class="inline">
                <input type="hidden" name="search" value="{{ $search }}">
                <input type="hidden" name="type_filter" value="{{ $type_filter }}">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Export to PDF
                </button>
            </form>
        </div>

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

                            <td class="p-2 space-x-2">
                                <button 
                                    @click="openEdit({!! htmlspecialchars(json_encode($d), ENT_QUOTES, 'UTF-8') !!})" 
                                    class="text-blue-600 hover:underline">
                                    Edit
                                </button>

                                <form action="{{ route('donations.destroy', $d) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Move donation to trash?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-4 text-center text-gray-500">No donations found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Edit Donation Modal --}}
        <div x-show="open" x-transition class="fixed inset-0 flex items-center justify-center z-50">
            <div class="absolute inset-0 bg-black/50" @click="close()"></div>

            <div class="bg-white dark:bg-gray-800 rounded p-6 shadow-lg max-w-lg w-full z-10 text-black dark:text-white max-h-[90vh] overflow-y-auto">
                <h2 class="text-lg font-bold mb-4">Edit Donation</h2>

                <form :action="updateUrl()" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">

                    <div class="grid gap-3">
                        <div>
                            <label class="block text-sm font-medium mb-1">Donor Name</label>
                            <input name="donor_name" x-model="form.donor_name" required class="w-full border rounded p-2 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Donation Type</label>
                            <select name="donation_type_id" x-model="form.donation_type_id" class="w-full border rounded p-2 dark:bg-gray-700 dark:text-white">
                                <option value="">-- Optional --</option>
                                @foreach ($types as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Amount</label>
                            <input name="amount" type="number" step="0.01" x-model="form.amount" class="w-full border rounded p-2 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Items</label>
                            <input name="items" x-model="form.items" class="w-full border rounded p-2 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Donation Date</label>
                            <input type="date" name="donation_date" x-model="form.donation_date" required class="w-full border rounded p-2 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Status</label>
                            <select name="status" x-model="form.status" required class="w-full border rounded p-2 dark:bg-gray-700 dark:text-white">
                                <option value="received">Received</option>
                                <option value="pending">Pending</option>
                                <option value="distributed">Distributed</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Photo (JPG/PNG, max 2MB)</label>
                            <input type="file" name="photo" accept="image/jpeg,image/jpg,image/png" class="w-full border rounded p-2 dark:bg-gray-700 dark:text-white">
                            <p class="text-xs text-gray-500 mt-1">Leave blank to keep current photo</p>
                            <div class="mt-2" x-show="form.photo">
                                <img
                                    :src="'/storage/' + form.photo"
                                    alt="Current photo"
                                    class="w-16 h-16 rounded-full object-cover"
                                >
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" @click="close()" class="px-4 py-2 border rounded hover:bg-gray-100 dark:hover:bg-gray-700">Cancel</button>
                        <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
function donationModal() {
    return {
        open: false,
        form: {},

        openEdit(data) {
            // Format date for input
            if (data.donation_date) {
                const date = new Date(data.donation_date);
                data.donation_date = date.toISOString().split('T')[0];
            }
            this.form = JSON.parse(JSON.stringify(data));
            this.open = true;
        },

        close() {
            this.open = false;
        },

        updateUrl() {
            return `/donations/${this.form.id}`;
        }
    }
}
</script>

@endsection
