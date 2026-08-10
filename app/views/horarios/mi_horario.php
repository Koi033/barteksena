<?php /* app/views/horarios/mi_horario.php - Horario personal del empleado (solo lectura) */ ?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-clock" aria-hidden="true"></i> Mi Horario</h1>
    <p class="page-subtitle">Turnos asignados para <?= htmlspecialchars($nombreMes, ENT_QUOTES, 'UTF-8') ?> de <?= (int) $anio ?>.</p>
</div>

<div class="calendar-section">

    <div class="pagination-section" style="margin-bottom:1rem;">
        <div class="pagination-controls">
            <a href="?mes=<?= $mesAnterior ?>&anio=<?= $anioAnterior ?>" class="pagination-btn">
                <i class="fas fa-arrow-left" aria-hidden="true"></i> Mes anterior
            </a>
            <span class="pagination-info"><?= htmlspecialchars($nombreMes, ENT_QUOTES, 'UTF-8') ?> <?= (int) $anio ?></span>
            <a href="?mes=<?= $mesSiguiente ?>&anio=<?= $anioSiguiente ?>" class="pagination-btn">
                Mes siguiente <i class="fas fa-arrow-right" aria-hidden="true"></i>
            </a>
        </div>
    </div>

    <table class="bartek-datatable calendar-table" id="tablaMiHorario">
        <thead>
            <tr>
                <th>Lun</th><th>Mar</th><th>Mié</th><th>Jue</th><th>Vie</th><th>Sáb</th><th>Dom</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <?php
                    // Celdas vacías antes del día 1 del mes
                    for ($i = 1; $i < $primerDiaSem; $i++) {
                        echo '<td class="calendar-empty"></td>';
                    }

                    $columna = $primerDiaSem - 1;
                    for ($dia = 1; $dia <= $totalDias; $dia++):
                        if ($columna === 7) {
                            echo '</tr><tr>';
                            $columna = 0;
                        }
                        $columna++;
                        $turnos = $porDia[$dia] ?? [];
                ?>
                    <td class="calendar-day<?= !empty($turnos) ? ' calendar-day-active' : '' ?>">
                        <div class="calendar-day-number"><?= $dia ?></div>
                        <?php foreach ($turnos as $t): ?>
                            <?php
                                $estadoClase = [
                                    'pendiente' => 'status-pending',
                                    'aprobado'  => 'status-completed',
                                    'rechazado' => 'status-cancelled',
                                ][$t['estado']] ?? 'status-pending';
                            ?>
                            <div class="calendar-shift">
                                <span class="calendar-shift-time">
                                    <?= htmlspecialchars($t['hora_inicio'], ENT_QUOTES, 'UTF-8') ?>
                                    – <?= htmlspecialchars($t['hora_fin'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                                <span class="status-badge <?= $estadoClase ?>">
                                    <?= ucfirst(htmlspecialchars($t['estado'], ENT_QUOTES, 'UTF-8')) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </td>
                <?php endfor; ?>

                <?php
                    // Celdas vacías al cierre de la última semana
                    while ($columna < 7) {
                        echo '<td class="calendar-empty"></td>';
                        $columna++;
                    }
                ?>
            </tr>
        </tbody>
    </table>

    <?php if (empty($porDia)): ?>
        <p class="text-muted" style="margin-top:1rem;">No tienes turnos asignados este mes.</p>
    <?php endif; ?>

</div>
