<?php include 'inc/header.php'; ?>

<?php
// CONFIGURACIÓN:
// Reemplaza esta URL con la URL base del sitio WordPress del que quieres obtener los posts.
// Ejemplo: 'https://wordpress.org/news/'
// Asegúrate de que termine con una barra inclinada /.
$wordpressBaseUrl = 'https://wordpress.org/news/'; // Puedes cambiar esto a tu propio sitio WP
$numberOfPosts = 5; // Cantidad de posts a mostrar

$posts = [];
$error = null;

// Construir la URL de la API REST de WordPress
// Ejemplo: https://wordpress.org/news/wp-json/wp/v2/posts?per_page=5
$apiUrl = rtrim($wordpressBaseUrl, '/') . '/wp-json/wp/v2/posts?per_page=' . (int)$numberOfPosts;

// Realizar la solicitud a la API usando cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Para desarrollo local
// Añadir User-Agent para evitar problemas con algunos servidores que bloquean requests sin él
curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-APIAssistant/1.0'); 
$response = curl_exec($ch);

if (curl_errno($ch)) {
    $error = "¡Error de conexión! No se pudo conectar con el sitio WordPress. " . curl_error($ch);
} else {
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode >= 400) {
        $error = "Error al obtener los posts de WordPress. Código HTTP: " . $httpCode . ". Verifica la URL del sitio WordPress o si la API está habilitada.";
        $data = json_decode($response, true);
        if (isset($data['message'])) {
            $error .= " Mensaje: " . htmlspecialchars($data['message']);
        }
    } else {
        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $error = "Error al decodificar los datos de los posts o formato inesperado de la API.";
        } elseif (empty($data)) {
            $error = "No se encontraron posts para la URL proporcionada o el blog no tiene posts públicos.";
        } else {
            $posts = $data;
        }
    }
}
curl_close($ch);
?>

<div class="row justify-content-center my-5">
    <div class="col-md-9">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white text-center">
                <h2 class="mb-0">Posts Recientes de WordPress 📰</h2>
            </div>
            <div class="card-body">
                <p class="text-muted text-center">
                    Mostrando posts de: <a href="<?php echo htmlspecialchars($wordpressBaseUrl); ?>" target="_blank"><?php echo htmlspecialchars($wordpressBaseUrl); ?></a>
                </p>

                <?php if ($error) : ?>
                    <div class="alert alert-danger text-center mt-4" role="alert">
                        <?php echo $error; ?>
                    </div>
                <?php elseif (!empty($posts)) : ?>
                    <?php foreach ($posts as $post) : ?>
                        <div class="card mb-3 shadow-sm">
                            <div class="card-body">
                                <h4 class="card-title">
                                    <a href="<?php echo htmlspecialchars($post['link']); ?>" target="_blank" class="text-decoration-none">
                                        <?php echo htmlspecialchars($post['title']['rendered']); ?>
                                    </a>
                                </h4>
                                <h6 class="card-subtitle mb-2 text-muted">
                                    Por <?php echo htmlspecialchars($post['_embedded']['author'][0]['name'] ?? 'Desconocido'); ?>
                                    el <?php echo date('d/m/Y', strtotime($post['date'])); ?>
                                </h6>
                                <p class="card-text">
                                    <?php echo strip_tags($post['excerpt']['rendered']); ?>
                                </p>
                                <a href="<?php echo htmlspecialchars($post['link']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">Leer más</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="alert alert-info text-center mt-4" role="alert">
                        No hay posts para mostrar en este momento.
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