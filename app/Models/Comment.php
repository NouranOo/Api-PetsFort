<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
 

class Comment extends Model  
{
 
     protected $table="Comments";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Comment', 'Question_id','User_id'
    ];

    public function Replaies(){
       return  $this->hasMany('App\Models\Replay','Comment_id');
    }
    public function User(){
        return $this->belongsTo('App\Models\User','User_id');
    }
    public function question(){

      return $this->belongsTo('App\Models\Question','Question_id');

   }
    

   
}
