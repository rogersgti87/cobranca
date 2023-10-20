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

    <form class="form" enctype="multipart/form-data">

        <input type="hidden" id="url" value="{{url($link)}}">
        <input type="hidden" id="user-id" value="{{ isset($data->id) ? $data->id : '' }}">
        <input type="hidden" id="access-token-wp" value="{{ isset($data->api_access_token_whatsapp) ? $data->api_access_token_whatsapp: '' }}">

        <div class="col-md-12">
        <div class="form-row">

                    <div class="col-md-4 col-sm-12">
                    <fieldset>
                        <legend>Logo</legend>

                        <div class="form-group col-md-12 col-sm-12 text-center">
                            <a class="btn btn-default" style="border:1px solid #333;" id="lfm" data-input="thumbnail" data-preview="holder" style="cursor: pointer">
                                <img src="{{ isset($data->image) && $data->image != null ? url("$data->image") : url('assets/admin/img/thumb.png') }}" id="holder" style="height: 100px;width: 100px;">
                            </a>
                            <input type="hidden" id="thumbnail" name="image" value="{{ isset($data->image) ? $data->image : '' }}">
                        </div>

                    </fieldset>

                    </div>

                    <div class="col-md-12 col-sm-12">
                        <fieldset>
                            <legend>Integrações</legend>
                            @if(isset($data->id))
                                <button type="button" data-toggle="modal" data-target="#modal-whatsapp" id="{{ isset($data->id) ? $data->id : '' }}" data-original-title="NOVO QRCODE" data-tt="tooltip" class="btn btn-success btn-md"> <i class="fa fa-qrcode"></i> WHATSAPP</button>
                                <button type="button" data-toggle="modal" data-target="#modal-inter" data-original-title="Configurar Banco Inter" data-tt="tooltip" class="btn btn-md" style="background:#ff8c00;color:#fff;"><i class="fas fa-university"></i> BANCO INTER</button>
                                <button type="button" data-toggle="modal" data-target="#modal-paghiper" id="{{ isset($data->id) ? $data->id : '' }}" data-original-title="Configurar PagHiper" data-tt="tooltip" class="btn btn-md" style="background:blue;color:#fff;"><i class="fas fa-university"></i> PAG HIPER</button>
                                <button type="button" data-toggle="modal" data-target="#modal-mp" id="{{ isset($data->id) ? $data->id : '' }}" data-original-title="Configurar Mercado Pago" data-tt="tooltip" class="btn btn-md" style="background:#48c5d6;color:#fff;"><i class="fas fa-university"></i> MERCADO PAGO</button>
                            @endif
                        </fieldset>

                        </div>

                    <div class="col-md-12">

                    <fieldset>
                        <legend>Dados do Usuário/Empresa</legend>

                    <div class="form-row">

                    <div class="form-group col-md-2 col-sm-12">
                        <label>CPF/CNPJ</label>
                        <input type="text" class="form-control" name="document" id="" autocomplete="off" required value="{{isset($data->document) ? $data->document : ''}}">
                    </div>

                    <div class="form-group col-md-4 col-sm-12">
                        <label>Nome</label>
                        <input type="text" class="form-control" name="name" id="name" autocomplete="off" required value="{{isset($data->name) ? $data->name : ''}}">
                    </div>


                    <div class="form-group col-md-6 col-sm-12">
                        <label>Empresa</label>
                        <input type="text" class="form-control" name="company" id="company" autocomplete="off" required value="{{isset($data->company) ? $data->company : ''}}">
                    </div>

                    <div class="form-group col-md-6 col-sm-12">
                        <label>E-mail</label>
                        <input type="email" class="form-control" name="email" id="email" autocomplete="off" required value="{{isset($data->email) ? $data->email : ''}}">
                    </div>

                    <div class="form-group col-md-3 col-sm-12">
                        <label>Senha</label>
                        <input type="password" class="form-control" name="password" id="password" autocomplete="off" required value="">
                    </div>

                    <div class="form-group col-md-3 col-sm-12">
                        <label>Confirme a senha</label>
                        <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" autocomplete="off" required value="">
                    </div>

                    <div class="form-group col-md-2 col-sm-12">
                        <label>Cep</label>
                        <input type="text" class="form-control" name="cep" id="cep" autocomplete="off" required value="{{isset($data->cep) ? $data->cep : ''}}">
                    </div>

                    <div class="form-group col-md-8 col-sm-12">
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

                    <div class="form-group col-md-2 col-sm-12">
                        <label>Estado</label>
                        <input type="text" class="form-control" name="state" id="state" autocomplete="off" readonly="readonly" value="{{isset($data->state) ? $data->state : ''}}">
                    </div>

                    <div class="form-group col-md-4 col-sm-12">
                        <label>Complemento</label>
                        <input type="text" class="form-control" name="complement" id="complement" autocomplete="off" required value="{{isset($data->complement) ? $data->complement : ''}}">
                    </div>

                    <div class="form-group col-md-3 col-sm-12">
                        <label>Telefone</label>
                        <input type="text" class="form-control" name="telephone" id="telephone" autocomplete="off" required value="{{isset($data->telephone) ? $data->telephone : ''}}">
                    </div>

                    <div class="form-group col-md-3 col-sm-12">
                        <label>Whatsapp</label>
                        <input type="text" class="form-control" name="whatsapp" id="whatsapp" autocomplete="off" required value="{{isset($data->whatsapp) ? $data->whatsapp : ''}}">
                    </div>

                    <div class="form-group col-md-2 col-sm-12">
                        <label>Status</label>
                        <select class="form-control custom-select" name="status" id="status">
                            <option {{ isset($data->status) && $data->status === 'Ativo' ? 'selected' : '' }} value="Ativo">Ativo</option>
                            <option {{ isset($data->status) && $data->status === 'Inativo' ? 'selected' : '' }} value="Inativo">Inativo</option>
                        </select>
                    </div>

                        </div>

                    </fieldset>

                    <fieldset>
                        <legend>Configurações da Fatura</legend>

                        <div class="form-row">

                    <div class="form-group col-md-4 col-sm-12">
                        <label>Dia para gerar as faturas</label>
                        <select class="form-control custom-select" name="day_generate_invoice" id="day_generate_invoice">
                            <option {{ isset($data->day_generate_invoice) && $data->day_generate_invoice === 15 ? 'selected' : '' }} value="15">15</option>
                            <option {{ isset($data->day_generate_invoice) && $data->day_generate_invoice === 20 ? 'selected' : '' }} value="20">20</option>
                            <option {{ isset($data->day_generate_invoice) && $data->day_generate_invoice === 25 ? 'selected' : '' }} value="25">25</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4 col-sm-12">
                        <label>Enviar fatura no dia que for gerada?</label>
                        <select class="form-control custom-select" name="send_generate_invoice" id="send_generate_invoice">
                            <option {{ isset($data->send_generate_invoice) && $data->send_generate_invoice === 'Não' ? 'selected' : '' }} value="Não">Não</option>
                            <option {{ isset($data->send_generate_invoice) && $data->send_generate_invoice === 'Sim' ? 'selected' : '' }} value="Sim">Sim</option>
                        </select>
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




 <!-- Modal Whatsapp -->
 <div class="modal fade" id="modal-whatsapp">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-success">
            <h5 class="modal-title">Configurar Whatsapp</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        <div class="modal-body-whatsapp">
            <div class="col-md-12">
                <div class="d-flex justify-content-center p-2">
                <a href="#" data-original-title="Gerar Sessão" data-tt="tooltip" class="btn btn-secondary" id="btn-generate-session" data-user-email="{{ isset($data->email) ? $data->email : '' }}"><i class="fa fa-save fa-1x"></i> Gerar Sessão</a>
                </div>
        <div class="table-responsive fixed-solution">
            <table class="table table-hover table-striped table-sm">
                <thead class="thead-light">
                <tr>
                    <th> Sessão</th>
                    <th> Padrão</th>
                    <th> Status</th>
                    <th style="width: 200px;"></th>
                </tr>
                </thead>
                <tbody class="tbodyCustom" id="load-whatsapp-sessions">
                </tbody>
            </table>
        </div>

            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->


   <!-- Modal Banco Inter -->
 <div class="modal fade" id="modal-inter">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" style="background:#ff8c00;color:#fff;">
            <h5 class="modal-title">Configurar Banco Inter</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        <div class="modal-body-inter">
            <div class="col-md-12">
                <form id="form-inter" enctype="multipart/form-data">
            <div class="form-row">

                <input type="hidden" class="form-control" name="inter_host" id="inter_host" autocomplete="off" value="{{isset($data->inter_host) ? $data->inter_host : 'https://cdpj.partners.bancointer.com.br/'}}">

                <div class="form-group col-md-4 col-sm-12">
                    <label>Client ID</label>
                    <input type="text" class="form-control" name="inter_client_id" id="inter_client_id" autocomplete="off" value="{{isset($data->inter_client_id) ? $data->inter_client_id : ''}}">
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label>Client Secret</label>
                    <input type="text" class="form-control" name="inter_client_secret" id="inter_client_secret" autocomplete="off" value="{{isset($data->inter_client_secret) ? $data->inter_client_secret : ''}}">
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label>Chave PIX</label>
                    <input type="text" class="form-control" name="inter_chave_pix" id="inter_chave_pix" autocomplete="off" value="{{isset($data->inter_chave_pix) ? $data->inter_chave_pix : ''}}">
                </div>

                <div class="form-group col-md-12 col-sm-12">
                    <label>Scope</label>
                    <input type="text" class="form-control" name="inter_scope" id="inter_scope" autocomplete="off" value="{{isset($data->inter_scope) ? $data->inter_scope : 'boleto-cobranca.read boleto-cobranca.write extrato.read cob.write cob.read cobv.write cobv.read pix.write pix.read webhook.read webhook.write'}}">
                </div>

                <div class="form-group col-md-6 col-sm-12">
                    <label>Arquivo CRT <span style="font-size:10px !important;" class="text-xs badge badge-{{isset($data->inter_crt_file) && $data->inter_crt_file != null ? 'success' : 'danger'}}">{{isset($data->inter_crt_file) && $data->inter_crt_file != null ? 'Arquivo enviado' : 'Arquivo não enviado'}}</span></label>
                    <input type="file" class="form-control" name="inter_crt_file" id="inter_crt_file" autocomplete="off" value="">
                </div>

                <div class="form-group col-md-6 col-sm-12">
                    <label>Arquivo KEY <span style="font-size:10px !important;" class="text-xs badge badge-{{isset($data->inter_key_file) && $data->inter_key_file != null ? 'success' : 'danger'}}">{{isset($data->inter_key_file) && $data->inter_key_file != null ? 'Arquivo enviado' : 'Arquivo não enviado'}}</span></label>
                    <input type="file" class="form-control" name="inter_key_file" id="inter_key_file" autocomplete="off" value="">
                </div>

                {{-- <div class="form-group col-md-4 col-sm-12">
                    <label>Arquivo CRT WebHook <span style="font-size:10px !important;" class="text-xs badge badge-{{isset($data->inter_crt_file_webhook) && $data->inter_crt_file_webhook != null ? 'success' : 'danger'}}">{{isset($data->inter_crt_file_webhook) && $data->inter_crt_file_webhook != null ? 'Arquivo enviado' : 'Arquivo não enviado'}}</span></label>
                    <input type="file" class="form-control" name="inter_crt_file_webhook" id="inter_crt_file_webhook" autocomplete="off" value="">
                </div> --}}

                    <input type="hidden" class="form-control" name="inter_webhook_url_billet" id="inter_webhook_url_billet" autocomplete="off" value="{{isset($data->inter_webhook_url_billet) ? $data->inter_webhook_url_billet : 'https://cobrancasegura.com.br/webhook/intermediumbillet'}}">
                    <input type="hidden" class="form-control" name="inter_webhook_url_billet_pix" id="inter_webhook_url_billet_pix" autocomplete="off" value="{{isset($data->inter_webhook_url_billet_pix) ? $data->inter_webhook_url_billet_pix : 'https://cobrancasegura.com.br/webhook/intermediumbilletpix'}}">
                    <input type="hidden" class="form-control" name="inter_webhook_url_pix" id="inter_webhook_url_pix" autocomplete="off" value="{{isset($data->inter_webhook_url_pix) ? $data->inter_webhook_url_pix : 'https://cobrancasegura.com.br/webhook/intermediumpix'}}">
                </div>
                </form>

            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-success" id="btn-save-inter"><i class="fa fa-check"></i> Salvar</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->



    <!-- Modal Pag Hiper -->
 <div class="modal fade" id="modal-paghiper">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" style="background:blue;color:#fff;">
            <h5 class="modal-title">Configurar Pag Hiper</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        <div class="modal-body-paghiper">
            <div class="col-md-12">
                <form id="form-paghiper" enctype="multipart/form-data">
            <div class="form-row">

                <div class="form-group col-md-12 col-sm-12">
                    <label>Token</label>
                    <input type="text" class="form-control" name="token_paghiper" id="token_paghiper" autocomplete="off" required value="{{isset($data->token_paghiper) ? $data->token_paghiper : ''}}">
                </div>

                <div class="form-group col-md-12 col-sm-12">
                    <label>Key</label>
                    <input type="text" class="form-control" name="key_paghiper" id="key_paghiper" autocomplete="off" required value="{{isset($data->key_paghiper) ? $data->key_paghiper : ''}}">
                </div>

                </div>
                </form>

            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-success" id="btn-save-ph"><i class="fa fa-check"></i> Salvar</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->


    <!-- Modal MP -->
 <div class="modal fade" id="modal-mp">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" style="background:#48c5d6;color:#fff;">
            <h5 class="modal-title">Configurar Mercado Pago</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        <div class="modal-body-mp">
            <div class="col-md-12">
                <form id="form-mp" enctype="multipart/form-data">
            <div class="form-row">

                <div class="form-group col-md-12 col-sm-12">
                    <label>Access Token</label>
                    <input type="text" class="form-control" name="access_token_mp" id="access_token_mp" autocomplete="off" required value="{{isset($data->access_token_mp) ? $data->access_token_mp : ''}}">
                </div>

                </div>
                </form>

            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-success" id="btn-save-mp"><i class="fa fa-check"></i> Salvar</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->


