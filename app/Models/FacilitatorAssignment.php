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

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            if (auth()->check() && $model->facilitator) {
                $model->facilitator->update(['updated_by' => auth()->id()]);
            }
        });

        static::deleted(function ($model) {
            if (auth()->check() && $model->facilitator) {
                $model->facilitator->update(['updated_by' => auth()->id()]);
            }
        });
    }

    public function facilitator()
    {
        return $this->belongsTo(Facilitator::class);
    }
}
