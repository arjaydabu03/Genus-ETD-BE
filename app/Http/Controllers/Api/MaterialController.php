<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Material;
use App\Response\Status;
use App\Functions\GlobalFunction;
class MaterialController extends Controller
{
   
    public function index()
    {
      $material = Material::with('category')->get(['code','description','category_id']);
      return $material;
    }

    public function store(Request $request)
    {
      $code=$request->code;
      $description=$request->description;
      $category_id=$request->category_id;
      $is_exists= Material::where('code',$code)->exists();
   
      if ($is_exists){
       return GlobalFunction::exists(Status::FAILED,Status::EXISTS_STATUS);
      }
      $result = Material:: create([
       "code"=>$code,
       "description"=>$description,
       "category_id"=>$category_id
      ]);
      return GlobalFunction::save(Status::SUCCESS,Status::CREATE_STATUS,$result);
    }
    public function show($id)
    {
        $material = Material::with('category')->where('id',$id)->get(['code','description','category_id']);
        return $material;
    }
    public function update(Request $request, $id)
    {
      $result = Material::find($id);
      $result->update([
        'code'=>$request['code'],
        'description'=>$request['description'],
        'category_id'=>$request['category_id']
      ]);
      return GlobalFunction::update_response(Status::SUCCESS,Status::UPDATE_STATUS,$result);
    }

    public function destroy($id)
    {
      $result = Material::withTrashed()->find($id);

      $model = new Material;
      $is_active = Material::withTrashed()
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
