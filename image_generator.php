<?php include 'inc/header.php'; ?>

<?php
$keyword = $_GET['keyword'] ?? '';
$generatedSvg = null;
$error = null;

if (!empty($keyword)) {
    $searchSeed = urlencode(trim($keyword));
    $imageStyle = 'identicon'; // Puedes mantener 'identicon' o 'avataaars'

    // Generar un color hexadecimal aleatorio
    $randomColor = str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);

    $apiUrl = "https://api.dicebear.com/8.x/{$imageStyle}/svg?seed={$searchSeed}&backgroundColor={$randomColor}&radius=10";

    // === CÓDIGO DE DEPURACIÓN (déjalo por ahora) ===
    echo '<div class="alert alert-warning text-center mt-4"><strong>URL de API para depuración:</strong> <a href="' . htmlspecialchars($apiUrl) . '" target="_blank">' . htmlspecialchars($apiUrl) . '</a></div>';
    // === FIN CÓDIGO DE DEPURACIÓN ===

    // Realizar la solicitud a la API usando cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = "Error al obtener la imagen generada: " . curl_error($ch);
    } else {
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode >= 400) {
            $error = "Error HTTP al obtener la imagen. Código: " . $httpCode . ". Mensaje de la API: " . htmlspecialchars($response); // Muestra el mensaje de la API
        } elseif (strpos(curl_getinfo($ch, CURLINFO_CONTENT_TYPE), 'image/svg+xml') === false) {
             $error = "La respuesta no es un SVG válido. Tipo: " . curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        }
        else {
            $generatedSvg = $response;
        }
    }
    curl_close($ch);

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
                <?php elseif ($generatedSvg) : ?>
                    <div class="mt-4 p-3 border rounded text-center">
                        <h4>Imagen Generada para "<?php echo htmlspecialchars($keyword); ?>"</h4>
                        <div style="max-width: 300px; margin: 0 auto; border-radius: 8px; overflow: hidden; display: flex; justify-content: center; align-items: center;">
                             <?php echo $generatedSvg; ?>
                        </div>
                        <p class="text-muted small mt-3">La imagen es generada de forma única a partir de tu palabra clave.</p>
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