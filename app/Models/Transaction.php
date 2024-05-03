<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'courier',
        'payment',
        'payment_url',
        'status',
        'total_price',
    ];

    public function user()
    {
        return $this->BelongsTo(User::class);
    }

    public function transaction_items()
    {
        return $this->hasMany(TransactionItem::class);
    }
}
