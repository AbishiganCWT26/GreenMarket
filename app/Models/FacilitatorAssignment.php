<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacilitatorAssignment extends Model
{
    protected $table = 'facilitator_assignments';

    protected $fillable = [
        'facilitator_id',
        'district',
        'divisional_secretariat',
        'gn_division',
        'gn_division_code'
    ];

    public function facilitator()
    {
        return $this->belongsTo(Facilitator::class);
    }
}
