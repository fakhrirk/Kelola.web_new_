<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetRequest extends Model
{
    use HasFactory;
    protected $table = 'password_reset_requests';
    protected $fillable = [
        'email',
        'token',
        'status',
    ];
}
