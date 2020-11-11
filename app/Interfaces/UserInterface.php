<?php

namespace App\Interfaces;

interface UserInterface
{

    public function SignIn($data);//1
    public function SignUp($data);//2
    public function MakePetProfile($data);//3
    /* public function RecoverEmail($data); */
    public function AskQuestion($data);//4
    public function PostComment($data);//5
    public function PostReplay($data);//6
    public function GetUserPets($data);//7
    public function GetQuestions($data);//8
    public function GetQuestionById($data);//9
    public function GetMyQuestions($data);//10
    public function ShowUserProfileById($data); //11
    public function ShowPetProfileById($data); //12
    public function GetAllUsers($data); //13
    public function LikeUserProfile($data);
    public function LikePetProfile($data);
    public function LovePetProfile($data);
    public function SearchPet($data);
    public function SendMessage($data);
    public function GetAllMessagesByUserId($data);
    public function GetMyNotfication($data);
    public function ShowMessageById($data);
    public function SendFriendRequest($data);
    public function SendMatcheRequest($data);
    public function GetMyComingFriendRequests($data);
    public function GetMyComingMatchesRequest($data);
    public function AcceptMatchRequest($data);
    public function AcceptFriendRequest($data);
    public function RejectMatchRequest($data);
    public function RejectFriendRequest($data);

    public function GetUserFriends($data);
    public function GetUserMatchesPets($data);
    public function gethome($data);

 
    /* public function UploadPetIMages($data); */

}
