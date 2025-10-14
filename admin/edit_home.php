<?php 
$page_title = "Editar Página de Inicio";
include 'admin_header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ... (La lógica de actualización se mantiene igual, ya funciona para el nuevo campo)
    $message = '';
    $error = false;
    
    $stmt = $conn->prepare("UPDATE contenido_editable SET valor = ? WHERE id = ?");

    if (isset($_POST['content']) && is_array($_POST['content'])) {
        foreach ($_POST['content'] as $id => $value) {
            $id = intval($id);
            $type = isset($_POST['content_type'][$id]) ? $_POST['content_type'][$id] : 'text';
            $sanitized_value = ($type == 'textarea') ? $value : htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            
            $stmt->bind_param("si", $sanitized_value, $id);
            if (!$stmt->execute()) {
                $error = true;
                $message = "<div class='alert alert-danger'>Error al actualizar el contenido.</div>";
                break;
            }
        }
    }
    if (!$error) {
        $message = "<div class='alert alert-success'>¡Contenido de la página de inicio actualizado!</div>";
    }
}

$sql = "SELECT id, campo, valor, descripcion, tipo FROM contenido_editable WHERE seccion = 'home' ORDER BY orden";
$result = $conn->query($sql);
$contents = $result->fetch_all(MYSQLI_ASSOC);
?>

<h1 class="mb-4"><?php echo $page_title; ?></h1>
<p>Desde aquí puedes cambiar los textos e imágenes que aparecen en la página de inicio.</p>

<?php if (isset($message)) { echo $message; } ?>

<div class="card">
    <div class="card-body">
        <form method="POST" action="edit_home.php">
             <?php 
             $current_group = '';
             foreach ($contents as $item): 
                // Detectar cambio de grupo para poner un separador
                if (str_starts_with($item['campo'], 'about_') && $current_group != 'about') {
                    $current_group = 'about';
                    echo '<hr class="my-4"><h4 class="mb-3">Sección "Sobre Nosotros"</h4>';
                } elseif (str_starts_with($item['campo'], 'why_') && $current_group != 'why') {
                    $current_group = 'why';
                    echo '<hr class="my-4"><h4 class="mb-3">Sección "¿Por Qué Elegirnos?"</h4>';
                }
             ?>
                <div class="mb-3">
                    <label for="content_<?php echo $item['id']; ?>" class="form-label fw-bold"><?php echo htmlspecialchars($item['descripcion']); ?></label>
                    <input type="hidden" name="content_type[<?php echo $item['id']; ?>]" value="<?php echo $item['tipo']; ?>">
                    
                    <?php if ($item['tipo'] == 'textarea'): ?>
                        <textarea class="form-control" id="content_<?php echo $item['id']; ?>" name="content[<?php echo $item['id']; ?>]" rows="5"><?php echo htmlspecialchars($item['valor']); ?></textarea>
                    <?php elseif ($item['tipo'] == 'url'): ?>
                        <input type="url" class="form-control" id="content_<?php echo $item['id']; ?>" name="content[<?php echo $item['id']; ?>]" value="<?php echo htmlspecialchars($item['valor']); ?>" placeholder="https://ejemplo.com/imagen.jpg">
                    <?php else: ?>
                        <input type="<?php echo $item['tipo']; ?>" class="form-control" id="content_<?php echo $item['id']; ?>" name="content[<?php echo $item['id']; ?>]" value="<?php echo htmlspecialchars($item['valor']); ?>">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary btn-lg mt-3">Guardar Cambios</button>
        </form>
    </div>
</div>

<?php include 'admin_footer.php'; ?>
