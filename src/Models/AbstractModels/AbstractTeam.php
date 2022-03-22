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
* @property bigInteger $owner_id
* @property string $name
* @property string $email
* @property string $currency
* @property json $languages
* @property string $extra_billing_information
* @property string $billing_address
* @property string $billing_address_line_2
* @property string $billing_city
* @property string $billing_state
* @property string $billing_postal_code
* @property string $billing_country
* @property string $receipt_emails
* @property string $vat_id
* @property \Carbon\Carbon $created_at
* @property \Carbon\Carbon $updated_at
* @property \Carbon\Carbon $deleted_at
* @property \Go2Flow\SaasRegisterLogin\Models\User|null $owner
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
*/ 
abstract class AbstractTeam extends Model
{
    /**  
     * The attributes that should be cast to native types.
     * 
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'psp_id' => 'string',
        'owner_id' => 'integer',
        'name' => 'string',
        'email' => 'string',
        'currency' => 'string',
        'languages' => 'array',
        'extra_billing_information' => 'string',
        'billing_address' => 'string',
        'billing_address_line_2' => 'string',
        'billing_city' => 'string',
        'billing_state' => 'string',
        'billing_postal_code' => 'string',
        'billing_country' => 'string',
        'receipt_emails' => 'string',
        'vat_id' => 'string',
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
        'owner_id',
        'name',
        'email',
        'currency',
        'languages',
        'extra_billing_information',
        'billing_address',
        'billing_address_line_2',
        'billing_city',
        'billing_state',
        'billing_postal_code',
        'billing_country',
        'receipt_emails',
        'vat_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    
    public function owner()
    {
        return $this->belongsTo('\Go2Flow\SaasRegisterLogin\Models\User', 'owner_id', 'id');
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
        return $this->hasMany('\App\Models\Course\Attribute\Group', 'team_id', 'id');
    }
    
    public function fields()
    {
        return $this->hasMany('\App\Models\Course\Field\Group', 'team_id', 'id');
    }
    
    public function options()
    {
        return $this->hasMany('\App\Models\Course\Option', 'team_id', 'id');
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
    
    public function users()
    {
        return $this->belongsToMany('\Go2Flow\SaasRegisterLogin\Models\User', 'team_user', 'team_id', 'user_id');
    }
}
