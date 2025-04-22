<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany};

class Product extends Model
{
	protected $fillable = [
		'rozetka_id', 'title', 'url',
		'parse_link_id', 'category_id',
		'price', 'old_price', 'currency', 'in_stock', 'brand',
		'image_url', 'description', 'last_detail_parsed_at',
	];

	protected $casts = [
		'in_stock'			   => 'boolean',
		'last_detail_parsed_at' => 'datetime',
	];

	public function parseLink(): BelongsTo
	{
		return $this->belongsTo(ParseLink::class);
	}

	public function category(): BelongsTo
	{
		return $this->belongsTo(Category::class);
	}

	public function attributes(): BelongsToMany
	{
		return $this->belongsToMany(Attribute::class, 'product_attributes')
			->withPivot('value');
	}
}
