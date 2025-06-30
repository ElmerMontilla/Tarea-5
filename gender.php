<?php include 'inc/header.php'; ?>

<di class="row justify-content-center my-5">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white text-center">
                <h2 class="mb-0">Predicción de Género 👦👧</h2>
            </div>
            <div class="card-body">
                <p class="text-muted text-center">Ingresa un nombre para predecir si es masculino o femenino.</p>

                <form action="" method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" class="form-control" name="name" placeholder="Ej: Juan, María" required value="<?php echo htmlspecialchars($_GET['name'] ?? ''); ?>">
                        <button type="submit" class="btn btn-outline-primary">Predecir</button>
                    </div>
                </form>

                <?php
                // Verificar si se ha enviado el formulario y el nombre no está vacío
                if (isset($_GET['name']) && !empty($_GET['name'])) {
                    $name = htmlspecialchars($_GET['name']); // Sanitizar el nombre para seguridad

                    // Construir la URL de la API
                    $apiUrl = "https://api.genderize.io/?name=" . urlencode($name);

                    // Realizar la solicitud a la API
                    $response = @file_get_contents($apiUrl); // @ suprime errores si la URL no es accesible

                    // Verificar si la solicitud fue exitosa
                    if ($response === FALSE) {
                        echo '<div class="alert alert-danger text-center" role="alert">';
                        echo '<strong>¡Error de conexión!</strong> No se pudo conectar con la API de predicción de género. Por favor, inténtalo de nuevo más tarde.';
                        echo '</div>';
                    } else {
                        // Decodificar la respuesta JSON
                        $data = json_decode($response, true);

                        // Verificar si la decodificación fue exitosa y si hay datos
                        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['gender'])) {
                            echo '<div class="alert alert-warning text-center" role="alert">';
                            echo '<strong>¡Error en los datos!</strong> No se pudo obtener una predicción de género válida para el nombre "<strong>' . $name . '</strong>".';
                            echo '</div>';
                        } elseif ($data['gender'] === null) {
                            // Nombre no encontrado o no hay datos suficientes
                            echo '<div class="alert alert-info text-center" role="alert">';
                            echo 'No hay suficientes datos para predecir el género de "<strong>' . $name . '</strong>". Por favor, intenta con otro nombre.';
                            echo '</div>';
                        } else {
                            // Mostrar el resultado
                            $gender = $data['gender'];
                            $probability = round($data['probability'] * 100, 2); // Convertir a porcentaje

                            $genderClass = '';
                            $genderText = '';
                            $genderIcon = '';

                            if ($gender === 'male') {
                                $genderClass = 'text-primary'; // Azul de Bootstrap
                                $genderText = 'Masculino';
                                $genderIcon = '♂️';
                            } elseif ($gender === 'female') {
                                $genderClass = 'text-danger'; // Rosa/Rojo de Bootstrap (puede ajustarse con CSS personalizado si quieres un rosa más claro)
                                $genderText = 'Femenino';
                                $genderIcon = '♀️';
                            } else {
                                // Esto debería ser cubierto por el caso 'null' pero como fallback
                                $genderClass = 'text-muted';
                                $genderText = 'Desconocido';
                                $genderIcon = '❓';
                            }
                ?>
                            <div class="alert alert-success text-center">
                                <h4>Resultado para "<strong><?php echo $name; ?></strong>"</h4>
                                <p class="fs-4 <?php echo $genderClass; ?>">
                                    <strong>Género Predicho:</strong> <?php echo $genderText; ?> <?php echo $genderIcon; ?>
                                </p>
                                <p class="mb-0 text-muted">Probabilidad: <?php echo $probability; ?>%</p>
                                <div class="progress mt-2">
                                    <div class="progress-bar <?php echo ($gender === 'male' ? 'bg-primary' : 'bg-danger'); ?>" role="progressbar" style="width: <?php echo $probability; ?>%;" aria-valuenow="<?php echo $probability; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
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