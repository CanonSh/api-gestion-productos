<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Review;

class Product extends Model
{
    //
    protected $fillable = [
        'name',
        'description',
        'price'
    ];
    public function reviews(){
        return $this->hasMany(Review::class);
    }

}
