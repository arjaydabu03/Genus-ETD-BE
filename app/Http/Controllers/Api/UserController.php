<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Response\Status;
use App\Functions\GlobalFunction;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function register(Request $request){
       
        $request->validate([
            'account_code'=> 'required|string',
            'account_name'=> 'required|string',
            'position_id'=> 'required',
            'store'=> 'required|string',
            'username'=> 'required|string|unique:users',
            'password'=> 'required|string|min:6',
            'password_confirmation' => 'required_with:password|same:password|min:6'
        ]);
        $user=new User([
            'account_code'=> $request->account_code,
            'account_name'=> $request->account_name,
            'position_id'=> $request->position_id,
            'store'=> $request->store,
            'username'=> $request->username,
            'password'=> Hash::make($request->password)
        ]);
          $user->save();
        return response()->json(['message'=>'User Has been Registered!']);
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
        $user_id = Auth()->user()->id;
        $not_allowed = User::where('id',$id)
        ->where('id',$user_id)->exists();
        return $user_id;

          $result = User::find($id);
          $result->update([
            'account_code'=> $request['account_code'],
            'account_name'=> $request['account_name'],
            'position'=> $request['position'],
            'store'=> $request['store'],
            'username'=> $request['username'],
            'password'=> Hash::make($request['password'])
          ]);
          return GlobalFunction::update_response(Status::SUCCESS,Status::UPDATE_STATUS,$result);
      }

    public function index(Request $request)
    {
        $user = User::with('position')->get();
        return $user;
    }
}