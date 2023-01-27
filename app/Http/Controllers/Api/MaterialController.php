<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Material;
use App\Response\Status;
use App\Functions\GlobalFunction;

use App\Http\Requests\Material\MaterialRequest;
use App\Http\Requests\Material\DisplayRequest;

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
            return GlobalFunction::display_response(Status::USER_DISPLAY,$material);
    }

    public function show($id){
      $material = Material::with('category')->where('id',$id)->get(['code','description','category_id']);
      return GlobalFunction::display_response(Status::USER_DISPLAY,$material);
    }

    public function store(MaterialRequest $request){
  
      $validated=$request->validated();

      $material = Material:: create([
        'code'=> $validated['code'],
        'description'=> $validated['description'],
        'category_id'=> $validated['category_id']
      ]);
      return GlobalFunction::save(Status::MATERIAL_SAVE,$material);
    }

    public function update(Request $request, $id){
      $material = Material::find($id);
      $material->update([
        'code'=>$request['code'],
        'description'=>$request['description'],
        'category_id'=>$request['category_id']
      ]);
      return GlobalFunction::update_response(Status::MATERIAL_UPDATE,$material);
    }

    public function destroy($id){
      $material = Material::withTrashed()->find($id);

      $model = new Material;
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
}
