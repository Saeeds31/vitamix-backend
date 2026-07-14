<?php

namespace Modules\Major\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Major\Database\Factories\MajorFactory;

class Major extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [

        'first_name',
        'last_name',
        'mobile',
        'province_id',
        'city_id',
        'email',
        'product_name',
        'product_type',
        'weight',
        'status',
        'last_call_summary',
    ];
    protected $table = "major_requests";
}
