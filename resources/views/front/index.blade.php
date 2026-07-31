<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cobrança Segura</title>
  <link rel="icon" href="{{url('/img/favicon.png')}}">
  <!-- CSS only -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="{{url('assets/front/css/owl.carousel.min.css')}}">
   <link rel="stylesheet" href="{{url('assets/front/css/owl.theme.default.min.css')}}">
   <!-- fancybox -->
   <link rel="stylesheet" href="{{url('assets/front/css/jquery.fancybox.min.css')}}">
   <!-- Font Awesome 6 -->
   <link rel="stylesheet" href="{{url('assets/front/css/fontawesome.min.css')}}">
   <!-- style -->
   <link rel="stylesheet" href="{{url('assets/front/css/style.css?')}}{{mt_rand(0,999)}}">
   <!-- responsive -->
   <link rel="stylesheet" href="{{url('assets/front/css/responsive.css')}}">
   <!-- color -->
   <link rel="stylesheet" href="{{url('assets/front/css/color.css')}}">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{url('assets/front/css/landing-theme.css')}}?v=2">

</head>
<body>
<!-- preloader -->
  <div class="preloader">
    <div>
      <div class="spinner spinner-3"></div>
    </div>
  </div>
<!-- preloader end -->
<!-- header -->
  <header id="stickyHeader">
    <div class="container">
      <div class="top-bar">
        <div class="logo">
          <a href="#inicio" aria-label="Cobrança Segura — início">
            <img alt="Cobrança Segura" src="{{url('/img/logo.png?')}}{{mt_rand(0,999)}}">
          </a>
        </div>
        <nav aria-label="Navegação principal">
          <ul>
            <li><a href="#sobre">Sobre</a></li>
            <li><a href="#recursos">Recursos</a></li>
            <li><a href="#precos">Preços</a></li>
            <li><a href="#duvidas">Dúvidas</a></li>
          </ul>
        </nav>
        <div class="header-actions">
          <a href="{{url('/admin')}}" class="nav-cta nav-cta-outline">Acessar Plataforma</a>
          <a href="https://api.whatsapp.com/send?phone=5522988280129&text=Olá, gostaria de falar sobre o sistema de cobrança." target="_blank" rel="noopener" class="nav-cta">
            <i class="fa-brands fa-whatsapp"></i> Fale conosco
          </a>
        </div>
      </div>
    </div>
  </header>
<!-- header end -->

<section class="hero-section two" id="inicio">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="hero-text">
          <div class="hero-badge">
            <i class="fa-solid fa-sparkles"></i>
            Plataforma de gestão financeira
          </div>
          <h2>Gestão Financeira <span>INTELIGENTE</span></h2>
          <p>Automatize suas cobranças, controle contas a pagar e receber, e transforme a gestão financeira da sua empresa com tecnologia de ponta.</p>
          <div class="hero-actions">
            <a href="https://api.whatsapp.com/send?phone=5522988280129&text=Olá, gostaria de falar sobre o sistema de cobrança." class="btn" target="_blank" rel="noopener"><span>Começar Agora</span></a>
            <a href="#recursos" class="btn btn-outline-gold"><span>Conhecer Recursos</span></a>
          </div>
          <div class="hero-features">
            <div class="hero-feature-item">
              <i class="fa-solid fa-check-circle"></i>
              <span>100% Automatizado</span>
            </div>
            <div class="hero-feature-item">
              <i class="fa-solid fa-shield-halved"></i>
              <span>Seguro e Confiável</span>
            </div>
            <div class="hero-feature-item">
              <i class="fa-solid fa-rocket"></i>
              <span>Implementação Rápida</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<section id="sobre" class="gap">
  <div class="container">
    <div class="heading">
      <span>Cobrança Segura</span>
      <h2>Automatize processos e minimize erros </h2>
    </div>
    <div class="row align-items-center">
      <div class="col-lg-6">
        <div class="customize-img">
          <img alt="customize" src="{{url('assets/front/img/customize.png')}}">
        </div>
      </div>
      <div class="col-lg-6">
        <div class="customize-text">
          <p>A empresa surgiu da necessidade de simplificar e otimizar o processo de gestão de cobranças para outras empresas. A plataforma possibilita gerar cobranças recorrentes de forma automatizada e eficiente, permitindo que você acompanhe as cobranças enviadas, saldos e projeções de forma clara e acessível.</p>
          <div class="heading heading-left heading-compact">
            <h2>Recursos Principais</h2>
          </div>
          <ul class="recursos-principais">
            <li><img alt="check" src="{{url('assets/front/img/check-b.png')}}">Cadastro de clientes</li>
            <li><img alt="check" src="{{url('assets/front/img/check-b.png')}}">Cadastro de serviços</li>
            <li><img alt="check" src="{{url('assets/front/img/check-b.png')}}">Cobrança recorrente</li>
            <li><img alt="check" src="{{url('assets/front/img/check-b.png')}}">Contas a receber</li>
            <li><img alt="check" src="{{url('assets/front/img/check-b.png')}}">Contas a pagar</li>
            <li><img alt="check" src="{{url('assets/front/img/check-b.png')}}">Integrações de pagamento</li>
            <li><img alt="check" src="{{url('assets/front/img/check-b.png')}}">Baixa automática</li>
            <li><img alt="check" src="{{url('assets/front/img/check-b.png')}}">Envio por WhatsApp e E-mail</li>
            </ul>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- Seção de Recursos Completa -->
