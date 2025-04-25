<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
	protected $fillable = ['rozetka_id','name','url'];

	public function products() {
		return $this->hasMany(Product::class);
	}
}
