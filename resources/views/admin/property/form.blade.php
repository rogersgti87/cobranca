@extends('layouts.admin')

@section('content')

 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">{{ $title }}</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('admin')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{url($link)}}">{{ $title }}</a></li>
              <li class="breadcrumb-item active">{{Request::get('act') == 'add' ? $breadcrumb_new : $breadcrumb_edit}}</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <ul class="button-action">
                    <li><a href="#" data-original-title="Salvar" data-toggle="tooltip" class="btn btn-secondary" id="btn-salvar"><i class="fa fa-save fa-2x"></i></a></li>
                </ul>
            </div>

    <form class="form">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <div class="col-md-12">
        <div class="form-row">

                    <div class="col-md-3 col-sm-12">
                    <fieldset>
                        <legend>Imagem principal</legend>

                        <div class="form-group col-md-12 col-sm-12 text-center">
                            <a class="btn btn-default" style="border:1px solid #333;" id="lfm" data-input="thumbnail" data-preview="holder" style="cursor: pointer">
                                <img src="{{ isset($data->image_thumb) && $data->image_thumb != null ? url("$data->image_thumb") : url('assets/admin/img/thumb.png') }}" id="holder" style="height: 100px;width: 100px;">
                            </a>
                            <input type="hidden" id="thumbnail" name="image" value="{{ isset($data->image) ? $data->image : '' }}">
                        </div>

                    </fieldset>

                    </div>

                    @if(Request::get('id'))
                    <div class="col-md-12 col-sm-12">
                        <fieldset>
                            <legend>Galeria de Fotos</legend>

                            <div class="card-body">
                                <div id="actions" class="row">
                                  <div class="col-lg-6">
                                    <div class="btn-group w-100">
                                      <span class="btn btn-success col fileinput-button">
                                        <i class="fas fa-plus"></i>
                                        <span>Selecionar Fotos</span>
                                      </span>
                                    </div>
                                  </div>
                                  <div class="col-lg-6 d-flex align-items-center">
                                    <div class="fileupload-process w-100">
                                      <div id="total-progress" class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                        <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="table table-striped files" id="previews">
                                  <div id="template" class="row mt-2">
                                    <div class="col-auto">
                                        <span class="preview"><img src="data:," alt="" data-dz-thumbnail /></span>
                                    </div>
                                    <div class="col d-flex align-items-center">
                                        <p class="mb-0">
                                          <span class="lead" data-dz-name></span>
                                          (<span data-dz-size></span>)
                                        </p>
                                        <strong class="error text-danger" data-dz-errormessage></strong>
                                    </div>
                                    <div class="col-4 d-flex align-items-center">
                                        <div class="progress progress-striped active w-100" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                          <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
                                        </div>
                                    </div>

                                  </div>
                                </div>


                                  <!-- Show images -->
                                    <div class="form-group col-md-12 col-sm-12 mt-5">
                                        <fieldset>
                                            <div class="row" id="load-images" style="height: 300px;overflow-y: scroll;"></div>
                                        </fieldset>
                                    </div>
                                <!-- fim Show images -->
                              </div>
                              <!-- /.card-body -->

                        </fieldset>

                        </div>

                        @endif

                    <div class="col-md-12">

                    <fieldset>
                        <legend>Informações da Propriedade</legend>
                        <div class="form-row">

                            <div class="form-group col-md-3 col-sm-12">
                                <label>Localidade</label>
                                <select class="form-control select2bs4" name="local_id" id="locale" style="width:100%">
                                    @if(isset($locales))
                                        @foreach($locales as $locale)
                                            <option value="{{ $locale->id }}" selected>{{ $locale->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="form-group col-md-2 col-sm-12">
                                <label>Tipo</label>
                                <select class="form-control custom-select" name="type" id="type">
                                    <option {{ isset($data->type) && $data->type == 'Casa'          ? 'selected' : '' }} value="Casa">Casa</option>
                                    <option {{ isset($data->type) && $data->type == 'Apartamento'   ? 'selected' : '' }} value="Apartamento">Apartamento</option>
                                </select>
                            </div>

                            <div class="form-group col-md-5 col-sm-12">
                                <label>Finalidade</label>
                                <select class="form-control custom-select" name="finality[]" multiple="multiple" id="finality" style="width:100%">
                                    @if(isset($data->finality))
                                        @foreach(json_decode($data->finality) as $finality)
                                            <option value="{{ $finality }}" selected>{{ $finality }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>


                            <div class="form-group col-md-2 col-sm-12">
                                <label>Status</label>
                                <select class="form-control custom-select" name="status" id="status" {{ \Auth::user()->type == 'Membro' ? 'disabled' : ''}}>
                                    <option {{ isset($data->status) && $data->status === 1 ? 'selected' : '' }} value="1">Ativo</option>
                                    <option {{ isset($data->status) && $data->status === 0 ? 'selected' : '' }} value="0">Inativo</option>
                                </select>
                            </div>


                    <div class="form-group col-md-12 col-sm-12">
                        <label>Caracaterísticas</label>
                        <select class="form-control select2bs4" name="characteristics[]" multiple="multiple" id="characteristics" style="width:100%">
                            @if(isset($characteristics))
                                @foreach($characteristics as $characteristic)
                                    <option value="{{ $characteristic->characteristic_id }}" selected>{{ $characteristic->characteristic }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>




                    <div class="form-group col-md-12 col-sm-12">
                        <label>Nome</label>
                        <input type="text" class="form-control" name="name" id="name" autocomplete="off" required value="{{isset($data->name) ? $data->name : ''}}">
                    </div>

                    <div class="form-group col-md-3 col-sm-12">
                        <label>Cep</label>
                        <input type="text" class="form-control" name="cep" id="cep" autocomplete="off" required value="{{isset($data->cep) ? $data->cep : ''}}">
                    </div>

                    <div class="form-group col-md-7 col-sm-12">
                        <label>Endereço</label>
                        <input type="text" class="form-control" name="address" id="address" autocomplete="off" required value="{{isset($data->address) ? $data->address : ''}}">
                    </div>

                    <div class="form-group col-md-2 col-sm-12">
                        <label>Número</label>
                        <input type="text" class="form-control" name="number" id="number" autocomplete="off" required value="{{isset($data->number) ? $data->number : ''}}">
                    </div>

                    <div class="form-group col-md-3 col-sm-12">
                        <label>Bairro</label>
                        <input type="text" class="form-control" name="district" id="district" autocomplete="off" required value="{{isset($data->district) ? $data->district : ''}}">
                    </div>

                    <div class="form-group col-md-3 col-sm-12">
                        <label>Cidade</label>
                        <input type="text" class="form-control" name="city" id="city" autocomplete="off" required value="{{isset($data->city) ? $data->city : ''}}">
                    </div>

                    <div class="form-group col-md-3 col-sm-12">
                        <label>Estado</label>
                        <input type="text" class="form-control" name="state" id="state" autocomplete="off" required value="{{isset($data->state) ? $data->state : ''}}">
                    </div>

                    <div class="form-group col-md-3 col-sm-12">
                        <label>Complemento</label>
                        <input type="text" class="form-control" name="complement" id="complement" autocomplete="off" required value="{{isset($data->complement) ? $data->complement : ''}}">
                    </div>

                    <div class="form-group col-md-2 col-sm-12">
                        <label>Quartos</label>
                        <input type="number" class="form-control" min="0" name="bedrooms" id="bedrooms" autocomplete="off" required value="{{isset($data->bedrooms) ? $data->bedrooms : 0}}">
                    </div>

                    <div class="form-group col-md-2 col-sm-12">
                        <label>Banheiros</label>
                        <input type="number" class="form-control" min="0" name="bathrooms" id="bathrooms" autocomplete="off" required value="{{isset($data->bathrooms) ? $data->bathrooms : 0}}">
                    </div>

                    <div class="form-group col-md-2 col-sm-12">
                        <label>Garagens</label>
                        <input type="number" class="form-control" min="0" name="garages" id="garages" autocomplete="off" required value="{{isset($data->garages) ? $data->garages : 0}}">
                    </div>

                    <div class="form-group col-md-2 col-sm-12">
                        <label>Área</label>
                        <input type="text" class="form-control" name="area" id="area" autocomplete="off" required value="{{isset($data->area) ? $data->area : 0}}">
                    </div>

                    <div class="form-group col-md-2 col-sm-12">
                        <label>Preço</label>
                        <input type="text" class="form-control money" name="price" id="price" autocomplete="off" required value="{{isset($data->price) ? number_format($data->price,2,',','.') : '0,00'}}">
                    </div>

                    <div class="form-group col-md-2 col-sm-12">
                        <label>Preço condomínio</label>
                        <input type="text" class="form-control money" name="price_condominium" id="price_condominium" autocomplete="off" required value="{{isset($data->price_condominium) ? number_format($data->price_condominium,2,',','.') : '0,00'}}">
                    </div>

                    <div class="form-group col-md-6 col-sm-12">
                        <label>Youtube</label>
                        <input type="text" class="form-control" name="youtube" id="youtube" autocomplete="off" required value="{{isset($data->youtube) ? $data->youtube : ''}}">
                    </div>

                    <div class="form-group col-md-6 col-sm-12">
                        <label>Google Maps</label>
                        <textarea class="form-control" name="google_maps" id="google_maps" autocomplete="off" rows="3">{{isset($data->google_maps) ? $data->google_maps : ''}}</textarea>
                    </div>


                    <div class="form-group col-md-12 col-sm-12">
                        <label>Descrição da Empresa</label>
                        <textarea class="form-control my-editor" name="description" id="content" rows="25">
                            {!! isset($data->description) ? $data->description : '' !!}
                        </textarea>
                    </div>


                    </div>

                    </fieldset>


                </div>



        </div>

        </div>

    </form>

        </div>
    </div>


</div>
</div>
</div>
  </div>
 </div>

@section('scripts')

<script>

$(document).ready(function () {

    $('#lfm').filemanager('image');

    tinymce.init(editor_config);

    @if(Request::get('id'))
        loadImages();
    @endif

});

    $('#characteristics').select2({
        theme: 'bootstrap4',
        placeholder: "Selecione a Característica...",
        allowClear: true,
        //minimumInputLength: 2,
        language: 'pt-BR',
        ajax: {
            url: '{{url("admin/characteristics/getcharacteristics")}}',
            dataType: 'json',

            data: function(params){
                return {
                    characteristic: params.term,
                }
            },

            processResults: function (data) {
                return {
                    results:  data.map(function (characteristic) {
                        return {
                            text: characteristic.name,
                            id: characteristic.id
                        };
                    })
                };
            },
            cache: true
        }

    });

    $('#locale').select2({
        theme: 'bootstrap4',
        placeholder: "Selecione uma Localidade...",
        allowClear: true,
        //minimumInputLength: 2,
        language: 'pt-BR',
        ajax: {
            url: '{{url("admin/locales/getlocales")}}',
            dataType: 'json',

            data: function(params){
                return {
                    locale: params.term,
                }
            },

            processResults: function (data) {
                return {
                    results:  data.map(function (locale) {
                        return {
                            text: locale.name,
                            id: locale.id
                        };
                    })
                };
            },
            cache: true
        }

    });


    var finality = [
    {
        id: 'Compra',
        text: 'Compra'
    },
    {
        id: 'Aluguel',
        text: 'Aluguel'
    },
    {
        id: 'Temporada',
        text: 'Temporada'
    }
];

    $('#finality').select2({
        theme: 'bootstrap4',
        placeholder: "Selecione uma Finalidade...",
        allowClear: true,
        //minimumInputLength: 2,
        language: 'pt-BR',
        data:finality
    });


    //Save data
        $(document).on('click', '#btn-salvar', function(e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                }
            });
            tinymce.triggerSave();
            var url_act = "{{Request::get('act')}}";
            if(url_act == 'edit'){
                var url = "{{ url($linkUpdate) }}";
                var method = 'PUT';
            }else{
                var url = "{{ url("$linkStore") }}";
                var method = 'POST';
            }
            var data = $('.form').serialize();
            $.ajax({
                url: url,
                data:data,
                method:method,
                success:function(data){
                    Swal.fire({
                        width:350,
                        title: "<h5 style='color:#007bff'>" + data + "</h5>",
                        icon: 'success',
                        showConfirmButton: false,
                        showClass: {
                            popup: 'animate__animated animate__backInUp'
                        },
                        allowOutsideClick: false,
                        html:
                        '<a href="{{url($linkFormAdd)}}"  class="btn btn-secondary btn-md"> <i class="fa fa-plus"></i> Novo</a>  ' +
                        `<a href="{{url($linkFormEdit)}}" class="btn btn-success btn-md" style="${url_act == 'add' ? 'display:none;' : ''}"> <i class="fa fa-plus"></i> Editar</a>  ` +
                        '<a href="{{url($link)}}" data-original-title="Listar" data-toggle="tooltip" class="btn btn-primary btn-md"> <i class="fa fa-list"></i> Listar</a>',
                    });
                },
                error:function (xhr) {

                    if(xhr.status === 422){
                        Swal.fire({
                            text: xhr.responseJSON,
                            width:300,
                            icon: 'warning',
                            color: '#007bff',
                            confirmButtonColor: "#007bff",
                            showClass: {
                                popup: 'animate__animated animate__wobble'
                            }
                        });
                    } else{
                        Swal.fire({
                            text: xhr.responseJSON,
                            width:300,
                            icon: 'error',
                            color: '#007bff',
                            confirmButtonColor: "#007bff",
                            showClass: {
                                popup: 'animate__animated animate__wobble'
                            }
                        });
                    }


                }
            });



        });




 // DropzoneJS Demo Code Start
 Dropzone.autoDiscover = false

// Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
var previewNode = document.querySelector("#template")
previewNode.id = ""
var previewTemplate = previewNode.parentNode.innerHTML
previewNode.parentNode.removeChild(previewNode)

var urlUpload  = "{{ url('admin/properties/images/') }}";
var property_id = "{{ Request::get('id') }}";

var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
  url: urlUpload+'/'+property_id, // Set the url
  thumbnailWidth: 80,
  thumbnailHeight: 80,
  parallelUploads: 20,
  previewTemplate: previewTemplate,
  autoQueue: true, // Make sure the files aren't queued until manually added
  previewsContainer: "#previews", // Define the container to display the previews
  clickable: ".fileinput-button", // Define the element that should be used as click trigger to select files.
  headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
  init: function() {
		this.on('success', function(){
			if (this.getQueuedFiles().length == 0 && this.getUploadingFiles().length == 0) {
     				loadImages();
			}
	    });
	},
    queuecomplete: function () {
        this.removeAllFiles();
    }

});


