<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
	protected $fillable = [
		'rozetka_id','title','url','parent_id',
		'h1','meta_title','meta_description','meta_keywords',
	];	
	

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
