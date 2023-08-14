@extends('layouts.app')

@section('content')


<div class="login-form-bd">
    <div class="form-wrapper">
      <div class="form-container">
        <div class="logo">
            <img alt="logo" src="{{url('/img/logo.png?')}}{{mt_rand(0,999)}}">
          </div>
        <h1> Acessar Plataforma</h1>
        <form method="POST" action="{{ route('login') }}">
            @csrf
          <div class="form-control">
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                <label> E-mail</label>
          </div>

          <div class="form-control">
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
            <label> Senha</label>
          </div>
          <button type="submit" class="login-btn">Acessar</button>
            @if (Route::has('password.request'))
              <p class="text"><a href="{{ route('password.request') }}"> Esqueceu a senha?</a></p>
            @endif
        </form>
      </div>
    </div>
  </div>




@endsection
