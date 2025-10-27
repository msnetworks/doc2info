<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class HeroCase extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hero_cases';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ref_no',
        'product',
        'profiling',
        'employer_business_name',
        'fi_cpv_type',
        'state',
        'customer_name',
        'customer_address',
        'mobile_no',
        'alt_mob_no',
        'email_id',
        'loan_amount',
        'ownership_type',
        'remarks',
        'status',
        'verification_status',
        'verification_pdf',
        'verified_by',
        'verified_on',
        'verification_remarks',
        'appointment_remarks',
        'appointment_status',
        'appointment_by',
        'appointment_on',
        'reschedule_on',
        'reschedule_remarks',
        'deleted_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'loan_amount' => 'decimal:2',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'ref_no';
    }
    
    public function states()
    {
        return $this->belongsTo(State::class, 'state');
    }
    
    public function fitypes()
    {
        return $this->belongsTo(FiType::class, 'fi_cpv_type');
    }
    
    public function products()
    {
        return $this->belongsTo(FiType::class, 'product');
    }
    
    public function verificationStatus()
    {
        return $this->belongsTo(Status::class, 'verification_status');
    }
    
    public function appointmentStatus()
    {
        return $this->belongsTo(Status::class, 'appointment_status');
    }
    
}