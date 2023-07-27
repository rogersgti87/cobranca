@extends('front.template.index')

@section('content')

 <!--Property Info Section-->
 <section class="property-info-section">
    <div class="auto-container">

        <div class="row clearfix">
            <!--Left Column-->
            <div class="left-column col-md-6 col-sm-12 col-xs-12">
                <!--Product Carousel-->
                <div class="product-carousel-outer">
                    <!--Product image Carousel-->
                    <ul class="prod-image-carousel">
                        @foreach($property_image as $image)
                            <li><figure class="image"><a class="lightbox-image option-btn" data-fancybox-group="example-gallery" href="{{ url("$image->image") }}" title="Image Title Here"><img src="{{ url("$image->image") }}" alt="" style="width:100%;height:400px;"></a></figure></li>
                        @endforeach
                    </ul>

                    <!--Product Thumbs Carousel-->
                    <div class="prod-thumbs-carousel">
                        @foreach($property_image as $image)
                            <div class="thumb-item"><figure class="thumb-box"><img src="{{ url("$image->image") }}" style="width:150px;height:150px;" alt=""></figure></div>
                        @endforeach
                    </div>

                </div><!--End Product Carousel-->

                <hr>
                <div class="big-title"><h4>Descrição do imóvel</h4></div>
                <hr>
                <div class="text">{!! $property->description !!}</div>

            </div><!--Left Column-->

            <!--Right Column-->
            <div class="right-column col-md-6 col-sm-12 col-xs-12">
                <!--Title Style One-->
                <div class="title-style-one extended left-aligned">
                    <div class="title"><h2>{{ $property->name }}</h2></div>
                </div>

                <div class="links">
                    <a href="https://api.whatsapp.com/send?phone=5522992375388&text=ol%C3%A1,%20me%20interessei%20por%20este%20im%C3%B3vel: {{request()->url()}}" target="_blank" class="theme-btn btn-style-two" style="display: block; text-align:center;font-size:16px;padding:10px;border-radius:7px;">Falar com corretor <span class="fa fa-whatsapp" style="font-size: 2rem;"></span></a>
                </div>
                <div class="content">
                    <hr>
                    <div class="big-title"><h4>Valores</h4></div>
                    <hr>
                        Valor: R$ {{ number_format($property->price,2,',','.') }}<br>
                        Condomínio: R$ {{ number_format($property->price_condominium,2,',','.')}}
                    <hr>
                    <div class="big-title"><h4>Características do imóvel</h4></div>
                    <hr>
                    <div class="row clearfix">
                       @foreach($characteristics->chunk(5) as $c)
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <ul class="list-style-one">
                                @foreach($c as $cname)
                                <li>{{ $cname->name }}</li>
                                @endforeach
                            </ul>
                        </div>
                       @endforeach
                    </div>
                    <hr>
                    <!--Specs Listing-->
                                <div class="specs-listing">
                                	<div class="row clearfix">
                                    	<!--Spect COlumn-->
                                        <div class="spec-column col-md-3 col-sm-4 col-xs-12">
                                        	<h5><span class="icon flaticon-double-king-size-bed"></span> {{ $property->bedrooms }} {{ $property->bedrooms > 1 ? 'Quartos' : 'Quarto' }}</h5>
                                        </div>
                                        <!--Spect COlumn-->
                                        <div class="spec-column col-md-3 col-sm-4 col-xs-12">
                                        	<h5><span class="icon flaticon-bathtub"></span> {{ $property->bathrooms }} {{ $property->bathrooms > 1 ? 'Banheiros' : 'Banheiro' }}</h5>
                                        </div>
                                        <!--Spect COlumn-->
                                        <div class="spec-column col-md-3 col-sm-4 col-xs-12">
                                        	<h5><span class="icon fa fa-car"></span> {{ $property->garages }} {{ $property->garages > 1 ? 'Garagens' : 'Garagem' }}</h5>
                                        </div>
                                         <!--Spect COlumn-->
                                         <div class="spec-column col-md-3 col-sm-4 col-xs-12">
                                        	<h5><span class="icon fa fa-expand"></span> {{ $property->area }} m2</h5>
                                        </div>
                                    </div>
                    </div><!--End Specs Listing-->
                    <hr>
                    <div class="big-title"><h4>Vídeo</h4></div>
                    <hr>
                    <div class="video-container">
                        {!! $property->youtube !!}
                    </div>
                    <hr>
                    <div class="big-title"><h4>Localização</h4></div>
                    <hr>
                    <div class="responsive-map-container">
                        {!! $property->google_maps !!}
                    </div>

                </div>

            </div><!--Right Column-->
        </div>

    </div>
</section>


@section('scripts')




@endsection

@endsection
