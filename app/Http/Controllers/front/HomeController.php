<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\Characteristic;
use App\Models\Locale;
use Illuminate\Support\Facades\Validator;
use RuntimeException;
use DB;

class HomeController extends Controller
{

    public function __construct()
    {

    }

    public function index(){

        $data = Property::where('status',1)->orderby('name','ASC')->get();

        foreach($data as $key => $result){

            $data[$key]['finality']    = json_decode($result->finality);

            if(isJSON($result->image) == true){
                $data[$key]['image_thumb']    = property_exists(json_decode($result->image), 'thumb')    ? json_decode($result->image)->thumb : '';
                $data[$key]['image_original'] = property_exists(json_decode($result->image), 'original') ? json_decode($result->image)->original : '';
            }else{
                $data[$key]['image_thumb']    = '';
                $data[$key]['image_original'] = '';
            }
        }

        return view('front.index', compact('data'));
    }


    public function Properties(){
        return view('front.properties');
    }

    public function Property($slug){
        $property        = DB::table('properties as p')
        ->join('locales as l','p.local_id','l.id')
        ->select('l.name as local','p.id','p.finality','p.type','p.name','p.bedrooms','p.bathrooms',
        'p.garages', 'p.area','p.price','p.price_condominium','p.description','p.cep','p.address',
        'p.number','p.complement','p.district','p.city','p.state','p.google_maps','p.youtube','p.slug','p.image','p.status')
        ->where('p.slug',$slug)->first();

        $property_image  = PropertyImage::where('property_id',$property->id)->get();

        $characteristics = DB::table('property_characteristics as pc')
        ->select('c.name')
        ->join('characteristics as c','c.id','pc.characteristic_id')
        ->where('pc.property_id',$property->id)
        ->get();

        if(isJSON($property->image) == true){
            $property->image_thumb    = property_exists(json_decode($property->image), 'thumb')    ? json_decode($property->image)->thumb : '';
            $property->image_original = property_exists(json_decode($property->image), 'original') ? json_decode($property->image)->original : '';
        }else{
            $property->image_thumb    = '';
            $property->image_original = '';
        }


        $property->youtube = convertYoutube($property->youtube);

        return view('front.property',compact('property','property_image','characteristics'));
    }



    public function getProperties(Request $request){

        $finality   = "finality like '%".$request->input('finality')."%' and ";
        $type       = $request->input('type');
        $locale     = $request->input('locale');
        $price      = $request->input('price');


        $type   = $type   != null ? "type = '".$type."'" : "type like '%%'";
        $locale = $locale != null ? " and local_id= '".$locale."'" : null;

        if(strlen($price)){
            $price = explode('-',$price);
            $price = " and (price BETWEEN '".$price[0]."' and '".$price[1]."')";
        }else{
            $price = null;
        }

        //$data = Property::whereraw("$finality $type $locale $price")->orderby('name','ASC')->get();

        $data = DB::table('properties as p')
                ->join('locales as l','p.local_id','l.id')
                ->select('l.name as local','p.id','p.finality','p.type','p.name','p.bedrooms','p.bathrooms',
                'p.garages', 'p.area','p.price','p.price_condominium','p.description','p.cep','p.address',
                'p.number','p.complement','p.district','p.city','p.state','p.google_maps','p.youtube','p.slug','p.image','p.status')
                ->whereraw("$finality $type $locale $price and p.status = 1")
                ->orderby('name','ASC')
                ->get();


        foreach($data as $key => $result){


            $data[$key]->finality    = json_decode($result->finality);

            if(isJSON($result->image) == true){
                $data[$key]->image_thumb    = property_exists(json_decode($result->image), 'thumb')    ? json_decode($result->image)->thumb : '';
                $data[$key]->image_original = property_exists(json_decode($result->image), 'original') ? json_decode($result->image)->original : '';
            }else{
                $data[$key]->image_thumb    = '';
                $data[$key]->image_original = '';
            }
        }

        return response()->json($data);


    }


}
