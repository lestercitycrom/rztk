<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParseError extends Model
{
	protected $fillable = ['parse_link_id','stage','message'];
	public function link() { return $this->belongsTo(ParseLink::class,'parse_link_id'); }
}
