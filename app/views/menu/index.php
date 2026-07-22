<?php /* app/views/menu/index.php - Gestión del menú interactivo del bar */ ?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-cocktail" aria-hidden="true"></i> Menú Interactivo</h1>
    <p class="page-subtitle">Administra las categorías y bebidas del menú digital.</p>
</div>

<!-- Botón agregar categoría -->
<div class="action-buttons">
    <button class="btn-primary"
            onclick="document.getElementById('modalCategoria').style.display='flex'">
        <i class="fas fa-plus" aria-hidden="true"></i> Nueva Categoría
    </button>
    <a href="<?= BASE_URL ?>/inventario/crear" class="btn-primary"><i class="fas fa-plus" aria-hidden="true"></i> Nueva Bebida</a>
    <a href="<?= BASE_URL ?>/inventario" class="btn-secondary"><i class="fas fa-cogs" aria-hidden="true"></i> Ver Inventario Completo</a>
</div>

<!-- Tabs de navegación -->
<div class="tabs-section">
    <button class="tab-button active" onclick="mostrarTab('categorias', this)">Categorías</button>
    <button class="tab-button"        onclick="mostrarTab('tabla', this)">Tabla de Bebidas</button>
</div>

<!-- ── Tab: Categorías ──────────────────────────────────────────────── -->
<div id="tab-categorias" class="tab-content">
    <?php if (empty($categorias)): ?>
        <div class="empty-state">
            <div class="empty-state-icon"><i class="fas fa-cocktail" aria-hidden="true"></i></div>
            <p>No hay categorías creadas aún. Agrega la primera.</p>
        </div>
    <?php else: ?>
        <div class="categories-list">
            <?php foreach ($categorias as $cat): ?>
            <div class="category-card">
                <div class="category-info">
                    <div class="category-name">
                        <?= htmlspecialchars($cat['nombre'],      ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <div class="category-description">
                        <?= htmlspecialchars($cat['descripcion'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <div class="category-count">
                        <?= (int)$cat['total_bebidas'] ?> bebida(s)
                    </div>
                </div>
                <div class="category-actions">
                    <!-- Eliminar categoría -->
                    <form method="POST" action="<?= BASE_URL ?>/menu/eliminar"
                          style="display:inline"
                          onsubmit="return confirm('¿Eliminar esta categoría?')">
                        <input type="hidden" name="csrf_token"
                               value="<?= htmlspecialchars($tokenCSRF, ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="id" value="<?= (int)$cat['id'] ?>">
                        <button type="submit" class="action-btn delete-btn" title="Eliminar"><i class="fas fa-trash" aria-hidden="true"></i></button>
                    </form>
                    <!-- Ver bebidas de esta categoría -->
                    <a href="<?= BASE_URL ?>/inventario?categoria=<?= (int)$cat['id'] ?>"
                       class="view-products-btn">Ver Bebidas</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- ── Tab: Tabla de bebidas (DataTable) ───────────────────────────── -->
<div id="tab-tabla" class="tab-content" style="display:none">
    <div class="table-section">
        <table class="bartek-datatable" id="tablaMenu">
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th>Código</th>
                    <th>Bebida</th>
                    <th>Stock</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categorias as $cat):
                    // Nota: las bebidas se cargarían vía AJAX en una versión avanzada.
                    // Aquí se muestran desde inventario de forma estática.
                ?>
                <?php endforeach; ?>
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        Usa el módulo de <a href="<?= BASE_URL ?>/inventario">Inventario</a>
                        para gestionar bebidas.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: Nueva Categoría -->
<div id="modalCategoria" class="modal-overlay" style="display:none">
    <div class="modal-box">
        <h2><i class="fas fa-plus" aria-hidden="true"></i> Nueva Categoría</h2>
        <form method="POST" action="<?= BASE_URL ?>/menu/guardar" novalidate>
            <input type="hidden" name="csrf_token"
                   value="<?= htmlspecialchars($tokenCSRF, ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-group">
                <label for="mcNombre">Nombre *</label>
                <input type="text" id="mcNombre" name="nombre"
                       maxlength="80" required placeholder="Ej: Cócteles">
            </div>

            <div class="form-group">
                <label for="mcDesc">Descripción</label>
                <textarea id="mcDesc" name="descripcion" rows="3"
                          placeholder="Breve descripción de la categoría"></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Crear Categoría</button>
                <button type="button" class="btn-secondary"
                        onclick="document.getElementById('modalCategoria').style.display='none'">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
/**
 * Cambia la pestaña activa en la sección de menú.
 * @param {string} nombre - Nombre del tab a mostrar ('categorias' | 'tabla')
 * @param {HTMLElement} btn - Botón que fue clickeado
 */
function mostrarTab(nombre, btn) {
    document.querySelectorAll('.tab-content').forEach(t => t.style.display = 'none');
    document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + nombre).style.display = 'block';
    btn.classList.add('active');
}
</script>

    <script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>
<script src="https://files.bpcontent.cloud/2026/06/25/20/20260625200158-LXETZX3T.js" defer></script>
    