<section id="recursos" class="features-section">
  <div class="container">
    <div class="heading text-center mb-4">
      <span>Recursos Completos</span>
      <h2>Tudo que você precisa para uma gestão financeira eficiente</h2>
    </div>
    <div class="row g-3">
      <div class="col-lg-4 col-md-6">
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fa-solid fa-users"></i>
          </div>
          <h4>Cadastro de Clientes</h4>
          <p>Gerencie todos os seus clientes em um só lugar com informações completas e organizadas.</p>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fa-solid fa-briefcase"></i>
          </div>
          <h4>Cadastro de Serviços</h4>
          <p>Cadastre seus serviços e produtos de forma rápida e organize suas ofertas.</p>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fa-solid fa-repeat"></i>
          </div>
          <h4>Cobrança Recorrente</h4>
          <p>Configure cobranças automáticas e nunca mais se preocupe com pagamentos recorrentes.</p>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fa-solid fa-arrow-down"></i>
          </div>
          <h4>Contas a Receber</h4>
          <p>Controle completo de todas as contas a receber com acompanhamento em tempo real e relatórios detalhados.</p>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fa-solid fa-arrow-up"></i>
          </div>
          <h4>Contas a Pagar</h4>
          <p>Gerencie suas obrigações financeiras com controle de vencimentos, categorização e planejamento.</p>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fa-solid fa-credit-card"></i>
          </div>
          <h4>Integrações de Pagamento</h4>
          <p>Banco Inter, PagHiper e Mercado Pago. Pix e Boleto com baixa automática.</p>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fa-solid fa-robot"></i>
          </div>
          <h4>Baixa Automática</h4>
          <p>Recebimentos confirmados automaticamente, sem necessidade de intervenção manual.</p>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fa-brands fa-whatsapp"></i>
          </div>
          <h4>Envio Automático</h4>
          <p>Envie cobranças automaticamente por WhatsApp e E-mail com templates personalizados.</p>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fa-solid fa-chart-line"></i>
          </div>
          <h4>Relatórios e Análises</h4>
          <p>Acompanhe métricas, projeções financeiras e tenha visão completa do seu negócio.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="social-media-style">
  <div class="container">
    <ul class="social-media-section">
        <li><a href="#"><i class="fa-brands fa-linkedin-in"></i>linkedin</a></li>
        <li><a href="#"><i class="fa-brands fa-youtube"></i>Youtube</a></li>
        <li><a href="#"><i class="fa-brands fa-facebook-f"></i>facebook</a></li>
        <li><a href="#"><i class="fa-brands fa-instagram"></i>instagram</a></li>
    </ul>
  </div>
