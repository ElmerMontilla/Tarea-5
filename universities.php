<?php include 'inc/header.php'; ?>

<div class="row justify-content-center my-5">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white text-center">
                <h2 class="mb-0">Universidades de un País 🎓</h2>
            </div>
            <div class="card-body">
                <p class="text-muted text-center">Ingresa el nombre de un país en inglés para ver sus universidades.</p>
                <p class="text-muted text-center small">(Ej: Republica Dominicana, Alemania, Suiza)</p>

                <form action="" method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" class="form-control" name="country" placeholder="Ej: Dominican Republic" required value="<?php echo htmlspecialchars($_GET['country'] ?? ''); ?>">
                        <button type="submit" class="btn btn-outline-info">Buscar Universidades</button>
                    </div>
                </form>

                <?php
                // Verificar si se ha enviado el formulario y el nombre del país no está vacío
                if (isset($_GET['country']) && !empty($_GET['country'])) {
                    $country = htmlspecialchars($_GET['country']); // Sanitizar el nombre del país

                    // Construir la URL de la API
                    // Nota: La API requiere el nombre del país con espacios codificados como '+' o '%20'
                    $apiUrl = "http://universities.hipolabs.com/search?country=" . urlencode($country);

                    // Realizar la solicitud a la API
                    $response = @file_get_contents($apiUrl);

                    // Verificar si la solicitud fue exitosa
                    if ($response === FALSE) {
                        echo '<div class="alert alert-danger text-center" role="alert">';
                        echo '<strong>¡Error de conexión!</strong> No se pudo conectar con la API de universidades. Por favor, inténtalo de nuevo más tarde.';
                        echo '</div>';
                    } else {
                        // Decodificar la respuesta JSON
                        $universities = json_decode($response, true);

                        // Verificar si la decodificación fue exitosa y si es un array
                        if (json_last_error() !== JSON_ERROR_NONE || !is_array($universities)) {
                            echo '<div class="alert alert-warning text-center" role="alert">';
                            echo '<strong>¡Error en los datos!</strong> No se pudo obtener una lista válida de universidades para "<strong>' . $country . '</strong>".';
                            echo '</div>';
                        } elseif (empty($universities)) {
                            // No se encontraron universidades para el país
                            echo '<div class="alert alert-info text-center" role="alert">';
                            echo 'No se encontraron universidades para "<strong>' . $country . '</strong>". Por favor, verifica el nombre del país (en inglés) e intenta de nuevo.';
                            echo '</div>';
                        } else {
                            // Mostrar la lista de universidades
                ?>
                            <h4 class="mt-4 mb-3">Universidades en "<?php echo $country; ?>"</h4>
                            <div class="list-group">
                                <?php foreach ($universities as $uni) : ?>
                                    <a href="<?php echo htmlspecialchars($uni['web_pages'][0] ?? '#'); ?>" target="_blank" class="list-group-item list-group-item-action flex-column align-items-start mb-2">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1"><?php echo htmlspecialchars($uni['name'] ?? 'Nombre no disponible'); ?></h5>
                                            <?php if (isset($uni['country'])) : ?>
                                                <small class="text-muted"><?php echo htmlspecialchars($uni['country']); ?></small>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (isset($uni['domains'][0])) : ?>
                                            <p class="mb-1">Dominio: <?php echo htmlspecialchars($uni['domains'][0]); ?></p>
                                        <?php endif; ?>
                                        <?php if (isset($uni['web_pages'][0])) : ?>
                                            <small class="text-primary">Visitar Sitio Web</small>
                                        <?php endif; ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                <?php
                        }
                    }
                } elseif (isset($_GET['country']) && empty($_GET['country'])) {
                    // Mensaje si se envió el formulario pero el campo está vacío
                    echo '<div class="alert alert-warning text-center" role="alert">';
                    echo 'Por favor, ingresa el nombre de un país para buscar universidades.';
                    echo '</div>';
                }
                ?>
            </div>
            <div class="card-footer text-center">
                <a href="index.php" class="btn btn-outline-secondary btn-sm">Volver al Inicio</a>
            </div>
        </div>
    </div>
</div>

<?php include 'inc/footer.php'; ?>