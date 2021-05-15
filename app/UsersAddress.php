<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class UsersAddress extends Model
{
    /** This model uses the SoftDeletes trait for a deleted_at datetime column. */
    use SoftDeletes;

    /** 
     * This models immutable values.
     *
     * @var array 
     */
    protected $guarded = [];

    /**
     * This model relationship belongs to \App\User
     * 
     * @return  \Illuminate\Database\Eloquent\Model
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    /**
     * This model relationship belongs to \App\OrderHistory
     * 
     * @return  \Illuminate\Database\Eloquent\Model
     */
    public function orderHistory()
    {
        return $this->hasMany('App\OrderHistory', 'users_addresses_id');
    }

    /**
     * Set a publicily accessible identifier to get the formatted phone number for this unique instance.
     * 
     * @return  string
     */
    public function getFormattedPhoneNumberAttribute()
    {
        return $this->attributes['phone_number_extension'] . ' ' . $this->attributes['phone_number'];
    }

    /**
     * Set a publicily accessible identifier to get the formatted mobile number for this unique instance.
     * 
     * @return  string
     */
    public function getFormattedMobileNumberAttribute()
    {
        return $this->attributes['mobile_number_extension'] . ' ' . $this->attributes['mobile_number'];
    }

    /**
     * Returns each address value in a single line when this model instance is treated as a string.
     *
     * @return string
     */
    public function __tostring()
    {
        return $this->attributes['building_name'].' '.$this->attributes['street_address1'].
               ' '.$this->attributes['street_address2'].' '.$this->attributes['street_address3'].
               ' '.$this->attributes['street_address4'].' '.$this->attributes['county'].
               ' '.$this->attributes['city'].' '.$this->attributes['postcode'].
               ' '.$this->attributes['country'].' '.($this->attributes['formatted_phone_number'] ?? null).
               ' '.($this->attributes['formatted_mobile_number'] ?? null);
    }
}
