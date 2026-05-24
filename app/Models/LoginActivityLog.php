<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'email', 'ip_address', 'user_agent', 'action'];

    protected $casts = ['created_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function log(string $action, string $email, ?int $userId = null): void
    {
        static::create([
            'user_id'    => $userId,
            'email'      => $email,
            'ip_address' => request()->ip(),
            'user_agent' => substr(request()->userAgent() ?? '', 0, 255),
            'action'     => $action,
        ]);
    }
}
