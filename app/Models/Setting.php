<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'request_delay',
        'details_per_category',
    ];

    /** Shortcut helper */
    public static function value(string $key, $default = null)
    {
        return optional(static::first())->{$key} ?? $default;
    }
}
