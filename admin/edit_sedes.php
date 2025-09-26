<?php 
$page_title = "Editar Página de Sedes";
include 'admin_header.php';

// Lógica para procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = '';
    $error = false;
    
    $stmt = $conn->prepare("UPDATE contenido_editable SET valor = ? WHERE id = ?");

    if (isset($_POST['content']) && is_array($_POST['content'])) {
        foreach ($_POST['content'] as $id => $value) {
            $id = intval($id);
            $sanitized_value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

            $stmt->bind_param("si", $sanitized_value, $id);
            if (!$stmt->execute()) {
                $error = true;
                $message = "<div class='alert alert-danger'>Error al actualizar el contenido.</div>";
                break;
            }
        }
    }

    if (!$error) {
        $message = "<div class='alert alert-success'>¡Contenido de la página de sedes actualizado correctamente!</div>";
    }
}

// Lógica para obtener el contenido actual de la base de datos
$sql = "SELECT id, campo, valor, descripcion FROM contenido_editable WHERE seccion = 'ubicacion' ORDER BY orden";
$result = $conn->query($sql);
$contents = $result->fetch_all(MYSQLI_ASSOC);

?>

<h1 class="mb-4"><?php echo $page_title; ?></h1>
<p>Desde aquí puedes cambiar los textos de la página de ubicación, como la dirección y los horarios.</p>

<?php if (isset($message)) { echo $message; } ?>

<div class="card">
    <div class="card-body">
        <form method="POST" action="edit_sedes.php">
            <?php foreach ($contents as $item): ?>
                <div class="mb-3">
                    <label for="content-<?php echo $item['id']; ?>" class="form-label">
                        <strong><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $item['campo']))); ?></strong>
                        <?php if (!empty($item['descripcion'])): ?>
                            <small class="text-muted d-block"><?php echo htmlspecialchars($item['descripcion']); ?></small>
                        <?php endif; ?>
                    </label>
                    
                    <?php if (strlen($item['valor']) > 100): // Si el texto es largo, usamos un textarea ?>
                        <textarea class="form-control" id="content-<?php echo $item['id']; ?>" name="content[<?php echo $item['id']; ?>]" rows="4"><?php echo htmlspecialchars($item['valor']); ?></textarea>
                    <?php else: ?>
                        <input type="text" class="form-control" id="content-<?php echo $item['id']; ?>" name="content[<?php echo $item['id']; ?>]" value="<?php echo htmlspecialchars($item['valor']); ?>">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </form>
    </div>
</div>

<?php include 'admin_footer.php'; ?>
