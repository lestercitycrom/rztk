<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;   // ← добавили
use Filament\Panel;                           // ← добавили
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser   // ← implements
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password'];
    protected $hidden   = ['password', 'remember_token'];
    protected $casts    = ['email_verified_at' => 'datetime', 'password' => 'hashed'];

    // разрешаем вход в указанную панель всем авторизованным.

    public function canAccessPanel(Panel $panel): bool
    {
        return true;          // замените своей логикой, если нужно
    }
}
