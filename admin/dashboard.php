<?php 
$page_title = "Dashboard";
include 'admin_header.php';

// --- LÓGICA PARA OBTENER ESTADÍSTICAS (sin cambios) ---
// ... (código existente)
$total_cotizaciones_result = $conn->query("SELECT COUNT(id) as total FROM cotizaciones");
$total_cotizaciones = $total_cotizaciones_result->fetch_assoc()['total'] ?? 0;
$total_usuarios_result = $conn->query("SELECT COUNT(id) as total FROM usuarios");
$total_usuarios = $total_usuarios_result->fetch_assoc()['total'] ?? 0;
$cotizaciones_por_estado_result = $conn->query("SELECT estado, COUNT(id) as count FROM cotizaciones GROUP BY estado");
$cotizaciones_por_estado = [];
while($row = $cotizaciones_por_estado_result->fetch_assoc()) {
    $cotizaciones_por_estado[$row['estado']] = $row['count'];
}
$pendientes = $cotizaciones_por_estado['pendiente'] ?? 0;
$agendadas = $cotizaciones_por_estado['agendado'] ?? 0;
$ultimas_cotizaciones_result = $conn->query("SELECT id, nombre_cliente, marca_vehiculo, modelo_vehiculo, fecha_creacion FROM cotizaciones WHERE estado = 'pendiente' ORDER BY fecha_creacion DESC LIMIT 5");
$ultimas_cotizaciones = $ultimas_cotizaciones_result->fetch_all(MYSQLI_ASSOC);


// --- LÓGICA PARA MEJORAS ÉPICAS ---

// 1. Saludo Dinámico
$hora_actual = date('H');
$saludo = '';
if ($hora_actual < 12) {
    $saludo = "Buenos días";
} elseif ($hora_actual < 18) {
    $saludo = "Buenas tardes";
} else {
    $saludo = "Buenas noches";
}

// 2. Dato Científico del Día
$datos_curiosos = [
    "El primer auto del mundo (el Benz Patent-Motorwagen de 1886) solo tenía 3 ruedas.",
    "Un motor de combustión interna tiene en promedio unos 230 componentes móviles.",
    "La invención del control de crucero fue hecha por un ingeniero ciego llamado Ralph Teetor.",
    "El olor a 'carro nuevo' es en realidad el aroma de más de 50 compuestos orgánicos volátiles.",
    "El récord de kilometraje para un solo auto es de más de 4.8 millones de kilómetros, un Volvo P1800 de 1966.",
    "Las bolsas de aire (airbags) se inflan en tan solo 30 milisegundos después de un impacto.",
    "El primer espejo retrovisor fue utilizado en la primera carrera de las 500 millas de Indianápolis en 1911."
];
$dato_de_hoy = $datos_curiosos[array_rand($datos_curiosos)];

?>

<div class="row">
    <div class="col-lg-8">
        <h1 class="mb-2"><?php echo $saludo; ?>, <?php echo htmlspecialchars(explode(' ', $_SESSION['admin_nombre'])[0]); ?>!</h1>
        <p class="lead mb-4">Un resumen general del estado de tu taller y la actividad reciente.</p>
    </div>
</div>


