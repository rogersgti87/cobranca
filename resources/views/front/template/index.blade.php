<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Malu Imóveis</title>
<!-- Stylesheets -->
<link href="{{ url('assets/front/css/bootstrap.css') }}" rel="stylesheet">
<link href="{{ url('assets/front/css/jquery.mCustomScrollbar.min.css') }}" rel="stylesheet">
<link href="{{ url('assets/front/css/revolution-slider.css') }}" rel="stylesheet">
<link href="{{ url('assets/front/css/jquery-ui.css') }}" rel="stylesheet">
<link href="{{ url('assets/front/css/style.css?') }}{{rand(1,999)}}" rel="stylesheet">
<!--Favicon-->
<link rel="shortcut icon" href="{{ url('assets/front/images/favicon.ico') }}" type="image/x-icon">
<link rel="icon" href="{{ url('assets/front/images/favicon.ico') }}" type="image/x-icon">
<!-- Responsive -->
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<link href="{{ url('assets/front/css/responsive.css?') }}{{rand(1,999)}}" rel="stylesheet">
<!--[if lt IE 9]><script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script><![endif]-->
<!--[if lt IE 9]><script src="js/respond.js"></script><![endif]-->

<!-- Select2 -->
<link rel="stylesheet" href="{{url('assets/admin/plugins/select2/css/select2.min.css')}}">
<link rel="stylesheet" href="{{url('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">

<style>
    .nav-tabs{
        justify-content: center;
        border: 0;
    }

    .nav-tabs>li>a, .nav>li>a:focus, .nav>li>a:hover {
        background: #1e1e1e7a;
        color: #fff;
        border: 0 !important;
        box-shadow: none !important;
        border-radius: 0;
        margin: 0;
        padding: 15px 40px;
    }

    .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover {
        background: #003f94;
        color: #FFF;
        border: 0 !important;
    }


    .nav-tabs + .tab-content {
        background: #003f94;
        padding: 20px 10px;
        display: block;
        margin: 0 0 20px 0;
    }

    .nav-tabs + .tab-content .form-control {
        border-radius: 0 !important;
        padding: 10px 9px;
        height: auto;
        border: 0;
    }

    @media screen and (max-width:992px){
    .nav-tabs + .tab-content .form-control{
        margin-bottom:10px;
    }

    .nav-tabs>li>a, .nav>li>a:focus, .nav>li>a:hover{
        padding:10px 20px;
    }
}

    .nav-tabs + .tab-content .btn.btn-primary {
        padding: 10px 22px;
        font-weight: bold;
        background: #ff6c00;
        border: 1px solid rgb(0 0 0 / 29%);
        transition: 0.3s all ease;
    }

    .nav-tabs + .tab-content .btn.btn-primary:hover {
        background: #dd5e01;
    }
    @media screen and (max-width:767px){
    .nav-tabs{
        display: flex !important;
    }

    .nav-tabs li {
        flex: 1 1 auto!important;
        text-align: center;
    }
}



@media screen and (max-width:390px){
    .nav-tabs{
        display: block !important;
    }

    .nav-tabs li {
        width:100%;
        text-align: left;
    }
}

.select2-selection__rendered {
    line-height: 44px !important;
}
.select2-container .select2-selection--single {
    height: 44px !important;
}
.select2-selection__arrow {
    height: 44px !important;
}
</style>


</head>

<body>
<div class="page-wrapper">

    <!-- Preloader -->
    <div class="preloader"></div>

 <!-- Main Header / Header Style Two-->
 <header class="main-header header-style-two">

    <!--Header-Upper-->
    <div class="header-upper">
        <div class="auto-container">
            <div class="clearfix">

                <div class="logo-outer">
                    <div class="logo"><a href="{{ url('/') }}"><img src="{{ url('img/logo.png') }}" style="max-width: 200px;" alt="Malu Imóveis" title="Malu Imóveis"></a></div>
                </div>

                <!-- Hidden Nav Toggler -->
                <div class="nav-toggler">
                    <button class="hidden-bar-opener"><span class="line big-line"></span><span class="line small-line"></span><span class="line big-line"></span><span class="line small-line"></span></button>
                </div><!-- / Hidden Nav Toggler -->

            </div>
        </div>
    </div>

</header>
<!--End Main Header -->

  <!-- Hidden Navigation Bar -->
  <section class="hidden-bar right-align">

    <div class="hidden-bar-closer">
        <button class="btn"><i class="fa fa-close"></i></button>
    </div>

    <!-- Hidden Bar Wrapper -->
    <div class="hidden-bar-wrapper">

        <!-- .logo -->
        <div class="logo text-center">
            <a href="{{ url('/') }}"><img src="{{ url('img/logo.png') }}" style="max-width: 200px;filter: brightness(50%);" alt="Malu Imóveis" title="Malu Imóveis"></a>
        </div><!-- /.logo -->

        <!-- .Side-menu -->
        <div class="side-menu">
            <!-- .navigation -->
            <ul class="navigation clearfix">
                <li><a href="{{ url('/') }}">Home</a></li>
                {{-- <li><a href="{{ url('/imoveis') }}">Imóveis</a></li> --}}
                <li><a href="#contato">Contato</a></li>
            </ul>
        </div><!-- /.Side-menu -->

        <div class="social-icons">
            <ul>
                <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                <li><a href="#"><i class="fa fa-instagram"></i></a></li>
            </ul>
        </div>

    </div><!-- / Hidden Bar Wrapper -->
