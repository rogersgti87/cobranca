<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Autenticação</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">


    <style>
/* ============================================
   VARIÁVEIS DE CORES DO PROJETO
   ============================================ */
:root {
  --primary-blue: #06b8f7;
  --primary-green: #6ccb48;
  --primary-yellow: #fec911;
  --bg-white: #FFFFFF;
  --text-dark: #333333;
  --text-gray: #666666;
  --border-color: rgba(0, 0, 0, 0.1);
  --shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  --shadow-hover: 0 4px 12px rgba(0, 0, 0, 0.1);
}

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: "Roboto", "Source Sans Pro", sans-serif;
  margin: 0 !important;
  background: linear-gradient(135deg, #e5e7eb 0%, #f3f4f6 50%, #ffffff 100%);
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px !important;
  width: 100% !important;
  box-sizing: border-box;
  position: relative;
}

#app {
  position: relative;
  z-index: 1;
}

/* Garantir que containers não limitem a largura */
#app {
  width: 100% !important;
  max-width: none !important;
  padding: 0 !important;
}

#app > main {
  width: 100% !important;
  max-width: none !important;
  display: flex !important;
  align-items: center;
  justify-content: center;
  padding: 0 !important;
  margin: 0 !important;
}

/* Sobrescrever qualquer container do Bootstrap */
#app .container,
#app .container-fluid,
#app main.container,
#app main.container-fluid {
  width: 100% !important;
  max-width: none !important;
  padding: 0 !important;
}

/* FORM STYLE */
.login-form-bd {
  width: 100% !important;
  max-width: 650px !important;
  min-width: auto !important;
  margin: 0 auto !important;
  flex: 0 0 auto;
  padding: 0 !important;
  box-sizing: border-box !important;
}

.form-wrapper {
  width: 100% !important;
  max-width: none !important;
}

.form-container {
  font-family: "Poppins", "Source Sans Pro", sans-serif;
  font-size: 1rem;
  padding: 3.5rem 3rem;
  background: var(--bg-white);
  border-radius: 24px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  width: 100% !important;
  max-width: none !important;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.8);
}

.form-container:hover {
  box-shadow: 0 20px 60px rgba(6, 184, 247, 0.12), 0 8px 24px rgba(0, 0, 0, 0.08);
  transform: translateY(-2px);
}

.logo {
  text-align: center;
  padding-bottom: 30px;
  margin-bottom: 30px;
}

.logo img {
  max-width: 200px;
  height: auto;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.08));
}

.logo img:hover {
  transform: scale(1.05) translateY(-2px);
  filter: drop-shadow(0 8px 20px rgba(6, 184, 247, 0.2));
}

.form-container h1 {
  text-align: center;
  margin-bottom: 2rem;
  color: var(--text-dark);
  font-weight: 600;
  font-size: 1.75rem;
  letter-spacing: -0.5px;
}

.form-container a {
  text-decoration: none;
  color: var(--primary-blue);
  transition: color 0.3s ease;
  font-weight: 500;
}

.form-container a:hover {
  color: #05a0d6;
  text-decoration: underline;
}

.login-btn {
  cursor: pointer;
  display: inline-block;
  width: 100%;
  background: linear-gradient(135deg, var(--primary-blue) 0%, #05a0d6 100%);
  padding: 1rem 1.5rem;
  font-family: inherit;
  font-weight: 600;
  font-size: 1.063rem;
  color: var(--bg-white);
  border: 0;
  border-radius: 12px;
  margin-bottom: 1.5rem;
  margin-top: 0.5rem;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 16px rgba(6, 184, 247, 0.35), 0 2px 8px rgba(6, 184, 247, 0.2);
  position: relative;
  overflow: hidden;
}

.login-btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s;
}

.login-btn:hover::before {
  left: 100%;
}

