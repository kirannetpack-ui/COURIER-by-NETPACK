<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // =============================================
    // USER TYPE CONSTANTS
    // =============================================
    const TYPE_SUPER_ADMIN = 'super_admin';
    // const TYPE_ADMIN = 'admin'; // REMOVED - Use super_admin instead
    const TYPE_STAFF = 'staff';
    const TYPE_DOMESTIC_ADMIN = 'domestic_admin';
    const TYPE_INTERNATIONAL_ADMIN = 'international_admin';
    const TYPE_SELLER = 'seller';
    const TYPE_RIDER = 'rider';
    const TYPE_PARTNER = 'partner';
    const TYPE_OVERSEAS = 'overseas';
    const TYPE_CUSTOMER = 'customer';
    const TYPE_CLIENT = 'client';

    const USER_TYPES = [
        self::TYPE_SUPER_ADMIN => 'Super Administrator',
        self::TYPE_STAFF => 'Staff',
        self::TYPE_DOMESTIC_ADMIN => 'Domestic & E-commerce Admin',
        self::TYPE_INTERNATIONAL_ADMIN => 'International Service Admin',
        self::TYPE_SELLER => 'Seller',
        self::TYPE_RIDER => 'Rider',
        self::TYPE_PARTNER => 'Domestic Partner',
        self::TYPE_OVERSEAS => 'Overseas Partner',
        self::TYPE_CUSTOMER => 'Customer',
        self::TYPE_CLIENT => 'Client',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'password_changed',
        'user_type',
        'verification_status',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'company_name',
        'business_name',
        'business_address',
        'contact_person',
        'is_online',
        'is_available',
        'vehicle_type',
        'vehicle_number',
        'current_latitude',
        'current_longitude',
        'last_location_update',
        'total_deliveries',
        'total_earnings',
        'rating',
        'rider_deposit_balance',
        'rider_deposit_limit',
        'rider_commission_rate',
        'rider_delivery_fee',
        'rider_margin_rate',
        'bank_name',
        'account_holder_name',
        'account_number',
        'account_type',
        'ifsc_code',
        'email_notifications',
        'sms_notifications',
        'order_updates',
        'registration_completed',
        'approved_at',
        'last_login_at',
        'metadata',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'last_login_at' => 'datetime',
        'last_location_update' => 'datetime',
        'is_online' => 'boolean',
        'is_available' => 'boolean',
        'registration_completed' => 'boolean',
        'password_changed' => 'boolean',
        'email_notifications' => 'boolean',
        'sms_notifications' => 'boolean',
        'order_updates' => 'boolean',
        'rider_deposit_balance' => 'decimal:2',
        'rider_deposit_limit' => 'decimal:2',
        'rider_commission_rate' => 'decimal:2',
        'rider_delivery_fee' => 'decimal:2',
        'rider_margin_rate' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'rating' => 'decimal:2',
        'metadata' => 'array',
    ];

    // =============================================
    // RELATIONSHIPS
    // =============================================

    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'seller_id');
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'rider_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class);
    }

    public function riderDeposits()
    {
        return $this->hasMany(RiderDeposit::class, 'rider_id');
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Check if user is a system admin (super admin or staff)
     */
    public function isSystemAdmin()
    {
        return in_array($this->user_type, [self::TYPE_SUPER_ADMIN, self::TYPE_STAFF]);
    }

    /**
     * Check if user is a super admin
     */
    public function isSuperAdmin()
    {
        return $this->user_type === self::TYPE_SUPER_ADMIN;
    }

    /**
     * Check if user is a domestic admin
     */
    public function isDomesticAdmin()
    {
        return $this->user_type === self::TYPE_DOMESTIC_ADMIN;
    }

    /**
     * Check if user is an international admin
     */
    public function isInternationalAdmin()
    {
        return $this->user_type === self::TYPE_INTERNATIONAL_ADMIN;
    }

    /**
     * Check if user is a seller
     */
    public function isSeller()
    {
        return $this->user_type === self::TYPE_SELLER;
    }

    /**
     * Check if user is a rider
     */
    public function isRider()
    {
        return $this->user_type === self::TYPE_RIDER;
    }

    /**
     * Check if user is a partner
     */
    public function isPartner()
    {
        return $this->user_type === self::TYPE_PARTNER;
    }

    /**
     * Check if user is a customer
     */
    public function isCustomer()
    {
        return $this->user_type === self::TYPE_CUSTOMER || $this->user_type === self::TYPE_CLIENT;
    }

    /**
     * Get user type label
     */
    public function getUserTypeLabelAttribute()
    {
        return self::USER_TYPES[$this->user_type] ?? ucfirst($this->user_type);
    }

    /**
     * Get user role badge
     */
    public function getRoleBadgeAttribute()
    {
        $badges = [
            self::TYPE_SUPER_ADMIN => 'bg-purple-100 text-purple-800',
            self::TYPE_STAFF => 'bg-gray-100 text-gray-800',
            self::TYPE_DOMESTIC_ADMIN => 'bg-blue-100 text-blue-800',
            self::TYPE_INTERNATIONAL_ADMIN => 'bg-indigo-100 text-indigo-800',
            self::TYPE_SELLER => 'bg-green-100 text-green-800',
            self::TYPE_RIDER => 'bg-yellow-100 text-yellow-800',
            self::TYPE_PARTNER => 'bg-orange-100 text-orange-800',
            self::TYPE_OVERSEAS => 'bg-pink-100 text-pink-800',
            self::TYPE_CUSTOMER => 'bg-gray-100 text-gray-800',
            self::TYPE_CLIENT => 'bg-gray-100 text-gray-800',
        ];
        return $badges[$this->user_type] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Check if rider is online
     */
    public function isOnline()
    {
        return $this->is_online && $this->is_available;
    }

    /**
     * Get rider's deposit balance
     */
    public function getDepositBalanceAttribute()
    {
        return $this->rider_deposit_balance ?? 0;
    }

    /**
     * Check if rider has sufficient deposit for COD
     */
    public function hasSufficientDeposit($amount)
    {
        return ($this->rider_deposit_balance ?? 0) >= $amount;
    }

    /**
     * Deduct from rider deposit
     */
    public function deductDeposit($amount, $description = null)
    {
        $this->rider_deposit_balance -= $amount;
        $this->save();

        // Create deposit record
        RiderDeposit::create([
            'rider_id' => $this->id,
            'amount' => -$amount,
            'balance' => $this->rider_deposit_balance,
            'type' => 'settlement',
            'description' => $description ?? 'Deposit deduction',
            'status' => 'completed',
            'verified_at' => now(),
        ]);

        return $this;
    }

    /**
     * Add to rider deposit
     */
    public function addDeposit($amount, $description = null)
    {
        $this->rider_deposit_balance += $amount;
        $this->save();

        // Create deposit record
        RiderDeposit::create([
            'rider_id' => $this->id,
            'amount' => $amount,
            'balance' => $this->rider_deposit_balance,
            'type' => 'deposit',
            'description' => $description ?? 'Deposit added',
            'status' => 'completed',
            'verified_at' => now(),
        ]);

        return $this;
    }
}
