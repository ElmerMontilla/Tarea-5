

<?php include 'inc/header.php'; ?>

<?php
// TU CLAVE API DE EXCHANGERATE-API.COM - ¡CLAVE INSERTADA!
$apiKey = 'b6a20f4eb700d3c63030cec4'; 

// Monedas comunes para los selectores (código de 3 letras)
$commonCurrencies = [
    'USD' => 'Dólar Estadounidense',
    'EUR' => 'Euro',
    'DOP' => 'Peso Dominicano', // ExchangeRate-API.com suele soportar esta
    'GBP' => 'Libra Esterlina',
    'JPY' => 'Yen Japonés',
    'CAD' => 'Dólar Canadiense',
    'MXN' => 'Peso Mexicano',
    'COP' => 'Peso Colombiano',
    'CLP' => 'Peso Chileno',
    'ARS' => 'Peso Argentino',
    'BRL' => 'Real Brasileño',
    'CNY' => 'Yuan Chino',
    'INR' => 'Rupia India',
    'AUD' => 'Dólar Australiano',
    'CHF' => 'Franco Suizo',
    'RUB' => 'Rublo Ruso',
    'ZAR' => 'Rand Sudafricano',
    'KRW' => 'Won Surcoreano',
    'SGD' => 'Dólar de Singapur',
    'NZD' => 'Dólar Neozelandés',
    'SEK' => 'Corona Sueca',
    'NOK' => 'Corona Noruega',
    'DKK' => 'Corona Danesa',
    'PLN' => 'Zloty Polaco',
    'TRY' => 'Lira Turca',
    'IDR' => 'Rupia Indonesia',
    'MYR' => 'Ringgit Malayo',
    'PHP' => 'Peso Filipino',
    'THB' => 'Baht Tailandés',
    'CZK' => 'Corona Checa',
    'HUF' => 'Florín Húngaro',
    'ILS' => 'Shekel Israelí',
    'KZT' => 'Tenge Kazajo'
];

$amount = $_GET['amount'] ?? '';
$fromCurrency = $_GET['from_currency'] ?? 'USD';
$toCurrency = $_GET['to_currency'] ?? 'DOP';
$convertedAmount = null;
$exchangeRate = null;
$error = null;

if (isset($_GET['amount']) && !empty($_GET['amount'])) {
    // Validar que el monto sea numérico
    if (!is_numeric($amount) || $amount < 0) {
        $error = "Por favor, ingresa un monto numérico válido y positivo.";
    } else {
        // Construir la URL de la API
        // Ejemplo: https://v6.exchangerate-api.com/v6/YOUR-API-KEY/latest/USD
        $apiUrl = "https://v6.exchangerate-api.com/v6/" . urlencode($apiKey) . "/latest/" . urlencode($fromCurrency);

        // Realizar la solicitud a la API
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = "¡Error de conexión! No se pudo conectar con la API de conversión. " . curl_error($ch);
        } else {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode >= 400) {
                $error = "Error al obtener las tasas de cambio. Código HTTP: " . $httpCode . ". Por favor, verifica tu clave API o las monedas seleccionadas.";
                $data = json_decode($response, true);
                if (isset($data['error-type'])) {
                    $error .= " Mensaje: " . htmlspecialchars(str_replace('_', ' ', $data['error-type']));
                }
            } else {
                $data = json_decode($response, true);

                if (json_last_error() !== JSON_ERROR_NONE || !isset($data['conversion_rates'])) {
                    $error = "Error al decodificar los datos de la conversión o formato inesperado.";
                } elseif ($data['result'] !== 'success') {
                    $error = "La API de conversión reportó un error: " . htmlspecialchars($data['error-type'] ?? 'Desconocido');
                } elseif (!isset($data['conversion_rates'][$toCurrency])) {
                    $error = "No se encontró la tasa de conversión para la moneda de destino seleccionada (" . htmlspecialchars($toCurrency) . ").";
                } else {
                    $exchangeRate = $data['conversion_rates'][$toCurrency];
                    $convertedAmount = $amount * $exchangeRate;
                }
            }
        }
        curl_close($ch);
    }
} elseif (isset($_GET['amount']) && empty($_GET['amount'])) {
    $error = "Por favor, ingresa un monto a convertir.";
}
?>

<div class="row justify-content-center my-5">
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark text-center">
                <h2 class="mb-0">Conversor de Monedas 💸</h2>
            </div>
            <div class="card-body">
                <p class="text-muted text-center">Convierte un monto entre diferentes monedas.</p>

                <form action="" method="GET" class="mb-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="amount" class="form-label">Monto</label>
                            <input type="number" step="0.01" class="form-control" id="amount" name="amount" placeholder="Ej: 100" required value="<?php echo htmlspecialchars($amount); ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="from_currency" class="form-label">De Moneda</label>
                            <select class="form-select" id="from_currency" name="from_currency">
                                <?php foreach ($commonCurrencies as $code => $name) : ?>
                                    <option value="<?php echo $code; ?>" <?php echo ($fromCurrency === $code) ? 'selected' : ''; ?>>
                                        <?php echo $code; ?> - <?php echo $name; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="to_currency" class="form-label">A Moneda</label>
                            <select class="form-select" id="to_currency" name="to_currency">
                                <?php foreach ($commonCurrencies as $code => $name) : ?>
                                    <option value="<?php echo $code; ?>" <?php echo ($toCurrency === $code) ? 'selected' : ''; ?>>
                                        <?php echo $code; ?> - <?php echo $name; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 text-center mt-4">
                            <button type="submit" class="btn btn-outline-warning btn-lg">Convertir</button>
                        </div>
                    </div>
                </form>

                <?php if ($error) : ?>
                    <div class="alert alert-danger text-center mt-4" role="alert">
                        <?php echo $error; ?>
                    </div>
                <?php elseif ($convertedAmount !== null) : ?>
                    <div class="alert alert-success text-center mt-4">
                        <h4>Resultado de la Conversión</h4>
                        <p class="fs-4">
                            **<?php echo number_format($amount, 2); ?> <?php echo htmlspecialchars($fromCurrency); ?>** equivale a
                            <br>
                            **<?php echo number_format($convertedAmount, 2); ?> <?php echo htmlspecialchars($toCurrency); ?>**
                        </p>
                        <p class="mb-0 text-muted small">
                            Tasa actual: 1 <?php echo htmlspecialchars($fromCurrency); ?> = <?php echo number_format($exchangeRate, 4); ?> <?php echo htmlspecialchars($toCurrency); ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer text-center">
                <a href="index.php" class="btn btn-outline-secondary btn-sm">Volver al Inicio</a>
            </div>
        </div>
    </div>

    <?php include 'inc/footer.php'; ?>