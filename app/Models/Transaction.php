<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',           // Add type to fillable
        'amount',         // Add amount to fillable
        'description',    // Add description to fillable
        'status',         // Add status to fillable
    ];

    // You can also define relationships here if needed
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
