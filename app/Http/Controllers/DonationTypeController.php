<?php
namespace App\Http\Controllers;
use App\Models\DonationType;
use Illuminate\Http\Request;

class DonationTypeController extends Controller {
    public function index() {
        $types = DonationType::withCount('donations')->paginate(10);
        return view('donation_types.index', compact('types'));
    }

    public function store(Request $r) {
        $r->validate(['name'=>'required|string|unique:donation_types,name']);
        DonationType::create($r->only('name','description'));
        return back()->with('success','Donation type added.');
    }

    public function update(Request $r, DonationType $donationType) {
        $r->validate(['name'=>"required|string|unique:donation_types,name,{$donationType->id}"]);
        $donationType->update($r->only('name','description'));
        return back()->with('success','Donation type updated.');
    }

    public function destroy(DonationType $donationType) {
        $donationType->delete();
        return back()->with('success','Donation type deleted.');
    }
}