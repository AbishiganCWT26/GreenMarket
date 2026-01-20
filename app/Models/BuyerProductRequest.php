<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyerProductRequest extends Model
{
	use HasFactory;

	protected $fillable = [
		'buyer_id',
		'product_name',
		'product_image',
		'needed_quantity',
		'unit_of_measure',
		'needed_date',
		'unit_price',
		'description',
		'status'
	];

	protected $casts = [
		'needed_date' => 'date',
		'needed_quantity' => 'decimal:2',
		'unit_price' => 'decimal:2'
	];

	public function buyer()
	{
		return $this->belongsTo(Buyer::class);
	}
}
