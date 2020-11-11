<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
 

class PetProfile extends Model  
{
 
    protected $table="PetProfiles";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Type', 'Photo','Love','Like','BirthDay','Gender','User_id','Name','Description',
        'Location','Breed_id',

    ];

     public function User(){
         return $this->belongTo('App\Models\User','User_id');
     }
     public function PetsImages(){
        return  $this->hasMany('App\Models\PetImages','petprofile_id');
     }

      public function breed(){
        return $this->belongsTo('App\Models\Breed','Breed_id');
    }
   
   
}
