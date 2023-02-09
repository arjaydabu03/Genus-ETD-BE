<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Response\Status;
use App\Functions\GlobalFunction;

use App\Models\User;
use App\Models\TagAccount;
use App\Models\Location;

use App\Http\Resources\UserResource;
use App\Http\Resources\LoginResource;

use App\Http\Requests\User\Validation\UsernameRequest;
use App\Http\Requests\User\Validation\CodeRequest;
use App\Http\Requests\User\Validation\MobileRequest;
use App\Http\Requests\User\UserRequest;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\ChangeRequest;
use App\Http\Requests\User\DisplayRequest;
use App\Http\Requests\User\Validation\NameRequest;


class UserController extends Controller
{
    public function index(DisplayRequest $request){

         $search=$request->search;
         $status=$request->status;
         $rows=$request->rows;
        
         $users = User::with('scope')
         ->when($status === 'inactive',function($query){
            $query->onlyTrashed();
         })
         ->when($search,function($query) use($search){
            $query->where('account_code','like','%'.$search.'%')
            ->orWhere('account_name','like','%'.$search.'%')
            ->orWhere('company_code','like','%'.$search.'%')
            ->orWhere('company','like','%'.$search.'%')
            ->orWhere('department_code','like','%'.$search.'%')
            ->orWhere('department','like','%'.$search.'%')
            ->orWhere('location_code','like','%'.$search.'%')
            ->orWhere('location','like','%'.$search.'%')
            ->orWhere('mobile_no','like','%'.$search.'%')
            ->orWhere('username','like','%'.$search.'%')
            ->orWhere('type','like','%'.$search.'%');
        })
        ->orderByDesc('created_at')
        ->paginate($rows);

        $is_empty = $users->isEmpty();
        if($is_empty){
           return GlobalFunction::not_found(Status::NOT_FOUND);
        }

        UserResource::collection($users);
        return GlobalFunction::display_response(Status::USER_DISPLAY,$users);
    
    }

    public function show($id){

        $not_found = User::where('id',$id)->get();
        //  return $not_found;
        if($not_found->isEmpty()){
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }
        $users = User::where('id',$id)->with('scope')->get();
        $user_collection = UserResource::collection($users)->first();
            
          return GlobalFunction::display_response(Status::USER_DISPLAY,$user_collection);
    }

    public function store(UserRequest $request){

         $validated=$request->validated();

         $user=new User([
            'account_code'=> $validated['code'],
            'account_name'=> $validated['name'],
            'location_code'=> $validated['location']['code'],
            'location'=> $validated['location']['name'],
            'department_code'=> $validated['department']['code'],
            'department'=> $validated['department']['name'],
            'company_code'=> $validated['company']['code'],
            'company'=> $validated['company']['name'],
            'type'=> $validated['type'],
            'mobile_no'=> $validated['mobile_no'],
            'username'=> $validated['username'],
            'password'=> Hash::make($validated['username'])
        ]);

        $user->save();

            $account_id=$user->id;
            $scope_ids = $validated['scope_id'];

         foreach($scope_ids as $location_id) {
            $tagaccount = TagAccount::create([
                "account_id" => $account_id,
                "location_id"=> $location_id
            ]);
        }

          $user= new UserResource($user);

          return GlobalFunction::save(Status::REGISTERED,$user);
    }

