<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;



class Notfication extends Model  
{
 
     protected $table="Notfications";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'User_id', 'Seen','Title','body',
        'key','seen_at','Model',
        'notify_target_id','Type'
    ];

    

 
    
    

   
}
