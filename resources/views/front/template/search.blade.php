 <!--Main Banner Slider / Extended-->
 <section class="main-banner-slider extended">
    <div class="slider-container">
        <div class="main-slider">


            <!--Side Item-->
            <div class="slide-item" style="background-image:url({{url('/img/header1.jpg')}});">
                <div class="overlay-layer"></div>

                <div class="auto-container">
                    <div class="content-box">


                        <h2>Viva o paraíso o ano inteiro<br> Cabo Frio, a cidade dos sonhos!</h2>
                        {{-- <div class="text-content">Imóveis para Comprar, Alugar e para passar Férias </div> --}}
                        <br>
                        <br>
                    </div>


                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a href="#comprar-tab-pane" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="false">Comprar</a>
                                    </li>
                                    <li class="nav-item active" role="presentation">
                                        <a href="#alugar-tab-pane" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="true">Alugar</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a href="#temporada-tab-pane" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="false">Temporada</a>
                                    </li>
                                </ul>
                                <div class="tab-content py-3 px-5" id="myTabContent">
                                    <div class="tab-pane fade" id="comprar-tab-pane" role="tabpanel" aria-labelledby="comprar-tab" tabindex="0">
                                        <!-- conteudo -->
                                        <div class="row">
                                            <form method="get" action="{{url('/')}}">
                                            <input type="hidden" name="finality" value="Compra">

                                            <div class="col-xs-12 col-md-2">
                                                <select name="type" class="form-control type-comprar" id="type" aria-label="Floating label select example">
                                                    <option selected="" disabled="">Tipo</option>
                                                    <option value="Casa">Casa</option>
                                                    <option value="Apartamento">Apartamento</option>
                                                </select>
                                            </div><!-- col -->
                                            <div class="col-xs-12 col-md-5">
                                                    <select class="form-control locale-comprar" name="local_id" id="locale-comprar" style="width:100%"></select>
                                            </div><!-- col -->
                                            <div class="col-xs-12 col-md-3">
                                                <select name="price" class="form-control price-comprar" id="range_price" aria-label="Floating label select example">
                                                    <option selected="" disabled="">Preço</option>
                                                    <option value="0-999">R$0,00 - R$999,00</option>
                                                    <option value="999-1999">R$999,00 - R$1.999,00</option>
                                                    <option value="1999-9999999999">Acima de R$1.999,00</option>
                                                </select>
                                            </div><!-- col -->
                                            <div class="col-xs-12 col-md-2">
                                                <button type="submit" class="btn btn-primary" id="filtro-compra"><i class="fa fa-search"></i> Buscar</button>
                                            </div>
                                        </form>
                                        </div>
                                        <!-- conteudo -->
                                    </div>
                                    <div class="tab-pane fade active in" id="alugar-tab-pane" role="tabpanel" aria-labelledby="alugar-tab" tabindex="0">
                                        <!-- conteudo -->
                                        <div class="row">
                                            <form method="get" action="{{url('/')}}">
                                                <input type="hidden" name="finality" value="Aluguel">
                                            <div class="col-xs-12 col-md-2">
                                                <select name="type" class="form-control type-alugar" id="type" aria-label="Floating label select example">
                                                    <option selected="" disabled="">Tipo</option>
                                                     <option value="Casa">Casa</option>
                                                    <option value="Apartamento">Apartamento</option>
                                                </select>
                                            </div><!-- col -->
                                            <div class="col-xs-12 col-md-5">
                                                <select class="form-control locale-comprar" name="local_id" id="locale-alugar" style="width:100%"></select>
                                            </div><!-- col -->
                                            <div class="col-xs-12 col-md-3">
                                                <select name="price" class="form-control" id="range_price price-alugar" aria-label="Floating label select example">
                                                    <option selected="" disabled="">Preço</option>
                                                    <option value="0-999">R$0,00 - R$999,00</option>
                                                    <option value="999-1999">R$999,00 - R$1.999,00</option>
                                                    <option value="1999-9999999999">Acima de R$1.999,00</option>
                                                </select>
                                            </div><!-- col -->

                                            <div class="col-xs-12 col-md-2">
                                                <button type="subimit" class="btn btn-primary" onclick="searchProperty();"><i class="fa fa-search"></i> Buscar</button>
                                            </div>
                                            </form>
                                        </div>

                                        <!-- conteudo -->
                                    </div>
                                    <div class="tab-pane fade" id="temporada-tab-pane" role="tabpanel" aria-labelledby="temporada-tab" tabindex="0">
                                        <!-- conteudo -->
                                        <div class="row">
                                            <form method="get" action="{{url('/')}}">
                                                <input type="hidden" name="finality" value="Temporada">
                                            <div class="col-xs-12 col-md-2">
                                                <select name="type" class="form-control type-temporada" id="type" aria-label="Floating label select example">
                                                    <option selected="" disabled="">Tipo</option>
                                                   <option value="Casa">Casa</option>
                                                    <option value="Apartamento">Apartamento</option>
                                                </select>
                                            </div><!-- col -->
                                            <div class="col-xs-12 col-md-5">
                                                <select class="form-control locale-comprar" name="local_id" id="locale-temporada" style="width:100%"></select>
                                            </div><!-- col -->
                                            <div class="col-xs-12 col-md-3">
                                                <select name="price" class="form-control price-temporada" id="range_price" aria-label="Floating label select example">
                                                    <option selected="" disabled="">Preço</option>
                                                    <option value="0-999">R$0,00 - R$999,00</option>
                                                    <option value="999-1999">R$999,00 - R$1.999,00</option>
                                                    <option value="1999-9999999999">Acima de R$1.999,00</option>
                                                </select>
                                            </div><!-- col -->
                                            <div class="col-xs-12 col-md-2">
                                                <button type="submit" class="btn btn-primary" onclick="searchProperty();"><i class="fa fa-search"></i> Buscar</button>
                                            </div>
                                            </form>
                                        </div>
                                        <!-- conteudo -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>



        </div>
    </div>
</section>
