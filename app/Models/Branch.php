<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = ['name', 'code', 'address', 'city', 'phone', 'manager_id', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function staff()
    {
        return $this->hasMany(User::class, 'branch_id');
    }

    public function posSessions()
    {
        return $this->hasMany(PosSession::class);
    }

    public function activeSession(int $userId): ?PosSession
    {
        return $this->posSessions()
            ->where('user_id', $userId)
            ->where('status', 'open')
            ->latest()
            ->first();
    }
}
