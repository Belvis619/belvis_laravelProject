<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Donation extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
    'donation_date' => 'date', // returns a Carbon instance
    ];

    protected $fillable = [
        'donor_name','donation_type_id','amount','items','donation_date','status','photo'
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(DonationType::class);
    }

    /**
     * Get the donor's initials
     */
    public function initials(): string
    {
        return Str::of($this->donor_name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('')
            ->upper();
    }
}