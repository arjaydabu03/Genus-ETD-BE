<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\carbon;
class Order extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "order";
    protected $fillable = ['order_no','date_ordered','date_needed',
    'date_approved','company_id','company_name','department_id','department_name',
    'location_id','location_name','customer_code','customer_name','material_code',
    'material_name','category_id','category_name','quantity','remarks','is_approved'];

    //Date format
   
public function setBegan_atAttribute($date)
{
  $this->attributes['date_ordered'] = Carbon\Carbon::parse($date)->format('Y-m-d H:m:s');
}


}
