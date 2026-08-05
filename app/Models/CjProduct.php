<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CjProduct extends Model
{
    use HasFactory;
    protected $table = 'cj_products';
    protected $guarded = ['id'];
}
