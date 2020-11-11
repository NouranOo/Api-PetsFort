<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
 

class Replay extends Model  
{
   protected $table="Replay";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Replay', 'Comment_id','User_id'
    ];

    // public function Comment(){
    //     return $this->belongsTo('App\Model\Comment','Comment_id');

    // }
   
}
