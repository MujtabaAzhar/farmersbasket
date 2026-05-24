<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    protected $fillable = ['type', 'title', 'message', 'url', 'is_read'];

    protected $casts = ['is_read' => 'boolean'];

    public static function notify(string $type, string $title, string $message, ?string $url = null): void
    {
        static::create(compact('type', 'title', 'message', 'url'));
    }

    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }
}
