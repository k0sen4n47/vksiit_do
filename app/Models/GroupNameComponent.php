<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupNameComponent extends Model
{
    protected $fillable = ['full_name', 'short_name', 'suffix'];
}
