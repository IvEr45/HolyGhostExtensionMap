<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8" />
    <title>Mapbox Draw Polygon on Click</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Mapbox CSS -->
    <link href="https://api.mapbox.com/mapbox-gl-js/v3.11.0/mapbox-gl.css" rel="stylesheet">
    <link href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.5.0/mapbox-gl-draw.css" rel="stylesheet">

    <style>
        body { margin: 0; padding: 0; }
        #map { width: 100vw; height: 100vh; }
        #draw-btn {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 999;
            padding: 10px 15px;
            background: white;
            border: 1px solid #ccc;
            cursor: pointer;
        }
        #save-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
        }
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px;
            border-radius: 5px;
            color: white;
            z-index: 1000;
        }
        .success {
            background-color: #4CAF50;
        }
        .error {
            background-color: #f44336;
        }
    </style>
</head>
<body>
    <!-- Show flash messages -->
    @if(session('success'))
    <div class="notification success">
        {{ session('success') }}
    </div>
    @endif
    
    @if(session('error'))
    <div class="notification error">
        {{ session('error') }}
    </div>
    @endif

    <button id="draw-btn">Draw Polygon</button>
    <div id="map"></div>

    <!-- Modal for saving coordinates -->
    <div id="save-modal">
        <div class="modal-content">
            <h3>Save Polygon Coordinates</h3>
            <form id="polygon-form" action="/save-polygon" method="POST">
                @csrf
                <textarea id="polygon-coordinates" name="coordinates" rows="5" cols="40" readonly></textarea><br><br>
                <button type="submit">Save</button>
                <button type="button" id="cancel-btn">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Mapbox Scripts -->
    <script src="https://api.mapbox.com/mapbox-gl-js/v3.11.0/mapbox-gl.js"></script>
    <script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.5.0/mapbox-gl-draw.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
    mapboxgl.accessToken = '{{ config('services.mapbox.token') }}';

    const map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/iver123/cm775xc8p00rq01re7n1t9gy7',
        center: [120.60487594896269, 16.41863965440813],
        zoom: 16
    });

    const draw = new MapboxDraw({
        displayControlsDefault: false,
        controls: {
            polygon: true,
            trash: true
        }
    });

    map.addControl(draw);

    document.getElementById('draw-btn').addEventListener('click', () => {
        draw.changeMode('draw_polygon');
    });

    // Show the modal with coordinates
    map.on('draw.create', (event) => {
        const polygon = event.features[0];
        const coordinates = polygon.geometry.coordinates[0];
        document.getElementById('polygon-coordinates').value = JSON.stringify(coordinates);
        document.getElementById('save-modal').style.display = 'flex';
    });

    // Hide modal on cancel
    document.getElementById('cancel-btn').addEventListener('click', () => {
        document.getElementById('save-modal').style.display = 'none';
    });

    // Hide notifications after 3 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const notifications = document.querySelectorAll('.notification');
        notifications.forEach(notification => {
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        });
    });
    </script>
</body>
</html>