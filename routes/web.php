<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
 */

$router->get('/', function () use ($router) {
    return 'Hello in PetsFort Apies';
});

/**
 * UserAuth
 */

$router->group(['prefix' => 'Api/User', 'middleware' => ['cors2', 'cors']], function () use ($router) {
    $router->post('/SignUp', 'UserController@SignUp'); //1
    $router->post('/SignIn', 'UserController@SignIn'); //2
});
$router->group(['prefix' => 'Api/User', 'middleware' => ['cors2', 'cors', 'UserAuth']], function () use ($router) {
    $router->post('MakePetProfile', 'UserController@MakePetProfile');//3
    $router->post('AskQuestion', 'UserController@AskQuestion');//4
    $router->post('PostComment', 'UserController@PostComment');//5
    $router->post('PostReplay', 'UserController@PostReplay');//6
    $router->post('GetUserPets', 'UserController@GetUserPets');//7
    $router->post('GetQuestions', 'UserController@GetQuestions');//8
    $router->post('GetQuestionById', 'UserController@GetQuestionById');//9
    $router->post('GetMyQuestions', 'UserController@GetMyQuestions');//10
    $router->post('ShowUserProfileById', 'UserController@ShowUserProfileById'); //11
    $router->post('ShowPetProfileById', 'UserController@ShowPetProfileById'); //12
    $router->post('GetAllUsers', 'UserController@GetAllUsers');
    $router->post('LikeUserProfile', 'UserController@LikeUserProfile');
    $router->post('LovePetProfile', 'UserController@LovePetProfile');
    $router->post('LikePetProfile', 'UserController@LikePetProfile');
    $router->post('SearchPet', 'UserController@SearchPet');
    $router->post('SendMessage', 'UserController@SendMessage');
    $router->post('GetMyConversion', 'UserController@GetAllMessagesByUserId');
    $router->post('GetMyNotfication', 'UserController@GetMyNotfication');
    $router->post('ShowMessageById', 'UserController@ShowMessageById');
    $router->post('SendFriendRequest', 'UserController@SendFriendRequest');
    $router->post('SendMatcheRequest', 'UserController@SendMatcheRequest');
    $router->post('GetMyComingFriendRequests', 'UserController@GetMyComingFriendRequests');
    $router->post('GetMyComingMatchesRequest', 'UserController@GetMyComingMatchesRequest');
    $router->post('AcceptMatchRequest', 'UserController@AcceptMatchRequest');
    $router->post('AcceptFriendRequest', 'UserController@AcceptFriendRequest');
    $router->post('RejectFriendRequest', 'UserController@RejectFriendRequest');
    $router->post('RejectMatchRequest', 'UserController@RejectMatchRequest');
    $router->post('GetUserFriends', 'UserController@GetUserFriends');
    $router->post('GetUserMatchesPets', 'UserController@GetUserMatchesPets');
   
    $router->post('GetHome', 'UserController@gethome');
});
