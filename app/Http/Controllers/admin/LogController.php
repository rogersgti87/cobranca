<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Image;
use DB;
use App\Models\LogGatewayPayment;
use RuntimeException;

class LogController extends Controller
{


    public function __construct(Request $request)
    {
        $this->request              = $request;

        $this->datarequest = [
            'title'             => 'Logs',
            'link'              => 'admin/logs',
            'path'              => 'admin.log.'
        ];

    }

    public function index(){

        return view($this->datarequest['path'].'index')->with($this->datarequest);
    }

    public function list(){
        $data = LogGatewayPayment::when(request('date') != null, function($query){
            return $query->whereraw("cast(created_at as date) = '".request('date')."'");
        })->paginate(2);

        return view($this->datarequest['path'].'list')->with('data',$data);

    }


    public function getlog($id){
        $data = LogGatewayPayment::select('log')->where('id',$id)->first();
        return response()->json($data);
    }

    public function destroy()
    {
        $model = new Service;
        $data = $this->request->all();

        if(!isset($data['selected'])){
            return response()->json('Selecione ao menos um registro', 422);
        }

        try{
            foreach($data['selected'] as $result){
                $find = $model->where('id',$result)->where('user_id',auth()->user()->id);
                $find->delete();
            }

        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }


        return response()->json(true, 200);


    }

}
