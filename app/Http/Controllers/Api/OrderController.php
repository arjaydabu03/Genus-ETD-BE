<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\OrderResource;

use App\Models\Order;

use App\Response\Status;
use App\Functions\GlobalFunction;
use App\Http\Requests\Order\StoreRequest;

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
        $order = Order::where('id',$id)->get();

        $order_collection = OrderResource::collection($order);

        return GlobalFunction::display_response(Status::USER_DISPLAY,$order_collection->first());
    }

    public function store(StoreRequest $request){
  
       $validated=$request->validated();

      $order = Order:: create([
        'order_no'=> $validated['order_no'],
        'date_ordered'=> $validated['dates']['date_ordered'],
        'date_needed'=> $validated['dates']['date_needed'],
        'company_id'=> $validated['company']['id'],
        'company_name'=> $validated['company']['name'],
        'department_id'=> $validated['department']['id'],
        'department_name'=> $validated['department']['name'],
        'location_id'=> $validated['location']['id'],
        'location_name'=> $validated['location']['name'],
        'customer_code'=> $validated['customer']['code'],
        'customer_name'=> $validated['customer']['name'],
        'material_code'=> $validated['material']['code'],
        'material_name'=> $validated['material']['name'],
        'category_id'=> $validated['category']['id'],
        'category_name'=> $validated['category']['name'],
        'quantity'=> $validated['quantity'],
        'remarks'=> $validated['remarks'],

      ]);
     $order= new OrderResource($order);
      return GlobalFunction::save(Status::ORDER_SAVE,$order);
    }

    public function update(Request $request, $id){
      $order = Order::find($id);
      $order->update([
        'remarks'=>$request['remarks'],
        'quantity'=>$request['quantity']
      ]);
      $order_collection = new OrderResource($order);
      return GlobalFunction::update_response(Status::ORDER_UPDATE,$order_collection);
    }

    public function destroy($id){

      $invalid_id = Order::where('id',$id)->withTrashed()->get();
        
            if($invalid_id->isEmpty()){
              return GlobalFunction::not_found(Status::NOT_FOUND);
         }

        $order = Order::withTrashed()->find($id);
        $is_active = Order::withTrashed()
            ->where('id', $id)
            ->first();
        if(!$is_active){
            return $is_active;
        }else if(!$is_active->deleted_at){
            $order->delete();
            $message = Status::ARCHIVE_STATUS;
        }else {
            $order->restore();
            $message = Status::RESTORE_STATUS;
        }
        $order_collection = new OrderResource($order);
            return GlobalFunction::delete_response($message,$order_collection);
    }
}
