<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemStandard extends Model
{
    protected $table = 'system_standards';

    protected $fillable = [
        'standard_type', 'standard_value', 'description', 'is_active', 'display_order'
    ];
}
