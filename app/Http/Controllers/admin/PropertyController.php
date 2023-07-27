<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Image;
use DB;
use App\Models\Characteristic;
use App\Models\Locale;
use App\Models\PropertyCharacteristic;
use App\Models\PropertyImage;
use App\Models\Property;
use RuntimeException;

class PropertyController extends Controller
{


    public function __construct(Request $request)
    {
        $this->request              = $request;

        $this->datarequest = [
            'title'             => 'Propriedades',
            'link'              => 'admin/properties',
            'filter'            => 'admin/properties?filter',
            'linkFormAdd'       => 'admin/properties/form?act=add',
            'linkFormEdit'      => 'admin/properties/form?act=edit',
            'linkStore'         => 'admin/properties',
            'linkUpdate'        => 'admin/properties/',
            'linkCopy'          => 'admin/properties/copy',
            'linkDestroy'       => 'admin/properties',
            'breadcrumb_new'    => 'Nova Propriedade',
            'breadcrumb_edit'   => 'Editar Propriedade',
            'path'              => 'admin.property.'
        ];

    }

    public function index(){

        $column    = $this->request->input('column');
        $order     = $this->request->input('order') == 'desc' ? 'asc' : 'desc';

        if($column){
            $column = $this->request->input('column');
            $column_name = "$column $order";
        } else {
            $column_name = "id desc";
        }

        $field     = $this->request->input('field')    ? $this->request->input('field')    : 'name';
        $operator  = $this->request->input('operator') ? $this->request->input('operator') : 'like';
        $value     = $this->request->input('value')    ? $this->request->input('value')    : '';

        if($field == 'data' || $field == 'dataini' || $field == 'datafim' || $field == 'date'){
            $value = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
        }

        if($field == 'created_at'){
            $field = 'CAST(created_at as DATE)';
            $value = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
        }


        if($field == 'characteristic_id'){
            $value = implode(',',$value);
            $newValueCategory = " id in (select pc.property_id from proeprty_characteristics as pc
            inner join characteristics as c on c.id = pc.characteristic_id where pc.characteristic_id in($value))";
        }

        if($field != 'characteristic_id'){
            if($operator == 'like'){
                $newValue = "'%$value%'";
            }else{
                $newValue = "'$value'";
            }
        }


            if($this->request->input('filter') && $field == 'characteristic_id'){
                $data = Property::orderByRaw("$column_name")
                ->whereraw("$newValueCategory")
                ->paginate(15);
            }
            else if($this->request->input('filter')){
                $data = Property::orderByRaw("$column_name")
                            ->whereraw("$field $operator $newValue")
                            ->paginate(15);
            }else{
                $data = Property::orderByRaw("$column_name")
                            ->paginate(15);
            }



        foreach($data as $key => $result){
            if(isJSON($result->image) == true){
                $data[$key]['image_thumb']    = property_exists(json_decode($result->image), 'thumb')    ? json_decode($result->image)->thumb : '';
                $data[$key]['image_original'] = property_exists(json_decode($result->image), 'original') ? json_decode($result->image)->original : '';
            }else{
                $data[$key]['image_thumb']    = '';
                $data[$key]['image_original'] = '';
            }
        }



        $characteristics = DB::table('property_characteristics as pc')
                        ->select('pc.property_id as property_id','pc.characteristic_id as characteristic_id','c.name as characteristic')
                        ->join('characteristics as c','c.id','pc.characteristic_id')
                        ->get();