function loadImages(){

    var urlUpload  = "{{ url('admin/properties/images/') }}";
    var property_id = "{{ Request::get('id') }}";

    $.ajax({
        url: urlUpload+'/'+property_id,
        method:'GET',
        success:function(data){
            console.log(data);

        $('#load-images').html('');

    var html = '';
    if(data.length > 0){
        $.each(data, function(i, item) {

        html += '<div class="col-md-2 p-2 text-center">';
        html += `<img src="{{ url('${item.image}')}}" id="${item.id}" style="width:120px;height:120px;pointer-events: none;" class="img-thumbnail mb-1">`;
        html += `<a href="#" onclick="removeImage(${item.id})" class="btn btn-sm btn-danger" style="width:100px;"><i class="fas fa-trash"></i></a>`;
        html += '</div>';

    });
    }else{
        html += '<div class="col-12 text-center">Nenhuma imagem foi enviada ainda.</div>';
    }

    $('#load-images').append(html);


    },
    error:function (xhr) {

        if(xhr.status === 422){
            Swal.fire({
                text: xhr.responseJSON,
                width:300,
                icon: 'warning',
                color: '#007bff',
                confirmButtonColor: "#007bff",
                showClass: {
                    popup: 'animate__animated animate__wobble'
                }
            });
        } else{
            Swal.fire({
                text: xhr.responseJSON,
                width:300,
                icon: 'error',
                color: '#007bff',
                confirmButtonColor: "#007bff",
                showClass: {
                    popup: 'animate__animated animate__wobble'
                }
            });
        }


    }
});

}


function removeImage(id){

    var urlUpload  = "{{ url('admin/properties/images/') }}";

    Swal.fire({
    title: 'Deseja remover esta imagem?',
    text: "Você não poderá reverter isso!",
    icon: 'question',
    showCancelButton: true,
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sim, deletar!'
}).then((result) => {
    if (result.value) {
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                }
            });
        $.ajax({
            url: urlUpload+'/'+id,
            method:'DELETE',
            success:function(data){
                loadImages();
            },
            error:function (xhr) {

                if(xhr.status === 422){
                    Swal.fire({
                        text: xhr.responseJSON,
                        icon: 'warning',
                        showClass: {
                            popup: 'animate__animated animate__wobble'
                        }
                    });
                } else{
                    Swal.fire({
                        text: xhr.responseJSON,
                        icon: 'error',
                        showClass: {
                            popup: 'animate__animated animate__wobble'
                        }
                    });
                }


            }
        });

    }
});



}


</script>




@endsection


@endsection
