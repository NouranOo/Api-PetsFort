<?php
namespace App\Repositories;

use App\Helpers\ApiResponse;
use App\helpers\FCMHelper;
use App\Helpers\GeneralHelper;
use App\Interfaces\UserInterface;
use App\Models\Comment;
use App\Models\Friend;
use App\Models\FriendRequest;
use App\Models\Match;
use App\Models\MatchRequest;
use App\Models\Message;
use App\Models\Notfication;
use App\Models\PetProfile;
use App\Models\Question;
use App\Models\Replay;
use App\Models\User;
use App\Models\UserRate;
use App\Models\Report;
use App\Models\PetImages;
use App\Models\Breed;
use DB;
use Illuminate\Support\Collection;
use App\Notifications\VerifyUserEmail;
use App\Notifications\VerifyUserLink;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserInterface
{

    public $apiResponse;
    public $generalhelper;
    public function __construct(GeneralHelper $generalhelper, ApiResponse $apiResponse)
    {
        $this->generalhelper = $generalhelper;

        $this->apiResponse = $apiResponse;

    }
    public function SignUp($data) //1
    {
        $data['ApiToken'] = base64_encode(str_random(40));
        $data['VerifyCode'] = base64_encode(str_random(6));
        $data['Token_verify'] = base64_encode(str_random(6));
        $data['Password'] = app('hash')->make($data['Password']);

        try {
            $user = User::create($data);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->apiResponse->setError("Missing data ")->setData();
        }
        $user->notify(new VerifyUserLink($user->UserName, $user->Token_verify)); 
        $user->notify(new VerifyUserEmail($user->UserName, $user->VerifyCode)); //send verify code to email
        return $this->apiResponse->setSuccess("User created succesfully")->setData($user);
    }
    public function SignIn($data) //2
    {
        $user = User::where('Email', $data['Email'])->first();
        /*   if ($user and $user->is_verified == 0) {
        return $this->apiResponse->setError("Email Not verfied!")->setVerify("false")->setData();

        } */
        if ($user) {
            $check = Hash::check($data['Password'], $user->Password);
        } else {
            return $this->apiResponse->setError("Your email not found!")->setData();
        }
        if ($check) {
            
            try {
                $user->update(['ApiToken'=>base64_encode(str_random(40))]);
                $user->save();
            } catch (\Illuminate\Database\QueryException $ex) {
                return $this->apiResponse->setError($ex->getMessage())->setData();
            }
            return $this->apiResponse->setSuccess("Login Successfuly")->setData($user);

        } else {
            return $this->apiResponse->setError("Your Password not correct!")->setData();
        }

    }
    public function MakePetProfile($data) //3
    {
        $user = GeneralHelper::getcurrentUser();
        $data['User_id'] = $user->id;
        try {
            $Pet = PetProfile::create($data);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->apiResponse->setError($ex->getMessage())->setData();
        }
        return $this->apiResponse->setSuccess("Pet Profile set Successfuly")->setData($Pet);
    }
    public function AskQuestion($data) //4
    {
        $user = GeneralHelper::getcurrentUser();
        $data['User_id'] = $user->id;
        try {
            $Question = Question::create($data);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->apiResponse->setError($ex->getMessage())->setData();
        }
        return $this->apiResponse->setSuccess("Question Posted Successfuly")->setData($Question);

    }
    public function PostComment($data) //5
    {
        $user = GeneralHelper::getcurrentUser();
        $data['User_id'] = $user->id;
        try {
            $Comment = Comment::create($data);
            $QuestionOwner = Question::find($data['Question_id']);
            if ($QuestionOwner->User_id != $user->id) {
                GeneralHelper::SetNotfication($user->UserName . ' ' . ' Commented On Your Question', $data['Comment'], 'Question', $user->id, $QuestionOwner->User_id, $QuestionOwner->id, "Comment");
                //--------------------------FireBaseNotfication------------------------------------------
                $targetUser = User::where('id', $QuestionOwner->User_id)->first();
                $data1 = array('title' => 'PetsFort', 'body' => $user->UserName . ' ' . ' Commented On Your Question', 'Key' => 'Notify');
                $res = FCMHelper::sendFCMMessage($data1, $targetUser->Token);
                //---------------------------------------------------------------------
            }

        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->apiResponse->setError($ex->getMessage())->setData();
        }
        return $this->apiResponse->setSuccess("Comment Posted Successfuly")->setData($Comment);

    }
    public function PostReplay($data) //6
    {
        $user = GeneralHelper::getcurrentUser();
        $data['User_id'] = $user->id;
        try {
            $question = Question::where('id', Comment::find($data['Comment_id'])->first()->Question_id)->first();
            $Replay = Replay::create($data);
            if ($Replay->User_id != $user->id) {
                GeneralHelper::SetNotfication($user->UserName . ' ' . 'Replayed On Your Comment', $data['Replay'], 'Question', $user->id, $question->User_id, $question->id, "Replay");
                //--------------------------FireBaseNotfication------------------------------------------
                $TargetUser = User::where('id', $question->User_id)->first();
                $data1 = array('title' => 'PetsFort', 'body' => $user->UserName . ' ' . 'Replayed On Your Comment', 'Key' => 'Notify');
                $res = FCMHelper::sendFCMMessage($data1, $TargetUser->Token);
                //---------------------------------------------------------------------
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->apiResponse->setError($ex->getMessage())->setData();
        }
        return $this->apiResponse->setSuccess("Replay Posted Successfuly")->setData($Replay);

    }
    public function GetUserPets($data) //7
    {

        $user = GeneralHelper::getcurrentUser();
        $data['User_id'] = $user->id;
        try {
            $pets = User::where('id', $data['User_id'])->first()->PetsProfile;
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->apiResponse->setError($ex->getMessage())->setData();
        }
        return $this->apiResponse->setSuccess("User Pets Fetched Successfuly")->setData($pets);

    }
    public function GetQuestions($data) //8
    {

        $user = GeneralHelper::getcurrentUser();
        $data['User_id'] = $user->id;
        try {

            // $Questions = Question::with('Owner')->withCount('Comments')->get();
            //  $result = array('questions' => $Questions, 'userrate' => $user->rate);
            $Questions = Question::with('Owner')->withCount(['Comments' => function ($query) {
                $query->with('Replaies');
            }])->get();

        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->apiResponse->setError($ex->getMessage())->setData();
        }
        return $this->apiResponse->setSuccess("User Questions Fetched Successfuly")->setData($Questions);

    }
    public function GetQuestionById($data) //9
    {
        try {
            $question = Question::where('id', $data['id'])->with('Owner')->with(['Comments' => function ($query) {
                $query->with('Replaies');
            }])->first();
            // $question = Question::where('id', $data['id'])->with('Owner')->with(['Comments' => function ($query) {
            //     $query->with('User');

            // }])->withCount('Comments')->first();

        } catch (\Exception $ex) {
            return $this->apiResponse->setError($ex->getMessage())->setData();
        }
        return $this->apiResponse->setSuccess(" Question Fetched Successfuly")->setData($question);
    }
    public function GetMyQuestions($data) //10
    {
        try {
            $user = GeneralHelper::getcurrentUser();
            $questions = Question::where('User_id', $user->id)->with(['Comments' => function ($query) {
                $query->with('Replaies');
            }])->get();
            // $questions = Question::where('User_id', $user->id)->withCount('Comments')->get();
            $avg= DB::table('userrates')
            ->where('User_id',$user->id)
            ->avg('rate');
            $result = array('questions' => $questions, 'userrate' => $avg);

        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->apiResponse->setError($ex->getMessage())->setData();
        }
        return $this->apiResponse->setSuccess(" Question Fetched Successfuly")->setData($result);
    }
    public function ShowUserProfileById($data) //11
    {
        try {
            $user = GeneralHelper::getcurrentUser();
            $User = User::where('id', $data['User_id'])->with('PetsProfile')->first();
            if ($User->id != $user->id) {

                GeneralHelper::SetNotfication($user->UserName . ' ' . ' Viewed Your Profile', 'UserProfile', 'Love', $user->id, $User->id, $User->id, "ViewUserProfile");
                    //--------------------------FireBaseNotfication------------------------------------------
                     $data1 = array('title' => 'PetsFort', 'body' =>$user->UserName . ' ' . ' Viewed Your Profile', 'Key' => 'Notify');
                    $res = FCMHelper::sendFCMMessage($data1,  $User->Token);
                    //---------------------------------------------------------------------
            }
        } catch (\Exception $ex) {
            return $this->apiResponse->setError($ex->getMessage())->setData();
        }
        return $this->apiResponse->setSuccess("User Profile Fetched Successfuly")->setData($User);

    }
    public function ShowPetProfileById($data) //12 **
    {
        try {
            $user = GeneralHelper::getcurrentUser();
            $pet = PetProfile::where('id', $data['Pet_id'])->first();
            // $pet = PetProfile::where('id', $data['Pet_id'])->with('PetsImages')->first();
            // $Breed = PetProfile::find($data['Pet_id'])->breed->name->first();
            // $result = array('pet' => $pet, 'BreedName' => $Breed);

        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->apiResponse->setError($ex->getMessage())->setData();
        }
        return $this->apiResponse->setSuccess("pet Profile Fetched Successfuly")->setData($pet);

    }
    public function GetAllUsers($data) //13
    {
        try {
            $users = User::with('PetsProfile')->get();
            $user = GeneralHelper::getcurrentUser();
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->apiResponse->setError($ex->getMessage())->setData();
        }
        return $this->apiResponse->setSuccess("users Fetched Successfuly")->setData($users);

    }
    public function LikeQuestion($data)
    {
        $user = GeneralHelper::getcurrentUser();
        $data['User_id'] = $user->id;
        $newLike = $user->Like + 1;
        try {
            $Question = Question::where('id', $data['Question_id'])->update(['Like' => $newLike]);
            //message , Model,From , To , Target id ,type
            GeneralHelper::SetNotfication($user->UserName . ' ' . ' Liked Your Question', 'Like', 'Question', $user->id, $Question->User_id, $Question->id, "Like");
            //--------------------------FireBaseNotfication------------------------------------------
            $targetUser = User::where('id', $Question->User_id)->first();
            $data1 = array('title' => 'PetsFort', 'body' => $user->UserName . ' ' . ' Liked  Your Question', 'Key' => 'Notify');
            $res = FCMHelper::sendFCMMessage($data1, $targetUser->Token);
            //---------------------------------------------------------------------
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->apiResponse->setError($ex->getMessage())->setData();
        }
        return $this->apiResponse->setSuccess("Question Posted Successfuly")->setData($Question);
    }
   
   
   
   
   
   
   
    public function LikeUserProfile($data)
    {
        try {
            $user = GeneralHelper::getcurrentUser();

            $User = User::where('id', $data['User_id'])->first();
            $User->Likes = $User->Likes + 1;
            $User->save();
            GeneralHelper::SetNotfication($user->UserName . ' ' . 'Liked Your Profile', 'Like', 'UserProfile', $user->id, $User->id, $User->id, "Like");
            //--------------------------FireBaseNotfication------------------------------------------
            $TargetUser = User::where('id', $User->id)->first();
            $data1 = array('title' => 'PetsFort', 'body' => $user->UserName . ' ' . 'Liked Your Profile', 'Key' => 'Notify');
            $res = FCMHelper::sendFCMMessage($data1, $TargetUser->Token);
            //---------------------------------------------------------------------
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->apiResponse->setError($ex->getMessage())->setData();
        }
        return $this->apiResponse->setSuccess("Profile Liked Successfuly")->setData($User);

    }
    public function LikePetProfile($data)
    {
        try {
            $pet = PetProfile::where('id', $data['Pet_id'])->first();
            $pet->Like = $pet->Like + 1;
            $pet->save();
            $user = GeneralHelper::getcurrentUser();
            GeneralHelper::SetNotfication($user->UserName . ' ' . 'Liked' . $pet->name . 'Profile', 'Like', 'PetProfile', $user->id, $pet->User_id, $pet->id, 'Like');
            //--------------------------FireBaseNotfication------------------------------------------
            $TargetUser = User::where('id', $pet->User_id)->first();
            $data1 = array('title' => 'PetsFort', 'body' => $user->UserName . ' ' . 'Liked Your ' . $pet->name, 'Key' => 'Notify');
            $res = FCMHelper::sendFCMMessage($data1, $TargetUser->Token);
            //---------------------------------------------------------------------
        } catch (\Exception $ex) {
            return $this->apiResponse->setError($ex->getMessage())->setData();
        }
        return $this->apiResponse->setSuccess("pet Profile Liked Successfuly")->setData($pet);

    }
    public function LovePetProfile($data)
    {
        try {
            $pet = PetProfile::where('id', $data['Pet_id'])->first();
            $pet->Love = $pet->Love + 1;
            $pet->save();
            $user = GeneralHelper::getcurrentUser();
            GeneralHelper::SetNotfication($user->UserName . ' ' . 'Loved' . $pet->name . 'Profile', 'Like', 'PetProfile', $user->id, $pet->User_id, $pet->id, 'Love');
            //--------------------------FireBaseNotfication------------------------------------------
            $TargetUser = User::where('id', $pet->User_id)->first();
            $data1 = array('title' => 'PetsFort', 'body' => $user->UserName . ' ' . 'Loved Your' . $pet->name, 'Key' => 'Notify');
            $res = FCMHelper::sendFCMMessage($data1, $TargetUser->Token);
            //---------------------------------------------------------------------
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->apiResponse->setError($ex->getMessage())->setData();
        }
        return $this->apiResponse->setSuccess("pet Profile Loved Successfuly")->setData($pet);

    }
    public function SearchPet($data)
    {
        try {
            $UserIds = User::where('Location', 'like', '%' . $data['Location'] . '%')->pluck('id')->toArray();
            $pets = PetProfile::wherein('User_id', $UserIds)->where('Type', $data['Type'])->where('Gender', $data['Gender'])->where('Bread', 'like', '%' . $data['Bread'] . '%')->get();

        } catch (\Exception $ex) {
            return $this->apiResponse->setError($ex->getMessage())->setData();
        }
        return $this->apiResponse->setSuccess("Matched Pets Founded Successfuly")->setData($pets);

    }
    public function SendMessage($data)
    {
        try {
            $user = GeneralHelper::getcurrentUser();
            $message = new Message();
            $message->Message = $data['Message'];
            $message->Message_From = $user->id;
            $message->Message_To = $data['Message_to'];
            $message->save();
            GeneralHelper::SetNotfication($user->UserName . ' ' . 'Send New Message', $message->Message, 'Message', $user->id, $message->Message_To, $message->id, 'NewMessage');
            //--------------------------FireBaseNotfication------------------------------------------
            $TargetUser = User::where('id', $message->Message_To)->first();
            $data1 = array('title' => 'PetsFort', 'body' => $user->UserName . ' ' . 'Send New Message', 'Key' => 'Message');
            $res = FCMHelper::sendFCMMessage($data1, $TargetUser->Token);
            //---------------------------------------------------------------------
        } catch (\Exception $ex) {
            return $this->apiResponse->setError($ex->getMessage())->setData();
        }
        return $this->apiResponse->setSuccess("Message sent Successfuly")->setData($message);

    }
    public function GetAllMessagesByUserId($data)
    {
        try {
            $user = GeneralHelper::getcurrentUser();

            $messagesSource = Message::where('Message_From', $user->id)->where('Message_To', $data['To'])->orderBy('id', 'ASC')->with('UserRecived')->get()->toArray();
            $messagesDestination = Message::where('Message_From', $data['To'])->where('Message_To', $user->id)->orderBy('id', 'ASC')->with('UserSent')->get()->toArray();
            $Conversion = array();
            $Conversion = array_merge($messagesSource, $messagesDestination);
            usort($Conversion, function ($item1, $item2) {
                return $item1['id'] <=> $item2['id'];
            });

        } catch (\Exception $ex) {
            return $this->apiResponse->setError($ex->getMessage())->setData();
        }
        return $this->apiResponse->setSuccess(" Messages Fetched Successfuly")->setData($Conversion);

    }
    public function GetMyNotfication($data)
    {
        try {
            $user = GeneralHelper::getcurrentUser();
            $messages = Notfication::where('User_id', $user->id)->get();

        } catch (\Exception $ex) {
            return $this->apiResponse->setError($ex->getMessage())->setData();
        }
        return $this->apiResponse->setSuccess(" Messages Fetched Successfuly")->setData($messages);

    }

   
    public function ShowMessageById($data)
    {
        try {
            Message::where('id', $data['id'])->update(['Seen' => 1, 'Seen_at' => Carbon::now()]);

            $Message = Message::where('id', $data['id'])->with('UserSent')->with('UserRecived')->first();

        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->apiResponse->setError($ex->getMessage())->setData();
        }
        return $this->apiResponse->setSuccess(" Message Viewed And Marked As Read")->setData($Message);
    }
    public function SendFriendRequest($data)
    {
        try {
            $user = GeneralHelper::getcurrentUser();
            $request = FriendRequest::create(['ReqFrom' => $user->id, 'ReqTo' => $data['Friend_id'], 'Status' => 'Pending', 'Type' => 'FriendRequest']);
            GeneralHelper::SetNotfication($user->UserName . ' ' . 'Send Friend Request', 'Friend Request', 'FriendRequest', $user->id, $data['Friend_id'], $request->id, 'FriendRequest');
            //--------------------------FireBaseNotfication------------------------------------------
            $TargetUser = User::where('id', $data['Friend_id'])->first();
            $data1 = array('title' => 'PetsFort', 'body' => $user->UserName . ' ' . 'Send Friend Request', 'Key' => 'Notify');
            $res = FCMHelper::sendFCMMessage($data1, $TargetUser->Token);
            //---------------------------------------------------------------------
        } catch (\Exception $ex) {
            return $this->apiResponse->setError($ex->getMessage())->setData();
        }
        return $this->apiResponse->setSuccess(" Friend Request Sent Successfuly")->setData($request);
    }
    public function SendMatcheRequest($data)
    {
        try {
            $user = GeneralHelper::getcurrentUser();
            $PetOwner = PetProfile::where('id', $data['ToPet_id'])->first()->User_id;
            $request = MatchRequest::create(['ReqFrom' => $user->id, 'ReqTo' => $PetOwner, 'ReqFrom_Pet_id' => $data['MyPet_id'], 'ReqTo_Pet_id' => $data['ToPet_id'], 'Status' => 'Pending']);
            GeneralHelper::SetNotfication($user->UserName . ' ' . 'Send Match Request', 'Match Request', 'MatchRequest', $user->id, $PetOwner, $request->id, 'Match Request');
            //--------------------------FireBaseNotfication------------------------------------------
            $TargetUser = User::where('id', $PetOwner)->first();
            $data1 = array('title' => 'PetsFort', 'body' => $user->UserName . ' ' . 'Send Match Request', 'Key' => 'Notify');
            $res = FCMHelper::sendFCMMessage($data1, $TargetUser->Token);
            //---------------------------------------------------------------------
        } catch (\Exception $ex) {
            return $this->apiResponse->setError($ex->getMessage())->setData();
        }
        return $this->apiResponse->setSuccess(" Pet Request Sent Successfuly")->setData($request);
    }
    
    public function GetMyComingFriendRequests($data)
    {
        try {
            $user = GeneralHelper::getcurrentUser();
            $requests = FriendRequest::where('ReqTo', $user->id)->where('Status', 'Pending')->where('Type', 'FriendRequest')->with(['Sender' => function ($query) {
                $query->with('PetsProfile');
            }])->get();

        } catch (\Exception $ex) {
            return $this->apiResponse->setError($ex->getMessage())->setData();
        }
        return $this->apiResponse->setSuccess(" Friend Requests fetched Successfuly")->setData($requests);
    }
    public function GetMyComingMatchesRequest($data)
    {

        try {
            $user = GeneralHelper::getcurrentUser();
            $requests = MatchRequest::where('ReqTo', $user->id)->where('Status', 'Pending')->with(['Sender', 'PetSender', 'Reciver', 'PetReciver'])->get();

        } catch (\Exception $ex) {
            return $this->apiResponse->setError($ex->getMessage())->setData();
        }
        return $this->apiResponse->setSuccess(" Matches Requests fetched Successfuly")->setData($requests);

    }
    public function AcceptFriendRequest($data)
    {

        try {

            $user = GeneralHelper::getcurrentUser();
            $request = FriendRequest::find($data['Request_id'])->update(['Status' => 'Accepted']);
            $Req = FriendRequest::find($data['Request_id']);
            Friend::create(['User_id' => $user->id, 'Friend_id' => $Req->ReqFrom]);
            GeneralHelper::SetNotfication($user->UserName . ' ' . 'Accepted Your  Request', 'Accept Request', 'User', $user->id, $Req->ReqFrom, $user->id, 'Accept Friend Request');
            //--------------------------FireBaseNotfication------------------------------------------
            $TargetUser = User::where('id', $Req->ReqFrom)->first();
            $data1 = array('title' => 'PetsFort', 'body' => $user->UserName . ' ' . 'Accepted Your  Request', 'Key' => 'Notify');
            $res = FCMHelper::sendFCMMessage($data1, $TargetUser->Token);
            //---------------------------------------------------------------------
        } catch (\Exception $ex) {
            return $this->apiResponse->setError($ex->getMessage())->setData();
        }
        return $this->apiResponse->setSuccess("  Request Accepted  Successfuly")->setData($request);

    }
    public function AcceptMatchRequest($data)
    {
        try {
            $user = GeneralHelper::getcurrentUser();
            $request = MatchRequest::find($data['Request_id'])->update(['Status' => 'Accepted']);
            $Req = MatchRequest::find($data['Request_id']);

            $PetProfile = PetProfile::where('User_id', $Req->ReqTo)->first(); //get Pet Owner Id
            Match::create(['User_id' => $user->id, 'Owner_id' => $PetProfile->User_id, 'Pet_id' => $Req->ReqTo]);
            GeneralHelper::SetNotfication($user->UserName . ' ' . 'Accepted Your Match Request', 'Accept Match Request', 'User', $user->id, $Req->ReqFrom, $user->id, 'Accept Match Request');
            //--------------------------FireBaseNotfication------------------------------------------
            $TargetUser = User::where('id', $Req->ReqFrom)->first();
            $data1 = array('title' => 'PetsFort', 'body' => $user->UserName . ' ' . 'Accepted Your Match Request', 'Key' => 'Notify');
            $res = FCMHelper::sendFCMMessage($data1, $TargetUser->Token);
            //---------------------------------------------------------------------
        } catch (\Exception $ex) {
            return $this->apiResponse->setError($ex->getMessage())->setData();
        }
        return $this->apiResponse->setSuccess(" Match Request Accepted  Successfuly")->setData($request);

    }
    public function RejectFriendRequest($data)
    {
        try {
            $user = GeneralHelper::getcurrentUser();
            $request = FriendRequest::find($data['Request_id'])->update(['Status' => 'Rejected']);

            GeneralHelper::SetNotfication($user->UserName . ' ' . 'Rejected Your  Request', 'Reject Request', 'User', $user->id, $request->ReqFrom, $user->id, 'Reject Friend Request');
            //--------------------------FireBaseNotfication------------------------------------------
            $TargetUser = User::where('id', $request->ReqFrom)->first();
            $data1 = array('title' => 'PetsFort', 'body' => $user->UserName . ' ' . 'Rejected Your  Request', 'Key' => 'Notify');
            $res = FCMHelper::sendFCMMessage($data1, $TargetUser->Token);
            //---------------------------------------------------------------------
        } catch (\Exception $ex) {
            return $this->apiResponse->setError($ex->getMessage())->setData();
        }
        return $this->apiResponse->setSuccess("Request Rejected  Successfuly")->setData($request);

    }
    public function RejectMatchRequest($data)
    {

        try {
            $user = GeneralHelper::getcurrentUser();

            $re = MatchRequest::where('id', $data['Request_id'])->first();

            if (empty($re)) {
                return $this->apiResponse->setSuccess(" Not FOUND")->setData();

            }
            $request = MatchRequest::find($data['Request_id'])->update(['Status' => 'Rejected']);
            $Req = MatchRequest::find($data['Request_id']);
            $PetProfile = PetProfile::where('User_id', $Req->ReqTo)->first(); //get Pet Owner Id

            GeneralHelper::SetNotfication($user->UserName . ' ' . 'Rjected Your Match Request', 'Reject Match Request', 'User', $user->id, $Req->ReqFrom, $user->id, 'Reject Match Request');
            //--------------------------FireBaseNotfication------------------------------------------
            $TargetUser = User::where('id', $Req->ReqFrom)->first();
            $data1 = array('title' => 'PetsFort', 'body' => $user->UserName . ' ' . 'Rjected Your Match Request', 'Key' => 'Notify');
            $res = FCMHelper::sendFCMMessage($data1, $TargetUser->Token);
            //---------------------------------------------------------------------
        } catch (\Exception $ex) {
            return $this->apiResponse->setError($ex->getMessage())->setData();
        }
        return $this->apiResponse->setSuccess(" Match Request Rejected  Successfuly")->setData($request);

    }
    public function GetUserFriends($data)
    {
        try {
            $user = GeneralHelper::getcurrentUser();
            $friends = Friend::where('User_id', $user->id)->with('MyFriend')->get();
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->apiResponse->setError($ex->getMessage())->setData();
        }
        return $this->apiResponse->setSuccess("Friends fetched Successfuly")->setData($friends);

    }
    public function GetUserMatchesPets($data)
    {
        try {
            $user = GeneralHelper::getcurrentUser();
            $friends = Match::where('User_id', $user->id)->with('Pet')->with('PetOwner')->get();
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->apiResponse->setError($ex->getMessage())->setData();
        }
        return $this->apiResponse->setSuccess("Friends fetched Successfuly")->setData($friends);

    }
    public function gethome($data){
        try {
            $user = GeneralHelper::getcurrentUser();
            $pets = PetProfile::paginate(2);
            $users = User::paginate(1);
            $questions = Question::paginate(1);
            $recommendedPets = PetProfile::inRandomOrder()->take(2)->get();
            $recommendedUsers = User::inRandomOrder()->take(2)->get();
            // $questions = Question::inRandomOrder()->take(1)->get();
            // $users = User::where('User_id', $user->id)->with('Pet')->with('PetOwner')->get();
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->apiResponse->setError($ex->getMessage())->setData();
        }
        
        return $this->apiResponse->setSuccess("Get Home Successfuly")->setData([
            "pets" => $pets,
            "users" => $users, 
            "questions" => $questions,
            "recommended" => [
                "pets" => $recommendedPets,
                "users" => $recommendedUsers
            ]             
        ]);
    }

}
