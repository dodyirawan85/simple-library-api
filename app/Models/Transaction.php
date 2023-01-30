<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = "transactions";
    protected $fillable = [
        'book_id',
        'user_id',
        'returned_date',
        'status'
    ];
}
