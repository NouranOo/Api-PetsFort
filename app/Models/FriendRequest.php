<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
 

class FriendRequest extends Model  
{
     protected $table="FriendRequests";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ReqFrom', 'ReqTo','Type','Status'  
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    public function Sender(){
              return $this->belongsTo('App\Models\User','ReqFrom');
    }
    public function Reciver(){
         return $this->belongsTo('App\Models\User','ReqTo');
    }
   
}