@section('scripts')


<script>

$(window).on("load", function(){

@if(isset($data))
    loadWhatsapp();
@endif

});


$("#btn-generate-session").on("click", function(e) {

    if(confirm("Deseja gerar uma nova sessão?")){
        var email = $(this).data('user-email');
    //$(".modal-body-whatsapp").html(`<img src="${data.qrcode}" style="width:500px; height:500px;">`);
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                }
            });
            $.ajax({
                url: `https://zapestrategico.com.br/api/create-session/${email}`,
                method:'POST',
                data:{company:$('#company').val(),password:$('#password').val(),user_id_cobseg:$('#user-id').val()},
            success:function(data){
                    console.log(data);
                    Swal.fire({
                        width:350,
                        title: "<h5 style='color:#007bff'>Leia o QRCODE abaixo</h5>",
                        icon: 'success',
                        showConfirmButton: true,
                        showClass: {
                            popup: 'animate__animated animate__backInUp'
                        },
                        allowOutsideClick: false,
                        html:
                        `<div class="text-center"><img src="${data.qrcode}" style="width:250px; height:250px;"></div>`
                    });
                    loadWhatsapp();
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

        })


    }else{
        return false;
    }

    });
//End create session whatsapp

$(document).on('click', '#btn-delete-whatsapp', function(e) {

if(confirm("Deseja remover esta sessão?")){
    var access_token = $(this).data('access-token');

        e.preventDefault();
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                }
            });
        $.ajax({
            url: `https://zapestrategico.com.br/api/delete-session/${access_token}`,
            method:'DELETE',
        success:function(data){
            console.log(data);
                Swal.fire({
                    width:350,
                    title: "<h5 style='color:#007bff'>Sessão Removida com sucesso!</h5>",
                    icon: 'success',
                    showConfirmButton: true,
                    showClass: {
                        popup: 'animate__animated animate__backInUp'
                    },
                    allowOutsideClick: false
                });
                loadWhatsapp();
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

    })


}else{
    return false;
}

});
//End edit session whatsapp

