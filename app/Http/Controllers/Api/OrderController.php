<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\OrderResource;

use App\Models\Order;
use App\Models\Material;
use App\Models\Category;

use App\Response\Status;
use App\Functions\GlobalFunction;
use App\Http\Requests\Order\StoreRequest;

class OrderController extends Controller
{

    public function index(Request $request){

         $search=$request->search;
         $status=$request->status;
        //  $paginate=isset($request->paginate) ? $request->paginate : 1;
         $is_approved=$request->is_approved?$request->is_approved:0;
         $order = Order::when($status === 'inactive',function($query){
              $query->onlyTrashed();
            })
              ->where('is_approved',$is_approved)
              ->when($search,function($query) use($search){
               $query->where('code','like','%'.$search.'%')
              ->orWhere('description','like','%'.$search.'%');
            })->paginate($request->rows);

            $is_empty = $order->isEmpty();
            if($is_empty){
              return GlobalFunction::not_found(Status::NOT_FOUND);
              }
             
              OrderResource::collection($order);
              return GlobalFunction::display_response(Status::USER_DISPLAY,$order);
    }

    public function show($id){
        $order = Order::where('id',$id)->get();

        $order_collection = OrderResource::collection($order);

        return GlobalFunction::display_response(Status::USER_DISPLAY,$order_collection->first());
    }

    public function store(StoreRequest $request){
      
      // $tz=date('setBegan_atAttribute');

      // return $tz;

       $order = Order:: create([
        'order_no'=> $request['order_no'],
        'date_ordered'=> now('GMT+8'),
        'date_needed'=> $request['dates']['date_needed'],
        'company_id'=> $request['company']['id'],
        'company_name'=> $request['company']['name'],
        'department_id'=> $request['department']['id'],
        'department_name'=> $request['department']['name'],
        'location_id'=> $request['location']['id'],
        'location_name'=> $request['location']['name'],
        'customer_code'=> $request['customer']['code'],
        'customer_name'=> $request['customer']['name'],
        'material_code'=> $request['order']['material']['code'],
        'material_name'=> $request['order']['material']['name'],
        'category_id'=> $request['order']['category']['id'],
        'category_name'=> $request['order']['category']['name'],
        'quantity'=> $request['order']['quantity'],
        'remarks'=> $request['order']['remarks'],

      ]);
          $order= new OrderResource($order);

          return GlobalFunction::save(Status::ORDER_SAVE,$order);
    }

    public function update(Request $request, $id){

      $not_found = Order::where('id',$id)->get();

      if($not_found->isEmpty()){
         return GlobalFunction::not_found(Status::NOT_FOUND);
         }


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