<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Response\Status;
use App\Functions\GlobalFunction;
class CategoryController extends Controller
{
   
    public function index()
    {
      $category = Category::get();
      return $category;
    }
    public function store(Request $request)
    {
      $name=$request->name;
      $is_exists= Category::where('name',$name)->exists();
   
      if ($is_exists){
       return GlobalFunction::exists(Status::FAILED,Status::EXISTS_STATUS);
      }
      $result = Category:: create([
       "name"=>$name
      ]);
      return GlobalFunction::save(Status::SUCCESS,Status::CREATE_STATUS,$result);
    }
    public function show($id)
    {
      $result = Category::where('id',$id)->get();
      return $result;
    }
    public function update(Request $request, $id)
    {
      $result = Category::find($id);
      $result->update([
        'name'=>$request['name']
      ]);
      return GlobalFunction::update_response(Status::SUCCESS,Status::UPDATE_STATUS,$result);
    }
    public function destroy($id)
    {
      $result = Category::withTrashed()->find($id);
      $is_active = Category::withTrashed()
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
