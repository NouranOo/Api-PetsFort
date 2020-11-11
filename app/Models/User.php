<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/* use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract; */

class User extends Model  
{
    use Notifiable;
     protected $table="Users";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'UserName', 'Email','Password','Fname','Lname','BirthDay','Phone','CountryCode',
        'Location','VerifyCode','RecoveryCode','Token','ApiToken','FacebookId','Likes',
        'Token_verify','Verified','Photo','Describition' ,'Rate' ,'HostPost',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];
    public function PetsProfile()
    {
         return $this->hasMany('App\Models\PetProfile','User_id');
    }
    public function userrate()
    {
         return $this->hasMany('App\Models\UserRate','User_id');
    }

    public function Comments()
    {
     return $this->hasMany('App\Models\Comment','User_id');
    }
 
 
 public function questions(){
      return $this->hasMany('App\Models\Question','User_id');
 
    }
 
    public function Reports()
    {
     return $this->hasMany('App\Models\Report');
    }
  /*   public function Messages(){
        return $this->hasMany('App\Models\Message','User_id');

    } */

    // public function TokenVerify(){
    //     return $this->belongsTo('App\Model\TokenVerify');
    // }
    

}
