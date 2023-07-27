<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class CompanyCategoryController extends Controller
{

    public function index($slug = null)
    {
        if($slug != null){

            $companyCategories = DB::table('company_categories as cc')
                                    ->join('companies as co','cc.company_id','co.id')
                                    ->join('categories as ca','cc.category_id','ca.id')
                                    ->join('users as u','co.user_id','u.id')
                                    ->select('cc.id','cc.category_id as category_id','cc.company_id as company_id',
                                    'co.document','co.name','co.description','co.telephone','co.telephone',
                                    'co.whatsapp','co.instagram','co.facebook','co.youtube','co.site','co.google_maps',
                                    'co.slug as company_slug','co.image','co.status','ca.slug','ca.name as category_name')
                                    ->where('u.status',1)
                                    ->where('co.status',1)
                                    ->where('ca.slug',$slug)
                                    ->get();

            } else {
            $companyCategories = DB::table('companies as co')
                                    ->join('company_categories as cc','cc.company_id','co.id')
                                    ->join('categories as ca','cc.category_id','ca.id')
                                    ->join('users as u','co.user_id','u.id')
                                    ->select('co.id',DB::raw("max(cc.category_id) as category_id, max(ca.slug) as slug, max(ca.name) as category_name"),
                                    'co.document','co.name','co.description','co.telephone','co.telephone',
                                    'co.whatsapp','co.instagram','co.facebook','co.youtube','co.site','co.google_maps',
                                    'co.slug as company_slug','co.image','co.status')
                                    ->where('u.status',1)
                                    ->where('co.status',1)
                                    ->groupby('co.id','co.document','co.name','co.description','co.telephone','co.telephone',
                                    'co.whatsapp','co.instagram','co.facebook','co.youtube','co.site','co.google_maps',
                                    'co.slug','co.image','co.status')
                                    ->get();
            }

            foreach($companyCategories as $key => $result){
                if(isJSON($result->image) == true){
                    $companyCategories[$key]->image_thumb    = property_exists(json_decode($result->image), 'thumb')    ? \Config('app.url').json_decode($result->image)->thumb : '';
                    $companyCategories[$key]->image_original = property_exists(json_decode($result->image), 'original') ? \Config('app.url').json_decode($result->image)->original : '';
                }else{
                    $companyCategories[$key]->image_thumb    = '';
                    $companyCategories[$key]->image_original = '';
                }
            }

            return response()->json($companyCategories, 200);
    }


    public function store(Request $request)
    {
        //
    }


    public function show($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
