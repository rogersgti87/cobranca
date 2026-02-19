@extends('layouts.app')

@section('content')

<div class="login-form-bd">
    <div class="form-wrapper">
        <div class="form-container">
            <div class="logo-modern">
                <div class="logo-background">
                    <img alt="logo" src="{{url('/img/logo.png?')}}{{mt_rand(0,999)}}">
                </div>
                <h1 class="welcome-text">Bem-vindo</h1>
                <p class="subtitle-text">Faça login para continuar</p>
            </div>
            
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="form-group-modern">
                    <label for="email" class="form-label-modern">E-mail</label>
                    <input
                        id="email"
                        type="email"
                        class="form-input-modern @error('email') is-invalid @enderror"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                        autofocus
                        placeholder="Digite seu e-mail"
                    >
                    @error('email')
                        <span class="error-message" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group-modern">
                    <label for="password" class="form-label-modern">Senha</label>
                    <input
                        id="password"
                        type="password"
                        class="form-input-modern @error('password') is-invalid @enderror"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="Digite sua senha"
                    >
                    @error('password')
                        <span class="error-message" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <button type="submit" class="login-btn-modern">
                    <span>Acessar</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </button>

                @if (Route::has('password.request'))
                    <div class="forgot-password">
                        <a href="{{ route('password.request') }}" class="forgot-link">
                            Esqueceu a senha?
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>

<style>
.login-form-bd {
    min-height: 100vh;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    background: linear-gradient(135deg, #e5f7fd 0%, #f3f4f6 50%, #ffffff 100%);
    padding: 0 15px 15px 15px;
    overflow-y: auto;
}

.form-wrapper {
    width: 100%;
    max-width: 440px;
    max-height: 95vh;
    overflow-y: auto;
}

.form-container {
    background: #ffffff;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 20px 60px rgba(6, 184, 247, 0.15), 0 8px 24px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(6, 184, 247, 0.1);
}

.logo-modern {
    text-align: center;
    margin-bottom: 28px;
}

.logo-background {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 140px;
    height: 90px;
    background: #ffffff;
    border-radius: 16px;
    margin-bottom: 16px;
    border: 2px solid #e2e8f0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.logo-background:hover {
    border-color: #06b8f7;
    box-shadow: 0 6px 20px rgba(6, 184, 247, 0.15);
}

.logo-background img {
    max-width: 120px;
    max-height: 70px;
    object-fit: contain;
}

.welcome-text {
    font-size: 24px;
    font-weight: 700;
    color: #333333;
    margin: 0 0 6px 0;
}

.subtitle-text {
    font-size: 14px;
    color: #666666;
    margin: 0;
}

.form-group-modern {
    margin-bottom: 18px;
}

.form-label-modern {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #333333;
    margin-bottom: 6px;
}

.form-input-modern {
    width: 100%;
    padding: 12px 14px;
    font-size: 14px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    transition: all 0.3s ease;
    background: #f7fafc;
    color: #333333;
    box-sizing: border-box;
}

.form-input-modern:focus {
    outline: none;
    border-color: #06b8f7;
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(6, 184, 247, 0.1);
}

.form-input-modern::placeholder {
    color: #a0aec0;
}

.form-input-modern.is-invalid {
    border-color: #fc8181;
    background: #fff5f5;
}

.error-message {
    display: block;
    margin-top: 6px;
    font-size: 12px;
    color: #e53e3e;
}

.login-btn-modern {
    width: 100%;
    padding: 14px;
    font-size: 15px;
    font-weight: 600;
    color: #ffffff;
    background: linear-gradient(135deg, #06b8f7 0%, #05a0d6 100%);
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 4px 16px rgba(6, 184, 247, 0.4);
}

.login-btn-modern:hover {
    background: linear-gradient(135deg, #05a0d6 0%, #0489b8 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(6, 184, 247, 0.5);
}

.login-btn-modern:active {
    transform: translateY(0);
}

.forgot-password {
    text-align: center;
    margin-top: 18px;
}

.forgot-link {
    color: #06b8f7;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: color 0.3s ease;
}

.forgot-link:hover {
    color: #05a0d6;
    text-decoration: underline;
}

@media (max-width: 480px) {
    .form-container {
        padding: 24px 20px;
    }
    
    .logo-background {
        width: 120px;
        height: 75px;
    }
    
    .logo-background img {
        max-width: 100px;
        max-height: 55px;
    }
    
    .welcome-text {
        font-size: 20px;
    }
    
    .subtitle-text {
        font-size: 13px;
    }
}

@media (max-height: 700px) {
    .form-container {
        padding: 24px;
    }
    
    .logo-modern {
        margin-bottom: 20px;
    }
    
    .logo-background {
        width: 120px;
        height: 75px;
        margin-bottom: 12px;
    }
    
    .logo-background img {
        max-width: 100px;
        max-height: 55px;
    }
    
    .welcome-text {
        font-size: 20px;
        margin-bottom: 4px;
    }
    
    .subtitle-text {
        font-size: 13px;
    }
    
    .form-group-modern {
        margin-bottom: 14px;
    }
    
    .forgot-password {
        margin-top: 14px;
    }
}
</style>

@endsection
