<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setelan extends Model
{
    protected $table = 'setelan';
    protected $fillable = ['kunci', 'nilai'];
}