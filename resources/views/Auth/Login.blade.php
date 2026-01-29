<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventario</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        :root {
            --blue-900: #1e3a8a;
            --blue-600: #2563eb;
            --blue-400: #60a5fa;
            --bg-white: #ffffff;
            --bg-light: #f8fafc;
            --text-main: #0f172a;
            --text-secondary: #64748b;
            --main-gradient: linear-gradient(135deg, #172554 0%, #1e40af 50%, #3b82f6 100%);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }

        body { height: 100vh; display: flex; background: var(--bg-white); overflow: hidden; }

        /* --- IZQUIERDA --- */
        .left-panel {
            flex: 1.2;
            background: var(--main-gradient);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            color: white;
            overflow: hidden;
        }

        .circle-decoration {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(to right, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
            z-index: 1;
        }
        .circle-1 { width: 400px; height: 400px; top: -100px; right: -50px; }
        .circle-2 { width: 600px; height: 600px; bottom: -200px; left: -100px; }

        .panel-content { position: relative; z-index: 10; }

        .welcome-text h2 { font-size: 3rem; font-weight: 700; line-height: 1.1; margin-bottom: 20px; }
        .welcome-text p { font-size: 1.1rem; color: rgba(255, 255, 255, 0.8); max-width: 400px; }

        .glass-card {
            margin-top: 40px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-width: 300px;
            display: flex; align-items: center; gap: 15px;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        /* --- DERECHA --- */
        .right-panel {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            background: var(--bg-white);
            padding: 40px;
        }

        .login-wrapper { width: 100%; max-width: 420px; }

        .login-header { margin-bottom: 35px; }
        .login-header h1 { font-size: 2rem; color: var(--text-main); font-weight: 700; margin-bottom: 10px; }
        .login-header p { color: var(--text-secondary); }

        .form-group { margin-bottom: 24px; }
        .form-label { display: block; margin-bottom: 8px; font-size: 0.9rem; font-weight: 600; color: var(--text-main); }

        /* --- CORRECCIÓN DE INPUTS E ICONOS --- */
        .input-container {
            position: relative; /* Necesario para que el icono se posicione respecto a esto */
        }

        .input-container input {
            width: 100%;
            padding: 14px 14px 14px 45px; /* Espacio a la izquierda para el icono */
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s;
            background: var(--bg-light);
            color: var(--text-main);
        }

        /* AQUÍ ESTÁ LA CORRECCIÓN: Apuntamos a 'svg' en lugar de 'i' */
        .input-container svg {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%); /* Centrado vertical perfecto */
            color: #94a3b8;
            transition: 0.3s;
            pointer-events: none; /* Permite hacer clic en el input a través del icono */
        }

        .input-container input:focus {
            outline: none;
            border-color: var(--blue-600);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        /* Cambiar color del SVG cuando el input tiene foco */
        .input-container input:focus + svg {
            color: var(--blue-600);
        }

        .btn-submit {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(90deg, var(--blue-600), var(--blue-400));
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.4);
            display: flex; justify-content: center; align-items: center; gap: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(37, 99, 235, 0.5);
        }

        @media (max-width: 900px) {
            body { flex-direction: column; overflow-y: auto; }
            .left-panel { flex: 0 0 150px; padding: 30px; }
            .circle-decoration, .glass-card, .welcome-text p { display: none; }
            .welcome-text h2 { font-size: 1.8rem; text-align: center; margin: 0; }
            .right-panel { flex: 1; padding: 40px 20px; }
        }
    </style>
</head>
<body>

    <div class="left-panel">
        <div class="circle-decoration circle-1"></div>
        <div class="circle-decoration circle-2"></div>
        <div class="panel-content">
            <div class="welcome-text">
                <h2>Control <br> Inteligente.</h2>
                <p>Gestiona tu inventario con la plataforma más segura y eficiente.</p>
            </div>
            <div class="glass-card">
                <div style="background: rgba(255,255,255,0.2); padding: 10px; border-radius: 50%;">
                    <i data-lucide="shield-check" color="white"></i>
                </div>
                <div>
                    <span style="display: block; font-weight: 700; font-size: 0.9rem;">Sistema Seguro</span>
                    <span style="font-size: 0.8rem; opacity: 0.8;">Verificado en tiempo real</span>
                </div>
            </div>
        </div>
    </div>

    <div class="right-panel">
        <div class="login-wrapper">
            <div class="login-header">
                <h1>Bienvenido</h1>
                <p>Por favor ingresa tus credenciales.</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="form-group">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <div class="input-container">
                        <input id="email" type="email" name="email" required autofocus placeholder="nombre@gmail.com">
                        <i data-lucide="mail" size="20"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="input-container">
                        <input id="password" type="password" name="password" required placeholder="••••••••">
                        <i data-lucide="lock" size="20"></i>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    Iniciar Sesión <i data-lucide="arrow-right" size="20"></i>
                </button>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>