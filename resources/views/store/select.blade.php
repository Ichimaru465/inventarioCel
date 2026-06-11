<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar tienda - Inventario</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        :root {
            --blue-900: #1e3a8a;
            --blue-600: #2563eb;
            --blue-400: #60a5fa;
            --red-900: #7f1d1d;
            --red-600: #dc2626;
            --red-400: #f87171;
            --bg-light: #f8fafc;
            --text-main: #0f172a;
            --text-secondary: #64748b;
            --main-gradient: linear-gradient(135deg, #172554 0%, #1e40af 50%, #3b82f6 100%);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }

        body {
            min-height: 100vh;
            background: var(--main-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
            color: var(--text-main);
        }

        .select-wrapper {
            width: 100%;
            max-width: 920px;
            background: rgba(255, 255, 255, 0.96);
            border-radius: 28px;
            padding: 44px;
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.28);
        }

        .select-header {
            text-align: center;
            margin-bottom: 34px;
        }

        .select-header h1 {
            font-size: 2.35rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .select-header p {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        .store-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 22px;
        }

        .store-card {
            width: 100%;
            min-height: 220px;
            border: 2px solid #e2e8f0;
            border-radius: 22px;
            background: var(--bg-light);
            padding: 28px;
            cursor: pointer;
            text-align: left;
            transition: all 0.25s ease;
        }

        .store-card:hover {
            border-color: var(--blue-600);
            transform: translateY(-4px);
            box-shadow: 0 18px 35px rgba(37, 99, 235, 0.18);
            background: #ffffff;
        }

        .store-card.store-red:hover {
            border-color: var(--red-600);
            box-shadow: 0 18px 35px rgba(220, 38, 38, 0.2);
        }

        .store-icon {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            background: linear-gradient(135deg, var(--blue-600), var(--blue-400));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 22px;
        }

        .store-red .store-icon {
            background: linear-gradient(135deg, var(--red-600), var(--red-400));
        }

        .store-red h2 {
            color: var(--red-900);
        }

        .store-card h2 {
            font-size: 1.6rem;
            margin-bottom: 8px;
        }

        .store-card p {
            color: var(--text-secondary);
            line-height: 1.55;
        }

        .error-message {
            margin-bottom: 22px;
            padding: 14px 16px;
            border-radius: 12px;
            background: #fee2e2;
            color: #991b1b;
            font-weight: 600;
            text-align: center;
        }

        @media (max-width: 720px) {
            .select-wrapper { padding: 28px; }
            .store-grid { grid-template-columns: 1fr; }
            .select-header h1 { font-size: 1.8rem; }
        }
    </style>
</head>
<body>
    <main class="select-wrapper">
        <div class="select-header">
            <h1>Selecciona la tienda</h1>
            <p>El inventario, usuarios y ventas quedaran separados por tienda.</p>
        </div>

        @if ($errors->has('store'))
            <div class="error-message">{{ $errors->first('store') }}</div>
        @endif

        <div class="store-grid">
            @foreach ($stores as $key => $store)
                <form method="POST" action="{{ route('store.select.store') }}">
                    @csrf
                    <input type="hidden" name="store" value="{{ $key }}">
                    <button type="submit" class="store-card {{ $key === 'tienda_2' ? 'store-red' : '' }}">
                        <span class="store-icon">
                            <i data-lucide="store" size="30"></i>
                        </span>
                        <h2>{{ $store['name'] }}</h2>
                        <p>Entrar con la base de datos propia de {{ strtolower($store['name']) }}.</p>
                    </button>
                </form>
            @endforeach
        </div>
    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
