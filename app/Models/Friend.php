<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
 

class Friend extends Model  
{
     protected $table="Friends";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'User_id', 'Friend_id' 
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    public function Sender(){
              return $this->belongsTo('App\Models\User','User_id');
    }
    public function Reciver(){
         return $this->belongsTo('App\Models\User','Friend_id');
    }
    public function MyFriend(){
        return $this->belongsTo('App\Models\User','Friend_id');
   }
}
