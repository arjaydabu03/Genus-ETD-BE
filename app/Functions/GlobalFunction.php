<?php
namespace App\Functions;

class GlobalFunction
{

    public static function exists($code,$message,$result=[]){
        return response()->json([
            'code'=>$code,
            'message'=>$message,
            'errors'=>$result
          ]);
    }
    public static function save($code,$message,$result=[]){
        return response()->json([ 
            'code'=>$code,
            'message'=>$message,
            'result'=>$result
          ]);
    }
    public static function update_response($code,$message,$result=[]){
        return response()->json([ 
            'code'=>$code,
            'message'=>$message,
            'result'=>$result
          ]);
    }
}