$(document).on('click', '#btn-default-whatsapp', function(e) {

    var url = '{{url("admin/user-default-whatsapp")}}';

if(confirm("Deseja definir esta sessão como padrão?")){
    var access_token = $(this).data('access-token');

        e.preventDefault();
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                }
            });
        $.ajax({
            url: `${url+'/'+access_token}`,
            method:'POST',
        success:function(data){
            console.log(data);
                Swal.fire({
                    width:350,
                    title: "<h5 style='color:#007bff'>Sessão definida como padrão!</h5>",
                    icon: 'success',
                    showConfirmButton: true,
                    showClass: {
                        popup: 'animate__animated animate__backInUp'
                    },
                    allowOutsideClick: false
                });
                //loadWhatsapp();
                location.reload();

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

    })


}else{
    return false;
}

});
//End default session whatsapp

function loadWhatsapp(){

var user_id = $('#user-id').val();
var access_token = $('#access-token-wp').val();

$.ajax({
            url: `https://zapestrategico.com.br/api/list-sessions/${user_id}`,
            method: 'GET',
            success:function(data){
                console.log(data);
                $('#load-whatsapp-sessions').html('');
                var html = '';
                $.each(data, function(i, item) {
                    html += '<tr>';
                    html += `<td>${item.session}</td>`;
                    html += `<td>${item.access_token == access_token ? 'Sim' : 'Não'}</td>`;
                    html += `<td><label class="badge badge-${item.status == 'Conectado' ? 'success' : 'danger'}">${item.status}</label></td>`;
                    html += `<td>
                        <a href="#" data-original-title="Definir como padrão" id="btn-default-whatsapp" data-access-token="${item.access_token}" data-tt="tooltip" class="btn btn-success btn-xs"> <i class="fa fa-list"></i> Definir como padrão</a>
                        <a href="#" data-original-title="Deletar" id="btn-delete-whatsapp" data-access-token="${item.access_token}" data-tt="tooltip" class="btn btn-danger btn-xs"> <i class="fa fa-list"></i> Deletar</a>
                        </td>`;
                    html += '</tr>';

                });
                $('#load-whatsapp-sessions').append(html);

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



</script>


<script>

    $('#lfm').filemanager('image');

    //Save data
        $(document).on('click', '#btn-salvar', function(e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                }
            });
            var url_act = "{{Request::get('act')}}";
            if(url_act == 'edit'){
                var url = "{{ url("$linkUpdate") }}";
                var method = 'POST';
            }else{
                var url = "{{ url("$linkStore") }}";
                var method = 'POST';
            }

            var data = new FormData($('.form')[0]);

            //var data = $('.form').serialize();
            console.log(url);

            $.ajax({
                url: url,
                data:data,
                method:method,
                processData: false,
                contentType: false,
                success:function(data){
                    console.log(data);
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
                        '<a href="{{url($linkFormAdd)}}" data-original-title="Novo" data-toggle="tooltip" class="btn btn-secondary btn-md"> <i class="fa fa-plus"></i> Novo</a>  ' +
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



//Save Inter
$(document).on('click', '#btn-save-inter', function(e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                }
            });

            var data = new FormData($('#form-inter')[0]);

            $.ajax({
                url: '{{url('admin/user-inter')}}',
                data:data,
                method:'POST',
                processData: false,
                contentType: false,
                success:function(data){
                    console.log(data);
                    Swal.fire({
                        width:350,
                        title: "<h5 style='color:#007bff'>" + data + "</h5>",
                        icon: 'success',
                        showConfirmButton: true,
                        showClass: {
                            popup: 'animate__animated animate__backInUp'
                        },
                        allowOutsideClick: false,
                    });
                    $('#modal-inter').modal('hide');
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



        //Save PH
$(document).on('click', '#btn-save-ph', function(e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                }
            });

            var data = new FormData($('#form-paghiper')[0]);

            $.ajax({
                url: '{{url('admin/user-ph')}}',
                data:data,
                method:'POST',
                processData: false,
                contentType: false,
                success:function(data){
                    Swal.fire({
                        width:350,
                        title: "<h5 style='color:#007bff'>" + data + "</h5>",
                        icon: 'success',
                        showConfirmButton: true,
                        showClass: {
                            popup: 'animate__animated animate__backInUp'
                        },
                        allowOutsideClick: false,
                    });
                    $('#modal-paghiper').modal('hide');
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


          //Save PH
$(document).on('click', '#btn-save-mp', function(e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                }
            });

            var data = new FormData($('#form-mp')[0]);

            $.ajax({
                url: '{{url('admin/user-mp')}}',
                data:data,
                method:'POST',
                processData: false,
                contentType: false,
                success:function(data){
                    Swal.fire({
                        width:350,
                        title: "<h5 style='color:#007bff'>" + data + "</h5>",
                        icon: 'success',
                        showConfirmButton: true,
                        showClass: {
                            popup: 'animate__animated animate__backInUp'
                        },
                        allowOutsideClick: false,
                    });
                    $('#modal-mp').modal('hide');
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

    </script>




@endsection


@endsection
