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

    {{-- Add Donation Form --}}
    <div class="p-4 bg-white dark:bg-gray-800 rounded shadow mb-6 text-black dark:text-white">

        <form action="{{ route('donations.store') }}" method="POST" class="grid md:grid-cols-4 gap-3">
            @csrf

            <input name="donor_name" class="border rounded p-2 dark:bg-gray-700 dark:text-white" placeholder="Donor name">

            <select name="donation_type_id" class="border rounded p-2 dark:bg-gray-700 dark:text-white">
                <option value="">-- Type (optional) --</option>
                @foreach($types as $t)
                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                @endforeach
            </select>

            <input name="amount" placeholder="Amount (leave blank for items)" class="border rounded p-2 dark:bg-gray-700 dark:text-white">

            <input type="date" name="donation_date" class="border rounded p-2 dark:bg-gray-700 dark:text-white">

            <input name="items" placeholder="Items description" class="border rounded p-2 dark:bg-gray-700 dark:text-white md:col-span-2">

            <select name="status" class="border rounded p-2 dark:bg-gray-700 dark:text-white">
                <option value="received">Received</option>
                <option value="pending">Pending</option>
                <option value="distributed">Distributed</option>
            </select>

            <button class="bg-blue-600 text-white p-2 rounded md:col-span-4">Add Donation</button>
        </form>
    </div>

    {{-- Donations Table --}}
    <div x-data="donationModal()" x-cloak class="p-4 bg-white dark:bg-gray-800 rounded shadow text-black dark:text-white">

        <table class="w-full text-left">
            <thead class="border-b">
                <tr>
                    <th class="p-2">Donor</th>
                    <th class="p-2">Type</th>
                    <th class="p-2">Amount / Items</th>
                    <th class="p-2">Date</th>
                    <th class="p-2">Status</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($donations as $d)
                    <tr class="border-b">
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
                                class="text-blue-600">
                                Edit
                            </button>

                            <form action="{{ route('donations.destroy', $d) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Delete donation?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Edit Donation Modal --}}
        <div x-show="open" x-transition class="fixed inset-0 flex items-center justify-center z-50">
            <div class="absolute inset-0 bg-black/50" @click="close()"></div>

            <div class="bg-white dark:bg-gray-800 rounded p-6 shadow-lg max-w-lg w-full z-10 text-black dark:text-white">
                <h2 class="text-lg font-bold mb-4">Edit Donation</h2>

                <form :action="updateUrl()" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">

                    <div class="grid gap-3">
                        <input name="donor_name" x-model="form.donor_name" class="border rounded p-2 dark:bg-gray-700 dark:text-white">

                        <select name="donation_type_id" x-model="form.donation_type_id" class="border rounded p-2 dark:bg-gray-700 dark:text-white">
                            <option value="">-- Optional --</option>
                            @foreach ($types as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>

                        <input name="amount" x-model="form.amount" class="border rounded p-2 dark:bg-gray-700 dark:text-white">

                        <input name="items" x-model="form.items" class="border rounded p-2 dark:bg-gray-700 dark:text-white">

                        <input type="date" name="donation_date" x-model="form.donation_date" class="border rounded p-2 dark:bg-gray-700 dark:text-white">

                        <select name="status" x-model="form.status" class="border rounded p-2 dark:bg-gray-700 dark:text-white">
                            <option value="received">Received</option>
                            <option value="pending">Pending</option>
                            <option value="distributed">Distributed</option>
                        </select>
                    </div>

                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" @click="close()" class="px-4 py-2 border rounded">Cancel</button>
                        <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
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