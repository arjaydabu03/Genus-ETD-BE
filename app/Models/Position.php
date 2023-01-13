<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\carbon;

class Position extends Model
{
    use HasFactory,SoftDeletes;
    
protected $table ="position";
protected $fillable =['position','description'];

public function user()
{
    return $this->belongsTo(User::class,'position');
}

}
