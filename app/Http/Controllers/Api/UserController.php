<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Response\Status;
use App\Functions\GlobalFunction;
use App\Models\User;
use App\Http\Requests\UserRequest;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function store(UserRequest $request){
    
        $code=$request->code;
        $name=$request->name;

        $is_exists= User::where('account_code',$code)
        ->where('account_name',$name)->exists(); 
        if ($is_exists){
        return GlobalFunction::exists(Status::FAILED,Status::EXISTS_STATUS);
        }

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
            'scope_id'=> $validated['scope_id'],
            'type'=> $validated['type'],
            'mobile_no'=> $validated['mobile_no'],
            'username'=> $validated['username'],
            'password'=> Hash::make($validated['username'])
        ]);
          $user->save();
        return GlobalFunction::save('Successfully Registered!',[$user]);
    }
    public function login(Request $request){
        
        $request->validate([
            'username'=> 'required',
            'password'=> 'required|string'
        ]);
        $user2 =  User::where('username', $request->username)->first();

        if (! $user2 || ! Hash::check($request->password, $user2->password))
         {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }
        $tokenResult=$user2->createToken('PersonalAccessToken')->plainTextToken;
        $token = $tokenResult;
        $result = [
            'token'=>$token,
            'user'=>$user2
        ];
        $cookie=cookie('authcookie',$token);
        return response($result,200)->withCookie($cookie);
    }
    public function logout(Request $request){

        auth()->user()->tokens()->delete();
        return response()->json(['message'=>'Loged out successfully!']);
    }
    public function destroy(Request $request, $id)
    {
        $user_id = Auth()->user()->id;
        $not_allowed = User::where('id',$id)
        ->where('id',$user_id)->exists();
 
        if($not_allowed){
            return response()->json(['Invalid Action']);
                }

        $result = User::withTrashed()->find($id);
        $is_active = User::withTrashed()
                ->where('id', $id)
                ->first();
        if(!$is_active){
            return $is_active;
        }else if(!$is_active->deleted_at){
            $result->delete();
            $message = Status::ARCHIVE_STATUS;
        }else {
            $result->restore();
            $message = Status::RESTORE_STATUS;
        }
            return response()->json([$message,$result]);
      }
    public function update(Request $request, $id)
      {   
          $result = User::find($id);
          $result->update([
            'account_code'=> $request['account_code'],
            'account_name'=> $request['account_name'],
            'location'=> $request['location'],
            'department'=> $request['department'],
            'company'=> $request['company'],
            'scope'=> $request['scope'],
            'type'=> $request['type'],
            'mobile_no'=> $request['mobile_no'],
            'username'=> $request['username'],
            'password'=> Hash::make($request['password'])
          ]);
          return GlobalFunction::update_response(Status::SUCCESS,Status::UPDATE_STATUS,$result);
      }

    public function index(Request $request)
    {
        $user = User::get();
        return $user;
    }
}