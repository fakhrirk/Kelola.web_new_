<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'period',
        'base_salary',
        'deductions',
        'bonuses',
        'final_salary',
        'status',
    ];

    // Relasi ke tabel User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
