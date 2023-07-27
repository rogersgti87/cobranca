imovel
@extends('front.template.index')

@section('content')

    <!--Properties Section-->
    <section class="properties-section-two">
    	<div class="auto-container">


            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item active" role="presentation">
                                <a href="#comprar-tab-pane" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="true" style="color:#fff !important;background:#1e1e1e7a !important;">Filtrar</a>
                            </li>
                        </ul>
                        <div class="tab-content py-3 px-5" id="myTabContent" style="background:#1e1e1e7a !important;">
                            <div class="tab-pane fade active in" id="comprar-tab-pane" role="tabpanel" aria-labelledby="comprar-tab" tabindex="0">
                                <!-- conteudo -->
                                <div class="row">

                                    <div class="col-xs-12 col-md-12" style="margin-bottom:2px;">
                                        <label style="color:#fff;">Localidade</label>
                                            <select class="form-control locale-comprar" name="local_id" id="locale-comprar" style="width:100%"></select>
                                    </div><!-- col -->

                                    <div class="col-xs-12 col-md-12" style="margin-bottom:2px;">
                                        <label style="color:#fff;">Tipo</label>
                                        <select class="form-control type-comprar" id="type">
                                            <option selected="" disabled="">Tipo</option>
                                            <option value="Casa">Casa</option>
                                            <option value="Apartamento">Apartamento</option>
                                        </select>
                                    </div><!-- col -->

                                    <div class="col-xs-12 col-md-12" style="margin-bottom:2px;">
                                        <label style="color:#fff;">Preço</label>
                                        <select class="form-control price-comprar" id="range_price" aria-label="Floating label select example">
                                            <option selected="" disabled="">Preço</option>
                                            <option value="0-999">R$0,00 - R$999,00</option>
                                            <option value="999-1999">R$999,00 - R$1.999,00</option>
                                            <option value="1999-9999999999">Acima de R$1.999,00</option>
                                        </select>
                                    </div><!-- col -->
                                    <div class="col-xs-6 col-md-6" style="margin-bottom:2px;">
                                        <label style="color:#fff;">Quartos</label>
                                        <select class="form-control qtd-bathrooms" id="qtd-bathrooms" aria-label="Floating label select example">
                                            <option selected="" disabled="">0</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                            <option value="6">6</option>
                                            <option value="7">7</option>
                                        </select>
                                    </div><!-- col -->
                                    <div class="col-xs-6 col-md-6" style="margin-bottom:2px;">
                                        <label style="color:#fff;">Banheiros</label>
                                        <select class="form-control qtd-bathrooms" id="qtd-bathrooms" aria-label="Floating label select example">
                                            <option selected="" disabled="">0</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                            <option value="6">6</option>
                                            <option value="7">7</option>
                                        </select>
                                    </div><!-- col -->
                                    <div class="col-xs-12 col-md-12">
                                        <button type="buttom" class="btn btn-primary" onclick="searchProperty('Compra');"><i class="fa fa-search"></i> Buscar</button>
                                    </div>
                                </div>
                                <!-- conteudo -->
                            </div>
                        </div>
                    </div>

                    <div class="col-md-9">
                        <div class="row clearfix" id="load-properties"></div>
                    </div>

                </div>
            </div>



        </div>
    </section>


    @section('scripts')

    <script>

        $(document).ready(function(){

            searchProperty();

        });


        $('#locale-comprar,#locale-alugar,#locale-temporada').select2({
                theme: 'bootstrap4',
                placeholder: "Bairro...",
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


            function searchProperty(finality = null){

                var data = {};

                if(finality == 'Compra'){
                    data = {
                        finality:   'Compra',
                        type:       $('.type-comprar').val(),
                        locale:     $('.locale-comprar').val(),
                        price:      $('.price-comprar').val(),
                    }
                }

                if(finality == 'Aluguel'){
                    data = {
                        finality:   'Aluguel',
                        type:       $('.type-alugar').val(),
                        locale:     $('.locale-alugar').val(),
                        price:      $('.price-alugar').val(),
                    }
                }

                if(finality == 'Temporada'){
                    data = {
                        finality:   'Temporada',
                        type:       $('.type-temporada').val(),
                        locale:     $('.locale-temporada').val(),
                        price:      $('.price-temporada').val(),
                    }
                }

                $.ajax({
                url: "{{url('/getproperties')}}",
                method: 'GET',
                data: data,
                success:function(data){
                    console.log(data);
                    $('#load-properties').html('');
                    var html = '';
                    $.each(data, function(i, item) {
                        html +=      '<div class="property-block-grid col-lg-6 col-md-6 col-xs-12">';
                        html +=     '<div class="inner-box">';
                        html +=     '<div class="image-box">';
                        html +=     `<figure class="image"><a href="{{url('/imovel/${item.slug}')}}"><img src="{{ url('${item.image_original}') }}" alt=""></a></figure>`;
                        html +=     `{{-- <div class="ribbon">Oferta</div> --}}`;
                        html +=     `<a href="{{url('/imovel/${item.slug}')}}" class="read-more-link">Visualizar</a>`;
                        html +=     '</div>';
                        html +=     '<div class="lower-content">';
                        html +=     `<h3><a href="{{url('/imovel/${item.slug}')}}">${item.name}</a></h3>`;
                        html +=     `<div class="price">R$ ${item.price}</div>`;
                        html +=     `<div class="desc-text">Braga</div>`;
                        html +=     '<ul class="specs-list clearfix">';
                        html +=     `<li><div class="outer"><div class="icon"><span class="fa fa-expand"></span></div><div class="info">${item.area} m2</div></div></li>`;
                        html +=     `<li><div class="outer"><div class="icon"><span class="fa fa-bed"></span></div><div class="info">${item.bedrooms} ${item.bedrooms > 1 ? 'Quartos' : 'Quarto'}</div></div></li>`;
                        html +=     `<li><div class="outer"><div class="icon"><span class="flaticon-shape"></span></div><div class="info">${item.bathrooms} ${item.bathrooms > 1 ? 'Banheiros' : 'Banheiro' }</div></div></li>`;
                        html +=     `<li><div class="outer"><div class="icon"><span class="fa fa-car"></span></div><div class="info">${item.garages} ${item.garages > 1 ? 'Garagens' : 'Garagem'}</div></div></li>`;
                        html +=     '</ul>';
                        html +=     '</div>';
                        html +=     '</div>';
                        html +=     '</div>';
                    });
                    $('#load-properties').append(html);

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


    @endsection

    @endsection
