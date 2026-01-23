<?php
namespace App\Http\Controllers;
use App\Models\Donation;
use App\Models\DonationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DonationController extends Controller {
    public function index(Request $request)
    {
        $query = Donation::with('type')->orderBy('donation_date', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('donor_name', 'like', "%{$search}%")
                  ->orWhere('items', 'like', "%{$search}%");
            });
        }

        // Filter by donation type
        if ($request->filled('type_filter')) {
            $query->where('donation_type_id', $request->type_filter);
        }

        $donations = $query->get();
        $types = DonationType::orderBy('name')->get();

        return view('donations.dashboard', [
            'donations'    => $donations,
            'types'        => $types,
            'search'       => $request->search ?? '',
            'type_filter'  => $request->type_filter ?? '',

            // summary cards
            'totalDonations' => Donation::count(),
            'totalAmount'    => Donation::whereNotNull('amount')->sum('amount'),
            'totalTypes'     => DonationType::count(),
        ]);
    }

    public function store(Request $r) {
        $validated = $r->validate([
            'donor_name' => 'required|string|max:255',
            'donation_type_id' => 'nullable|exists:donation_types,id',
            'amount' => 'nullable|numeric|min:0',
            'items' => 'nullable|string|max:1000',
            'donation_date' => 'required|date',
            'status' => 'required|in:received,pending,distributed',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048'
        ]);

        // Handle photo upload
        if ($r->hasFile('photo')) {
            $validated['photo'] = $r->file('photo')->store('donations', 'public');
        }

        Donation::create($validated);
        return back()->with('success','Donation added.');
    }

    public function update(Request $r, Donation $donation) {
        $validated = $r->validate([
            'donor_name' => 'required|string|max:255',
            'donation_type_id' => 'nullable|exists:donation_types,id',
            'amount' => 'nullable|numeric|min:0',
            'items' => 'nullable|string|max:1000',
            'donation_date' => 'required|date',
            'status' => 'required|in:received,pending,distributed',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048'
        ]);

        // Handle photo upload
        if ($r->hasFile('photo')) {
            // Delete old photo if exists
            if ($donation->photo && Storage::disk('public')->exists($donation->photo)) {
                Storage::disk('public')->delete($donation->photo);
            }
            $validated['photo'] = $r->file('photo')->store('donations', 'public');
        } else {
            // Keep existing photo if no new photo uploaded
            unset($validated['photo']);
        }

        $donation->update($validated);
        return back()->with('success','Donation updated.');
    }

    public function destroy(Donation $donation) {
        $donation->delete(); // Soft delete
        return back()->with('success','Donation moved to trash.');
    }

    public function trash(Request $request)
    {
        $query = Donation::onlyTrashed()->with('type')->orderBy('deleted_at', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('donor_name', 'like', "%{$search}%")
                  ->orWhere('items', 'like', "%{$search}%");
            });
        }

        // Filter by donation type
        if ($request->filled('type_filter')) {
            $query->where('donation_type_id', $request->type_filter);
        }

        $donations = $query->get();
        $types = DonationType::orderBy('name')->get();

        return view('donations.trash', [
            'donations'    => $donations,
            'types'        => $types,
            'search'       => $request->search ?? '',
            'type_filter'  => $request->type_filter ?? '',
        ]);
    }

    public function restore($id)
    {
        $donation = Donation::onlyTrashed()->findOrFail($id);
        $donation->restore();
        return back()->with('success','Donation restored successfully.');
    }

    public function forceDelete($id)
    {
        $donation = Donation::onlyTrashed()->findOrFail($id);
        
        // Delete photo if exists
        if ($donation->photo && Storage::disk('public')->exists($donation->photo)) {
            Storage::disk('public')->delete($donation->photo);
        }
        
        $donation->forceDelete();
        return back()->with('success','Donation permanently deleted.');
    }

    public function exportPdf(Request $request)
    {
        $query = Donation::with('type')->orderBy('donation_date', 'desc');

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('donor_name', 'like', "%{$search}%")
                  ->orWhere('items', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type_filter')) {
            $query->where('donation_type_id', $request->type_filter);
        }

        $donations = $query->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('donations.pdf', compact('donations'));
        $filename = 'donations_' . now()->format('Y-m-d_His') . '.pdf';
        
        return $pdf->download($filename);
    }
}
