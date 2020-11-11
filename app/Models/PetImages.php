<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
 

class PetImages extends Model  
{
 
     protected $table="petimages";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Photoname', 'Petprofile_id'
    ];

    public function Pet(){
       return  $this->belongTo('App\Models\PetProfile','petprofile_id');
    }
    

   
}
