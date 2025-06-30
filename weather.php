<?php include 'inc/header.php'; ?>

<?php
$cityName = $_GET['city'] ?? 'Santo Domingo'; // Ciudad por defecto
$weatherData = null;
$error = null;
$backgroundClass = ''; // Clase CSS para el fondo

// Función para obtener latitud y longitud de una ciudad usando Nominatim (OpenStreetMap)
function getCoordinates($city) {
    $geocodeUrl = "https://nominatim.openstreetmap.org/search?q=" . urlencode($city) . "&format=json&limit=1";
    $options = [
        'http' => [
            'user_agent' => 'Mi_Web_Weather_App/1.0 (elmerjoelmc@example.com)' // Requiere User-Agent para Nominatim
        ]
    ];
    $context = stream_context_create($options);
    $response = @file_get_contents($geocodeUrl, false, $context);

    if ($response === FALSE) {
        return ['error' => 'No se pudo obtener las coordenadas de la ciudad. Error de conexión con el servicio de geocodificación.'];
    }

    $data = json_decode($response, true);

    if (empty($data) || !isset($data[0]['lat']) || !isset($data[0]['lon'])) {
        return ['error' => 'No se encontraron coordenadas para "' . htmlspecialchars($city) . '". Por favor, verifica el nombre de la ciudad o intenta una ciudad más conocida.'];
    }

    return [
        'latitude' => $data[0]['lat'],
        'longitude' => $data[0]['lon'],
        'display_name' => $data[0]['display_name'] // Nombre completo devuelto por el servicio
    ];
}

if (!empty($cityName)) {
    $coordinates = getCoordinates($cityName);

    if (isset($coordinates['error'])) {
        $error = $coordinates['error'];
    } else {
        $latitude = $coordinates['latitude'];
        $longitude = $coordinates['longitude'];
        $actualCityName = $coordinates['display_name']; // Nombre completo para mostrar

        // Construir la URL de la API de Open-Meteo
        // daily=weathercode,temperature_2m_max,temperature_2m_min,sunrise,sunset,uv_index_max
        // hourly=temperature_2m,weathercode
        $apiUrl = "https://api.open-meteo.com/v1/forecast?latitude=" . $latitude . "&longitude=" . $longitude . "&current_weather=true&hourly=temperature_2m,weathercode&daily=weathercode,temperature_2m_max,temperature_2m_min,sunrise,sunset&timezone=auto&forecast_days=1"; // Pedimos solo la previsión del día actual

        // Usar cURL para la solicitud
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Deshabilitar SSL para desarrollo local
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = "¡Error de conexión! No se pudo conectar con la API del clima. " . curl_error($ch);
        } else {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode >= 400) {
                $error = "Error al obtener el clima. Código HTTP: " . $httpCode . ". Por favor, verifica la ciudad.";
            } else {
                $data = json_decode($response, true);

                if (json_last_error() !== JSON_ERROR_NONE || !isset($data['current_weather'])) {
                    $error = "Error al decodificar los datos del clima o formato inesperado de Open-Meteo.";
                } else {
                    $weatherData = $data;

                    // Mapeo de Weather Codes de Open-Meteo a nombres y clases CSS (simplificado)
                    // https://www.open-meteo.com/en/docs
                    $weatherCode = $weatherData['current_weather']['weathercode'];
                    $weatherDescription = '';
                    $weatherIcon = ''; // Icono para mostrar

                    if ($weatherCode >= 0 && $weatherCode <= 1) { // Clear sky
                        $weatherDescription = 'Cielo Despejado';
                        $weatherIcon = '☀️'; // Emoji
                        $backgroundClass = 'bg-warning-sunny';
                    } elseif ($weatherCode >= 2 && $weatherCode <= 3) { // Partly cloudy, overcast
                        $weatherDescription = 'Mayormente Nublado';
                        $weatherIcon = '☁️'; // Emoji
                        $backgroundClass = 'bg-primary-cloudy';
                    } elseif ($weatherCode >= 45 && $weatherCode <= 48) { // Fog
                        $weatherDescription = 'Niebla';
                        $weatherIcon = '🌫️'; // Emoji
                        $backgroundClass = 'bg-secondary-haze';
                    } elseif (($weatherCode >= 51 && $weatherCode <= 67) || ($weatherCode >= 80 && $weatherCode <= 82)) { // Drizzle, Rain showers
                        $weatherDescription = 'Lluvia';
                        $weatherIcon = '🌧️'; // Emoji
                        $backgroundClass = 'bg-info-rainy';
                    } elseif (($weatherCode >= 71 && $weatherCode <= 77) || ($weatherCode >= 85 && $weatherCode <= 86)) { // Snow
                        $weatherDescription = 'Nieve';
                        $weatherIcon = '❄️'; // Emoji
                        $backgroundClass = 'bg-light-snowy';
                    } elseif (($weatherCode >= 95 && $weatherCode <= 99)) { // Thunderstorm
                        $weatherDescription = 'Tormenta';
                        $weatherIcon = '⛈️'; // Emoji
                        $backgroundClass = 'bg-dark-stormy';
                    } else {
                        $weatherDescription = 'Condición Desconocida';
                        $weatherIcon = '❓';
                        $backgroundClass = 'bg-primary'; // Default
                    }
                }
            }
        }
        curl_close($ch);
    }
} elseif (isset($_GET['city']) && empty($_GET['city'])) {
    // Mensaje si se envió el formulario pero el campo está vacío
    $error = 'Por favor, ingresa el nombre de una ciudad para buscar el clima.';
}
?>

<div class="row justify-content-center my-5">
    <div class="col-md-8">
        <div class="card shadow-lg <?php echo $backgroundClass; ?>" style="min-height: 400px;">
            <div class="card-header text-white text-center <?php echo $backgroundClass ? 'border-bottom-0' : 'bg-primary'; ?>">
                <h2 class="mb-0">El Clima 🌦️</h2>
            </div>
            <div class="card-body d-flex flex-column justify-content-between">
                <p class="text-center text-muted">Busca el clima en cualquier ciudad del mundo.</p>

                <form action="" method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" class="form-control" name="city" placeholder="Ej: Santo Domingo" required value="<?php echo htmlspecialchars($_GET['city'] ?? 'Santo Domingo'); ?>">
                        <button type="submit" class="btn btn-outline-primary">Buscar Clima</button>
                    </div>
                </form>

                <?php if ($error) : ?>
                    <div class="alert alert-danger text-center" role="alert">
                        <?php echo $error; ?>
                    </div>
                <?php elseif ($weatherData) : ?>
                    <div class="text-center text-white p-3 rounded" style="background-color: rgba(0,0,0,0.3);">
                        <h3 class="mb-2"><?php echo htmlspecialchars($cityName); ?></h3>
                        <p class="fs-1"><?php echo $weatherIcon; ?></p>
                        <p class="display-4 mb-1"><?php echo round($weatherData['current_weather']['temperature']); ?>°C</p>
                        <p class="lead"><?php echo $weatherDescription; ?></p>
                        <?php if (isset($weatherData['daily'])) : ?>
                            <small>Max: <?php echo round($weatherData['daily']['temperature_2m_max'][0]); ?>°C | Min: <?php echo round($weatherData['daily']['temperature_2m_min'][0]); ?>°C</small><br>
                            <small>Amanecer: <?php echo date('H:i', $weatherData['daily']['sunrise'][0]); ?> | Atardecer: <?php echo date('H:i', $weatherData['daily']['sunset'][0]); ?></small>
                        <?php endif; ?>
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