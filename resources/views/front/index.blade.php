@extends('front.template.index')

@section('content')

    <!--Properties Section-->
    <section class="properties-section-two">
    	<div class="auto-container">

            <div class="row clearfix" id="load-properties">
            </div>

        </div>
    </section>


    @section('scripts')


    <script>

        $(document).ready(function(){

            searchProperty();

        });




            function searchProperty(){

                const queryString = window.location.search;
                const urlParams = new URLSearchParams(queryString);

                // var data = {};

                // if(finality == 'Compra'){
                //     data = {
                //         finality:   'Compra',
                //         type:       $('.type-comprar').val(),
                //         locale:     $('.locale-comprar').val(),
                //         price:      $('.price-comprar').val(),
                //     }
                // }

                // if(finality == 'Aluguel'){
                //     data = {
                //         finality:   'Aluguel',
                //         type:       $('.type-alugar').val(),
                //         locale:     $('.locale-alugar').val(),
                //         price:      $('.price-alugar').val(),
                //     }
                // }

                // if(finality == 'Temporada'){
                //     data = {
                //         finality:   'Temporada',
                //         type:       $('.type-temporada').val(),
                //         locale:     $('.locale-temporada').val(),
                //         price:      $('.price-temporada').val(),
                //     }
                // }

                $.ajax({
                url: "{{url('/getproperties')}}",
                method: 'GET',
                data: {
                        finality:   urlParams.get('finality'),
                        type:       urlParams.get('type'),
                        locale:     urlParams.get('locale_id'),
                        price:      urlParams.get('price')
                },
                success:function(data){
                    console.log(data);
                    $('#load-properties').html('');
                    var html = '';
                    $.each(data, function(i, item) {
                        html +=      '<div class="property-block-grid col-lg-4 col-md-6 col-xs-12">';
                        html +=     '<div class="inner-box">';
                        html +=     '<div class="image-box">';
                        html +=     `<figure class="image"><a href="{{url('/imovel/${item.slug}')}}"><img src="{{ url('${item.image_original}') }}" alt=""></a></figure>`;
                        html +=     `{{-- <div class="ribbon">Oferta</div> --}}`;
                        html +=     `<a href="{{url('/imovel/${item.slug}')}}" class="read-more-link">Visualizar</a>`;
                        html +=     '</div>';
                        html +=     '<div class="lower-content">';
                        html +=     `<h3><a href="{{url('/imovel/${item.slug}')}}">${item.name}</a></h3>`;
                        html +=     `<div class="price">R$ ${item.price}</div>`;
                        html +=     `<div class="desc-text">${item.local}</div>`;
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
