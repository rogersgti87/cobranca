@extends('layouts.app')

@section('content')

<div class="login-form-bd">
    <div class="form-wrapper">
        <div class="form-container">
            <div class="logo">
                <img alt="logo" src="{{url('/img/logo.png?')}}{{mt_rand(0,999)}}">
            </div>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-control">
                    <input
                        id="email"
                        type="email"
                        class="@error('email') is-invalid @enderror"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
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

                <div class="form-control">
                    <input
                        id="password"
                        type="password"
                        class="@error('password') is-invalid @enderror"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder=" "
                    >
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <label>
                        <span>Senha</span>
                    </label>
                </div>

                <button type="submit" class="login-btn">Acessar</button>

                @if (Route::has('password.request'))
                    <p class="text">
                        <a href="{{ route('password.request') }}">Esqueceu a senha?</a>
                    </p>
                @endif
            </form>
        </div>
    </div>
</div>

@endsection
