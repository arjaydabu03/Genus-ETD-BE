<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Material;
use App\Models\Category;
use App\Response\Status;
use App\Functions\GlobalFunction;

use App\Http\Requests\Material\MaterialRequest;
use App\Http\Requests\Material\DisplayRequest;
use App\Http\Requests\Material\Validation\CodeRequest;

class MaterialController extends Controller
{
    public function index(DisplayRequest $request){

        $search=$request->search;
        $status=$request->status;
        $paginate=isset($request->paginate) ? $request->paginate : 1;
         
         $material = Material::with('category')
        ->select('id','code','name','category_id')
        ->when($paginate, function($query){
          $query->select('id','code','name','category_id','updated_at');
        })
        ->when($status === 'inactive',function($query){
        $query->onlyTrashed();
        })
        ->when($search,function($query) use($search){
          $query->where('code','like','%'.$search.'%')
        ->orWhere('name','like','%'.$search.'%');
        });

        $material=$paginate?$material->paginate($request->rows):$material->get();

        $is_empty = $material->isEmpty();
        if($is_empty){
          return GlobalFunction::not_found(Status::NOT_FOUND);
          }
        return GlobalFunction::display_response(Status::MATERIAL_DISPLAY,$material);
    }

    public function show($id){

      $material = Material::with('category')->where('id',$id)->get();
      if($material->isEmpty()){
        return GlobalFunction::not_found(Status::NOT_FOUND);
      }

      return GlobalFunction::display_response(Status::MATERIAL_DISPLAY,$material->first());
    }

    public function store(MaterialRequest $request){

      $material = Material:: create([
        'code'=> $request['code'],
        'name'=> $request['name'],
        'category_id'=> $request['category_id'],
      ]);
      $material=$material->with('category')->firstWhere('id',$material->id);
      return GlobalFunction::save(Status::MATERIAL_SAVE,$material);
    }

    public function update(MaterialRequest $request, $id){

        $not_found = Material::where('id',$id)->get();

       if($not_found->isEmpty()){
          return GlobalFunction::not_found(Status::NOT_FOUND);
          }

        $material = Material::find($id);

        $material->update([
          'code'=>$request['code'],
          'name'=>$request['name'],
          'category_id'=>$request['category_id']
        ]);

      $material=$material->with('category')->firstWhere('id',$material->id);
      return GlobalFunction::update_response(Status::MATERIAL_UPDATE,$material);
    }

    public function destroy($id){

      $material = Category::where('id',$id)->withTrashed()->get();

        if($material->isEmpty()){
         return GlobalFunction::not_found(Status::NOT_FOUND);
       }

      $material = Material::withTrashed()->find($id);
     
      $is_active = Material::withTrashed()
              ->where('id', $id)
              ->first();
      if(!$is_active){
        return $is_active;
      }else if(!$is_active->deleted_at){
          $material->delete();
          $message = Status::ARCHIVE_STATUS;
      }else {
          $material->restore();
          $message = Status::RESTORE_STATUS;
      }
      return GlobalFunction::delete_response($message,$material);
    }

    public function validate_code(CodeRequest $request){
        
       return GlobalFunction::single_validation(Status::SINGLE_VALIDATION);
    }
}