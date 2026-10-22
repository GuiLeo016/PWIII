<?php
require 'sessao.php';
require_once 'config/google_maps.php';

// Dados de uma cidade
$cidade = "São José dos Campos";
$latitude = -23.21984958134659;
$longitude = -45.90675969218883;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa - CRUD Mundo</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="main">

        <?php include 'header.php'; ?>

        <main id="container">

            <h1>Mapa</h1>
            <p>Localização geográfica da cidade no Google Maps.</p>

            <section class="mapa-container">

                <h2>Localização: <?= htmlspecialchars($cidade) ?></h2>

                <div class="info-grid">
                    <div class="info-item">
                        <strong>Cidade</strong>
                        <span><?= htmlspecialchars($cidade) ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Latitude</strong>
                        <span><?= number_format($latitude, 4, ',', '.') ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Longitude</strong>
                        <span><?= number_format($longitude, 4, ',', '.') ?></span>
                    </div>
                </div>

                <div id="mapa" role="region" aria-label="Mapa de <?= htmlspecialchars($cidade) ?>"></div>

            </section>

        </main>

    </div>

    <script>
        function iniciarMapa() {
            const localizacao = {
                lat: <?= json_encode($latitude) ?>,
                lng: <?= json_encode($longitude) ?>
            };
            const mapa = new google.maps.Map(
                document.getElementById("mapa"), {
                    zoom: 12,
                    center: localizacao
                }
            );
            new google.maps.Marker({
                position: localizacao,
                map: mapa,
                title: <?= json_encode($cidade) ?>
            });
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?= urlencode($googleMapsKey) ?>&callback=iniciarMapa&loading=async" async></script>
</body>
</html>