    public function login(LoginRequest $request){

        $user =  User::where('username', $request->username)->with('scope')->first();
  
        if (! $user || ! Hash::check($request->password, $user->password))
         {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
                'password' => ['The provided credentials are incorrect.'],
            ]);
        }
        $token = $user->createToken('PersonalAccessToken')->plainTextToken;
        $user['token']=$token;
        $user= new LoginResource($user);
     
        $cookie=cookie('authcookie',$token);

        return GlobalFunction::login_user(Status::LOGIN_USER,$user)->withCookie($cookie);
    }

    public function logout(Request $request){

        // auth()->user()->tokens()->delete();//all token of one user
        auth()->user()->currentAccessToken()->delete();//current user
        return GlobalFunction::logout_response(Status::LOGOUT_USER);
    }

    public function destroy(Request $request, $id){

        $invalid_id = User::where('id',$id)->withTrashed()->get();
    
        if($invalid_id->isEmpty()){
            return GlobalFunction::not_found(Status::NOT_FOUND);
         }

        $user_id = Auth()->user()->id;
        $not_allowed = User::where('id',$id)
        ->where('id',$user_id)->exists();
 
        if($not_allowed){
        return GlobalFunction::invalid(Status::FAILED,Status::INVALID_RESPONSE);
            }
        $user = User::withTrashed()->find($id);
        $is_active = User::withTrashed()
            ->where('id', $id)
            ->first();
        if(!$is_active){
            return $is_active;
        }else if(!$is_active->deleted_at){
            $user->delete();
            $message = Status::ARCHIVE_STATUS;
        }else {
            $user->restore();
            $message = Status::RESTORE_STATUS;
        }
            $user= new UserResource($user);
            return GlobalFunction::delete_response($message,$user);
    }

    public function update(UserRequest $request, $id){   

        $user = User::find($id);
        $scope_id=$request->scope_id;

        $not_found = User::where('id',$id)->get();
        if($not_found->isEmpty()){
            return GlobalFunction::not_found(Status::NOT_FOUND);
            }

          $not_allowed=User::where('id',$id)->onlyTrashed()->exists();
            if($not_allowed){
                return GlobalFunction::invalid(Status::INVALID_ACTION);
            }

        $is_exists=TagAccount::where('account_id',$id)->get();
    
        foreach($is_exists as $location_id){
           $location=$location_id->location_id;
        if(!in_array($location,$scope_id)){

            TagAccount::where('account_id',$id)->where('location_id',$location)->delete();
        }

         }
         foreach($scope_id as $scope){
            if(!TagAccount::where('account_id',$id)->where('location_id',$scope)->exists()){
                TagAccount::create([
                    'account_id'=>$id,
                    'location_id'=>$scope
                ]);
            }
         }

          $user->update([
            'account_code'=> $request['code'],
            'account_name'=> $request['name'],
            'location_code'=> $request['location']['code'],
            'location'=> $request['location']['name'],
            'department_code'=> $request['department']['code'],
            'department'=> $request['department']['name'],
            'company_code'=> $request['company']['code'],
            'company'=> $request['company']['name'],
            'scope'=> $request['scope'],
            'type'=> $request['type'],
            'mobile_no'=> $request['mobile_no'],
            'username'=> $request['username'],
          ]);

             $user_collection=UserResource::collection([$user])->first();
             return GlobalFunction::update_response(Status::USER_UPDATE,$user_collection);
    }

    public function reset_password(Request $request,$id){
        
         $user=User::find($id);
        
         $new_password=Hash::make($user->username);
        
            $user->update([
                'password'=>$new_password
            ]);

          return GlobalFunction::update_response(Status::CHANGE_PASSWORD);
    }

    public function change_password(ChangeRequest $request){

          $id=Auth::id();
          $user = User::find($id);

          if (! Hash::check($request->old_password, $user->password))
            {
                return GlobalFunction::invalid(Status::INVALID_RESPONSE);
            }
          $user->update([
            'password'=> Hash::make($request['password'])
          ]);

            auth()->user()->currentAccessToken()->delete();

            return GlobalFunction::update_response(Status::CHANGE_PASSWORD);
    }

    public function old_password(Request $request){
        
          $id=Auth::id();
          $user = User::find($id);
         //pwedi yang and &&
          if (! Hash::check($request->password, $user->password))
            {
                return GlobalFunction::invalid(Status::INVALID_RESPONSE);
            }
    }

    public function validate_username(UsernameRequest $request){
        
        return GlobalFunction::single_validation(Status::SINGLE_VALIDATION);
        
    }

    public function code_validate(CodeRequest $request){
        
        return GlobalFunction::single_validation(Status::SINGLE_VALIDATION);
        
    }
    
    public function validate_mobile(MobileRequest $request){
       
        return GlobalFunction::single_validation(Status::SINGLE_VALIDATION);
        
    }

    public function validate_name(NameRequest $request){
       
        return GlobalFunction::single_validation(Status::SINGLE_VALIDATION);
        
    }
}