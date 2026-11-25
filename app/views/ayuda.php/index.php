<?php /* app/views/ayuda/index.php - Manual de usuario */ ?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-circle-question" aria-hidden="true"></i> Centro de Ayuda</h1>
    <p class="page-subtitle">Manual de usuario de Bartek.</p>
</div>

<div class="ayuda-lista">

    <details class="ayuda-item" id="Introduccion">
        <summary><i class="fas fa-house"></i> Introducción</summary>
        <div class="ayuda-contenido">
            <p>El presente manual de usuario tiene como finalidad explicar el funcionamiento del sistema Bartek, una herramienta diseñada para facilitar la administración y organización de bares. El sistema permite gestionar diferentes actividades del negocio, como las mesas, el inventario, los empleados, las ventas, los horarios, los reportes y los puntos de fidelización de los clientes.
                Esta vista sirve como guía para que los usuarios puedan conocer las funciones del sistema y aprender a utilizarlas de manera sencilla.
                .</p>
        </div>
    </details>
    <details class="ayuda-item" id="Objetivo">
        <summary><i class="fas fa-house"></i> Objetivo</summary>
        <div class="ayuda-contenido">
            <p>Orientar a los usuarios en el manejo del sistema Bartek mediante instrucciones claras que les permitan utilizar sus diferentes módulos y realizar las actividades necesarias para la gestión del bar.
            </p>
        </div>
    </details>

    <?php if ($rolUsuario === 'dueno'): ?>
        <details class="ayuda-item" id="empleados">
            <summary><i class="fas fa-users"></i> Empleados</summary>
            <div class="ayuda-contenido">
                <ol>
                    <li>Pulsa <strong>+ Agregar Empleado</strong>.</li>
                    <li>Completa los datos y las credenciales de acceso.</li>
                    <li>Guarda. Se crea la cuenta con rol "empleado".</li>
                </ol>
            </div>
        </details>
    <?php endif; ?>

      <details class="ayuda-item" id="modulos">
    <summary><i class="fas fa-th-large"></i> Módulos</summary>
    <div class="ayuda-contenido">
        <p>Estos son los módulos con los que cuenta Bartek. Algunos están disponibles solo para el dueño.</p>

        <!-- ───────────── MESAS ───────────── -->
        <details class="ayuda-subitem" id="mesas">
            <summary><i class="fas fa-utensils"></i> Mesas</summary>
            <div class="ayuda-contenido">
                <p>Es la pantalla principal del empleado. Muestra todas las mesas del bar y su estado.</p>

                <h4>Estados de una mesa</h4>
                <ul>
                    <li><strong>Libre:</strong> no tiene cuenta abierta.</li>
                    <li><strong>Ocupada:</strong> tiene una cuenta abierta con productos.</li>
                </ul>

                <h4>Tomar un pedido</h4>
                <ol>
                    <li>Haz clic en la mesa que quieres atender.</li>
                    <li>En <strong>Productos Disponibles</strong>, busca la bebida y pulsa <strong>Agregar</strong>.</li>
                    <li>Ajusta la cantidad en la tabla <strong>Cuenta de la Mesa</strong> si es necesario.</li>
                    <li>Pulsa <strong>Guardar Orden</strong> para dejar la cuenta registrada.</li>
                </ol>

                <h4>Cerrar la cuenta</h4>
                <ol>
                    <li>Pulsa <strong>Cerrar Cuenta</strong>.</li>
                    <li>Elige el método de pago: Efectivo, Tarjeta de Crédito, Nequi/Daviplata o Bre-B.</li>
                    <li>Pulsa <strong>Confirmar y Cerrar</strong>. La mesa queda libre de nuevo.</li>
                </ol>

                <p class="ayuda-nota">
                    <i class="fas fa-circle-info"></i> Los productos agotados aparecen marcados y no se pueden agregar.
                    No puedes pedir más unidades de las que hay en stock.
                </p>
            </div>
        </details>

        <!-- ───────────── INVENTARIO ───────────── -->
        <details class="ayuda-subitem" id="inventario">
            <summary><i class="fas fa-boxes"></i> Inventario</summary>
            <div class="ayuda-contenido">
                <p>Aquí se controla el stock de todas las bebidas del bar.</p>

                <h4>Agregar una bebida</h4>
                <ol>
                    <li>Pulsa <strong>Agregar Bebida</strong>.</li>
                    <li>Selecciona la categoría y escribe el código, el nombre, el stock actual, el stock mínimo y el precio.</li>
                    <li>Pulsa <strong>Agregar Bebida</strong> para guardar.</li>
                </ol>

                <h4>Buscar y filtrar</h4>
                <ul>
                    <li>Usa el filtro de categoría y pulsa <strong>Buscar</strong>.</li>
                    <li>Pulsa <strong>Limpiar</strong> para volver a ver todo.</li>
                    <li>El cuadro de búsqueda de la tabla encuentra bebidas por nombre o código.</li>
                </ul>

                <h4>Colores del stock</h4>
                <ul>
                    <li><strong>Verde:</strong> el stock está por encima del mínimo.</li>
                    <li><strong>Amarillo:</strong> el stock llegó al mínimo.</li>
                    <li><strong>Rojo:</strong> quedan 2 unidades o menos.</li>
                </ul>

                <h4>Editar y eliminar</h4>
                <ul>
                    <li>El ícono del lápiz permite editar la bebida (el código no se puede cambiar).</li>
                    <li>El ícono de la papelera la elimina. No se borra de la base de datos, solo deja de mostrarse.</li>
                </ul>

                <p class="ayuda-nota">
                    <i class="fas fa-circle-info"></i> Las bebidas con stock bajo aparecen en una alerta roja
                    arriba de la tabla y también en las notificaciones del dueño.
                </p>
            </div>
        </details>

        <!-- ───────────── VENTAS ───────────── -->
        <details class="ayuda-subitem" id="ventas">
            <summary><i class="fas fa-cash-register"></i> Ventas</summary>
            <div class="ayuda-contenido">
                <p>Muestra el historial de transacciones del bar y un resumen de lo vendido.</p>

                <h4>Qué puedes ver</h4>
                <ul>
                    <li><strong>Ventas Hoy</strong> y <strong>Ventas del Mes:</strong> suman solo las cuentas cerradas.</li>
                    <li><strong>Bebidas Más Vendidas:</strong> las 5 con más unidades vendidas.</li>
                    <li><strong>Transacciones Recientes:</strong> ID de la venta, fecha, mesa, empleado, total, estado y método de pago.</li>
                </ul>

                <h4>Ver el detalle de una venta</h4>
                <ol>
                    <li>En la tabla, pulsa el ícono del ojo en la venta que quieres revisar.</li>
                    <li>Se abre una ventana con cada producto, su cantidad, el precio y el total.</li>
                </ol>

                <h4>Estados de una venta</h4>
                <ul>
                    <li><strong>Abierto:</strong> la mesa todavía tiene la cuenta pendiente.</li>
                    <li><strong>Cerrado:</strong> la cuenta ya fue pagada.</li>
                    <li><strong>Cancelado:</strong> la venta fue anulada.</li>
                </ul>

                <p class="ayuda-nota">
                    <i class="fas fa-circle-info"></i> El botón <strong>Generar Reporte</strong> abre la impresión de la página.
                    Para descargar los datos en Excel usa el módulo de Reportes.
                </p>
            </div>
        </details>

        <!-- ───────────── RESERVAS ───────────── -->
        <details class="ayuda-subitem" id="reservas">
            <summary><i class="fas fa-calendar-alt"></i> Reservas</summary>
            <div class="ayuda-contenido">
                <p>Lista las reservas de mesa registradas en el sistema.</p>

                <h4>Cómo se crean</h4>
                <p>
                    Los clientes reservan desde el chatbot de la página de inicio, indicando nombre, fecha, hora,
                    mesa, número de personas y teléfono. El sistema verifica que la mesa no esté reservada ese día.
                </p>

                <h4>Qué puedes hacer</h4>
                <ul>
                    <li>Consultar cliente, teléfono, mesa, personas, fecha y hora de cada reserva.</li>
                    <li>Buscar y ordenar la tabla con las herramientas que trae.</li>
                    <li>Exportar la lista a <strong>Excel</strong> o <strong>PDF</strong> con los botones de arriba de la tabla.</li>
                    <li>Eliminar una reserva con el ícono de la papelera (pide confirmación).</li>
                </ul>
            </div>
        </details>

        <!-- ───────────── PUNTOS ───────────── -->
        <details class="ayuda-subitem" id="puntos">
            <summary><i class="fas fa-coins"></i> Sistema de puntos</summary>
            <div class="ayuda-contenido">
                <p>Es el Club de Fidelización: los clientes acumulan puntos con su consumo y los canjean por recompensas.</p>

                <h4>Registro del cliente</h4>
                <ul>
                    <li>El cliente puede inscribirse solo en la sección <strong>Acumula Puntos</strong> de la página pública, con su nombre y su cédula.</li>
                    <li>Debe aceptar la política de tratamiento de datos.</li>
                    <li>Recibe puntos de bienvenida al inscribirse.</li>
                </ul>

                <h4>Abonar puntos</h4>
                <ol>
                    <li>Entra a <strong>Sistema de Puntos</strong> en el menú.</li>
                    <li>Escribe el nombre, la cédula y los puntos que le corresponden.</li>
                    <li>Pulsa <strong>Guardar Puntos</strong>.</li>
                </ol>

                <h4>Desde la vista de una mesa</h4>
                <p>En el panel <strong>Puntos del Cliente</strong> puedes:</p>
                <ul>
                    <li><strong>Buscar Cliente</strong> con su cédula para ver cuántos puntos tiene.</li>
                    <li><strong>Agregar</strong> o <strong>Descontar</strong> puntos.</li>
                    <li><strong>Redimir Recompensa:</strong> elige la recompensa y se descuentan los puntos que cuesta. Solo funciona si el cliente tiene puntos suficientes.</li>
                </ul>

                <h4>Historial de clientes</h4>
                <ul>
                    <li>Busca por cédula o nombre.</li>
                    <li>Con <strong>Editar</strong> puedes corregir la cantidad de puntos y el estado: Ganado, Canjeado o Cancelado.</li>
                    <li>Con <strong>Borrar</strong> eliminas un registro.</li>
                </ul>

                <?php if ($rolUsuario === 'dueno'): ?>
                <h4>Configuración de puntos (dueño)</h4>
                <ul>
                    <li>Define si los puntos se ganan por <strong>dinero gastado</strong> o por <strong>cantidad de productos</strong>.</li>
                    <li>Indica cuánto hay que gastar y cuántos puntos se otorgan.</li>
                    <li>Define los puntos de bienvenida.</li>
                    <li>Crea, edita o elimina las recompensas que los clientes pueden canjear.</li>
                </ul>
                <?php endif; ?>
            </div>
        </details>

        <!-- ───────────── HORARIOS ───────────── -->
        <details class="ayuda-subitem" id="horarios">
            <summary><i class="fas fa-clock"></i> Horarios</summary>
            <div class="ayuda-contenido">
                <?php if ($rolUsuario === 'dueno'): ?>
                    <p>Aquí se asignan los turnos de trabajo y se aprueban.</p>

                    <h4>Crear horarios</h4>
                    <ol>
                        <li>En <strong>Crear Nuevo Horario</strong>, elige el empleado.</li>
                        <li>Selecciona la fecha de inicio, la fecha de fin y los días de la semana.</li>
                        <li>Indica la hora de inicio y la de fin.</li>
                        <li>Pulsa <strong>Guardar Horario</strong>. Se crea un turno por cada día que coincida (máximo un año).</li>
                    </ol>

                    <h4>Aprobar o rechazar</h4>
                    <ol>
                        <li>En <strong>Pendientes de Aprobación</strong>, marca los turnos que quieras.</li>
                        <li>Usa <strong>Seleccionar todos</strong> si quieres marcarlos todos.</li>
                        <li>Pulsa <strong>Aprobar seleccionados</strong> o <strong>Rechazar seleccionados</strong>.</li>
                    </ol>

                    <p class="ayuda-nota">
                        <i class="fas fa-circle-info"></i> Con el ícono de la papelera puedes eliminar un horario de la tabla.
                    </p>
                <?php else: ?>
                    <p>Aquí ves tus turnos del mes en un calendario. Es solo de consulta.</p>

                    <h4>Cómo usarlo</h4>
                    <ul>
                        <li>Los días con turno aparecen resaltados, con la hora de inicio y fin.</li>
                        <li>Usa <strong>Mes anterior</strong> y <strong>Mes siguiente</strong> para cambiar de mes.</li>
                    </ul>

                    <h4>Estado de cada turno</h4>
                    <ul>
                        <li><strong>Pendiente:</strong> aún no ha sido aprobado.</li>
                        <li><strong>Aprobado:</strong> el turno está confirmado.</li>
                        <li><strong>Rechazado:</strong> el turno no fue aprobado.</li>
                    </ul>
                <?php endif; ?>
            </div>
        </details>

        <?php if ($rolUsuario === 'dueno'): ?>
        <!-- ───────────── EMPLEADOS (solo dueño) ───────────── -->
        <details class="ayuda-subitem" id="empleados">
            <summary><i class="fas fa-users"></i> Empleados</summary>
            <div class="ayuda-contenido">
                <p>Permite administrar el personal del bar y sus cuentas de acceso.</p>

                <h4>Agregar un empleado</h4>
                <ol>
                    <li>Pulsa <strong>+ Agregar Empleado</strong>.</li>
                    <li>Completa nombre, apellido, puesto, correo y teléfono.</li>
                    <li>En <strong>Credenciales de Acceso</strong>, escribe un usuario y una contraseña temporal (mínimo 8 caracteres).</li>
                    <li>Pulsa <strong>Crear Empleado</strong>. Se crea su cuenta con rol de empleado.</li>
                </ol>

                <h4>Editar y eliminar</h4>
                <ul>
                    <li>El lápiz edita los datos del empleado.</li>
                    <li>La papelera lo elimina (pide confirmación).</li>
                    <li>Los botones Excel y PDF exportan la lista.</li>
                </ul>
            </div>
        </details>

        <!-- ───────────── REPORTES (solo dueño) ───────────── -->
        <details class="ayuda-subitem" id="reportes">
            <summary><i class="fas fa-chart-bar"></i> Reportes</summary>
            <div class="ayuda-contenido">
                <p>Descarga la información del bar en archivos CSV que se abren en Excel.</p>

                <h4>Reportes disponibles</h4>
                <ul>
                    <li><strong>Empleados:</strong> lista del personal con puesto y departamento.</li>
                    <li><strong>Inventario:</strong> stock actual, mínimo y precios de las bebidas.</li>
                    <li><strong>Ventas:</strong> historial de transacciones con total y estado.</li>
                    <li><strong>Horarios:</strong> turnos asignados y su estado.</li>
                </ul>

                <p>Pulsa <strong>Descargar CSV</strong> en el reporte que necesites. El nombre del archivo incluye la fecha.</p>
            </div>
        </details>
        <?php endif; ?>

        <!-- ───────────── PERFIL ───────────── -->
        <details class="ayuda-subitem" id="perfil">
            <summary><i class="fas fa-user-circle"></i> Mi perfil</summary>
            <div class="ayuda-contenido">
                <p>Aquí actualizas tus datos personales y tu contraseña.</p>

                <h4>Cambiar tus datos</h4>
                <ul>
                    <li>Puedes modificar tu nombre, correo y teléfono.</li>
                    <li>El usuario y el rol aparecen bloqueados y no se pueden cambiar.</li>
                    <li>Pulsa <strong>Guardar Cambios</strong> al terminar.</li>
                </ul>

                <h4>Foto de perfil</h4>
                <p>Haz clic en el círculo de la parte superior, elige una imagen y guarda los cambios.</p>

                <h4>Cambiar la contraseña</h4>
                <ol>
                    <li>Escribe tu contraseña actual.</li>
                    <li>Escribe la nueva (mínimo 8 caracteres) y confírmala.</li>
                    <li>Pulsa <strong>Guardar Cambios</strong>.</li>
                </ol>

                <p class="ayuda-nota">
                    <i class="fas fa-circle-info"></i> Si olvidaste tu contraseña, usa <strong>¿Olvidaste tu contraseña?</strong>
                    en el inicio de sesión. Recibirás un enlace por correo que dura 5 minutos.
                </p>
            </div>
        </details>

    </div>
</details>