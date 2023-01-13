<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PositionRequest;
use Illuminate\Http\Request;
use App\Models\Position;
use App\Response\Status;
use App\Functions\GlobalFunction;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $pos = Position::get();
        return $pos;
    }
    public function store(PositionRequest $request)
    {
        $position=$request->position;
        $description=$request->description;
        $user_id=Auth()->user()->id;
        $is_exists= Position::where('position',$position)->exists();
   
       if ($is_exists){
        return GlobalFunction::exists(Status::FAILED,Status::EXISTS_STATUS);
       }
       $result = Position:: create([
        "position"=>$position,
        "description"=>$description
       ]);
       return GlobalFunction::save(Status::SUCCESS,Status::CREATE_STATUS,$result);
    }
    public function show($id)
    {
        $pos = Position::where('id',$id)->get();
        return $pos;
    }
    public function update(Request $request, $id)
    {
        $result = Position::find($id);
        $result->update([
          'position'=>$request['position'],
          'description'=>$request['description']
        ]);
        return GlobalFunction::update_response(Status::SUCCESS,Status::UPDATE_STATUS,$result);
    }
    public function destroy(Request $request, $id)
    {
        $result = Position::withTrashed()->find($id);
        $is_active = Position::withTrashed()
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
}