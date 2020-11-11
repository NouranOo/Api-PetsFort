<?php
namespace App\Helpers;

 
use App\Models\User;
use App\Models\Notfication;

class GeneralHelper
{
    protected static $currentUser;
   

    public static function SetCurrentUser($apitoken)
    {
        self::$currentUser = User::where('ApiToken', $apitoken)->first();

    }
   
    public static function getcurrentUser()
    {
        return self::$currentUser;
    }
    public static  function SetNotfication($title,$body,$model,$notify_from,$notify_to,$notify_target,$type)
    {
        $Notfiy = new Notfication();
        $Notfiy->Title=$title;
        $Notfiy->body=$body;
        $Notfiy->User_id=$notify_to;
        $Notfiy->Model=$model;
        $Notfiy->notify_from=$notify_from;
        $Notfiy->notify_target_id=$notify_target;
        $Notfiy->Type=$type;
        $Notfiy->seen_at="";
        $Notfiy->save();





         
    }

}
