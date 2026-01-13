<!DOCTYPE html>
<html lang="zxx">
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

   <!-- Estilos Modernos com Cores do Projeto -->
   <style>
   :root {
     --primary-blue: #06b8f7;
     --primary-green: #6ccb48;
     --blue-dark: #0489b8;
     --blue-light: #4dd0e1;
     --green-dark: #5aaf3a;
     --bg-white: #FFFFFF;
     --text-dark: #333333;
     --text-gray: #666666;
     --text-light: #999999;
     --border-color: rgba(0, 0, 0, 0.1);
     --shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
     --shadow-hover: 0 4px 12px rgba(0, 0, 0, 0.1);
     --shadow-blue: 0 8px 24px rgba(6, 184, 247, 0.2);
   }

   /* Header Modernizado */
   header {
     background: rgba(255, 255, 255, 0.95) !important;
     backdrop-filter: blur(10px);
     box-shadow: 0 2px 20px rgba(0, 0, 0, 0.05);
     transition: all 0.3s ease;
   }

   #stickyHeader.slideUp {
     background: rgba(255, 255, 255, 0.98) !important;
     box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
   }

   .top-bar ul li a {
     color: var(--text-dark) !important;
     font-weight: 500;
     transition: all 0.3s ease;
     position: relative;
   }

   .top-bar ul li a:hover {
     color: var(--primary-blue) !important;
   }

   .top-bar ul li a::after {
     content: '';
     position: absolute;
     bottom: -5px;
     left: 0;
     width: 0;
     height: 2px;
     background: linear-gradient(90deg, var(--primary-blue), var(--primary-green));
     transition: width 0.3s ease;
   }

   .top-bar ul li a:hover::after {
     width: 100%;
   }

   .top-bar > a {
     color: var(--primary-blue) !important;
     font-weight: 600;
     transition: all 0.3s ease;
   }

   .top-bar > a:hover {
     color: var(--primary-green) !important;
     transform: translateY(-2px);
   }

   .top-bar .logo img {
     transition: all 0.3s ease;
     filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.1));
   }

   .top-bar .logo:hover img {
     transform: scale(1.05);
     filter: drop-shadow(0 4px 12px rgba(6, 184, 247, 0.3));
   }

   /* Hero Section Sofisticado */
   .hero-section.two {
     background: linear-gradient(135deg, #0a4d68 0%, var(--primary-blue) 30%, #05a0d6 60%, var(--primary-green) 100%);
     position: relative;
     overflow: hidden;
     min-height: 90vh;
     display: flex;
     align-items: center;
     padding: 120px 0 80px;
   }

   .hero-section.two::before {
     content: '';
     position: absolute;
     top: 0;
     left: 0;
     right: 0;
     bottom: 0;
     background:
       radial-gradient(circle at 20% 50%, rgba(108, 203, 72, 0.15) 0%, transparent 50%),
       radial-gradient(circle at 80% 80%, rgba(6, 184, 247, 0.15) 0%, transparent 50%),
       url('data:image/svg+xml,<svg width="60" height="60" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="60" height="60" patternUnits="userSpaceOnUse"><path d="M 60 0 L 0 0 0 60" fill="none" stroke="rgba(255,255,255,0.08)" stroke-width="0.5"/></pattern></defs><rect width="60" height="60" fill="url(%23grid)"/></svg>');
     opacity: 1;
     animation: patternMove 20s linear infinite;
   }

   @keyframes patternMove {
     0% { background-position: 0 0, 0 0, 0 0; }
     100% { background-position: 100px 100px, -100px -100px, 60px 60px; }
   }

   .hero-section.two::after {
     content: '';
     position: absolute;
     top: -50%;
     right: -20%;
     width: 800px;
     height: 800px;
     background: radial-gradient(circle, rgba(6, 184, 247, 0.2) 0%, transparent 70%);
     border-radius: 50%;
     animation: float 6s ease-in-out infinite;
   }

   @keyframes float {
     0%, 100% { transform: translateY(0) scale(1); }
     50% { transform: translateY(-30px) scale(1.1); }
   }

   .hero-section.two .hero-text {
     position: relative;
     z-index: 2;
     text-align: center;
   }

   .hero-section.two .hero-text h2 {
     color: #fff;
     font-weight: 900;
     font-size: 4.5rem;
     line-height: 1.2;
     text-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
     margin-bottom: 30px;
     animation: fadeInUp 1s ease-out;
   }

   .hero-section.two .hero-text h2 span {
     background: linear-gradient(135deg, #fff 0%, rgba(255, 255, 255, 0.9) 100%);
     -webkit-background-clip: text;
     -webkit-text-fill-color: transparent;
     background-clip: text;
     display: inline-block;
     position: relative;
   }

   .hero-section.two .hero-text p {
     color: rgba(255, 255, 255, 0.95);
     font-size: 1.4rem;
     font-weight: 400;
     line-height: 1.8;
     max-width: 700px;
     margin: 0 auto 40px;
     text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
     animation: fadeInUp 1.2s ease-out;
   }

   @keyframes fadeInUp {
     from {
       opacity: 0;
       transform: translateY(30px);
     }
     to {
       opacity: 1;
       transform: translateY(0);
     }
   }

   .hero-features {
     display: flex;
     justify-content: center;
     gap: 40px;
     margin-top: 50px;
     flex-wrap: wrap;
     animation: fadeInUp 1.4s ease-out;
   }

   .hero-feature-item {
     display: flex;
     align-items: center;
     gap: 12px;
     background: rgba(255, 255, 255, 0.15);
     backdrop-filter: blur(10px);
     padding: 12px 24px;
     border-radius: 50px;
     border: 1px solid rgba(255, 255, 255, 0.2);
     transition: all 0.3s ease;
   }

   .hero-feature-item:hover {
     background: rgba(255, 255, 255, 0.25);
     transform: translateY(-3px);
     box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
   }

   .hero-feature-item i {
     font-size: 20px;
     color: #fff;
   }

   .hero-feature-item span {
     color: #fff;
     font-weight: 500;
     font-size: 14px;
   }

   /* Botões Modernizados */
   .btn {
     background: linear-gradient(135deg, var(--primary-blue) 0%, #05a0d6 100%) !important;
     border: none !important;
     border-radius: 50px !important;
     padding: 18px 40px !important;
     font-weight: 600 !important;
     font-size: 16px !important;
     color: #fff !important;
     box-shadow: 0 8px 24px rgba(6, 184, 247, 0.35), 0 4px 12px rgba(6, 184, 247, 0.2) !important;
     transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
     position: relative;
     overflow: hidden;
   }

   .btn::before {
     content: '';
     position: absolute;
     top: 0;
     left: -100%;
     width: 100%;
     height: 100%;
     background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
     transition: left 0.5s;
   }

   .btn:hover::before {
     left: 100%;
   }

   .btn:hover {
     transform: translateY(-3px) !important;
     box-shadow: 0 12px 32px rgba(6, 184, 247, 0.45), 0 6px 16px rgba(6, 184, 247, 0.3) !important;
     background: linear-gradient(135deg, #05a0d6 0%, #0489b8 100%) !important;
   }

   .btn:active {
     transform: translateY(-1px) !important;
   }

   .btn span {
     position: relative;
     z-index: 1;
     color: #fff !important;
   }

   /* Seções Modernizadas */
   section.gap {
     background: #f8f9fa;
     position: relative;
   }

   section.gap::before {
     content: '';
     position: absolute;
     top: 0;
     left: 0;
     right: 0;
     height: 1px;
     background: linear-gradient(90deg, transparent, rgba(6, 184, 247, 0.2), transparent);
   }

   /* Seção de Recursos Completa - Compacta */
   .features-section {
     background: #fff;
     padding: 50px 0;
   }

   .features-section .heading {
     margin-bottom: 35px !important;
   }

   .feature-card {
     background: #fff;
     border-radius: 16px;
     padding: 25px 20px;
     text-align: center;
     transition: all 0.3s ease;
     border: 1px solid rgba(6, 184, 247, 0.1);
     height: 100%;
     position: relative;
     overflow: hidden;
   }

   .feature-card::before {
     content: '';
     position: absolute;
     top: 0;
     left: 0;
     right: 0;
     height: 3px;
     background: linear-gradient(90deg, var(--primary-blue), var(--primary-green));
     transform: scaleX(0);
     transition: transform 0.3s ease;
   }

   .feature-card:hover::before {
     transform: scaleX(1);
   }

   .feature-card:hover {
     transform: translateY(-5px);
     box-shadow: 0 10px 30px rgba(6, 184, 247, 0.12);
     border-color: var(--primary-blue);
   }

   .feature-icon {
     width: 60px;
     height: 60px;
     margin: 0 auto 15px;
     background: linear-gradient(135deg, var(--primary-blue), var(--primary-green));
     border-radius: 12px;
     display: flex;
     align-items: center;
     justify-content: center;
     box-shadow: 0 4px 15px rgba(6, 184, 247, 0.25);
     transition: all 0.3s ease;
   }

   .feature-card:hover .feature-icon {
     transform: scale(1.05);
     box-shadow: 0 6px 20px rgba(6, 184, 247, 0.35);
   }

   .feature-icon i {
     font-size: 28px;
     color: #fff;
   }

   .feature-card h4 {
     color: var(--text-dark) !important;
     font-weight: 700;
     font-size: 18px;
     margin-bottom: 10px;
     line-height: 1.3;
   }

   .feature-card p {
     color: var(--text-gray);
     line-height: 1.6;
     font-size: 14px;
     margin: 0;
   }

   .heading {
     text-align: center;
     margin-bottom: 60px;
   }

   .heading span {
     color: var(--primary-blue) !important;
     font-weight: 600;
     text-transform: uppercase;
     letter-spacing: 2px;
     font-size: 13px;
     display: inline-block;
     padding: 8px 20px;
     background: rgba(6, 184, 247, 0.1);
     border-radius: 50px;
     margin-bottom: 15px;
   }

   .heading h2 {
     color: var(--text-dark) !important;
     font-weight: 800;
     margin-top: 15px;
     font-size: 2.8rem;
     line-height: 1.3;
   }

   /* Customize Section - Reorganizada */
   .customize-img {
     text-align: center;
   }

   .customize-img img {
     border-radius: 20px;
     box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
     transition: all 0.3s ease;
     max-width: 100%;
     height: auto;
   }

   .customize-img img:hover {
     transform: translateY(-5px);
     box-shadow: 0 20px 60px rgba(6, 184, 247, 0.2);
   }

   .customize-text {
     padding-left: 40px;
   }

   @media (max-width: 991px) {
     .customize-text {
       padding-left: 0;
       padding-top: 30px;
     }
   }

   .customize-text p {
     color: var(--text-gray);
     line-height: 1.8;
     font-size: 1rem;
     margin-bottom: 25px;
   }

   .customize-text ul {
     display: grid;
     grid-template-columns: repeat(2, 1fr);
     gap: 8px;
     list-style: none;
     padding: 0;
     margin: 0;
   }

   .customize-text ul li {
     color: var(--text-dark);
     font-weight: 500;
     font-size: 14px;
     padding: 10px 12px;
     border-radius: 8px;
     background: rgba(6, 184, 247, 0.05);
     border: 1px solid rgba(6, 184, 247, 0.1);
     transition: all 0.3s ease;
     display: flex;
     align-items: center;
   }

   .customize-text ul li:hover {
     background: rgba(6, 184, 247, 0.1);
     border-color: var(--primary-blue);
     transform: translateX(5px);
     color: var(--primary-blue);
   }

   .customize-text ul li img {
     margin-right: 12px;
     filter: drop-shadow(0 2px 4px rgba(6, 184, 247, 0.3));
     transition: transform 0.3s ease;
   }

   .customize-text ul li:hover img {
     transform: scale(1.1);
   }

   /* Social Media Section */
   .social-media-style {
     background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-green) 100%) !important;
     padding: 60px 0;
     position: relative;
     overflow: hidden;
   }

   .social-media-section li a {
     color: #fff !important;
     font-weight: 500;
     transition: all 0.3s ease;
     padding: 10px 20px;
     border-radius: 30px;
     display: inline-block;
   }

   .social-media-section li a:hover {
     background: rgba(255, 255, 255, 0.2);
     transform: translateY(-3px);
   }

   .social-media-section li a i {
     margin-right: 8px;
   }

   /* Pricing Section - Compacta e Organizada */
   .pricing-plans-section {
     background: #fff;
     padding: 70px 0;
   }

   .pricing-plans-section .heading {
     margin-bottom: 0;
   }

   .pricing-plans-section .row {
     align-items: flex-start;
     display: flex !important;
   }

   .pricing-plans-section .col-lg-6 {
     display: block !important;
     width: 50% !important;
     flex: 0 0 50% !important;
     max-width: 50% !important;
   }

   .pricing-plans-section .col-lg-6:first-child {
     padding-right: 25px;
   }

   .pricing-plans-section .col-lg-6:last-child {
     padding-left: 25px;
   }

   @media (max-width: 991px) {
     .pricing-plans-section .col-lg-6 {
       width: 100% !important;
       flex: 0 0 100% !important;
       max-width: 100% !important;
       padding-left: 15px !important;
       padding-right: 15px !important;
     }
   }

   .pricing-plans {
     background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
     border: 2px solid rgba(6, 184, 247, 0.15);
     border-radius: 20px;
     padding: 35px 30px;
     box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
     transition: all 0.3s ease;
     position: relative;
     overflow: hidden;
     text-align: center;
   }

   .pricing-plans::before {
     content: '';
     position: absolute;
     top: 0;
     left: 0;
     right: 0;
     height: 4px;
     background: linear-gradient(90deg, var(--primary-blue), var(--primary-green));
   }

   .pricing-plans:hover {
     transform: translateY(-5px);
     box-shadow: 0 15px 50px rgba(6, 184, 247, 0.15);
     border-color: var(--primary-blue);
   }

   .pricing-plans span {
     color: var(--primary-blue) !important;
     font-weight: 700;
     font-size: 16px;
     text-transform: uppercase;
     letter-spacing: 1px;
     display: inline-block;
     padding: 6px 16px;
     background: rgba(6, 184, 247, 0.1);
     border-radius: 50px;
     margin-bottom: 15px;
   }

   .pricing-plans h5 {
     color: var(--text-dark) !important;
     font-size: 42px;
     font-weight: 800;
     margin: 15px 0;
     line-height: 1;
   }

   .pricing-plans h5 sub {
     color: var(--text-gray);
     font-size: 16px;
     font-weight: 400;
   }

   .pricing-plans-text {
     background: #fff;
     border-radius: 16px;
     padding: 25px;
     margin-top: 25px;
     box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
     border: 1px solid rgba(0, 0, 0, 0.05);
   }

   .pricing-plans-text .hero-text ul {
     list-style: none;
     padding: 0;
     margin: 0 0 25px 0;
     display: grid;
     grid-template-columns: repeat(2, 1fr);
     gap: 10px;
   }

   .pricing-plans-text .hero-text ul li {
     color: var(--text-dark);
     padding: 10px 12px;
     font-size: 14px;
     border-radius: 8px;
     background: rgba(6, 184, 247, 0.05);
     border: 1px solid rgba(6, 184, 247, 0.1);
     display: flex;
     align-items: center;
     transition: all 0.3s ease;
   }

   .pricing-plans-text .hero-text ul li:hover {
     background: rgba(6, 184, 247, 0.1);
     border-color: var(--primary-blue);
     color: var(--primary-blue);
   }

   .pricing-plans-text .hero-text ul li img {
     margin-right: 10px;
     width: 18px;
     height: 18px;
     filter: drop-shadow(0 2px 4px rgba(6, 184, 247, 0.3));
   }

   .pricing-plans-text .btn {
     width: 100%;
     margin-top: 10px;
   }

   /* Accordion Modernizado */
   .accordion {
     margin-top: 0;
   }

   .accordion-item {
     background: #fff;
     border: 1px solid rgba(0, 0, 0, 0.08);
     border-radius: 12px !important;
     margin-bottom: 12px;
     overflow: hidden;
     transition: all 0.3s ease;
   }

   .accordion-item:hover {
     box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
   }

   .accordion-item.active {
     border-color: var(--primary-blue);
     box-shadow: 0 4px 16px rgba(6, 184, 247, 0.15);
   }

   .accordion-item .heading {
     color: var(--text-dark) !important;
     padding: 18px 20px;
     font-weight: 600;
     transition: all 0.3s ease;
     font-size: 15px;
   }

   .accordion-item.active .heading {
     color: var(--primary-blue) !important;
   }

   .accordion-item .content {
     padding: 0 20px 18px;
     color: var(--text-gray);
     line-height: 1.7;
     font-size: 14px;
   }

   .accordion-item .icon {
     background: var(--primary-blue);
   }

   .accordion-item .title {
     font-size: 15px;
   }

   /* Count Style Modernizado */
   .count-style h2 {
     color: var(--primary-blue) !important;
     font-weight: 800;
     font-size: 64px;
   }

   .count-style span {
     color: var(--text-gray);
     font-weight: 500;
     text-transform: uppercase;
     letter-spacing: 1px;
     font-size: 14px;
   }

   /* Clients Section */
   section.gap.no-top {
     background: #fff;
   }

   .clients {
     background: #fff;
     border-radius: 20px;
     padding: 40px;
     box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
     transition: all 0.3s ease;
     border-top: 4px solid var(--primary-blue);
     position: relative;
     overflow: hidden;
   }

   .clients::before {
     content: '';
     position: absolute;
     top: 0;
     left: 0;
     width: 4px;
     height: 100%;
     background: linear-gradient(180deg, var(--primary-blue), var(--primary-green));
   }

   .clients:hover {
     transform: translateY(-5px);
     box-shadow: 0 20px 60px rgba(6, 184, 247, 0.15);
   }

   .clients p {
     color: var(--text-gray);
     font-style: italic;
     line-height: 1.8;
     font-size: 1.1rem;
   }

   .clients h6 {
     color: var(--text-dark) !important;
     font-weight: 700;
     margin-top: 10px;
   }

   .clients span {
     color: var(--primary-blue);
     font-weight: 500;
   }

   /* Footer Modernizado */
   footer {
     background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
     color: #fff;
     position: relative;
     overflow: hidden;
   }

   footer::before {
     content: '';
     position: absolute;
     top: 0;
     left: 0;
     right: 0;
     height: 4px;
     background: linear-gradient(90deg, var(--primary-blue), var(--primary-green));
   }

   .footer-bottom h3 {
     color: #fff !important;
     font-weight: 700;
   }

   .footer-bottom p {
     color: rgba(255, 255, 255, 0.8);
   }

   .footer-bottom a {
     color: var(--primary-blue) !important;
     transition: all 0.3s ease;
   }

   .footer-bottom a:hover {
     color: var(--primary-green) !important;
   }

   .footer-end {
     border-top: 1px solid rgba(255, 255, 255, 0.1);
     padding-top: 20px;
     margin-top: 30px;
   }

   .footer-end p {
     color: rgba(255, 255, 255, 0.7);
   }

   .footer-end a {
     color: var(--primary-blue) !important;
   }

   .footer-end .fa-heart {
     color: #ff6b6b;
   }

   /* Progress Button */
   #progress {
     background: linear-gradient(135deg, var(--primary-blue), var(--primary-green));
     box-shadow: 0 4px 16px rgba(6, 184, 247, 0.4);
   }

   /* Responsive */
   @media (max-width: 768px) {
     .hero-section.two {
       min-height: 70vh;
       padding: 100px 0 60px;
     }

     .hero-section.two .hero-text h2 {
       font-size: 2.5rem;
     }

     .hero-section.two .hero-text p {
       font-size: 1.1rem;
     }

     .hero-features {
       gap: 15px;
     }

     .hero-feature-item {
       padding: 10px 18px;
       font-size: 12px;
     }

     .pricing-plans {
       padding: 30px 20px;
     }

     .count-style h2 {
       font-size: 48px;
     }

     .feature-card {
       padding: 20px 15px;
     }

     .feature-icon {
       width: 50px;
       height: 50px;
       margin-bottom: 12px;
     }

     .feature-icon i {
       font-size: 24px;
     }

     .feature-card h4 {
       font-size: 16px;
     }

     .feature-card p {
       font-size: 13px;
     }

     .customize-text ul {
       grid-template-columns: 1fr;
     }

     .pricing-plans-text .hero-text ul {
       grid-template-columns: 1fr;
     }

     .pricing-plans {
       padding: 25px 20px;
     }

     .pricing-plans h5 {
       font-size: 36px;
     }

     .pricing-plans-section .row {
       flex-wrap: wrap !important;
     }

     .pricing-plans-section .col-lg-6 {
       flex: 0 0 100% !important;
       max-width: 100% !important;
       margin-bottom: 40px;
       padding-left: 15px !important;
       padding-right: 15px !important;
     }

     .accordion-item .heading {
       padding: 15px 18px;
       font-size: 14px;
     }

     .accordion-item .content {
       padding: 0 18px 15px;
       font-size: 13px;
     }
   }
   </style>

 </head>
<body style="background: #f8f9fa;">
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
          <img alt="logo" src="{{url('/img/logo.png?')}}{{mt_rand(0,999)}}">
        </div>
        <nav>
          <ul>
            <li><a href="#sobre">Sobre</a></li>
            <li><a href="#recursos">Recursos</a></li>
            <li><a href="#precos">Preços</a></li>
            <li><a href="#duvidas">Dúvidas</a></li>
            <li><a href="{{url('/admin')}}">Acessar Plataforma</a></li>
          </ul>
        </nav>
        <a href="https://api.whatsapp.com/send?phone=5522988280129&text=Olá, gostaria de falar sobre o sistema de cobrança." target="_blank"> +55 (22)98828-0129 <i class="fa-brands fa-whatsapp"></i></a>
      </div>


    </div>




  </header>
<!-- header end -->

<section class="hero-section two" id="inicio">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="hero-text">
          <h2>Gestão Financeira <span>INTELIGENTE</span></h2>
          <p>Automatize suas cobranças, controle contas a pagar e receber, e transforme a gestão financeira da sua empresa com tecnologia de ponta.</p>
          <a href="https://api.whatsapp.com/send?phone=5522988280129&text=Olá, gostaria de falar sobre o sistema de cobrança." class="btn" target="_blank"><span>Começar Agora</span></a>
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
          <div class="heading" style="text-align: left !important; margin: 30px 0 20px 0;">
            <h2 style="font-size: 1.8rem; margin-top: 0;">Recursos Principais</h2>
          </div>
          <ul id="recursos">
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
      <h2 style="font-size: 2.2rem;">Tudo que você precisa para uma gestão financeira eficiente</h2>
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

<div class="social-media-style" style="background-image: url(assets/front/img/social-media.png);">
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
        <div class="heading" style="text-align: left; margin-bottom: 30px;">
          <span>Preços e Planos</span>
          <h2 style="font-size: 2rem; margin-top: 10px;">Escolha o melhor plano</h2>
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
      <div class="col-lg-6 col-md-12">
        <div class="heading" style="text-align: left; margin-bottom: 30px;">
          <span>Perguntas Frequentes</span>
          <h2 style="font-size: 2rem; margin-top: 10px;">Tire suas dúvidas</h2>
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
