<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
 

class Match extends Model  
{
     protected $table="Matches";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'User_id', 'Owner_id','Pet_id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    public function PetOwner(){
              return $this->belongsTo('App\Models\User','Owner_id');
    }
    public function Pet(){
         return $this->belongsTo('App\Models\PetProfile','Pet_id');
    }
  
}
