<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemConfig extends Model
{
    protected $table = 'system_config';

    protected $fillable = [
        'config_key', 'config_value', 'config_group',
        'description', 'is_public', 'updated_by'
    ];
}
