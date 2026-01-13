@extends('layouts.app')

@section('content')

<div class="login-form-bd">
    <div class="form-wrapper">
        <div class="form-container">
            <div class="logo">
                <img alt="logo" src="{{url('/img/logo.png?')}}{{mt_rand(0,999)}}">
            </div>
            <h1>Redefinir Senha</h1>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email Address -->
                <div class="form-control">
                    <input
                        id="email"
                        type="email"
                        class="@error('email') is-invalid @enderror"
                        name="email"
                        value="{{ old('email', $request->email) }}"
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

                <!-- Password -->
                <div class="form-control">
                    <input
                        id="password"
                        type="password"
                        class="@error('password') is-invalid @enderror"
                        name="password"
                        required
                        placeholder=" "
                    >
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <label>
                        <span>Nova Senha</span>
                    </label>
                </div>

                <!-- Confirm Password -->
                <div class="form-control">
                    <input
                        id="password_confirmation"
                        type="password"
                        class="@error('password_confirmation') is-invalid @enderror"
                        name="password_confirmation"
                        required
                        placeholder=" "
                    >
                    @error('password_confirmation')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <label>
                        <span>Confirmar Nova Senha</span>
                    </label>
                </div>

                <button type="submit" class="login-btn">Redefinir Senha</button>
            </form>
        </div>
    </div>
</div>

@endsection
