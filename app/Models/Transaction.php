<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Transaction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'amount',
        'currency',
        'bet_id',
        'transaction_id',
        'transaction_type'
    ];

    public function user():BelongsTo{
        return $this->belongsTo(User::class, 'foreign_key');
    }
}
