<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParseLink extends Model
{
	protected $fillable = [
		'url', 'type', 'title', 'total_pages', 'last_parsed_page',
		'status', 'status_message', 'last_parsed_at', 'is_active',
	];

	protected $casts = [
		'is_active'	   => 'boolean',
		'last_parsed_at' => 'datetime',
	];

	public function products(): HasMany
	{
		return $this->hasMany(Product::class);
	}
	
	public function errors() { return $this->hasMany(ParseError::class); }	
}
