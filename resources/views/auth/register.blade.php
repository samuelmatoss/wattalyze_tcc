<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar - Sistema de Monitoramento Energético</title>
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
            width: 100px;
            height: 100px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-elements::after {
            width: 60px;
            height: 60px;
            top: 70%;
            right: 10%;
            animation-delay: 4s;
        }

        @keyframes float-elements {
            0%, 100% { transform: translateY(0px) scale(1); opacity: 0.7; }
            50% { transform: translateY(-30px) scale(1.1); opacity: 0.3; }
        }

        .progress-dot {
            background-color: #27ae60;
        }

        .progress-dot-2 {
            background-color: #27ae60;
            opacity: 0.7;
        }

        .progress-dot-3 {
            background-color: #27ae60;
            opacity: 0.4;
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
        .aa{
            margin-top: -3vh;
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative">
    <div class="floating-elements"></div>
    
    <div class="max-w-md w-full space-y-8 relative z-10  aa">
        <!-- Header com animação -->
        <div class="text-center">
            <a href="{{ route('welcome') }}">
            <div class="relative inline-block">
                <div class="pulse-ring absolute inset-0 bg-white rounded-full opacity-20"></div>
                <div class="logo-gradient mx-auto h-16 w-16 rounded-full flex items-center justify-center mb-4 animate-bounce-slow">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
            </a>

        </div>

        <!-- Formulário Principal -->
        <div class="glass-card rounded-2xl p-8 space-y-6">
            <!-- Indicador de Progresso -->
            <div class="flex justify-center mb-6">
                <div class="flex space-x-2">
                    <div class="w-2 h-2 rounded-full animate-pulse progress-dot"></div>
                    <div class="w-2 h-2 rounded-full animate-pulse progress-dot-2" style="animation-delay: 0.2s;"></div>
                    <div class="w-2 h-2 rounded-full animate-pulse progress-dot-3" style="animation-delay: 0.4s;"></div>
                </div>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf
                <!-- Nome -->
                <div class="group">
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nome Completo <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <div class="feature-icon w-5 h-5">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        </div>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               class="input-glass w-full pl-12 pr-4 py-4 rounded-xl text-gray-800 placeholder-gray-500 focus:outline-none"
                               placeholder="Digite seu nome completo"
                               required>
                    </div>
                </div>

                <!-- Email -->
                <div class="group">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
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
                               required>
                    </div>
                </div>

                <!-- Senha -->
                <div class="group">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        Senha <span class="text-red-500">*</span>
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
                               placeholder="Mínimo 8 caracteres"
                               required>
                    </div>
                    <!-- Indicador de força da senha -->
                    <div class="mt-2 h-1 bg-gray-200 rounded overflow-hidden">
                        <div id="password-strength-bar" class="h-full transition-all duration-300 rounded" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Confirmar Senha -->
                <div class="group">
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                        Confirmar Senha <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <div class="feature-icon w-5 h-5">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <input type="password" 
                               name="password_confirmation" 
                               id="password_confirmation"
                               class="input-glass w-full pl-12 pr-4 py-4 rounded-xl text-gray-800 placeholder-gray-500 focus:outline-none"
                               placeholder="Digite a senha novamente"
                               required>
                    </div>
                </div>

                <!-- Termos -->
                <div class="flex items-start">
                    <div class="flex items-center h-5 mt-1">
                        <input id="terms" 
                               name="terms" 
                               type="checkbox" 
                               class="checkbox-green h-4 w-4 focus:ring-2 border-gray-300 rounded transition-all duration-200"
                               required>
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="terms" class="text-gray-700">
                            Eu concordo com os 
                            <a href="#" class="link-green font-medium hover:underline transition-all duration-200">Termos de Uso</a>
                            e 
                            <a href="#" class="link-green font-medium hover:underline transition-all duration-200">Política de Privacidade</a>
                        </label>
                    </div>
                </div>

                <!-- Botão de Submit -->
                <button type="submit" 
                        class="btn-primary w-full flex justify-center items-center gap-3 text-white font-semibold py-4 px-6 rounded-xl relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Criar Conta
                </button>

                <!-- Link para Login -->
                <div class="text-center">
                    <span class="text-sm text-gray-600">Já tem uma conta?</span>
                    <a href="{{ route('login') }}" class="link-green font-semibold hover:underline transition-colors duration-200 ml-1">
                        Fazer login
                    </a>
                </div>
            </form>
        </div>



        <!-- Footer -->
        <div class="text-center text-sm text-white/70">
            <p>&copy; 2025 Wattalyze. Todos os direitos reservados.</p>
        </div>
    </div>

    <script>
        // Animações e validações
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('password-strength-bar');
            let strength = 0;
            
            if (password.length >= 8) strength += 25;
            if (password.match(/[a-z]/)) strength += 25;
            if (password.match(/[A-Z]/)) strength += 25;
            if (password.match(/[0-9]/) || password.match(/[^a-zA-Z0-9]/)) strength += 25;
            
            strengthBar.style.width = strength + '%';
            
            if (strength < 50) {
                strengthBar.style.background = 'linear-gradient(to right, #e74c3c, #f39c12)';
            } else if (strength < 75) {
                strengthBar.style.background = 'linear-gradient(to right, #f39c12, #f1c40f)';
            } else {
                strengthBar.style.background = 'linear-gradient(to right, #27ae60, #2ecc71)';
            }
        });

        // Validação de confirmação de senha
        document.getElementById('password_confirmation').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password === confirmPassword && password.length >= 8) {
                this.style.borderColor = '#27ae60';
            } else if (confirmPassword.length > 0) {
                this.style.borderColor = '#e74c3c';
            }
        });

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
                }, index * 200);
            });
        });
    </script>
</body>
</html>