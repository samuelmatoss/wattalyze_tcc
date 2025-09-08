<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Monitoramento Energético</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            font-family: 'Inter', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #2c3e50 0%, #27ae60 100%);
            position: relative;
            overflow: hidden;
        }

        .gradient-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 30% 20%, rgba(39, 174, 96, 0.3) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 40% 40%, rgba(44, 62, 80, 0.2) 0%, transparent 50%);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(1deg); }
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .input-glass {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .input-glass:focus {
            background: rgba(255, 255, 255, 0.95);
            border-color: #27ae60;
            box-shadow: 0 0 0 4px rgba(39, 174, 96, 0.1);
            transform: translateY(-1px);
        }

        .btn-primary {
            background: linear-gradient(135deg, #2c3e50 0%, #27ae60 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px -12px rgba(39, 174, 96, 0.4);
        }

        .feature-icon {
            background: linear-gradient(135deg, #2c3e50, #27ae60);
            color: white;
            border-radius: 12px;
            padding: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .animate-bounce-slow {
            animation: bounce-slow 3s infinite;
        }

        @keyframes bounce-slow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .pulse-ring {
            animation: pulse-ring 2s cubic-bezier(0.455, 0.03, 0.515, 0.955) infinite;
        }

        @keyframes pulse-ring {
            0% { transform: scale(0.8); opacity: 1; }
            100% { transform: scale(2.4); opacity: 0; }
        }

        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .floating-elements::before,
        .floating-elements::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float-elements 8s ease-in-out infinite;
        }

        .floating-elements::before {
            width: 80px;
            height: 80px;
            top: 15%;
            left: 15%;
            animation-delay: 0s;
        }

        .floating-elements::after {
            width: 50px;
            height: 50px;
            top: 65%;
            right: 15%;
            animation-delay: 4s;
        }

        @keyframes float-elements {
            0%, 100% { transform: translateY(0px) scale(1); opacity: 0.7; }
            50% { transform: translateY(-30px) scale(1.1); opacity: 0.3; }
        }

        .link-green {
            color: #27ae60;
        }

        .link-green:hover {
            color: #219a52;
        }

        .checkbox-green {
            accent-color: #27ae60;
        }

        .logo-gradient {
            background: linear-gradient(135deg, #27ae60, #2c3e50);
        }

        .alert-success {
            background: rgba(39, 174, 96, 0.1);
            border-color: #27ae60;
            color: #1e7e34;
        }

        .alert-error {
            background: rgba(231, 76, 60, 0.1);
            border-color: #e74c3c;
            color: #721c24;
        }

        .alert-demo {
            background: rgba(241, 196, 15, 0.1);
            border-color: #f1c40f;
            color: #856404;
        }

        .slide-in {
            animation: slideIn 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative">
    <div class="floating-elements"></div>
    
    <div class="max-w-md w-full space-y-6 relative z-10">
        <!-- Header com animação -->
         <a href="{{ route('welcome') }}">
        <div class="text-center">
            <div class="relative inline-block">
                <div class="pulse-ring absolute inset-0 bg-white rounded-full opacity-20"></div>
                <div class="logo-gradient mx-auto h-16 w-16 rounded-full flex items-center justify-center mb-4 animate-bounce-slow">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
            </a>
            <h2 class="text-4xl font-bold text-white mb-2">Wattalyze</h2>
            <p class="text-xl text-white/90 mb-1">Bem-vindo de volta</p>
            <p class="text-sm text-white/70">
                Entre na sua conta para continuar
            </p>
        </div>

        <!-- Mensagens de Status -->
        <div id="alerts-container">
            <!-- Success Message -->
            <div class="glass-card alert-success border rounded-2xl p-4 mb-4 flex items-center slide-in" style="display: none;" id="success-alert">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span id="success-text">Operação realizada com sucesso!</span>
            </div>

            <!-- Error Message -->
            <div class="glass-card alert-error border rounded-2xl p-4 mb-4 slide-in" style="display: none;" id="error-alert">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h4 class="font-medium mb-2">Erro ao fazer login</h4>
                        <ul class="text-sm space-y-1" id="error-list">
                            <!-- Erros serão inseridos aqui via JavaScript -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>

       <form method="POST" action="{{ route('login') }}" class="space-y-6 glass-card rounded-2xl p-8">
        @csrf
        
            <form class="space-y-6" id="login-form">
                <!-- Email -->
                <div class="group">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <div class="feature-icon w-5 h-5">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                        </div>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               class="input-glass w-full pl-12 pr-4 py-4 rounded-xl text-gray-800 placeholder-gray-500 focus:outline-none"
                               placeholder="Digite seu email"
                               required 
                               autofocus>
                    </div>
                </div>

                <!-- Password -->
                <div class="group">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        Senha
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <div class="feature-icon w-5 h-5">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                        </div>
                        <input type="password" 
                               name="password" 
                               id="password"
                               class="input-glass w-full pl-12 pr-4 py-4 rounded-xl text-gray-800 placeholder-gray-500 focus:outline-none"
                               placeholder="Digite sua senha"
                               required>
                    </div>
                </div>

                <!-- Remember Me e Esqueceu Senha -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" 
                               name="remember" 
                               type="checkbox" 
                               class="checkbox-green h-4 w-4 focus:ring-2 border-gray-300 rounded transition-all duration-200">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            Lembrar-me
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="link-green font-medium hover:underline transition-all duration-200">
                            Esqueceu a senha?
                        </a>
                    </div>
                </div>

                <!-- Botão de Submit -->
                <button type="submit" 
                        class="btn-primary w-full flex justify-center items-center gap-3 text-white font-semibold py-4 px-6 rounded-xl relative"
                        id="login-btn">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    <span id="btn-text">Entrar</span>
                </button>

                <!-- Link para Registro -->
                <div class="text-center">
                    <span class="text-sm text-gray-600">Não tem uma conta?</span>
                    <a href="{{ route('register') }}" class="link-green font-semibold hover:underline transition-colors duration-200 ml-1">
                        Criar conta
                    </a>
                </div>
            </form>
 


        </form> 
        <!-- Footer -->
        <div class="text-center text-sm text-white/70">
            <p>&copy; 2025 Wattalyze. Todos os direitos reservados.</p>
        </div>
    </div>

    <script>


        // Simulação de login (para demonstração)
        document.getElementById('login-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const btn = document.getElementById('login-btn');
            const btnText = document.getElementById('btn-text');
            
            // Loading state
            btn.disabled = true;
            btnText.textContent = 'Entrando...';
            btn.style.opacity = '0.7';
            
            // Simular requisição
            setTimeout(() => {
                if (email === 'demo@wattalyze.com' && password === '123456') {
                    // Sucesso
                    showAlert('success', 'Login realizado com sucesso! Redirecionando...');
                    setTimeout(() => {
                        window.location.href = '/dashboard'; // Redirecionar para dashboard
                    }, 1500);
                } else {
                    // Erro
                    showAlert('error', ['Email ou senha incorretos.']);
                    btn.disabled = false;
                    btnText.textContent = 'Entrar';
                    btn.style.opacity = '1';
                }
            }, 1000);
        });

        // Função para mostrar alertas
        function showAlert(type, message) {
            hideAllAlerts();
            
            if (type === 'success') {
                const alert = document.getElementById('success-alert');
                document.getElementById('success-text').textContent = message;
                alert.style.display = 'flex';
                
                // Auto-hide após 5 segundos
                setTimeout(() => {
                    hideAlert(alert);
                }, 5000);
            } else if (type === 'error') {
                const alert = document.getElementById('error-alert');
                const errorList = document.getElementById('error-list');
                errorList.innerHTML = '';
                
                if (Array.isArray(message)) {
                    message.forEach(error => {
                        const li = document.createElement('li');
                        li.textContent = error;
                        errorList.appendChild(li);
                    });
                } else {
                    const li = document.createElement('li');
                    li.textContent = message;
                    errorList.appendChild(li);
                }
                
                alert.style.display = 'block';
            }
        }

        function hideAllAlerts() {
            document.getElementById('success-alert').style.display = 'none';
            document.getElementById('error-alert').style.display = 'none';
        }

        function hideAlert(alert) {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.style.display = 'none';
                alert.style.opacity = '1';
            }, 300);
        }

        // Animação de entrada dos elementos
        window.addEventListener('load', function() {
            const elements = document.querySelectorAll('.glass-card');
            elements.forEach((el, index) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    el.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, index * 150 + 200);
            });
        });

        // Validação visual em tempo real
        document.getElementById('email').addEventListener('input', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && emailRegex.test(this.value)) {
                this.style.borderColor = '#27ae60';
            } else if (this.value) {
                this.style.borderColor = '#e74c3c';
            } else {
                this.style.borderColor = '';
            }
        });

        document.getElementById('password').addEventListener('input', function() {
            if (this.value.length >= 6) {
                this.style.borderColor = '#27ae60';
            } else if (this.value.length > 0) {
                this.style.borderColor = '#f39c12';
            } else {
                this.style.borderColor = '';
            }
        });
    </script>
</body>
</html>