        return view($this->datarequest['path'].'.index',compact('column','order','data','characteristics'))->with($this->datarequest);
    }

    public function form(){

        if($this->request->input('act') == 'add'){
            return view($this->datarequest['path'].'form')->with($this->datarequest);
        }else if($this->request->input('act') == 'edit'){
            $this->datarequest['linkFormEdit'] = $this->datarequest['linkFormEdit'].'&id='.$this->request->input('id');
            $this->datarequest['linkUpdate']   = $this->datarequest['linkUpdate'].$this->request->input('id');

            $data = Property::where('id',$this->request->input('id'))->first();

            $characteristics = DB::table('property_characteristics as pc')
                        ->select('pc.property_id as property_id','pc.characteristic_id as characteristic_id','c.name as characteristic')
                        ->join('characteristics as c','c.id','pc.characteristic_id')
                        ->where('pc.property_id',$data->id)
                        ->get();

            $locales = Locale::where('id',$data->local_id)->get();

            if(isJSON($data->image) == true){
                    $data['image_thumb']    = property_exists(json_decode($data->image), 'thumb')    ? json_decode($data->image)->thumb : '';
                    $data['image_original'] = property_exists(json_decode($data->image), 'original') ? json_decode($data->image)->original : '';
                }else{
                    $data['image_thumb']    = '';
                    $data['image_original'] = '';
                }


            return view($this->datarequest['path'].'form',compact('data','characteristics','locales'))->with($this->datarequest);
        }else{
            return view($this->datarequest['path'].'index')->with($this->datarequest);
        }

    }


    public function store()
    {

        $newProperty          = new Property;

        $data = $this->request->all();

        $messages = [
            'name.required'         => 'O campo nome é obrigatório',
            'name.unique'           => 'Já existe uma propriedade com esse nome',
            'local_id.required'     => 'O campo Local é obrigatório',
            'finality.required'     => 'O campo Finalidade é obrigatório',
            'type.required'         => 'O campo Tipo é obrigatório',
            'description.required'  => 'O Campo descrição é obrigatório',
        ];

        $validator = Validator::make($data, [
            'name'          => "required|unique:properties,name",
            'local_id'      => "required",
            'finality'      => "required",
            'type'          => "required",
            'description'   => "required",
        ],$messages);

        if( $validator->fails() ){
            return response()->json($validator->errors()->first(), 422);
        }

        $verifySlug = Property::where('slug',Str::slug($data['name']))->count();

        if ($verifySlug > 0) {
            $pieces = explode('-', Str::slug($data['name']));
            $count = intval(end($pieces));
            $status = false;
            $count = 0;
            while($status != true){
                $newProperty->slug = Str::slug($data['name']).'-' . ($count + 1);
                $verifySlug = Property::where('slug',$newProperty->slug)->count();
                if($verifySlug <= 0){
                    $status = true;
                }
                $count++;
            }
        } else {
            $newProperty->slug          = Str::slug($data['name']);
        }

        $newProperty->local_id           = $data['local_id'];
        $newProperty->finality           = json_encode($data['finality']);
        $newProperty->type               = $data['type'];
        $newProperty->name               = $data['name'];
        $newProperty->bedrooms           = $data['bedrooms'];
        $newProperty->bathrooms          = $data['bathrooms'];
        $newProperty->garages            = $data['garages'];
        $newProperty->area               = $data['area'];
        $newProperty->price              = moeda($data['price']);
        $newProperty->price_condominium  = moeda($data['price_condominium']);
        $newProperty->description        = $data['description'];
        $newProperty->cep                = $data['cep'];
        $newProperty->address            = $data['address'];
        $newProperty->number             = $data['number'];
        $newProperty->district           = $data['district'];
        $newProperty->city               = $data['city'];
        $newProperty->state              = $data['state'];
        $newProperty->complement         = $data['complement'];
        $newProperty->google_maps        = $data['google_maps'];
        $newProperty->youtube            = $data['youtube'];
        $newProperty->image              = $data['image'];
        $newProperty->status             = $data['status'];


        try{
            $newProperty->save();

        foreach($data['characteristics'] as $characteristic){
                    $newPropertyCategory  = new PropertyCharacteristic;
                    $newPropertyCategory->characteristic_id = $characteristic;
                    $newPropertyCategory->property_id       = $newProperty->id;
                    $newPropertyCategory->save();
                }


        } catch(\Exception $e){
            \Log::info($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }

        return response()->json('Registro salvo com sucesso', 200);


    }

    public function copy()
    {

        $model = new Property;
        $data = $this->request->all();

        if(!isset($data['selected'])){
            return response()->json('Selecione ao menos um registro', 422);
        }

        try{
            foreach($data['selected'] as $result){
                $find = $model->find($result);
                $newRegister = $find->replicate();

                $pieces = explode('-', Str::slug($newRegister->name));
                $count = intval(end($pieces));
                $status = false;
                $count = 0;
                while($status != true){
                    $newRegister->name = $newRegister->name.'-'.$count + 1;
                    $newRegister->slug = Str::slug($newRegister->name).'-' . ($count + 1);
                    $verifySlug = Property::where('slug',$newRegister->slug)->count();
                    if($verifySlug <= 0){
                        $status = true;
                    }
                    $count++;
                }

                $newRegister->save();

                $findPropertyCharacteristic = PropertyCharacteristic::where('property_id',$find->id)->get();

                foreach($findPropertyCharacteristic as $characteristic){
                    $newPropertyCategory  = new PropertyCharacteristic;
                    $newPropertyCategory->characteristic_id = $characteristic->characteristic_id;
                    $newPropertyCategory->property_id       = $newRegister->id;
                    $newPropertyCategory->save();
                }

            }

        } catch(\Exception $e){
            \Log::info($e->getMessage());
            return response()->json($e->getMessage(), 500);

        }


        return response()->json(true, 200);


    }


    public function update($id)
    {

        $newProperty = Property::where('id',$id)->first();

        $data = $this->request->all();

        $messages = [
            'name.required'         => 'O campo nome é obrigatório',
            'name.unique'           => 'Já existe uma propriedade com esse nome',
            'local_id.required'     => 'O campo Local é obrigatório',
            'finality.required'     => 'O campo Finalidade é obrigatório',
            'type.required'         => 'O campo Tipo é obrigatório',
            'description.required'  => 'O Campo descrição é obrigatório',
        ];

        $validator = Validator::make($data, [
            'name'      => "required|unique:properties,name,$id",
            'local_id'      => "required",
            'finality'      => "required",
            'type'          => "required",
            'description'   => "required",
        ],$messages);

        if( $validator->fails() ){
            return response()->json($validator->errors()->first(), 422);
        }

        $verifySlug = Property::where('slug',Str::slug($data['name']))->where('id','!=',$id)->count();

        if ($verifySlug > 0) {
            $pieces = explode('-', Str::slug($data['name']));
            $count = intval(end($pieces));
            $status = false;
            $count = 0;
            while($status != true){
                $newProperty->slug = Str::slug($data['name']).'-' . ($count + 1);
                $verifySlug = Property::where('slug',$newProperty->slug)->count();
                if($verifySlug <= 0){
                    $status = true;
                }
                $count++;
            }
        } else {
            $newProperty->slug          = Str::slug($data['name']);
        }

        $newProperty->local_id           = $data['local_id'];
        $newProperty->finality           = json_encode($data['finality']);
        $newProperty->type               = $data['type'];
        $newProperty->name               = $data['name'];
        $newProperty->bedrooms           = $data['bedrooms'];
        $newProperty->bathrooms          = $data['bathrooms'];
        $newProperty->garages            = $data['garages'];
        $newProperty->area               = $data['area'];
        $newProperty->price              = moeda($data['price']);
        $newProperty->price_condominium  = moeda($data['price_condominium']);
        $newProperty->description        = $data['description'];
        $newProperty->cep                = $data['cep'];
        $newProperty->address            = $data['address'];
        $newProperty->number             = $data['number'];
        $newProperty->district           = $data['district'];
        $newProperty->city               = $data['city'];
        $newProperty->state              = $data['state'];
        $newProperty->complement         = $data['complement'];
        $newProperty->google_maps        = $data['google_maps'];
        $newProperty->youtube            = $data['youtube'];
        $newProperty->image              = $data['image'];
        $newProperty->status             = $data['status'];

        try{
            $newProperty->save();

            PropertyCharacteristic::where('property_id',$id)->delete();

            foreach($data['characteristics'] as $characteristic){
                $newPropertyCategory  = new PropertyCharacteristic;
                $newPropertyCategory->characteristic_id = $characteristic;
                $newPropertyCategory->property_id       = $newProperty->id;
                $newPropertyCategory->save();
            }

        } catch(\Exception $e){
            \Log::info($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }


        return response()->json('Registro salvo com sucesso', 200);

    }

    public function destroy()
    {
        $model = new Property;
        $data = $this->request->all();

        if(!isset($data['selected'])){
            return response()->json('Selecione ao menos um registro', 422);
        }

        try{
            foreach($data['selected'] as $result){

                PropertyCharacteristic::where('property_id',$result)->delete();

                $find = $model->where('id',$result);
                $find->delete();
            }

        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }


        return response()->json(true, 200);


    }


    public function showImages($property_id){

        $images = PropertyImage::where('property_id',$property_id)->get();
        return response()->json($images);
    }

    public function upload($property_id){

        $image     = $this->request->file('file');
        $imageName = $image->getClientOriginalName();
        $extension = $this->request->file('file')->extension();
        $newName   = md5($imageName).date('ymdhis').'.'.$extension;

        $image->move(storage_path('app/public/photos/shares'),$newName);

        $imageUpload                = new PropertyImage();
        $imageUpload->property_id    = $property_id;
        $imageUpload->image         = 'storage/photos/shares/'.$newName;
        $imageUpload->save();
        return response()->json(['success'=>$imageName]);

    }

    public function removeImage($id){


        $image = PropertyImage::find($id);

        $exp = explode('/',$image->image);
        $path_image = $exp['1'].'/'.$exp['2'].'/'.$exp['3'];

        unlink(storage_path('app/public/'.$path_image));

        $image->delete();

        return response()->json(true, 200);


    }


}
