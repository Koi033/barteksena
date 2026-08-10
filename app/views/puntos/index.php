<?php
/**
 * Vista del Sistema de Puntos - Bartek (Historial en la izquierda con diseño cool)
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fidelización de Clientes - Bartek</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #121212;
            background-image: radial-gradient(circle at 15% 20%, rgba(249, 115, 22, 0.12) 0%, transparent 30%),
                              radial-gradient(circle at 85% 80%, rgba(20, 20, 20, 0.9) 0%, transparent 45%);
        }
        
        .glass-effect {
            background: rgba(18, 18, 18, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.6);
        }
        
        .input-bartek {
            background-color: rgba(5, 5, 5, 0.6);
            border: 1px solid #333;
            transition: all 0.3s ease;
        }
        .input-bartek:focus-within {
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.2);
        }
        
        .btn-bartek {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            transition: all 0.3s ease;
        }
        .btn-bartek:hover {
            background: linear-gradient(135deg, #fb923c 0%, #f97316 100%);
            box-shadow: 0 0 25px rgba(249, 115, 22, 0.5);
            transform: translateY(-2px);
        }

        .brand-glow {
            color: #ff8533;
            text-shadow: 0 0 15px rgba(249, 115, 22, 0.5);
        }

        .history-card {
            background: linear-gradient(145deg, rgba(20, 20, 20, 0.9), rgba(10, 10, 10, 0.95));
            border: 1px solid rgba(249, 115, 22, 0.3);
            transition: all 0.3s ease;
        }
        .history-card:hover {
            border-color: #f97316;
            box-shadow: 0 0 20px rgba(249, 115, 22, 0.25);
            transform: translateY(-3px);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col text-gray-100 font-sans antialiased">

    <!-- Barra de navegación superior -->
    <header class="glass-effect sticky top-0 z-50 border-b border-gray-800/80">
        <nav class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-700 flex items-center justify-center shadow-lg shadow-orange-500/30 border border-orange-400/40">
                    <i class="fas fa-beer-mug-empty text-black text-xl"></i>
                </div>
                <span class="text-2xl font-black tracking-wider brand-glow">BARTEK</span>
            </div>
            <div class="flex items-center gap-4 text-sm text-gray-300">
                <span class="bg-black/40 px-3.5 py-1.5 rounded-lg border border-gray-800">
                    <i class="fas fa-user-circle mr-2 text-orange-500"></i>Operador: Adriana
                </span>
            </div>
        </nav>
    </header>

    <!-- Contenido Principal -->
    <main class="flex-grow max-w-7xl mx-auto w-full px-6 py-12 grid md:grid-cols-12 gap-12 items-center">
        
        <!-- Columna Izquierda: Bienvenida y Tarjeta Cool para Ver Historial -->
        <div class="md:col-span-5 flex flex-col items-center md:items-start text-center md:text-left space-y-6">
            <div class="p-5 bg-orange-500/10 rounded-3xl inline-block border border-orange-500/20 shadow-inner">
                <i class="fa-solid fa-award text-5xl text-orange-500"></i>
            </div>
            <h1 class="text-4xl md:text-5xl font-black text-white leading-tight tracking-tight">
                Sistema de <br/><span class="text-orange-500">Fidelización</span>
            </h1>
            <p class="text-gray-400 text-base leading-relaxed">
                Premia la preferencia de tus clientes en Bartek registrando su nombre y cédula para acumular puntos al instante de forma segura.
            </p>

            <!-- Tarjeta Llamativa para el Historial en la Izquierda -->
            <a href="<?php echo BASE_URL; ?>/puntos/listado" class="history-card w-full max-w-md p-5 rounded-2xl flex items-center justify-between group text-left">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-orange-500/15 flex items-center justify-center text-orange-500 group-hover:scale-110 transition-transform border border-orange-500/30">
                        <i class="fas fa-users-viewfinder text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-base group-hover:text-orange-400 transition-colors">Historial de Clientes</h3>
                        <p class="text-gray-400 text-xs mt-0.5">Consulta todos los puntos acumulados</p>
                    </div>
                </div>
                <div class="text-orange-500 text-lg group-hover:translate-x-1 transition-transform">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>

            <div class="pt-1">
                <a href="<?php echo BASE_URL; ?>/dashboard" class="text-sm font-semibold text-gray-400 hover:text-orange-500 transition-colors inline-flex items-center gap-2 bg-black/30 px-4 py-2.5 rounded-xl border border-gray-800">
                    <i class="fas fa-arrow-left"></i> Volver al panel principal
                </a>
            </div>
        </div>

        <!-- Columna Derecha: Formulario Limpio -->
        <div class="md:col-span-7 relative w-full max-w-xl mx-auto">
            <div class="absolute -inset-1 bg-gradient-to-r from-orange-600 to-amber-500 rounded-3xl blur-xl opacity-25"></div>
            
            <div class="glass-effect rounded-3xl p-8 md:p-10 relative">
                
                <div class="mb-6 flex items-center gap-5 border-b border-gray-800 pb-6">
                    <div class="w-16 h-16 rounded-2xl bg-orange-500/10 flex items-center justify-center border border-orange-500/20 text-orange-500 flex-shrink-0">
                        <i class="fa-solid fa-coins text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Abonar Puntos</h2>
                        <p class="text-gray-400 text-xs mt-0.5">Ingresa los datos del cliente para procesar el abono.</p>
                    </div>
                </div>

                <form action="<?php echo BASE_URL; ?>/puntos/guardar" method="POST" class="space-y-5">
                    
                    <!-- Campo Nombre y Apellido -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold tracking-wider text-gray-300 uppercase">
                            <i class="fas fa-signature text-orange-500 mr-1.5"></i> Nombre y Apellido *
                        </label>
                        <div class="input-bartek flex items-center rounded-xl overflow-hidden px-4 py-3">
                            <span class="text-orange-500 mr-3 text-lg">
                                <i class="fas fa-address-card"></i>
                            </span>
                            <input type="text" name="nombre" required 
                                class="w-full bg-transparent text-white placeholder-gray-500 focus:outline-none text-base"
                                placeholder="Ej. Carlos Pérez">
                        </div>
                    </div>

                    <!-- Campo Cédula -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold tracking-wider text-gray-300 uppercase">
                            <i class="fas fa-id-card text-orange-500 mr-1.5"></i> Cédula del Cliente *
                        </label>
                        <div class="input-bartek flex items-center rounded-xl overflow-hidden px-4 py-3">
                            <span class="text-orange-500 mr-3 text-lg">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" name="cedula" required 
                                class="w-full bg-transparent text-white placeholder-gray-500 focus:outline-none text-base font-mono"
                                placeholder="Ej. 1098765432">
                        </div>
                    </div>

                    <!-- Campo Puntos -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold tracking-wider text-gray-300 uppercase">
                            <i class="fas fa-plus-circle text-orange-500 mr-1.5"></i> Puntos a Sumar *
                        </label>
                        <div class="input-bartek flex items-center rounded-xl overflow-hidden px-4 py-3">
                            <span class="text-orange-500 mr-3 text-lg">
                                <i class="fas fa-star"></i>
                            </span>
                            <input type="number" name="puntos" required min="1" value="10"
                                class="w-full bg-transparent text-orange-400 placeholder-gray-500 focus:outline-none text-lg font-bold"
                                placeholder="10">
                        </div>
                    </div>

                    <!-- Botón de Envío -->
                    <button type="submit" class="w-full btn-bartek text-white font-bold py-4 px-6 rounded-xl shadow-lg flex items-center justify-center gap-2 uppercase tracking-wide text-sm cursor-pointer mt-4">
                        <i class="fa-solid fa-circle-check text-base"></i>
                        <span>Guardar Puntos</span>
                    </button>
                </form>

                <div class="mt-6 text-center text-xs text-gray-500">
                    <i class="fas fa-shield-alt mr-1 text-orange-500/70"></i> Módulo de control interno de puntos Bartek.
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-6 text-center text-xs text-gray-600 border-t border-gray-800 bg-black/40">
        &copy; 2026 Bartek - Sistema de Gestión Interna
    </footer>

</body>
</html>