</section>
<!-- / Hidden Bar -->



@include('front.template.search')


@yield('content')



  <!--Contact Section Two-->
  <section class="contact-section-two" id="contato">
    <div class="auto-container">

        <div class="title-box">
            <h2>Fale conosco</h2>
            <div class="text">Para falar com a gente, tirar dúvidas ou receber
                informações sobre imóveis, basta preencher o
                formulário abaixo. </div>
        </div>

        <div class="info-container">
            <div class="row clearfix">

                <!--Info Block-->
                <div class="info-block col-md-6 col-sm-6 col-xs-12">
                    <div class="inner-box">
                        <div class="inner">
                            <div class="icon"><span class="flaticon-envelope"></span></div>
                            <h4>Email</h4>
                            <ul>
                                <li>contato@maluimoveiscf.com.br</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!--Info Block-->
                <div class="info-block col-md-6 col-sm-6 col-xs-12">
                    <div class="inner-box">
                        <div class="inner">
                            <div class="icon"><span class="flaticon-technology-6"></span></div>
                            <h4>Telefones:</h4>
                            <ul>
                                <li>+55 (22) 99237-5388 <i class="fa fa-whatsapp"></i></li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>


        <!--Form Container-->
        <div class="form-container">
            <div class="default-form contact-form">
                <form method="post" id="contact-form">
                    <div class="row clearfix">

                        <div class="form-group col-md-6 col-sm-6 col-xs-12">
                            <input type="text" name="name" value="" placeholder="Nome">
                        </div>

                        <div class="form-group col-md-6 col-sm-6 col-xs-12">
                            <input type="email" name="email" value="" placeholder="E-mail">
                        </div>

                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <input type="text" name="subject" value="" placeholder="Assunto">
                        </div>

                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <textarea name="message" placeholder="Mensagem"></textarea>
                        </div>

                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <div class="text-center"><button type="button" class="theme-btn btn-style-two">Enviar</button></div>
                        </div>

                    </div>
                </form>
            </div>
        </div><!--End Form Container-->

    </div>
</section>


    <!--Footer Bottom-->
    <div class="footer-bottom">
        <div class="bottom-container">
            <div class="clearfix">
            	<div class="logo"><a href="{{url('/')}}"><img src="{{ url('img/logo.png') }}" alt="" style="max-width: 220px;"></a></div>

                <div class="copyright-text">Malu Imóveis &copy; {{date('Y')}}. Todos os direitos reservados.</div>
                <div class="author-info">Desenvolvido <span class="fa fa-heart"></span> por <a href="https://rogerti.com.br" target="_blank">ROGER.TI</a></div>
            </div>
        </div>
    </div>

</div>
<!--End pagewrapper-->

<!--Scroll to top-->
<div class="scroll-to-top scroll-to-target" data-target="html"><span class="icon fa fa-long-arrow-up"></span></div>


<script src="{{ url('assets/front/js/jquery.js') }}"></script>
<script src="{{ url('assets/front/js/bootstrap.min.js') }}"></script>
<script src="{{ url('assets/front/js/jquery-ui.js') }}"></script>
<script src="{{ url('assets/front/js/bxslider.js') }}"></script>
<script src="{{ url('assets/front/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
<script src="{{ url('assets/front/js/isotope.js') }}"></script>
<script src="{{ url('assets/front/js/mixitup.js') }}"></script>
<script src="{{ url('assets/front/js/jquery.fancybox.pack.js') }}"></script>
<script src="{{ url('assets/front/js/jquery.fancybox-media.js') }}"></script>
<script src="{{ url('assets/front/js/owl.js') }}"></script>
<script src="{{ url('assets/front/js/wow.js') }}"></script>
<script src="{{ url('assets/front/js/script.js') }}"></script>

<!--Google Map APi Key-->
<script src="http://maps.google.com/maps/api/js?key="></script>
<script src="{{ url('assets/front/js/map-script.js') }}"></script>
<!--End Google Map APi-->

<!-- Select2 -->
<script src="{{url('assets/admin/plugins/select2/js/select2.full.min.js')}}"></script>
<script src="{{url('assets/admin/plugins/select2/js/i18n/pt-BR.js')}}"></script>

<script>

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
</script>

<script>
    $(window).scroll(function() {
    var scroll = $(window).scrollTop();

    if (scroll >= 150) {
        $(".header-upper").addClass("darkHeader");
    } else {
        $(".header-upper").removeClass("darkHeader");
    }
});
</script>

@yield('scripts')

</body>
</html>
