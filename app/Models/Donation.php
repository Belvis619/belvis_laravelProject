<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    use HasFactory;

    protected $casts = [
    'donation_date' => 'date', // returns a Carbon instance
    ];

    protected $fillable = [
        'donor_name','donation_type_id','amount','items','donation_date','status'
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(DonationType::class);
    }
}