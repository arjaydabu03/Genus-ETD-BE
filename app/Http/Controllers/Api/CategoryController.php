<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Response\Status;
use App\Functions\GlobalFunction;

use App\Http\Requests\Category\CategoryRequest;
use App\Http\Requests\Category\DisplayRequest;
class CategoryController extends Controller
{
    public function index(DisplayRequest $request){
        $status=$request->status;
        $search=$request->search;
        $category = Category::when($status === 'inactive',function($query){
          $query->onlyTrashed();
      })
         ->when($search,function($query) use($search){
            $query->where('name','like','%'.$search.'%');
      })
         ->paginate($request->rows);

        $is_empty = $category->isEmpty();
        if($is_empty){

          return GlobalFunction::not_found(Status::NOT_FOUND);
        }
          return GlobalFunction::display_response(Status::USER_DISPLAY,$category);
    }

    public function show($id){
      $category = Category::where('id',$id)->get();
      return GlobalFunction::display_response(Status::USER_DISPLAY,$category);
    }

    public function store(CategoryRequest $request){

      $validated=$request->validated();

      $category = Category:: create([
       'name'=> $validated['name']
      ]);

      return GlobalFunction::save(Status::CATEGORY_SAVE,$category);
    }

    public function update(CategoryRequest $request, $id){
      $category = Category::find($id);
      $category->update([
        'name'=>$request['name']
      ]);
      return GlobalFunction::update_response(Status::CATEGORY_UPDATE,$category);
    }

    public function destroy($id){
      $category = Category::withTrashed()->find($id);
      $is_active = Category::withTrashed()
              ->where('id', $id)
              ->first();
      if(!$is_active){
        return $is_active;
      }else if(!$is_active->deleted_at){
          $category->delete();
          $message = Status::ARCHIVE_STATUS;
      }else {
          $category->restore();
          $message = Status::RESTORE_STATUS;
      }
      return GlobalFunction::delete_response($message,$category);
    }
}