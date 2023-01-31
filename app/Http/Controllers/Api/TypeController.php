<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Type;
use App\Response\Status;
use App\Functions\GlobalFunction;

class TypeController extends Controller
{
    public function index(Request $request){

        $type=Type::get();

        return GlobalFunction::display_response(Status::TYPE_DISPLAY,$type);
    }
}
