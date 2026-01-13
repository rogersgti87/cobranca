@extends('layouts.app')

@section('content')

<div class="login-form-bd">
    <div class="form-wrapper">
        <div class="form-container">
            <div class="logo">
                <img alt="logo" src="{{url('/img/logo.png?')}}{{mt_rand(0,999)}}">
            </div>
            <h1>Esqueceu a Senha?</h1>

            <p style="text-align: center; color: var(--text-gray); font-size: 0.938rem; margin-bottom: 2rem; line-height: 1.6;">
                Sem problemas. Informe seu endereço de e-mail e enviaremos um link para redefinir sua senha.
            </p>

            <!-- Session Status -->
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email Address -->
                <div class="form-control">
                    <input
                        id="email"
                        type="email"
                        class="@error('email') is-invalid @enderror"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        placeholder=" "
                    >
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <label>
                        <span>E-mail</span>
                    </label>
                </div>

                <button type="submit" class="login-btn">Enviar Link de Redefinição</button>

                <p class="text">
                    <a href="{{ route('login') }}">Voltar para o login</a>
                </p>
            </form>
        </div>
    </div>
</div>

@endsection
