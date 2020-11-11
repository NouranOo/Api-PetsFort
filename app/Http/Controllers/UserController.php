<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Interfaces\UserInterface;
use App\Models\User;
use Illuminate\Http\Request;
use validator;
use Illuminate\Validation\Rule;
use App\Helpers\GeneralHelper;

class UserController extends Controller
{

    public $user;
    public $apiResponse;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserInterface $user, ApiResponse $apiResponse)
    {
        $this->user = $user;
        $this->apiResponse = $apiResponse;
    }

    public function SignUp(Request $request) //1

    {

        $rules = [
            'Email' => 'required|unique:Users',
            'Phone' => 'required|numeric|unique:Users',
            'Password' => 'required|between:6,12',
            'UserName' => 'required|max:15|min:3|unique:Users',
            'Token' => 'required',
            'CountryCode' => 'required',
            'Fname' => 'required',
            'Lname' => 'required',
            'BirthDay' => 'required',
            'Location' => 'required',
            'ApiKey' => 'required',
            // 'Hostpet'=>'in:0,1',

        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->apiResponse->setError($validation->errors()->first())->send(); //new way to send responce

        }
        $api_key = env('APP_KEY');
        if ($api_key != $request->ApiKey) {
            return $this->apiResponse->setError("Unauthorized!")->send();
        }

        $data = $request->except('Photo');

        if ($request->hasFile('Photo')) {

            $file = $request->file("Photo");
            $filename = str_random(6) . '_' . time() . '_' . $file->getClientOriginalName();
            $path = 'ProjectFiles/UserPhotos';
            $file->move($path, $filename);
            $data['Photo'] = $path . '/' . $filename;
        }

        $result = $this->user->SignUp($data);
        return $result->send();
    }
    public function SignIn(Request $request) //2
    {
        $rules = [
            'Email' => 'required',
            'Password' => 'required|between:6,12',
            'ApiKey' => 'required',

        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->apiResponse->setError($validation->errors()->first())->send(); //new way to send responce

        }
        $api_key = env('APP_KEY');
        if ($api_key != $request->ApiKey) {
            return $this->apiResponse->setError("Unauthorized!")->send();
        }
        $result = $this->user->SignIn($request->all());
        return $result->send();

    }
    public function MakePetProfile(Request $request) //3
    {
        $rules = [
            'Type' => 'required|in:Cat,Dog',
            'Photo' => 'required|between:1,1000',
            'BirthDay' => 'required',
            'Name' => 'required',
            'Gender' => 'required|in:Male,Female',
            'Bread_id' => 'required',
            'Location' => 'required',

        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->apiResponse->setError($validation->errors()->first())->send(); //new way to send responce
        }
        $data = $request->except('Photo');

        if ($request->hasFile('Photo')) {

            $file = $request->file("Photo");
            $filename = str_random(6) . '_' . time() . '_' . $file->getClientOriginalName();
            $path = 'ProjectFiles/PetProfiles';
            $file->move($path, $filename);
            $data['Photo'] = $path . '/' . $filename;
        }

        $result = $this->user->MakePetProfile($data);
        return $result->send();

    }
    public function AskQuestion(Request $request) //4
    {
        $rules = [
            'Type' => 'required|in:Cat,Dog',
            'Question' => 'required',
            'Bread' => 'required',
        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->apiResponse->setError($validation->errors()->first())->send(); //new way to send responce
        }
        $data = $request->except('Photo');

        if ($request->hasFile('Photo')) {

            $file = $request->file("Photo");
            $filename = str_random(6) . '_' . time() . '_' . $file->getClientOriginalName();
            $path = 'ProjectFiles/QuestionsPhoto';
            $file->move($path, $filename);
            $data['Photo'] = $path . '/' . $filename;
        }
        $result = $this->user->AskQuestion($data);
        return $result->send();
    }
    public function PostComment(Request $request) //5
    {
        $rules = [
            'Question_id' => 'required',
            'Comment' => 'required',

        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->apiResponse->setError($validation->errors()->first())->send(); //new way to send responce
        }

        $result = $this->user->PostComment($request->all());
        return $result->send();
    }
    public function PostReplay(Request $request) //6
    {
        $rules = [
            'Comment_id' => 'required',
            'Replay' => 'required',

        ];
        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->apiResponse->setError($validation->errors()->first())->send(); //new way to send responce
        }

        $result = $this->user->PostReplay($request->all());
        return $result->send();
    }
    public function GetUserPets(Request $request) //7
    {

        $result = $this->user->GetUserPets($request->all());
        return $result->send();
    }
    public function GetQuestions(Request $request) //8
    {

        $result = $this->user->GetQuestions($request->all());
        return $result->send();

    }
    public function GetQuestionById(Request $request) //9
    {

        $rules = [

            'id' => 'required',

        ];
        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->apiResponse->setError($validation->errors()->first())->send();
        }

        $result = $this->user->GetQuestionById($request->all());
        return $result->send();
    }
    public function GetMyQuestions(Request $request) //10
    {

        $result = $this->user->GetMyQuestions($request->all());
        return $result->send();

    }
    public function ShowUserProfileById(Request $request) //11
    {
        $rules = [
            'User_id' => 'required',

        ];
        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->apiResponse->setError($validation->errors()->first())->send(); //new way to send responce
        }
        $result = $this->user->ShowUserProfileById($request->all());
        return $result->send();
    }
    public function ShowPetProfileById(Request $request) //12
    {
        $rules = [
            'Pet_id' => 'required',

        ];
        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->apiResponse->setError($validation->errors()->first())->send(); //new way to send responce
        }
        $result = $this->user->ShowPetProfileById($request->all());
        return $result->send();

    }
   
    public function GetAllUsers(Request $request)
    {
        $result = $this->user->GetAllUsers($request->all());
        return $result->send();

    }
    public function LikeUserProfile(Request $request)
    {
        $rules = [
            'User_id' => 'required',

        ];
        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->apiResponse->setError($validation->errors()->first())->send(); //new way to send responce
        }
        $result = $this->user->LikeUserProfile($request->all());
        return $result->send();

    }
    public function LikePetProfile(Request $request)
    {
        $rules = [
            'Pet_id' => 'required',

        ];
        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->apiResponse->setError($validation->errors()->first())->send(); //new way to send responce
        }
        $result = $this->user->LikePetProfile($request->all());
        return $result->send();
    }
    public function LovePetProfile(Request $request)
    {
        $rules = [
            'Pet_id' => 'required',

        ];
        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->apiResponse->setError($validation->errors()->first())->send(); //new way to send responce
        }
        $result = $this->user->LovePetProfile($request->all());
        return $result->send();
    }
    public function SearchPet(Request $request)
    {
        $rules = [
            'Type' => 'required|in:Cat,Dog',
            'Bread' => 'required',
            'Gender' => 'required|in:Male,Female',
            'Location' => 'required',

        ];
        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->apiResponse->setError($validation->errors()->first())->send(); //new way to send responce
        }
        $result = $this->user->SearchPet($request->all());
        return $result->send();
    }
    public function SendMessage(Request $request)
    {

        $rules = [
            'Message' => 'required',
            'Message_to' => 'required',

        ];
        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->apiResponse->setError($validation->errors()->first())->send(); //new way to send responce
        }
        $result = $this->user->SendMessage($request->all());
        return $result->send();
    }
    //user ID => who i sent to target
    public function GetAllMessagesByUserId(Request $request)
    {
        $rules = [

            'To' => 'required',

        ];
        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->apiResponse->setError($validation->errors()->first())->send();
        }
        $result = $this->user->GetAllMessagesByUserId($request->all());
        return $result->send();
    }
    public function GetMyNotfication(Request $request)
    {

        $result = $this->user->GetMyNotfication($request->all());
        return $result->send();
    }
   
    public function ShowMessageById(Request $request)
    {
    
        $rules = [

            'id' => 'required',

        ];
        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->apiResponse->setError($validation->errors()->first())->send();
        }

        $result = $this->user->ShowMessageById($request->all());
        return $result->send();
    }
    public function SendFriendRequest(Request $request)
    {
        $rules = [

            'Friend_id' => 'required',

        ];
        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->apiResponse->setError($validation->errors()->first())->send();
        }

        $result = $this->user->SendFriendRequest($request->all());
        return $result->send();

    }
    public function SendMatcheRequest(Request $request)
    {
        $rules = [
            'MyPet_id' => 'required',
            'ToPet_id' => 'required',
        ];
        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->apiResponse->setError($validation->errors()->first())->send();
        }

        $result = $this->user->SendMatcheRequest($request->all());
        return $result->send();

    }
    public function GetMyComingFriendRequests(Request $request)
    {
        

        $result = $this->user->GetMyComingFriendRequests($request->all());
        return $result->send();

    }
    public function GetMyComingMatchesRequest(Request $request)
    {
        

        $result = $this->user->GetMyComingMatchesRequest($request->all());
        return $result->send();

    }
    
    public function GetUserFriends(Request $request)
    {
        $rules = [
         
        ];
        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->apiResponse->setError($validation->errors()->first())->send();
        }

        $result = $this->user->GetUserFriends($request->all());
        return $result->send();

    }
    public function GetUserMatchesPets(Request $request)
    {
         

        $result = $this->user->GetUserMatchesPets($request->all());
        return $result->send();

    }
    public function AcceptMatchRequest(Request $request)
    {
        $rules = [
            'Request_id' => 'required',

        ];
        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->apiResponse->setError($validation->errors()->first())->send();
        }

        $result = $this->user->AcceptMatchRequest($request->all());
        return $result->send();

    }
    public function AcceptFriendRequest(Request $request)
    {
        $rules = [
            'Request_id' => 'required',

        ];
        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->apiResponse->setError($validation->errors()->first())->send();
        }

        $result = $this->user->AcceptFriendRequest($request->all());
        return $result->send();

    }
    
    public function RejectMatchRequest(Request $request)
    {
        
        $rules = [
            'Request_id' => 'required',
         
        ];
        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->apiResponse->setError($validation->errors()->first())->send();
        }

        $result = $this->user->RejectMatchRequest($request->all());
        return $result->send();

    }
    public function RejectFriendRequest(Request $request)
    {
        $rules = [
            'Request_id' => 'required',
         
        ];
        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->apiResponse->setError($validation->errors()->first())->send();
        }

        $result = $this->user->RejectFriendRequest($request->all());
        return $result->send();

    }
    public function gethome(Request $request){
        $rules = [
            
         
        ];
        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->apiResponse->setError($validation->errors()->first())->send();
        }

        $result = $this->user->gethome($request->all());
        return $result->send();

        // $g = $this->user->gethome($request->all());

        // return $this->user->gethome($request->all())->send();
    }

}
