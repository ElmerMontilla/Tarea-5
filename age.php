<?php include 'inc/header.php'; ?>

<div class="row justify-content-center my-5">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white text-center">
                <h2 class="mb-0">Predicción de Edad 🎂</h2>
            </div>
            <div class="card-body">
                <p class="text-muted text-center">Ingresa un nombre para estimar la edad.</p>

                <form action="" method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" class="form-control" name="name" placeholder="Ej: Pedro, Ana" required value="<?php echo htmlspecialchars($_GET['name'] ?? ''); ?>">
                        <button type="submit" class="btn btn-outline-success">Predecir Edad</button>
                    </div>
                </form>

                <?php
                // Verificar si se ha enviado el formulario y el nombre no está vacío
                if (isset($_GET['name']) && !empty($_GET['name'])) {
                    $name = htmlspecialchars($_GET['name']); // Sanitizar el nombre

                    // Construir la URL de la API
                    $apiUrl = "https://api.agify.io/?name=" . urlencode($name);

                    // Realizar la solicitud a la API
                    $response = @file_get_contents($apiUrl);

                    // Verificar si la solicitud fue exitosa
                    if ($response === FALSE) {
                        echo '<div class="alert alert-danger text-center" role="alert">';
                        echo '<strong>¡Error de conexión!</strong> No se pudo conectar con la API de predicción de edad. Por favor, inténtalo de nuevo más tarde.';
                        echo '</div>';
                    } else {
                        // Decodificar la respuesta JSON
                        $data = json_decode($response, true);

                        // Verificar si la decodificación fue exitosa y si hay datos
                        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['age'])) {
                            echo '<div class="alert alert-warning text-center" role="alert">';
                            echo '<strong>¡Error en los datos!</strong> No se pudo obtener una predicción de edad válida para el nombre "<strong>' . $name . '</strong>".';
                            echo '</div>';
                        } elseif ($data['age'] === null) {
                            // Nombre no encontrado o no hay datos suficientes
                            echo '<div class="alert alert-info text-center" role="alert">';
                            echo 'No hay suficientes datos para estimar la edad de "<strong>' . $name . '</strong>". Por favor, intenta con otro nombre.';
                            echo '</div>';
                        } else {
                            // Mostrar el resultado
                            $age = $data['age'];
                            $category = '';
                            $imageHtml = ''; // Para la imagen o emoji

                            if ($age < 18) {
                                $category = 'Joven';
                                $imageHtml = '<span class="fs-1">👶</span>'; // Emoji
                                // Si usas imagen: $imageHtml = '<img src="img/joven.png" alt="Joven" style="width: 80px;">';
                            } elseif ($age >= 18 && $age <= 60) {
                                $category = 'Adulto';
                                $imageHtml = '<span class="fs-1">🧑</span>'; // Emoji
                                // Si usas imagen: $imageHtml = '<img src="img/adulto.png" alt="Adulto" style="width: 80px;">';
                            } else {
                                $category = 'Anciano';
                                $imageHtml = '<span class="fs-1">👴</span>'; // Emoji
                                // Si usas imagen: $imageHtml = '<img src="img/anciano.png" alt="Anciano" style="width: 80px;">';
                            }
                ?>
                            <div class="alert alert-success text-center">
                                <h4>Resultado para "<strong><?php echo $name; ?></strong>"</h4>
                                <p class="fs-2 mb-0">Edad Estimada: <strong><?php echo $age; ?></strong> años</p>
                                <p class="fs-4"><?php echo $category; ?> <?php echo $imageHtml; ?></p>
                            </div>
                <?php
                        }
                    }
                } elseif (isset($_GET['name']) && empty($_GET['name'])) {
                    // Mensaje si se envió el formulario pero el campo está vacío
                    echo '<div class="alert alert-warning text-center" role="alert">';
                    echo 'Por favor, ingresa un nombre para realizar la predicción.';
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