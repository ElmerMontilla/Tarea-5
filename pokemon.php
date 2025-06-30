<?php include 'inc/header.php'; ?>

<?php
$pokemonSearch = $_GET['pokemon_name_id'] ?? '';
$pokemonData = null;
$error = null;

if (!empty($pokemonSearch)) {
    // Limpiar y normalizar la entrada del usuario
    $pokemonSearch = strtolower(trim($pokemonSearch)); // Convertir a minúsculas y quitar espacios

    // Construir la URL de la PokeAPI
    // Ejemplo: https://pokeapi.co/api/v2/pokemon/pikachu/
    // o https://pokeapi.co/api/v2/pokemon/25/
    $apiUrl = "https://pokeapi.co/api/v2/pokemon/" . urlencode($pokemonSearch) . "/";

    // Realizar la solicitud a la API usando cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Para desarrollo local
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = "¡Error de conexión! No se pudo conectar con la API de Pokémon. " . curl_error($ch);
    } else {
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode == 404) {
            $error = "¡Pokémon no encontrado! Por favor, verifica el nombre o ID e intenta de nuevo.";
        } elseif ($httpCode >= 400) {
            $error = "Error al obtener los datos del Pokémon. Código HTTP: " . $httpCode . ".";
        } else {
            $data = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE || !isset($data['name'])) {
                $error = "Error al decodificar los datos del Pokémon o formato inesperado de la API.";
            } else {
                $pokemonData = $data;
            }
        }
    }
    curl_close($ch);
} elseif (isset($_GET['pokemon_name_id']) && empty($_GET['pokemon_name_id'])) {
    // Mensaje si se envió el formulario pero el campo está vacío
    $error = "Por favor, ingresa el nombre o ID de un Pokémon para buscar.";
}
?>

<div class="row justify-content-center my-5">
    <div class="col-md-8">
        <div class="card shadow-lg">
            <div class="card-header bg-danger text-white text-center">
                <h2 class="mb-0">Pokédex ⚡</h2>
            </div>
            <div class="card-body">
                <p class="text-muted text-center">Busca tu Pokémon favorito por nombre o ID.</p>

                <form action="" method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" class="form-control" name="pokemon_name_id" placeholder="Ej: pikachu o 25" required value="<?php echo htmlspecialchars($_GET['pokemon_name_id'] ?? ''); ?>">
                        <button type="submit" class="btn btn-outline-danger">Buscar Pokémon</button>
                    </div>
                </form>

                <?php if ($error) : ?>
                    <div class="alert alert-danger text-center mt-4" role="alert">
                        <?php echo $error; ?>
                    </div>
                <?php elseif ($pokemonData) : ?>
                    <div class="text-center p-3 rounded" style="background-color: rgba(220,53,69,0.1);">
                        <h3 class="mb-3 text-danger text-capitalize"><?php echo htmlspecialchars($pokemonData['name']); ?> (#<?php echo htmlspecialchars($pokemonData['id']); ?>)</h3>

                        <?php if (isset($pokemonData['sprites']['front_default'])) : ?>
                            <img src="<?php echo htmlspecialchars($pokemonData['sprites']['front_default']); ?>" alt="<?php echo htmlspecialchars($pokemonData['name']); ?>" class="img-fluid mb-3" style="max-width: 150px;">
                        <?php else : ?>
                            <p class="text-muted">Imagen no disponible</p>
                        <?php endif; ?>

                        <div class="row text-start">
                            <div class="col-md-6 mb-2">
                                <strong>Tipos:</strong>
                                <?php
                                $types = [];
                                foreach ($pokemonData['types'] as $typeInfo) {
                                    $types[] = '<span class="badge bg-secondary">' . htmlspecialchars($typeInfo['type']['name']) . '</span>';
                                }
                                echo implode(' ', $types);
                                ?>
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Habilidades:</strong>
                                <?php
                                $abilities = [];
                                foreach ($pokemonData['abilities'] as $abilityInfo) {
                                    $abilities[] = htmlspecialchars($abilityInfo['ability']['name']);
                                }
                                echo implode(', ', $abilities);
                                ?>
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Altura:</strong> <?php echo htmlspecialchars($pokemonData['height'] / 10); ?> m
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Peso:</strong> <?php echo htmlspecialchars($pokemonData['weight'] / 10); ?> kg
                            </div>
                        </div>
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