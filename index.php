<?php include 'inc/header.php'; ?>

<div class="row justify-content-center text-center my-5">
    <div class="col-md-8">
        <?php 
        // Verifica si el archivo de imagen existe antes de mostrarlo
        $imagePath = 'img/Mi_Foto_2x2.jpg';
        if (file_exists($imagePath)) {
            echo '<img src="' . htmlspecialchars($imagePath) . '" class="img-fluid rounded-circle mb-4 profile-pic" alt="Foto de perfil de Elmer Joel Montilla Castro">';
        } else {
            echo '<div class="alert alert-warning" role="alert">¡Atención! No se encontró la imagen de perfil en ' . htmlspecialchars($imagePath) . '.</div>';
            echo '<div class="mb-4 d-inline-block p-4 bg-light rounded-circle shadow-sm" style="width: 180px; height: 180px; line-height: 150px; font-size: 0.9rem; color: #6c757d;">Foto no encontrada</div>';
        }
        ?>
        <h1 class="display-4">¡Bienvenido a mi web de APIs!</h1>
        <p class="lead">Hola, mi nombre es Elmer Joel Montilla Castro. Este portal es un proyecto de PHP donde exploro y muestro datos de diversas APIs externas, desde predicciones hasta información de Pokémon.</p>
        <hr class="my-4">
        <p>Usa el menú de navegación para explorar las diferentes funcionalidades de las APIs.</p>
        <a class="btn btn-primary btn-lg" href="about.php" role="button">Conoce más sobre mí y el proyecto</a>
    </div>
</div>

<div class="container my-5">
    <h2 class="text-center mb-4">Explora nuestras APIs</h2>
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <h5 class="card-title">Pronóstico del Tiempo</h5>
                    <p class="card-text text-muted">Obtén el clima actual y el pronóstico de cualquier ciudad.</p>
                    <a href="weather.php" class="btn btn-outline-primary mt-auto">Ver Clima</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <h5 class="card-title">Conversor de Monedas</h5>
                    <p class="card-text text-muted">Convierte entre diferentes divisas.</p>
                    <a href="currency_converter.php" class="btn btn-outline-warning mt-auto">Ir al Conversor</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <h5 class="card-title">Buscador de Pokémon</h5>
                    <p class="card-text text-muted">Encuentra información sobre tus Pokémon favoritos.</p>
                    <a href="pokemon.php" class="btn btn-outline-danger mt-auto">Buscar Pokémon</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <h5 class="card-title">Hecho Aleatorio</h5>
                    <p class="card-text text-muted">Descubre un dato interesante y sorprendente.</p>
                    <a href="random_fact.php" class="btn btn-outline-success mt-auto">Ver Hecho</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <h5 class="card-title">Posts de WordPress</h5>
                    <p class="card-text text-muted">Explora los últimos artículos de un blog de WordPress.</p>
                    <a href="wordpress_posts.php" class="btn btn-outline-info mt-auto">Ver Posts</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <h5 class="card-title">Usuario Aleatorio</h5>
                    <p class="card-text text-muted">Genera perfiles de usuarios ficticios con avatares.</p>
                    <a href="random_user.php" class="btn btn-outline-dark mt-auto">Generar Usuario</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <h5 class="card-title">Generador de Imágenes por Texto</h5>
                    <p class="card-text text-muted">Genera imágenes únicas a partir de una palabra clave.</p>
                    <a href="image_generator.php" class="btn btn-outline-secondary mt-auto">Generar Imagen</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <h5 class="card-title">Cita Aleatoria</h5>
                    <p class="card-text text-muted">Obtén una cita inspiradora o famosa al azar.</p>
                    <a href="random_quote.php" class="btn btn-outline-primary mt-auto">Ver Cita</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'inc/footer.php'; ?>