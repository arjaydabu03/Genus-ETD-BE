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
         $material = Material::with('category')
          ->when($status === 'inactive',function($query){
            $query->onlyTrashed();
            })
            ->when($search,function($query) use($search){
               $query->where('code','like','%'.$search.'%')
              ->orWhere('description','like','%'.$search.'%');
            })
           ->paginate($request->rows);

            $is_empty = $material->isEmpty();
            if($is_empty){
              return GlobalFunction::not_found(Status::NOT_FOUND);
              }
            return GlobalFunction::display_response(Status::MATERIAL_DISPLAY,$material);
    }

    public function show($id){

      $material = Material::with('category')->where('id',$id)->get(['code','description','category_id']);
      if($material->isEmpty()){
        return GlobalFunction::not_found(Status::NOT_FOUND);
      }
      return GlobalFunction::display_response(Status::MATERIAL_DISPLAY,$material->first());
    }

    public function store(MaterialRequest $request){

      $category_id=$request->category_id;
      $material = Category::where('id',$category_id)->get();
  
      if($material->isEmpty()){
        return GlobalFunction::invalid(Status::INVALID_ACTION);
      }
      
      $validated=$request->validated();

      $material = Material:: create([
        'code'=> $validated['code'],
        'description'=> $validated['description'],
        'category_id'=> $validated['category_id']
      ]);
      return GlobalFunction::save(Status::MATERIAL_SAVE,$material);
    }

    public function update(MaterialRequest $request, $id){

      $not_found = Material::where('id',$id)->get();

      if($not_found->isEmpty()){
          return GlobalFunction::not_found(Status::NOT_FOUND);
          }

          $category_id=$request->category_id;
          $invalid_category = Material::where('id',$category_id)->get();
  
          if($invalid_category->isEmpty()){
            return GlobalFunction::invalid_archived(Status::INVALID_CATEGORY);
            }
      $material = Material::find($id);

      $material->update([
        'code'=>$request['code'],
        'description'=>$request['description'],
        'category_id'=>$request['category_id']
      ]);
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