.login-btn:hover {
  background: linear-gradient(135deg, #05a0d6 0%, #0489b8 100%);
  transform: translateY(-3px);
  box-shadow: 0 8px 24px rgba(6, 184, 247, 0.45), 0 4px 12px rgba(6, 184, 247, 0.3);
}

.login-btn:active {
  transform: translateY(0);
  box-shadow: 0 2px 6px rgba(6, 184, 247, 0.3);
}

.login-btn:focus {
  outline: 0;
  box-shadow: 0 0 0 3px rgba(6, 184, 247, 0.2);
}

.text {
  margin-top: 1.25rem;
  text-align: center;
  color: var(--text-gray);
  font-size: 0.938rem;
}

.text a {
  font-weight: 600;
  transition: all 0.3s ease;
}

.text a:hover {
  color: #05a0d6;
  text-decoration: none;
  transform: translateY(-1px);
}

.form-control {
  position: relative;
  margin: 2rem 0;
}

.form-control input {
  background: transparent;
  border: 0;
  border-bottom: 2px solid var(--border-color);
  display: block;
  width: 100%;
  padding: 1.125rem 0 0.625rem 0;
  font-size: 1.063rem;
  color: var(--text-dark);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.form-control input:focus {
  outline: 0;
  border-bottom-color: var(--primary-blue);
  border-bottom-width: 3px;
  padding-bottom: 0.5rem;
}

.form-control input::placeholder {
  color: transparent;
}

.form-control label {
  position: absolute;
  top: 1rem;
  left: 0;
  color: var(--text-gray);
  pointer-events: none;
  transition: all 0.3s ease;
}

.form-control label span {
  display: inline-block;
  font-size: 1rem;
  min-width: 0.313rem;
  transition: 0.3s cubic-bezier(0.53, 0.246, 0.265, 1.66);
}

.form-control input:focus + label span,
.form-control input:valid + label span {
  color: var(--primary-blue);
  transform: translateY(-2rem);
  font-size: 0.813rem;
  font-weight: 600;
  letter-spacing: 0.3px;
}

.form-control .invalid-feedback {
  display: block;
  color: #dc3545;
  font-size: 0.875rem;
  margin-top: 0.5rem;
  padding-left: 0;
}

.form-control input.is-invalid {
  border-bottom-color: #dc3545;
}

.form-control input.is-invalid + label span {
  color: #dc3545;
}

/* Estilo para alertas de validação */
.alert {
  border-radius: 12px;
  padding: 1rem 1.25rem;
  margin-bottom: 1.5rem;
  font-size: 0.938rem;
  line-height: 1.5;
}

.alert-danger {
  background: linear-gradient(135deg, #fee 0%, #fdd 100%);
  border: 1px solid #fcc;
  color: #c33;
  box-shadow: 0 2px 8px rgba(204, 51, 51, 0.1);
}

.alert-danger ul {
  margin: 0.5rem 0 0 0;
  padding-left: 1.25rem;
}

.alert-danger li {
  margin-bottom: 0.25rem;
}

.alert-success {
  background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
  border: 1px solid #6ee7b7;
  color: #065f46;
  box-shadow: 0 2px 8px rgba(16, 185, 129, 0.1);
}

/* Responsividade */
@media (max-width: 768px) {
  body {
    padding: 15px;
  }

  .form-container {
    padding: 2.5rem 2rem;
    border-radius: 20px;
  }

  .form-container h1 {
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
  }

  .logo img {
    max-width: 150px;
  }

  .login-btn {
    font-size: 1rem;
    padding: 0.875rem 1.25rem;
  }
}

@media (max-width: 576px) {
  body {
    padding: 10px;
    background: var(--bg-white);
  }

  .login-form-bd {
    max-width: 100% !important;
    min-width: auto !important;
  }

  .form-container {
    padding: 2rem 1.5rem;
    border-radius: 20px;
    box-shadow: var(--shadow);
  }

  .form-container h1 {
    font-size: 1.375rem;
    margin-bottom: 1.25rem;
  }

  .logo {
    padding-bottom: 15px;
    margin-bottom: 15px;
  }

  .logo img {
    max-width: 120px;
  }

  .form-control {
    margin: 1.25rem 0;
  }

  .form-control input {
    padding: 0.875rem 0 0.5rem 0;
    font-size: 0.938rem;
  }

  .login-btn {
    font-size: 0.938rem;
    padding: 0.813rem 1rem;
    margin-bottom: 1rem;
  }

  .text {
    font-size: 0.875rem;
  }
}

@media (max-width: 375px) {
  .form-container {
    padding: 1.5rem 1.25rem;
  }

  .form-container h1 {
    font-size: 1.25rem;
  }

  .logo img {
    max-width: 100px;
  }
}

    </style>

</head>
<body>
    <div id="app">
        <main class="">
            @yield('content')
        </main>
    </div>


    <script>

const labels = document.querySelectorAll(".form-control label");

labels.forEach((label) => {
  label.innerHTML = label.innerText
    .split("")
    .map(
      (letter, idx) =>
        `<span style="transition-delay:${idx * 50}ms">${letter}</span>`
    )
    .join("");
});


    </script>
</body>
</html>
