<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryRider extends Model
{
	use HasFactory;

	protected $table = 'delivery_riders';

	protected $fillable = [
		'user_id',
		'is_online',
		'name',
		'nic_no',
		'primary_mobile',
		'email',
		'vehicle_number',
		'vehicle_type',
		'max_kg_capacity',
		'whatsapp_number',
		'residential_address',
	];

	protected $casts = [
		'is_online' => 'boolean',
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}
}
