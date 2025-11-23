<?php
namespace App\Http\Controllers;
use App\Models\Donation;
use App\Models\DonationType;
use Illuminate\Http\Request;

class DonationController extends Controller {
    public function index()
    {
        return view('donations.dashboard', [
            'donations'    => Donation::orderBy('donation_date', 'desc')->get(),
            'types'        => DonationType::orderBy('name')->get(),

            // summary cards
            'totalDonations' => Donation::count(),
            'totalAmount'    => Donation::whereNotNull('amount')->sum('amount'),
            'totalTypes'     => DonationType::count(),
        ]);
    }

    public function store(Request $r) {
        $r->validate([
            'donor_name' => 'required|string|max:255',
            'donation_type_id' => 'nullable|exists:donation_types,id',
            'amount' => 'nullable|numeric|min:0',
            'items' => 'nullable|string|max:1000',
            'donation_date' => 'required|date',
            'status' => 'required|in:received,pending,distributed'
        ]);
        Donation::create($r->only(['donor_name','donation_type_id','amount','items','donation_date','status']));
        return back()->with('success','Donation added.');
    }

    public function update(Request $r, Donation $donation) {
        $r->validate([
            'donor_name' => 'required|string|max:255',
            'donation_type_id' => 'nullable|exists:donation_types,id',
            'amount' => 'nullable|numeric|min:0',
            'items' => 'nullable|string|max:1000',
            'donation_date' => 'required|date',
            'status' => 'required|in:received,pending,distributed'
        ]);
        $donation->update($r->only(['donor_name','donation_type_id','amount','items','donation_date','status']));
        return back()->with('success','Donation updated.');
    }

    public function destroy(Donation $donation) {
        $donation->delete();
        return back()->with('success','Donation deleted.');
    }
}