<!DOCTYPE html>
<html>

<head>
    <title>Map</title>
    <script src="https://unpkg.com/maplibre-gl/dist/maplibre-gl.js"></script>
    <link href="https://unpkg.com/maplibre-gl/dist/maplibre-gl.css" rel="stylesheet" />
</head>

<body>
    <div id="map" style="width: 100%; height: 500px"></div>
    <script>
        const map = new maplibregl.Map({
            style: 'https://tiles.openfreemap.org/styles/liberty',
            center: [-42.1403695, -19.7899442],
            zoom: 14,
            container: 'map',
        })
    </script>
</body>

</html>