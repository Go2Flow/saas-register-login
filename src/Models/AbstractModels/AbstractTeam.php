<?php
/**
 * Model object generated by: Skipper (http://www.skipper18.com)
 * Do not modify this file manually.
 */

namespace Go2Flow\SaasRegisterLogin\Models\AbstractModels;

use Illuminate\Database\Eloquent\Model;

/**
* Class AbstractTeam
* @package Go2Flow\SaasRegisterLogin\Models\AbstractModels
*
* @property bigInteger $id
* @property string $psp_id
* @property string $psp_instance
* @property string $kyc_status
* @property string $payment_model
* @property bigInteger $owner_id
* @property string $name
* @property string $email
* @property string $phone_prefix
* @property string $phone_number
* @property string $currency
* @property integer $service_fee
* @property json $languages
* @property string $time_zone
* @property string $extra_billing_information
* @property string $billing_address
* @property string $billing_address_line_2
* @property string $billing_city
* @property string $billing_state
* @property string $billing_postal_code
* @property string $billing_country
* @property json $receipt_emails
* @property string $vat_id
* @property string $tax_number
* @property string $bank_name
* @property string $bank_iban
* @property string $bank_swift
* @property \Carbon\Carbon $created_at
* @property \Carbon\Carbon $updated_at
* @property \Carbon\Carbon $deleted_at
* @property \Go2Flow\SaasRegisterLogin\Models\User|null $owner
* @property \Illuminate\Database\Eloquent\Collection $invitations
* @property \Illuminate\Database\Eloquent\Collection $invoices
* @property \Illuminate\Database\Eloquent\Collection $customers
* @property \Illuminate\Database\Eloquent\Collection $orders
* @property \Illuminate\Database\Eloquent\Collection $courses
* @property \Illuminate\Database\Eloquent\Collection $attributes
* @property \Illuminate\Database\Eloquent\Collection $fields
* @property \Illuminate\Database\Eloquent\Collection $options
* @property \Illuminate\Database\Eloquent\Collection $locations
* @property \Illuminate\Database\Eloquent\Collection $type_sets
* @property \Illuminate\Database\Eloquent\Collection $taxes
* @property \Illuminate\Database\Eloquent\Collection $emailLogs
* @property \Illuminate\Database\Eloquent\Collection $courseIndices
* @property \Illuminate\Database\Eloquent\Collection $courzly_invoices
* @property \Illuminate\Database\Eloquent\Collection $defaultConfigs
* @property \Illuminate\Database\Eloquent\Collection $teamAdditionalSettings
* @property \Illuminate\Database\Eloquent\Collection $discoutRules
*/ 
abstract class AbstractTeam extends Model
{
    /**  
     * The model's default values for attributes.
     * 
     * @var array
     */
    protected $attributes = [
        'payment_model' => 'pay_as_you_go',
        'time_zone' => 'Europe/Berlin'
    ];
    
    /**  
     * The attributes that should be cast to native types.
     * 
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'psp_id' => 'string',
        'psp_instance' => 'string',
        'kyc_status' => 'string',
        'payment_model' => 'string',
        'owner_id' => 'integer',
        'name' => 'string',
        'email' => 'string',
        'phone_prefix' => 'string',
        'phone_number' => 'string',
        'currency' => 'string',
        'service_fee' => 'integer',
        'languages' => 'array',
        'time_zone' => 'string',
        'extra_billing_information' => 'string',
        'billing_address' => 'string',
        'billing_address_line_2' => 'string',
        'billing_city' => 'string',
        'billing_state' => 'string',
        'billing_postal_code' => 'string',
        'billing_country' => 'string',
        'receipt_emails' => 'array',
        'vat_id' => 'string',
        'tax_number' => 'string',
        'bank_name' => 'string',
        'bank_iban' => 'string',
        'bank_swift' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];
    
    /**  
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'id',
        'psp_id',
        'psp_instance',
        'kyc_status',
        'payment_model',
        'owner_id',
        'name',
        'email',
        'phone_prefix',
        'phone_number',
        'currency',
        'service_fee',
        'languages',
        'time_zone',
        'extra_billing_information',
        'billing_address',
        'billing_address_line_2',
        'billing_city',
        'billing_state',
        'billing_postal_code',
        'billing_country',
        'receipt_emails',
        'vat_id',
        'tax_number',
        'bank_name',
        'bank_iban',
        'bank_swift',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    
    public function owner()
    {
        return $this->belongsTo('\Go2Flow\SaasRegisterLogin\Models\User', 'owner_id', 'id');
    }
    
    public function invitations()
    {
        return $this->hasMany('\Go2Flow\SaasRegisterLogin\Models\Team\Invitation', 'team_id', 'id');
    }
    
    public function invoices()
    {
        return $this->hasMany('\App\Models\Invoice', 'team_id', 'id');
    }
    
    public function customers()
    {
        return $this->hasMany('\App\Models\Customer', 'team_id', 'id');
    }
    
    public function orders()
    {
        return $this->hasMany('\App\Models\Order\Order', 'team_id', 'id');
    }
    
    public function courses()
    {
        return $this->hasMany('\App\Models\Course\Course', 'team_id', 'id');
    }
    
    public function attributes()
    {
        return $this->hasMany('\App\Models\Course\Attribute\Attribute', 'team_id', 'id');
    }
    
    public function fields()
    {
        return $this->hasMany('\App\Models\Course\Field\Field', 'team_id', 'id');
    }
    
    public function options()
    {
        return $this->hasMany('\App\Models\Course\Option\Option', 'team_id', 'id');
    }
    
    public function locations()
    {
        return $this->hasMany('\App\Models\Course\Location', 'team_id', 'id');
    }
    
    public function type_sets()
    {
        return $this->hasMany('\App\Models\Course\Type\Set', 'team_id', 'id');
    }
    
    public function taxes()
    {
        return $this->hasMany('\App\Models\Tax', 'team_id', 'id');
    }
    
    public function emailLogs()
    {
        return $this->hasMany('\App\Models\EmailLog', 'team_id', 'id');
    }
    
    public function courseIndices()
    {
        return $this->hasMany('\App\Models\Course\Index\CourseIndex', 'team_id', 'id');
    }
    
    public function courzly_invoices()
    {
        return $this->hasMany('\App\Models\CourzlyInvoice', 'team_id', 'id');
    }
    
    public function defaultConfigs()
    {
        return $this->hasMany('\App\Models\DefaultConfig', 'team_id', 'id');
    }
    
    public function teamAdditionalSettings()
    {
        return $this->hasMany('\App\Models\TeamAdditionalSettings', 'team_id', 'id');
    }
    
    public function discoutRules()
    {
        return $this->hasMany('\App\Models\DiscountRules', 'team_id', 'id');
    }
    
    public function users()
    {
        return $this->belongsToMany('\Go2Flow\SaasRegisterLogin\Models\User', 'team_user', 'team_id', 'user_id');
    }
    
    public function layouts()
    {
        return $this->belongsToMany('\Go2Flow\SaasRegisterLogin\Models\Layouts', 'team_layouts', 'team_id', 'layouts_id')->withPivot('custom_css', 'primary_color', 'secondary_color');
    }
}
