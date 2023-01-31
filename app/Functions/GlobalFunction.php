<?php
namespace App\Functions;

class GlobalFunction
{

                    // SUCCESS
    public static function save($message,$result=[]){
        return response()->json([ 
            'message'=>$message,
            'result'=>$result
          ],201);
    }

    public static function update_response($message,$result=[]){
        return response()->json([ 
            'message'=>$message,
            'result'=>$result
          ],200);
    }

    public static function login_user($message,$result=[]){
        return response()->json([ 
            'message'=>$message,
            'result'=>$result
        ],200);
    }

    public static function delete_response($message,$result=[]){
        return response()->json([ 
            'message'=>$message,
            'result'=>$result
        ],200);
        
    }

    public static function logout_response($message,$result=[]){
        return response()->json([ 
            'message'=>$message,
            'result'=>$result
        ],200);
    }

    public static function display_response($message,$result=[]){
        return response()->json([ 
            'message'=>$message,
            'result'=>$result
        ],200);
        
    }
                // ERRORS
    public static function not_found($message,$result=[]){
        return response()->json([ 
            'message'=>$message,
            'result'=>$result
        ],404);
    }

    public static function invalid($message,$result=[]){
        return response()->json([ 
            'message'=>$message,
            'result'=>$result
        ],422);
    }
    public static function invalid_category($message,$result=[]){
        return response()->json([ 
            'message'=>$message,
            'result'=>$result
        ],422);
    }
    public static function single_validation($message,$result=[]){
        return response()->json([ 
            'message'=>$message,
            'result'=>$result
        ],422);
    }
    public static function invalid_archived($message,$result=[]){
        return response()->json([ 
            'message'=>$message,
            'result'=>$result
        ],404);
    }
}