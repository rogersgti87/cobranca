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

* {
  box-sizing: border-box;
  margin: 0;
}

body {
  font-family: "Roboto", sans-serif;
  margin:0 auto;
}

/* FORM STYLE */
.form-wrapper {
  background: #1b1a1a;
}

.login-form-bd {
  background: #fff;
  color: #000;
  padding:100px 300px;
}

.form-container {
  font-family: "Poppins", sans-serif;
  font-size: 1rem;
  padding: 5rem 2.5rem;
  border-radius: 0.313rem;
  box-shadow: 3px 0.25rem 1.25rem rgba(27, 27, 27, 0.2);
}

.form-container h1 {
  text-align: center;
  margin-bottom: 1.75rem;
  color: #ffff;
  font-weight: normal;
  font-size: 1.7rem;
}

.form-container a {
  text-decoration: none;
  color: #ffbd59;
}

.login-btn {
  cursor: pointer;
  display: inline-block;
  width: 100%;
  background: #ffbd59;
  padding: 0.938rem;
  font-family: inherit;
  font-weight: 500;
  font-size: 1.563rem;
  color: #0d0f42;
  border: 0;
  border-radius: 0.313rem;
  margin-bottom: 1.25rem;
}

.login-btn:focus {
  outline: 0;
}

.login-btn:active {
  transform: scale(0.98);
}

.text {
  margin-top: 0.938rem;
}

.form-control {
  position: relative;
  margin: 0.25rem 0 0.25rem;
}

.form-control input {
  background: transparent;
  border: 0;
  border-bottom: 1px #fff solid;
  display: block;
  width: 100%;
  padding: 1rem 0;
  font-size: 1rem;
  color: #fff;
}

.form-control input:focus {
  outline: 0;
  border-bottom-color: #ffbd59;
}

.form-control label {
  position: absolute;
  top: 0.938rem;
  left: 10;
}

.form-control label span {
  display: inline-block;
  font-size: 1rem;
  min-width: 0.313rem;
  transition: 0.3s cubic-bezier(0.53, 0.246, 0.265, 1.66);
}

.form-control input:focus + label span,
.form-control input:valid + label span {
  color: #ffbd59;
  transform: translateY(-1.875rem);
}
.logo{
    text-align: center;
    padding-bottom:10px;
    margin-bottom: 5px;
}
@media(max-width: 576px){
.form-wrapper {
  margin: 0 auto;
  max-width: 100%;
}
.form-control input {
  background: transparent;
  border: 0;
  border-bottom: 1px #fff solid;
  display: block;
  width: 100%;
  padding: 0;
  font-size: 1rem;
  color: #fff;
}

.login-btn {
  cursor: pointer;
  display: inline-block;
  width: 100%;
  background: #ffbd59;
  padding: 0;
  font-family: inherit;
  font-weight: 500;
  font-size: 1.2rem;
  color: #000;
  border: 0;
  border-radius: 0.313rem;
  margin-bottom: 1.25rem;
  padding:7px;
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
