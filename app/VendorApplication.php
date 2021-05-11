<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\UsersAddress;
use App\Company;

class VendorApplication extends Model
{
    /** 
     * This models immutable values.
     *
     * @var array 
     */
    protected $guarded = [];

    /**
     * Checks if a given user has applied with no response.
     * 
     * @param  \App\User  $userId
     * @return bool
     */
    public static function hasUserApplied($userId)
    {
        return ! Self::where([
            'user_id' => $userId,
            'accepted' => null
        ])->get()->isEmpty();
    }

    /**
     * Checks if a given user has been rejected.
     * 
     * @param   \App\User  $userId
     * @return  bool
     */
    public static function hasApplicationBeenRejected($userId)
    {
        return ! Self::where([
            'user_id' => $userId,
            'accepted' => 0
        ])->get()->isEmpty();
    }

    /**
     * Adds onto a query for where vendor applications are unanswered.
     * 
     * @param  \Illuminate\Database\Eloquent\Model  $query
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function scopeWhereFresh($query)
    {
        return $query->where('accepted', '=', NULL);
    }

    /**
     * This model relationship belongs to \App\User
     * 
     * @return  \Illuminate\Database\Eloquent\Model
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Returns an error in the application creation process.
     *
     * @param  int  $userId, string  $companyName, int  $usersAddressId
     * @return string|false The error text or false implying no errors occurred.
     */
    public static function getError($userId, $companyName, $usersAddressId)
    {
        /**
         * Error if previously applied
         */
        if(self::hasUserApplied($userId))
            return 'Your existing application is being processed.';

        /**
         * Error if previous application rejected
         */
        if(self::hasApplicationBeenRejected($userId))
            return 'Unfortunately your previous application was rejected and you cannot apply again. For more information contact administrator.';

        /**
         * Error if no address on file
         */
        if(UsersAddress::where('user_id', '=', $userId)->get()->isEmpty())
            return 'You must have at least one address on file.';

        /**
         * Error if company name not given |
         * Error if company name already exists
         */
        if(! isset($companyName))
        {
            return 'Company name not provided.';
        }
        elseif(self::doesCompanyNameExist($companyName) || Company::doesCompanyNameExist($companyName))
        {
            return 'Company Name already exists.';
        }
        elseif(strlen($companyName) > 191)
        {
            return 'Company Name exceeds maximum length 191.';
        }

        /**
         * Error if address not given |
         * Error if address doesn't exist
         */
        if(! isset($usersAddressId) || ! is_numeric($usersAddressId))
        {
            return 'Address not provided.';
        }
        else
        {
            if(UsersAddress::where([
                'id' => $usersAddressId,'user_id' => $userId,
                ])->get()->isEmpty())
            {
                return 'Address not provided.';
            }
        }

        return FALSE;
    }

    /**
     * Find whether a given company name already exists in this model.
     *
     * @param  string  $companyName
     */
    public static function doesCompanyNameExist($companyName)
    {
        return ! self::where('proposed_company_name', '=', $companyName)->get()->isEmpty();
    }

    /**
     * Returns an error in the decision process when a moderator reviews an instance of this model.
     *
     * @param  int  $userId, string  $companyName, int  $usersAddressId
     * @return string|false The error text or false implying no errors occurred.
     */
    public static function getModDecisionError($reasonGiven, $acceptDecision, $declineDecision)
    {
        if(! isset($reasonGiven))
            return 'Reason not provided.';
        elseif(strlen($reasonGiven) < 10)
            return 'Reason must be longer than 10 characters.';
        elseif(strlen($reasonGiven) > 191)
            return 'Reason exceeds maximum length 191.';

        if(! isset($acceptDecision) && ! isset($declineDecision))
            return 'Error processing that request. Contact system administrator.';

        return FALSE;
    }
}