<div class="row g-4">
    <!-- Columna Principal con Estadísticas -->
    <div class="col-lg-8">
        <!-- Fila de Tarjetas de Estadísticas -->
        <div class="row g-4 mb-4">
            <!-- Total Cotizaciones -->
            <div class="col-md-6">
                <div class="stat-card bg-metric-1 shadow-sm">
                    <div class="stat-icon"><i class="bi bi-file-earmark-text-fill"></i></div>
                    <p class="stat-number"><?php echo $total_cotizaciones; ?></p>
                    <p class="stat-label">Cotizaciones Totales</p>
                    <div class="card-footer">
                        <a href="view_cotizaciones.php" class="stretched-link">Ver todas <i class="bi bi-arrow-right-circle"></i></a>
                    </div>
                </div>
            </div>
            <!-- Total Usuarios -->
            <div class="col-md-6">
                <div class="stat-card bg-metric-2 shadow-sm">
                    <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                    <p class="stat-number"><?php echo $total_usuarios; ?></p>
                    <p class="stat-label">Usuarios Registrados</p>
                    <div class="card-footer">
                        <a href="view_usuarios.php" class="stretched-link">Gestionar usuarios <i class="bi bi-arrow-right-circle"></i></a>
                    </div>
                </div>
            </div>
            <!-- Cotizaciones Pendientes -->
            <div class="col-md-6">
                <div class="stat-card bg-metric-3 shadow-sm">
                    <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
                    <p class="stat-number"><?php echo $pendientes; ?></p>
                    <p class="stat-label">Cotizaciones Pendientes</p>
                    <div class="card-footer">
                        <a href="view_cotizaciones.php" class="stretched-link">Revisar ahora <i class="bi bi-arrow-right-circle"></i></a>
                    </div>
                </div>
            </div>
            <!-- Cotizaciones Agendadas -->
            <div class="col-md-6">
                <div class="stat-card bg-metric-4 shadow-sm">
                    <div class="stat-icon"><i class="bi bi-calendar-check-fill"></i></div>
                    <p class="stat-number"><?php echo $agendadas; ?></p>
                    <p class="stat-label">Servicios Agendados</p>
                    <div class="card-footer">
                        <a href="view_cotizaciones.php" class="stretched-link">Ver agenda <i class="bi bi-arrow-right-circle"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Tabla de Cotizaciones Recientes -->
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-clock-history me-2"></i>Últimas Cotizaciones Pendientes</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Vehículo</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ultimas_cotizaciones)): ?>
                                <tr>
                                    <td colspan="4" class="text-center p-4">
                                        <i class="bi bi-check2-circle fs-3 text-success"></i>
                                        <h5 class="mt-2">¡Felicidades!</h5>
                                        <p class="text-muted mb-0">No hay cotizaciones pendientes por el momento.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($ultimas_cotizaciones as $cotizacion): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y H:i', strtotime($cotizacion['fecha_creacion'])); ?></td>
                                        <td><?php echo htmlspecialchars($cotizacion['nombre_cliente']); ?></td>
                                        <td><?php echo htmlspecialchars($cotizacion['marca_vehiculo'] . ' ' . $cotizacion['modelo_vehiculo']); ?></td>
                                        <td>
                                            <a href="view_cotizaciones.php" class="btn btn-sm btn-outline-primary">Ver Detalles</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Columna Lateral con Widgets -->
    <div class="col-lg-4">
        <!-- Widget del Reloj -->
        <div class="card clock-widget text-center shadow-sm">
            <div class="card-body">
                <canvas id="analogClock" width="250" height="250"></canvas>
                <div id="digitalTime" class="digital-time"></div>
            </div>
        </div>

        <!-- Widget de Dato Curioso -->
        <div class="card fun-fact-card mt-4 shadow-sm">
            <div class="card-body">
                <div class="d-flex">
                    <i class="bi bi-lightbulb-fill fun-fact-icon"></i>
                    <div>
                        <h5 class="card-title">Dato Científico</h5>
                        <p class="card-text"><?php echo $dato_de_hoy; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// --- SCRIPT PARA EL RELOJ ANALÓGICO ---
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('analogClock');
    const digitalTimeEl = document.getElementById('digitalTime');
    const ctx = canvas.getContext('2d');
    const radius = canvas.height / 2;
    ctx.translate(radius, radius);
    const faceRadius = radius * 0.90;

    function drawClock() {
        drawFace(ctx, faceRadius);
        drawNumbers(ctx, faceRadius);
        drawTime(ctx, faceRadius);
        updateDigitalTime();
    }

    function drawFace(ctx, radius) {
        // Círculo principal
        ctx.beginPath();
        ctx.arc(0, 0, radius, 0, 2 * Math.PI);
        ctx.fillStyle = '#f8f9fa'; // Fondo claro
        ctx.fill();

        // Borde exterior
        const grad = ctx.createRadialGradient(0, 0, radius * 0.95, 0, 0, radius * 1.05);
        grad.addColorStop(0, '#e9ecef');
        grad.addColorStop(0.5, 'white');
        grad.addColorStop(1, '#e9ecef');
        ctx.strokeStyle = grad;
        ctx.lineWidth = radius * 0.1;
        ctx.stroke();

        // Punto central
        ctx.beginPath();
        ctx.arc(0, 0, radius * 0.05, 0, 2 * Math.PI);
        ctx.fillStyle = '#0d2c4f'; // Azul oscuro del taller
        ctx.fill();
    }

    function drawNumbers(ctx, radius) {
        ctx.font = radius * 0.15 + "px Montserrat";
        ctx.textBaseline = "middle";
        ctx.textAlign = "center";
        ctx.fillStyle = '#343a40'; // Texto oscuro
        for (let num = 1; num <= 12; num++) {
            let ang = num * Math.PI / 6;
            ctx.rotate(ang);
            ctx.translate(0, -radius * 0.85);
            ctx.rotate(-ang);
            ctx.fillText(num.toString(), 0, 0);
            ctx.rotate(ang);
            ctx.translate(0, radius * 0.85);
            ctx.rotate(-ang);
        }
    }

    function drawTime(ctx, radius) {
        const now = new Date();
        let hour = now.getHours();
        let minute = now.getMinutes();
        let second = now.getSeconds();

        // Hora
        hour = hour % 12;
        hour = (hour * Math.PI / 6) + (minute * Math.PI / (6 * 60)) + (second * Math.PI / (360 * 60));
        drawHand(ctx, hour, radius * 0.5, radius * 0.07, '#0d2c4f');
        
        // Minutos
        minute = (minute * Math.PI / 30) + (second * Math.PI / (30 * 60));
        drawHand(ctx, minute, radius * 0.8, radius * 0.07, '#343a40');
        
        // Segundos
        second = (second * Math.PI / 30);
        drawHand(ctx, second, radius * 0.9, radius * 0.02, '#fbc108'); // Segundero amarillo
    }

    function drawHand(ctx, pos, length, width, color) {
        ctx.beginPath();
        ctx.lineWidth = width;
        ctx.lineCap = "round";
        ctx.strokeStyle = color;
        ctx.moveTo(0, 0);
        ctx.rotate(pos);
        ctx.lineTo(0, -length);
        ctx.stroke();
        ctx.rotate(-pos);
    }
    
    function updateDigitalTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        digitalTimeEl.textContent = timeString;
    }

    setInterval(drawClock, 1000);
});
</script>

