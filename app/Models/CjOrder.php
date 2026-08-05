<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CjOrder extends Model
{
    use HasFactory;
    protected $table = 'cj_orders';
    protected $guarded = ['id'];
}
