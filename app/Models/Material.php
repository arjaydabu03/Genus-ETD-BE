<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Material extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "materials";
    protected $fillable = ['code','description','category_id'];

    protected $hidden = [
        'category_id',
    ];


    public function category()
    {
        return $this->belongsTo(Category::class)->select('id','name');
    }
}
