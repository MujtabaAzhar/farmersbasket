<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Order;
use App\Models\Review;
use App\Models\WishlistItem;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlistItems()
    {
        return $this->hasMany(WishlistItem::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function posSessions()
    {
        return $this->hasMany(PosSession::class);
    }

    public function customerAddresses()
    {
        return $this->hasMany(CustomerAddress::class, 'customer_id')->orderByDesc('is_default')->orderBy('id');
    }

    public function loginLogs()
    {
        return $this->hasMany(LoginActivityLog::class);
    }

    // ---- Role helpers (never use for mass assignment) ----

    public function isAdmin(): bool
    {
        return $this->utype === 'ADM';
    }

    public function isSupervisor(): bool
    {
        return $this->pos_role === 'pos_supervisor';
    }

    public function isCashier(): bool
    {
        return $this->pos_role === 'cashier';
    }

    public function isPosUser(): bool
    {
        return in_array($this->pos_role, ['pos_supervisor', 'cashier']);
    }

    public function activeSession(): ?PosSession
    {
        return $this->posSessions()
            ->where('status', 'open')
            ->latest()
            ->first();
    }

    public function roleBadge(): string
    {
        if ($this->utype === 'ADM')       return 'Super Admin';
        if ($this->pos_role === 'pos_supervisor') return 'POS Supervisor';
        if ($this->pos_role === 'cashier')        return 'Cashier';
        return 'Customer';
    }
}
