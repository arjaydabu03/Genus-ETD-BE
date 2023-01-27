<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;

use App\Response\Status;
use App\Functions\GlobalFunction;

use App\Models\User;
use App\Models\TagAccount;
use App\Models\Location;

use App\Http\Resources\UserResource;

use App\Http\Requests\User\UserRequest;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\ChangeRequest;
use App\Http\Requests\User\DisplayRequest;

class UserController extends Controller
{
    public function index(DisplayRequest $request){

         $search=$request->search;
         $status=$request->status;
         
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
            ->orWhere('location','like','%'.$search.'%');
        })
         ->paginate($request->rows);

        $is_empty = $users->isEmpty();
        if($is_empty){
           return GlobalFunction::not_found(Status::NOT_FOUND);
        }
         $user_collection = UserResource::collection($users);
         return GlobalFunction::display_response(Status::USER_DISPLAY,$user_collection);
    }

    public function show($id){

        $users = User::where('id',$id)->with('scope')->get()->first();
        $user_collection = UserResource::collection([$users]);
            
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
        $user= $user->with('location')->first();

          return GlobalFunction::save(Status::REGISTERED,$user);
    }

    public function login(LoginRequest $request){

        $user =  User::where('username', $request->username)->first();

        if (! $user || ! Hash::check($request->password, $user->password))
         {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
                'password' => ['The provided credentials are incorrect.'],
            ]);
        }
        $token = $user->createToken('PersonalAccessToken')->plainTextToken;
        $user = [
            'token'=>$token,
            'user'=>$user
        ];
        $cookie=cookie('authcookie',$token);
        return GlobalFunction::login_user(Status::LOGIN_USER,$user)->withCookie($cookie);
    }

    public function logout(Request $request){

        auth()->user()->tokens()->delete();
        return GlobalFunction::logout_response(Status::LOGOUT_USER);
    }

    public function destroy(Request $request, $id){

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
            return GlobalFunction::delete_response($message,$user);
    }

    public function update(UserRequest $request, $id){   

          $user = User::find($id);
          $user->update([
            'account_code'=> $request['account_code'],
            'account_name'=> $request['account_name'],
            'location'=> $request['location'],
            'department'=> $request['department'],
            'company'=> $request['company'],
            'scope'=> $request['scope'],
            'type'=> $request['type'],
            'mobile_no'=> $request['mobile_no'],
            'username'=> $request['username'],
          ]);
          
             return GlobalFunction::update_response(Status::USER_UPDATE,$user);
    }

    public function reset_password(Request $request){
        
         $id=Auth::id();
         $user=User::find($id);
         $new_password=Hash::make($user->username);
        
            $user->update([
                'password'=>$new_password
            ]);
            auth()->user()->tokens()->delete();

          return GlobalFunction::update_response(Status::CHANGE_PASSWORD);
    }

    public function change_password(ChangeRequest $request, $id){

          $id=Auth::id();
          $user = User::find($id);
        
          $user->update([
            'password'=> Hash::make($request['password'])
          ]);
            auth()->user()->tokens()->delete();
            return GlobalFunction::update_response(Status::CHANGE_PASSWORD);
    }

    public function old_password(Request $request){
        
          $id=Auth::id();
          $user = User::find($id);
         
          if (!$user || ! Hash::check($request->password, $user->password))
            {
                return GlobalFunction::invalid(Status::INVALID_RESPONSE);
            }
    }
}