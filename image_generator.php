<?php include 'inc/header.php'; ?>

<?php
$keyword = $_GET['keyword'] ?? '';
$imageUrl = null;
$error = null;

if (!empty($keyword)) {
    // Limpiar y preparar la palabra clave para la URL
    $searchSeed = urlencode(trim($keyword));

    // Elegir un estilo de avatar abstracto o de logotipo en DiceBear
    // Puedes probar otros como 'identicon', 'gridy', 'micah', 'bottts'
    $imageStyle = 'initials'; // Estilo que genera una imagen basada en iniciales o texto

    // Construir la URL de la DiceBear API
    // Ejemplo: https://api.dicebear.com/8.x/initials/svg?seed=palabra_clave&backgroundColor=random&radius=10
    $imageUrl = "https://api.dicebear.com/8.x/{$imageStyle}/svg?seed={$searchSeed}&backgroundColor=random&radius=10";

    // Opcional: Podrías intentar una llamada cURL para verificar la URL
    // Pero para DiceBear, si la URL es válida, la imagen se generará.
    // Los errores suelen ser de red o de URL mal formada, no de la API en sí.
} elseif (isset($_GET['keyword']) && empty($_GET['keyword'])) {
    $error = "Por favor, ingresa una palabra clave para generar una imagen.";
}
?>

<div class="row justify-content-center my-5">
    <div class="col-md-8">
        <div class="card shadow-lg">
            <div class="card-header bg-info text-white text-center">
                <h2 class="mb-0">Generador de Imágenes por Palabra Clave 🖼️</h2>
            </div>
            <div class="card-body text-center">
                <p class="text-muted">Ingresa una palabra para generar una imagen única basada en ella.</p>

                <form action="" method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" class="form-control" name="keyword" placeholder="Ej: naturaleza, abstracción, concepto" required value="<?php echo htmlspecialchars($keyword); ?>">
                        <button type="submit" class="btn btn-outline-info">Generar Imagen</button>
                    </div>
                </form>

                <?php if ($error) : ?>
                    <div class="alert alert-danger text-center mt-4" role="alert">
                        <?php echo $error; ?>
                    </div>
                <?php elseif ($imageUrl) : ?>
                    <div class="mt-4 p-3 border rounded text-center">
                        <h4>Imagen Generada para "<?php echo htmlspecialchars($keyword); ?>"</h4>
                        <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="Imagen generada para <?php echo htmlspecialchars($keyword); ?>" class="img-fluid my-3" style="max-width: 300px; border-radius: 8px;">
                        <p class="text-muted small">La imagen es generada de forma única a partir de tu palabra clave.</p>
                    </div>
                <?php else : ?>
                    <div class="alert alert-info text-center mt-4" role="alert">
                        Ingresa una palabra clave en el campo de arriba y haz clic en "Generar Imagen".
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer text-center">
                <a href="index.php" class="btn btn-outline-secondary btn-sm">Volver al Inicio</a>
            </div>
        </div>
    </div>
</div>

<?php include 'inc/footer.php'; ?>