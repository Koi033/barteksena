<?php
/**
 * Vista de Listado / Historial General de Puntos - Bartek (Diseño Limpio y Profesional)
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo ?? 'Historial - Bartek'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #121212; }
        .glass-effect {
            background: rgba(18, 18, 18, 0.75);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .brand-glow { color: #ff8533; text-shadow: 0 0 15px rgba(249, 115, 22, 0.5); }
        .btn-action {
            background: rgba(249, 115, 22, 0.1);
            border: 1px solid rgba(249, 115, 22, 0.3);
            transition: all 0.2s ease;
        }
        .btn-action:hover {
            background: rgba(249, 115, 22, 0.25);
            border-color: #f97316;
            box-shadow: 0 0 10px rgba(249, 115, 22, 0.4);
        }
        .btn-delete {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            transition: all 0.2s ease;
        }
        .btn-delete:hover {
            background: rgba(239, 68, 68, 0.25);
            border-color: #ef4444;
            box-shadow: 0 0 10px rgba(239, 68, 68, 0.4);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col text-gray-100 font-sans antialiased">

    <!-- Header -->
    <header class="glass-effect sticky top-0 z-50 border-b border-gray-800/80">
        <nav class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-700 flex items-center justify-center shadow-lg shadow-orange-500/30 border border-orange-400/40">
                    <i class="fas fa-beer-mug-empty text-black text-xl"></i>
                </div>
                <span class="text-2xl font-black tracking-wider brand-glow">BARTEK</span>
            </div>
            <a href="<?php echo BASE_URL; ?>/puntos" class="text-sm font-semibold text-gray-300 hover:text-orange-500 transition-colors inline-flex items-center gap-2 bg-black/40 px-4 py-2 rounded-xl border border-gray-800">
                <i class="fas fa-arrow-left"></i> Volver a Abonar Puntos
            </a>
        </nav>
    </header>

    <!-- Contenido -->
    <main class="flex-grow max-w-7xl mx-auto w-full px-6 py-10 space-y-6">
        
        <!-- Cabecera Limpia -->
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-orange-500/10 flex items-center justify-center text-orange-500 border border-orange-500/20 flex-shrink-0 shadow-inner">
                <i class="fas fa-history text-xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">Historial de Puntos</h1>
                <p class="text-gray-400 text-xs mt-0.5">Control y gestión de puntos para clientes</p>
            </div>
        </div>

        <!-- Barra de Búsqueda Independiente y Separada -->
        <form action="<?php echo BASE_URL; ?>/puntos/listado" method="GET" class="w-full flex items-center gap-3">
                <div class="relative flex-1">
                    <!-- Icono de lupa separado en su propio espacio contenedor -->
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-orange-500">
                        <i class="fas fa-search text-sm"></i>
                    </div>
                    <!-- Input con un padding izquierdo grande (pl-12) para que la letra empiece después del icono -->
                    <input type="text" name="buscar" value="<?php echo htmlspecialchars($_GET['buscar'] ?? ''); ?>" 
                           placeholder="Buscar cliente por cédula o nombre..." 
                           style="padding-left: 3rem !important;"
                           class="w-full bg-black/60 border border-gray-700/80 rounded-xl pr-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-orange-500 text-sm shadow-inner transition-all">
                </div>
                <button type="submit" class="btn-action text-orange-400 px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-wider flex items-center gap-2 shadow-md flex-shrink-0">
                    <i class="fas fa-filter"></i> Buscar
                </button>
                <?php if (!empty($_GET['buscar'])): ?>
                    <a href="<?php echo BASE_URL; ?>/puntos/listado" class="bg-gray-800 hover:bg-gray-700 text-gray-300 px-4 py-3 rounded-xl text-xs font-semibold transition-colors flex items-center flex-shrink-0">
                        <i class="fas fa-rotate-left mr-1.5"></i> Limpiar
                    </a>
                <?php endif; ?>
    
            </form>
        </div>

        <!-- Tabla de Datos -->
        <div class="glass-effect rounded-3xl overflow-hidden border border-gray-800 shadow-xl">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-black/60 border-b border-gray-800 text-orange-400 text-xs uppercase tracking-wider">
                        <th class="py-4 px-6">ID</th>
                        <th class="py-4 px-6">Nombre del Cliente</th>
                        <th class="py-4 px-6">Cédula</th>
                        <th class="py-4 px-6">Puntos</th>
                        <th class="py-4 px-6">Premio / Beneficio</th>
                        <th class="py-4 px-6">Estado</th>
                        <th class="py-4 px-6">Fecha</th>
                        <th class="py-4 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-sm">
                    <?php if (!empty($registros)): ?>
                        <?php foreach ($registros as $row): ?>
                            <?php
                                // Lógica de beneficios según los puntos acumulados
                                $puntosCliente = $row['cantidad_puntos'];
                                if ($puntosCliente >= 40) {
                                    $premio = '<span class="bg-purple-500/20 text-purple-300 border-purple-500/30 border px-3 py-1 rounded-full text-xs font-bold animate-pulse inline-flex items-center"><i class="fas fa-wine-bottle mr-1.5"></i> Rebaja Whisky</span>';
                                } elseif ($puntosCliente >= 20) {
                                    $premio = '<span class="bg-amber-500/20 text-amber-300 border-amber-500/30 border px-3 py-1 rounded-full text-xs font-bold inline-flex items-center"><i class="fas fa-beer-mug-empty mr-1.5"></i> Rebaja de cerveza</span>';
                                } else {
                                    $premio = '<span class="text-gray-500 text-xs italic">Acumulando...</span>';
                                }
                            ?>
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="py-4 px-6 font-mono text-gray-400"><?php echo $row['id']; ?></td>
                                <td class="py-4 px-6 font-bold text-white"><?php echo htmlspecialchars($row['nombre'] ?? 'Cliente Bartek'); ?></td>
                                <td class="py-4 px-6 font-mono text-gray-300"><?php echo htmlspecialchars($row['cedula_cliente']); ?></td>
                                <td class="py-4 px-6 font-black text-orange-400">+<?php echo $row['cantidad_puntos']; ?></td>
                                <td class="py-4 px-6"><?php echo $premio; ?></td>
                                <td class="py-4 px-6">
                                    <?php 
                                        $tipo = strtolower($row['tipo']);
                                        $claseBadge = ($tipo === 'canjeado' || $tipo === 'cancelado') 
                                            ? 'bg-red-500/10 text-red-400 border-red-500/20' 
                                            : 'bg-orange-500/10 text-orange-400 border-orange-500/20';
                                    ?>
                                    <span class="<?php echo $claseBadge; ?> border px-3 py-1 rounded-full text-xs font-bold uppercase">
                                        <?php echo $row['tipo']; ?>
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-gray-400 text-xs"><?php echo $row['fecha']; ?></td>
                                <td class="py-4 px-6 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Botón Editar Estado -->
                                        <a href="<?php echo BASE_URL; ?>/puntos/editar?id=<?php echo $row['id']; ?>" class="btn-action text-orange-400 px-3.5 py-2 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5" title="Cambiar Estado">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>

                                        <!-- Botón Eliminar Registro -->
                                        <a href="<?php echo BASE_URL; ?>/puntos/eliminar?id=<?php echo $row['id']; ?>" 
                                           onclick="return confirm('¿Estás segura de que deseas eliminar el registro de este cliente?');" 
                                             class="btn-delete text-red-400 px-3.5 py-2 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5" title="Eliminar Registro">
                                              <i class="fas fa-trash-can"></i> Borrar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-500">
                                <i class="fas fa-folder-open text-4xl mb-3 opacity-30 block"></i>
                                No se encontraron registros de puntos en este momento.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer class="py-6 text-center text-xs text-gray-600 border-t border-gray-800 bg-black/40">
        &copy; 2026 Bartek - Sistema de Gestión Interna
    </footer>
</body>
</html>