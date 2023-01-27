<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\OrderResource;

use App\Models\Order;

use App\Response\Status;
use App\Functions\GlobalFunction;

class OrderController extends Controller
{

    public function index(Request $request){
         $search=$request->search;
         $status=$request->status;
         $order = Order::when($status === 'inactive',function($query){
            $query->onlyTrashed();
            })
            ->when($search,function($query) use($search){
               $query->where('code','like','%'.$search.'%')
              ->orWhere('description','like','%'.$search.'%');
            })
           ->paginate($request->rows);

            $is_empty = $order->isEmpty();
            if($is_empty){
              return GlobalFunction::not_found(Status::NOT_FOUND);
              }
              $order_collection = OrderResource::collection($order);
              return GlobalFunction::display_response(Status::USER_DISPLAY,$order_collection);
    }

    public function show($id){
        $order = Order::where('id',$id)->get()->first();

        $order_collection = OrderResource::collection([$order]);

        return GlobalFunction::display_response(Status::USER_DISPLAY,$order_collection);
    }

    public function store(Request $request){
  
      $validated=$request->validated();

      $order = Material:: create([
        'order_no'=> $validated['order_no'],
        'date_ordered'=> $validated['date_ordered'],
        'date_needed'=> $validated['date_needed'],
        'date_approved'=> $validated['date_approved'],
        'company_id'=> $validated['company_id'],
        'company_name'=> $validated['company_name'],
        'department_id'=> $validated['department_id'],
        'department_name'=> $validated['department_name'],
        'location_id'=> $validated['location_id'],
        'location_name'=> $validated['location_name'],
        'customer_code'=> $validated['customer_code'],
        'customer_name'=> $validated['customer_name'],
        'material_code'=> $validated['material_code'],
        'material_name'=> $validated['material_name'],
        'category_id'=> $validated['category_id'],
        'category_name'=> $validated['category_name'],
        'quantity'=> $validated['quantity'],
        'remarks'=> $validated['remarks'],
        'is_approved'=> $validated['is_approved'],
      ]);
      return GlobalFunction::save(Status::ORDER_SAVE,$order);
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
