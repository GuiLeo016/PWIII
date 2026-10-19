<?php
require_once 'config/google_maps.php';
// Dados de uma cidade
$cidade = "São José dos Campos";
$latitude = -23.219847890432717; 
$longitude = -45.90678123298993;
?>
<html>

<head>
    <meta charset="UTF-8">
    <title>CRUD Mundo - Google Maps</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div id="header">
        <h1>CRUD Mundo</h1>
        <h2>Localização: <?= htmlspecialchars($cidade) ?></h2>
    </div>
    <div id="container">
        <div id="mapa"></div>
        <script>
            function iniciarMapa() {
                const localizacao = {
                    lat: <?= json_encode($latitude) ?>,
                    lng: <?= json_encode($longitude) ?>
                };
                const mapa = new google.maps.Map(
                    document.getElementById("mapa"), {
                        zoom: 18,
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
        <script src="https://maps.googleapis.com/maps/api/js?key=<?= urlencode($googleMapsKey)?>&callback=iniciarMapa&loading=async"async>
        </script>
    </div>
</body>

</html>