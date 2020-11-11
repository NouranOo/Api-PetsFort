<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
 

class Breed extends Model  
{
     protected $table="breed";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Name','Type',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    public function Pets()
    {
         return $this->hasMany('App\Models\PetProfile','Breed_id');
    }
  
}