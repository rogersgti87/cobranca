<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Autenticação</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">


    <style>
:root {
  --gold: #D4AF37;
  --gold-light: #F0C14B;
  --gold-dark: #B8960C;
  --bg-base: #0A0A0C;
  --bg-surface: #1A1A20;
  --bg-elevated: #121216;
  --text-primary: #F0F0F2;
  --text-secondary: #9898A4;
  --border-gold: rgba(212, 175, 55, 0.25);
}

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: 'Inter', 'Source Sans Pro', sans-serif;
  margin: 0 !important;
  background: var(--bg-base);
  background-image:
    radial-gradient(ellipse at 20% 50%, rgba(212, 175, 55, 0.06) 0%, transparent 50%),
    radial-gradient(ellipse at 80% 20%, rgba(212, 175, 55, 0.04) 0%, transparent 40%);
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px !important;
  width: 100% !important;
}

#app {
  position: relative;
  z-index: 1;
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

#app .container,
#app .container-fluid,
#app main.container,
#app main.container-fluid {
  width: 100% !important;
  max-width: none !important;
  padding: 0 !important;
}

.login-form-bd {
  width: 100% !important;
  max-width: 440px !important;
  margin: 0 auto !important;
  flex: 0 0 auto;
  padding: 0 !important;
}

.form-wrapper {
  width: 100% !important;
}

.form-container {
  font-family: 'Inter', sans-serif;
  font-size: 1rem;
  padding: 3rem 2.5rem;
  background: var(--bg-surface);
  border-radius: 20px;
  box-shadow: 0 8px 40px rgba(0, 0, 0, 0.5), 0 0 0 1px var(--border-gold);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  width: 100% !important;
}

.form-container:hover {
  box-shadow: 0 12px 48px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(212, 175, 55, 0.4), 0 4px 20px rgba(212, 175, 55, 0.1);
  transform: translateY(-2px);
}

.logo {
  text-align: center;
  padding-bottom: 24px;
  margin-bottom: 24px;
  border-bottom: 1px solid rgba(212, 175, 55, 0.15);
}

.logo img {
  max-width: 180px;
  height: auto;
  transition: all 0.3s ease;
  filter: drop-shadow(0 4px 12px rgba(212, 175, 55, 0.2));
}

.logo img:hover {
  transform: scale(1.03);
  filter: drop-shadow(0 8px 20px rgba(212, 175, 55, 0.3));
}

.form-container h1 {
  text-align: center;
  margin-bottom: 2rem;
  color: var(--text-primary);
  font-weight: 700;
  font-size: 1.5rem;
  letter-spacing: -0.02em;
}

.form-container a {
  text-decoration: none;
  color: var(--gold);
  transition: color 0.3s ease;
  font-weight: 500;
}

.form-container a:hover {
  color: var(--gold-light);
}

.login-btn {
  cursor: pointer;
  display: inline-block;
  width: 100%;
  background: linear-gradient(135deg, var(--gold-dark) 0%, var(--gold) 100%);
  padding: 1rem 1.5rem;
  font-family: inherit;
  font-weight: 600;
  font-size: 1rem;
  color: #0A0A0C;
  border: 0;
  border-radius: 12px;
  margin-bottom: 1.5rem;
  margin-top: 0.5rem;
  transition: all 0.3s ease;
  box-shadow: 0 4px 16px rgba(212, 175, 55, 0.3);
}

.login-btn:hover {
  background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(212, 175, 55, 0.4);
  color: #0A0A0C;
}

.login-btn:focus {
  outline: 0;
  box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.25);
}

.text {
  margin-top: 1.25rem;
  text-align: center;
  color: var(--text-secondary);
  font-size: 0.938rem;
}

.form-control {
  position: relative;
  margin: 2rem 0;
}

.form-control input {
  background: transparent;
  border: 0;
  border-bottom: 2px solid rgba(255, 255, 255, 0.1);
  display: block;
  width: 100%;
  padding: 1.125rem 0 0.625rem 0;
  font-size: 1rem;
  color: var(--text-primary);
  transition: all 0.3s ease;
}

.form-control input:focus {
  outline: 0;
  border-bottom-color: var(--gold);
  border-bottom-width: 2px;
}

.form-control input::placeholder {
  color: transparent;
}

.form-control label {
  position: absolute;
  top: 1rem;
  left: 0;
  color: var(--text-secondary);
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
  color: var(--gold);
  transform: translateY(-2rem);
  font-size: 0.813rem;
  font-weight: 600;
}

.form-control .invalid-feedback {
  display: block;
  color: #F87171;
  font-size: 0.875rem;
  margin-top: 0.5rem;
}

.form-control input.is-invalid {
  border-bottom-color: #F87171;
}

.alert {
  border-radius: 12px;
  padding: 1rem 1.25rem;
  margin-bottom: 1.5rem;
  font-size: 0.938rem;
}

.alert-danger {
  background: rgba(248, 113, 113, 0.12);
  border: 1px solid rgba(248, 113, 113, 0.3);
  color: #FCA5A5;
}

.alert-success {
  background: rgba(34, 197, 94, 0.12);
  border: 1px solid rgba(34, 197, 94, 0.3);
  color: #4ADE80;
}

@media (max-width: 576px) {
  .form-container {
    padding: 2rem 1.5rem;
    border-radius: 16px;
  }

  .logo img {
    max-width: 140px;
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
