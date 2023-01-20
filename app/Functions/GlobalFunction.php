<?php
namespace App\Functions;

class GlobalFunction
{

    public static function exists($message,$result=[]){
        return response()->json([
            'message'=>$message,
            'errors'=>$result
          ]);
    }
    public static function save($message,$result=[]){
        return response()->json([ 
            'message'=>$message,
            'result'=>$result
          ]);
    }
    public static function update_response($message,$result=[]){
        return response()->json([ 
            'message'=>$message,
            'result'=>$result
          ]);
    }
}