</div>
<section id="precos" class="pricing-plans-section gap">
  <div class="container">
    <div class="row g-4">
      <!-- Coluna de Preços -->
      <div class="col-lg-6 col-md-12">
        <div class="heading heading-left">
          <span>Preços e Planos</span>
          <h2>Escolha o melhor plano</h2>
        </div>
        <div class="pricing-plans">
          <span>Basic</span>
          <h5>R$49,90 <sub>/ mês</sub></h5>
        </div>
        <div class="pricing-plans-text">
            <div class="hero-text">
              <ul>
                <li><img alt="check" src="{{url('assets/front/img/check-b.png')}}"> Cobrança única e recorrente</li>
                <li><img alt="check" src="{{url('assets/front/img/check-b.png')}}"> Contas a receber</li>
                <li><img alt="check" src="{{url('assets/front/img/check-b.png')}}"> Contas a pagar</li>
                <li><img alt="check" src="{{url('assets/front/img/check-b.png')}}"> E-mail e WhatsApp</li>
                <li><img alt="check" src="{{url('assets/front/img/check-b.png')}}"> Integrações de pagamento</li>
                <li><img alt="check" src="{{url('assets/front/img/check-b.png')}}"> Baixa automática</li>
                <li><img alt="check" src="{{url('assets/front/img/check-b.png')}}"> Relatórios financeiros</li>
              </ul>
              <a href="https://api.whatsapp.com/send?phone=5522988280129&text=Olá, gostaria de falar sobre o sistema de cobrança." target="_blank" class="btn"><span>Contratar Agora</span></a>
            </div>
        </div>
      </div>

      <!-- Coluna de Perguntas Frequentes -->
      <div class="col-lg-6 col-md-12" id="duvidas">
        <div class="heading heading-left">
          <span>Perguntas Frequentes</span>
          <h2>Tire suas dúvidas</h2>
        </div>
        <div class="accordion">
          <div class="accordion-item">
            <a href="#" class="heading">
              <div class="icon"></div>
              <div class="title">Quantos clientes posso cadastrar?</div>
            </a>
            <div class="content">
              <p>Ilimitado. Não tem limite para cadastro de clientes.</p>
            </div>
          </div>

          <div class="accordion-item active">
            <a href="#" class="heading">
              <div class="icon"></div>
              <div class="title">Existe limite de cobranças por mês?</div>
            </a>
            <div class="content" style="display: block;">
              <p>Não. Poderá gerar faturas ilimitadas para cada cliente.</p>
            </div>
          </div>

          <div class="accordion-item">
            <a href="#" class="heading">
              <div class="icon"></div>
              <div class="title">Posso testar a plataforma?</div>
            </a>
            <div class="content">
              <p>Sim. Você terá 5 dias para testes.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<section class="gap no-top">
  <div class="container">
    <div class="heading">
      <span>Histórias de quem usa o Cobrança Segura</span>
      <h2>Venha fazer parte</h2>
    </div>
    <div class="row clients-slider owl-carousel owl-theme">
      <div class="col-lg-12 item">
        <div class="clients">
          <p>"Depois que comecei a utilizar a plataforma Cobrança Segura, 'poupei muito tempo', sem precisar ficar gerando faturas manualmente.”</p>
          <div class="d-flex align-items-center mt-4"><div><i><img alt="quote" src="{{url('assets/front/img/quote.png')}}"></i></div>
            <div>
              <h6>Roger.TI</h6>
              <span>CEO</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-12 item">
        <div class="clients two">
          <p>"Antes, havia muita confusão e atrasos nas comunicações com os clientes em relação aos pagamentos pendentes. Agora, com o novo sistema, as notificações automáticas são enviadas no momento certo, o que facilita muito o acompanhamento dos pagamentos em aberto. ”</p>
          <div class="d-flex align-items-center mt-4"><div><i><img alt="quote" src="{{url('assets/front/img/quote.png')}}"></i></div>
            <div>
              <h6>Condominio Soares</h6>
              <span>Admistradora</span>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
<footer class="gap no-bottom">
  <div class="container">
    <div class="row">
      <div class="col-lg-12 col-md-12 text-center">
        <div class="logo">
          <a href="#">
            <img alt="logo" src="{{url('/img/logo.png')}}">
          </a>
        </div>
      </div>

    </div>
    <div class="footer-bottom">
      <h3>Comece agora mesmo</h3>
      <p>Ajudamos empresas com inovação e crescimento há mais de 10 anos! </p>
      <a href="https://api.whatsapp.com/send?phone=5522988280129&text=Olá, gostaria de falar sobre o sistema de cobrança." target="_blank" class="btn"><span>Fale conosco</span></a>
      <br>
      <br>
      <a href="https://api.whatsapp.com/send?phone=5522988280129&text=Olá, gostaria de falar sobre o sistema de cobrança." target="_blank"><i class="fa-brands fa-whatsapp"></i> (22) 98828-0129</a><br>
      <a href="mailto:contato@cobrancasegura.com.br">contato@cobrancasegura.com.br</a>
    </div>
    <div class="footer-end">
      <p>{{date('Y')}} © Cobrança Segura | Desenvolvido <span class="fa fa-heart"></span> por <a href="https://rogerti.com.br" target="_blank">ROGER.TI</a></p>
    </div>
  </div>
  <div class="footer-shaps">
  </div>
</footer>
<!-- progress -->
<div id="progress">
      <span id="progress-value"><i class="fa-solid fa-arrow-up"></i></span>
</div>

   <!-- jquery -->
   <script src="{{url('assets/front/js/jquery-3.6.0.min.js')}}"></script>
   <script src="{{url('assets/front/js/preloader.js')}}"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Bootstrap Js -->
<script src="{{url('assets/front/js/bootstrap.min.js')}}"></script>
<script src="{{url('assets/front/js/owl.carousel.min.js')}}"></script>
<!-- fancybox -->
<script src="{{url('assets/front/js/jquery.fancybox.min.js')}}"></script>
<script src="{{url('assets/front/js/custom.js?')}}{{mt_rand(0,999)}}"></script>





</body>
