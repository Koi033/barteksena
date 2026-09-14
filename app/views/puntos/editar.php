<?php
/**
 * Vista de Edición y Eliminación de Estado de Puntos - Bartek
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo ?? 'Editar - Bartek'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            background-color: #121212; 
            background-image: radial-gradient(circle at 50% 30%, rgba(249, 115, 22, 0.1) 0%, transparent 65%);
            margin: 0;
            min-height: 100vh;
            display: flex;
        }
        .glass-effect {
            background: rgba(18, 18, 18, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 35px 70px rgba(0, 0, 0, 0.75);
        }
        .brand-glow { color: #ff8533; text-shadow: 0 0 15px rgba(249, 115, 22, 0.5); }
        
        .estado-radio:checked + .estado-card {
            border-color: #f97316;
            background: rgba(249, 115, 22, 0.15);
            box-shadow: 0 0 20px rgba(249, 115, 22, 0.3);
        }
        .estado-card {
            background: rgba(10, 10, 10, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.25s ease;
        }
        .estado-card:hover {
            border-color: rgba(249, 115, 22, 0.4);
            transform: translateY(-2px);
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
        .btn-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            transition: all 0.2s ease;
        }
        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.25);
            border-color: #ef4444;
            box-shadow: 0 0 15px rgba(239, 68, 68, 0.4);
        }
    </style>
</head>
<body>

    <div class="flex-1 flex flex-col justify-between min-h-screen">
        
        <!-- Header -->
        <header class="glass-effect border-b border-gray-800/80 w-full">
            <div class="max-w-7xl mx-auto px-8 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-700 flex items-center justify-center shadow-lg shadow-orange-500/30 border border-orange-400/40">
                        <i class="fas fa-beer-mug-empty text-black text-xl"></i>
                    </div>
                    <span class="text-2xl font-black tracking-wider brand-glow">BARTEK</span>
                </div>
                <a href="<?php echo BASE_URL; ?>/puntos/listado" class="text-sm font-semibold text-gray-300 hover:text-orange-500 transition-colors inline-flex items-center gap-2 bg-black/40 px-4 py-2.5 rounded-xl border border-gray-800 shadow-md">
                    <i class="fas fa-arrow-left"></i> Volver al Historial
                </a>
            </div>
        </header>

        <!-- Contenido Central -->
        <main class="w-full max-w-2xl mx-auto px-6 py-8 my-auto">
            <div class="relative">
                <div class="absolute -inset-2 bg-gradient-to-r from-orange-600 to-amber-500 rounded-3xl blur-2xl opacity-20"></div>
                
                <div class="glass-effect rounded-3xl p-8 md:p-10 relative border border-gray-800 shadow-2xl space-y-6">
                    
                    <div class="flex items-center justify-between border-b border-gray-800 pb-5">
                        <div class="flex items-center gap-5">
                            <div class="w-16 h-16 rounded-2xl bg-orange-500/10 flex items-center justify-center border border-orange-500/20 text-orange-500 flex-shrink-0 shadow-inner">
                                <i class="fa-solid fa-sliders text-2xl"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-black text-white tracking-tight">Gestión de Puntos</h2>
                                <p class="text-gray-400 text-sm mt-1">Cliente: <strong class="text-orange-400 font-bold"><?php echo htmlspecialchars($registro['nombre'] ?? 'Cliente'); ?></strong> <span class="text-gray-500 text-xs">(Cédula: <?php echo htmlspecialchars($registro['cedula_cliente']); ?>)</span></p>
                            </div>
                        </div>
                    </div>

                    <form action="<?php echo BASE_URL; ?>/puntos/actualizar" method="POST" class="space-y-5">
    <input type="hidden" name="id" value="<?php echo $registro['id']; ?>">

    <!-- Campo para modificar la cantidad de puntos -->
    <div class="space-y-2">
        <label class="block text-xs font-bold tracking-wider text-gray-300 uppercase">
            <i class="fas fa-coins text-orange-500 mr-1.5"></i> Cantidad de Puntos *
        </label>
        <input type="number" name="cantidad_puntos" value="<?php echo htmlspecialchars($registro['cantidad_puntos']); ?>" required
               class="w-full bg-black/60 border border-gray-700/85 rounded-2xl px-4 py-3.5 text-white font-bold text-lg focus:outline-none focus:border-orange-500 shadow-inner">
    </div>

    <!-- Resto de opciones de estado (Ganado, Canjeado, Cancelado) que ya tienes -->
    <div class="space-y-3">
        <label class="block text-xs font-bold tracking-wider text-gray-300 uppercase">
            <i class="fas fa-check-circle text-orange-500 mr-1.5"></i> Selecciona el estado del registro *
        </label>
        
        <div class="grid grid-cols-1 gap-3">
            <label class="cursor-pointer">
                <input type="radio" name="tipo" value="ganado" class="estado-radio sr-only" <?php echo (($registro['tipo'] ?? '') === 'ganado') ? 'checked' : ''; ?>>
                <div class="estado-card p-4 rounded-2xl flex items-center justify-between">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-500 border border-orange-500/20">
                            <i class="fas fa-star text-base"></i>
                        </div>
                        <div>
                            <div class="text-white font-bold text-sm">GANADO</div>
                            <div class="text-gray-400 text-xs">Los puntos se mantienen activos en el acumulado.</div>
                        </div>
                    </div>
                    <i class="fas fa-circle-check text-orange-500 text-lg opacity-80"></i>
                </div>
            </label>

            <label class="cursor-pointer">
                <input type="radio" name="tipo" value="canjeado" class="estado-radio sr-only" <?php echo (($registro['tipo'] ?? '') === 'canjeado') ? 'checked' : ''; ?>>
                <div class="estado-card p-4 rounded-2xl flex items-center justify-between">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400 border border-emerald-500/20">
                            <i class="fas fa-gift text-base"></i>
                        </div>
                        <div>
                            <div class="text-white font-bold text-sm">CANJEADO</div>
                            <div class="text-gray-400 text-xs">El cliente ya utilizó sus puntos en el establecimiento.</div>
                        </div>
                    </div>
                    <i class="fas fa-circle-check text-emerald-400 text-lg opacity-80"></i>
                </div>
            </label>

            <label class="cursor-pointer">
                <input type="radio" name="tipo" value="cancelado" class="estado-radio sr-only" <?php echo (($registro['tipo'] ?? '') === 'cancelado') ? 'checked' : ''; ?>>
                <div class="estado-card p-4 rounded-2xl flex items-center justify-between">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-red-500/10 flex items-center justify-center text-red-400 border border-red-500/20">
                            <i class="fas fa-ban text-base"></i>
                        </div>
                        <div>
                            <div class="text-white font-bold text-sm">CANCELADO</div>
                            <div class="text-gray-400 text-xs">El registro fue anulado por error o motivos internos.</div>
                        </div>
                    </div>
                    <i class="fas fa-circle-check text-red-400 text-lg opacity-80"></i>
                </div>
            </label>
        </div>
    </div>
    
        <!-- Botones de Acción -->
        <div class="flex gap-4 pt-2">
            <button type="submit" class="w-full btn-bartek text-white font-bold py-4 px-6 rounded-2xl shadow-xl flex items-center justify-center gap-2 uppercase tracking-wide text-sm cursor-pointer">
                <i class="fa-solid fa-save text-base"></i>
                <span>Guardar Cambios</span>
            </button>
        </div>

        <!-- Footer -->
        <footer class="py-4 text-center text-xs text-gray-600 border-t border-gray-800/60 bg-black/40 w-full">
            &copy; 2026 Bartek - Sistema de Gestión Interna
        </footer>

    </div>
</body>
</html>