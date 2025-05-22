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
        #map { width: calc(100% - 280px); height: 100vh; position: absolute; right: 0; }
        
        /* Info Card Styles */
        #info-card {
            position: absolute;
            left: 0;
            top: 0;
            width: 280px;
            height: 100%;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1;
            padding: 20px;
            box-sizing: border-box;
            overflow-y: auto;
            /* Always visible */
        }
        
        #info-card h3 {
            margin-top: 0;
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        
        .info-item {
            margin-bottom: 15px;
        }
        
        .info-item label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
            color: #666;
        }
        
        .info-item p {
            margin: 0;
            color: #333;
            font-size: 16px;
        }
        
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: #666;
        }
        
        .close-btn:hover {
            color: #333;
        }
        
        /* Info message styling */
        .info-message {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            text-align: center;
            color: #6c757d;
            font-style: italic;
        }
        
        #save-modal, #edit-modal {
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
            width: 400px;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .button-group {
            margin-top: 20px;
        }
        .button-group button {
            padding: 8px 16px;
            margin: 0 5px;
            cursor: pointer;
            border-radius: 4px;
            border: none;
        }
        .button-group button[type="submit"] {
            background-color: #4CAF50;
            color: white;
        }
        .button-group button[type="button"] {
            background-color: #f44336;
            color: white;
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
        
        /* Edit button styles */
        .action-buttons {
            margin-top: 20px;
            display: flex;
            justify-content: flex-start;
        }
        
        .edit-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            display: none; /* Hide by default */
        }
        
        .edit-btn:hover {
            background-color: #0069d9;
        }
        
        /* View mode and edit mode container */
        .view-mode, .edit-mode {
            margin-top: 10px;
        }
        
        .edit-mode {
            display: none; /* Hidden by default */
        }
        
        .edit-form input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        
        .edit-actions {
            display: flex;
            justify-content: space-between;
        }
        
        .save-edit-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .cancel-edit-btn {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<button id="focus-btn" style="position: absolute; top: 20px; left: 300px; z-index: 10; padding: 8px 12px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
    Reset View
</button>

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

    <!-- Property Info Card -->
    <div id="info-card">
    <div style="text-align: center; margin-bottom: 20px;">
        <img src="/images/Zlp5zJm7x6g6Ml9T_image-removebg-preview.png" alt="Logo" style="max-width: 80px; height: auto; display: block; margin: 0 auto;">
        <h2 style="font-size: 20px; margin: 10px 0 0; color: #333;">Holy Ghost Extension Map</h2>
    </div>
    
    <h3>Area Information</h3>
        
        <!-- View mode section -->
        <div class="view-mode">
            <div class="info-item">
                <label>Title:</label>
                <p id="info-house-number">-</p>
            </div>
            <div class="info-item">
                <label>Description:</label>
                <p id="info-residents">-</p>
            </div>
            
            <div class="action-buttons">
                <button id="edit-property-btn" class="edit-btn">Edit Property</button>
            </div>
        </div>
        
        <!-- Edit mode section -->
        <div class="edit-mode">
            <form id="edit-property-form" class="edit-form">
                <input type="hidden" id="edit-polygon-id">
                <div class="form-group">
                    <label for="edit-house-number">Title:</label>
                    <input type="text" id="edit-house-number" name="house_number" required>
                </div>
                <div class="form-group">
                    <label for="edit-residents">Description:</label>
                    <input type="text" id="edit-residents" name="residents" required>
                </div>
                <div class="edit-actions">
                    <button type="button" id="save-edit-btn" class="save-edit-btn">Save</button>
                    <button type="button" id="cancel-edit-btn" class="cancel-edit-btn">Cancel</button>
                </div>
                <div class="form-group">
    <label for="edit-color">Color:</label>
    <input type="color" id="edit-color" name="color" value="#0080ff">
</div>

            </form>
        </div>
        
        <div class="info-message" id="info-message">
            Click on an area to view details
        </div>
    </div>

    <div id="map"></div>

    <!-- Modal for saving polygon information -->
    <div id="save-modal">
        <div class="modal-content">
            <h3>Area Information</h3>
            <form id="polygon-form" action="/save-polygon" method="POST">
                @csrf
                <input type="hidden" id="polygon-coordinates" name="coordinates">
                
                <div class="form-group">
                    <label for="house-number">Title:</label>
                    <input type="text" id="house-number" name="house_number" required>
                </div>
                
                <div class="form-group">
                    <label for="residents">Description:</label>
                    <input type="text" id="residents" name="residents" required>
                </div>
                
                <div class="button-group">
                    <button type="submit">Save</button>
                    <button type="button" id="cancel-btn">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Hidden container to store the polygon data from the database -->
    <div id="saved-polygons" style="display: none;" data-polygons="{{ json_encode($polygons) }}"></div>

    <!-- Mapbox Scripts -->
    <script src="https://api.mapbox.com/mapbox-gl-js/v3.11.0/mapbox-gl.js"></script>
    <script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.5.0/mapbox-gl-draw.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
    mapboxgl.accessToken = '{{ config('services.mapbox.token') }}';

    const savedCenter = JSON.parse(localStorage.getItem('mapCenter'));
const savedZoom = localStorage.getItem('mapZoom');

const map = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/iver123/cm775xc8p00rq01re7n1t9gy7',
    center: savedCenter || [120.60487594896269, 16.41863965440813],
    zoom: savedZoom ? parseFloat(savedZoom) : 16
});
const defaultCenter = [120.60487594896269, 16.41863965440813];
const defaultZoom = 16;

document.getElementById('focus-btn').addEventListener('click', () => {
    localStorage.removeItem('mapCenter');
    localStorage.removeItem('mapZoom');

    map.flyTo({
        center: defaultCenter,
        zoom: defaultZoom,
        essential: true
    });
});


    map.on('moveend', function () {
    const center = map.getCenter();
    const zoom = map.getZoom();

    localStorage.setItem('mapCenter', JSON.stringify([center.lng, center.lat]));
    localStorage.setItem('mapZoom', zoom);
});

    // Store polygon IDs to track which features correspond to which database records
    const polygonIdMap = {};
    let currentPolygonId = null;

    // Info card functionality
    const infoCard = document.getElementById('info-card');
    const infoHouseNumber = document.getElementById('info-house-number');
    const infoResidents = document.getElementById('info-residents');
    const editPropertyBtn = document.getElementById('edit-property-btn');
    const viewModeSection = document.querySelector('.view-mode');
    const editModeSection = document.querySelector('.edit-mode');
    const editPolygonIdField = document.getElementById('edit-polygon-id');
    const editHouseNumberField = document.getElementById('edit-house-number');
    const editResidentsField = document.getElementById('edit-residents');
    const saveEditBtn = document.getElementById('save-edit-btn');
    const cancelEditBtn = document.getElementById('cancel-edit-btn');
    const mapElement = document.getElementById('map');
    const editColorField = document.getElementById('edit-color');
    
    // Function to display property info in the card
    function showPropertyInfo(properties, polygonId) {
        infoHouseNumber.textContent = properties.house_number || '-';
        infoResidents.textContent = properties.residents || '-';
        
        // Store the current polygon ID
        currentPolygonId = polygonId;
        
        // Show the edit button
        editPropertyBtn.style.display = 'block';
        
        // Hide the initial message
        document.getElementById('info-message').style.display = 'none';
    }

    // Toggle to edit mode
    editPropertyBtn.addEventListener('click', function() {
        // Populate the edit form with current values
        editPolygonIdField.value = currentPolygonId;
        editHouseNumberField.value = infoHouseNumber.textContent !== '-' ? infoHouseNumber.textContent : '';
        editResidentsField.value = infoResidents.textContent !== '-' ? infoResidents.textContent : '';
        editColorField.value = map.getPaintProperty(`polygon-layer-${currentPolygonId}`, 'fill-color') || '#0080ff';

        // Switch to edit mode
        viewModeSection.style.display = 'none';
        editModeSection.style.display = 'block';
    });

    // Cancel edit
    cancelEditBtn.addEventListener('click', function() {
        // Switch back to view mode
        editModeSection.style.display = 'none';
        viewModeSection.style.display = 'block';
    });

    // Save edit
    saveEditBtn.addEventListener('click', function() {
        const polygonId = editPolygonIdField.value;
        const houseNumber = editHouseNumberField.value;
        const residents = editResidentsField.value;
        const color = editColorField.value;

        // Validate inputs
        if (!houseNumber || !residents) {
            showNotification('Title and Description are required', 'error');
            return;
        }
        
        // Send AJAX request to update the polygon information
        $.ajax({
            url: `/polygons/${polygonId}`,
            type: 'PUT',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                house_number: houseNumber,
                residents: residents,
                color: color
            },
            success: function(response) {
                map.setPaintProperty(`polygon-layer-${polygonId}`, 'fill-color', color);

                if (response.success) {
                    // Update the info card
                    infoHouseNumber.textContent = response.data.house_number;
                    infoResidents.textContent = response.data.residents;
                    
                    // Update the map properties for this polygon
                    if (map.getSource(`polygon-source-${polygonId}`)) {
                        const source = map.getSource(`polygon-source-${polygonId}`);
                        const data = source._data;
                        data.properties.house_number = response.data.house_number;
                        data.properties.residents = response.data.residents;
                        
                        // Update the source data
                        map.getSource(`polygon-source-${polygonId}`).setData(data);
                    }
                    
                    // Show success notification
                    showNotification('Property information updated successfully', 'success');
                    
                    // Switch back to view mode
                    editModeSection.style.display = 'none';
                    viewModeSection.style.display = 'block';
                } else {
                    showNotification('Failed to update property information', 'error');
                }
            },
            error: function(error) {
                console.error('Error updating property information:', error);
                showNotification('Failed to update property information', 'error');
            }
        });
    });

    const draw = new MapboxDraw({
        displayControlsDefault: false,
        controls: {
            polygon: true,
            trash: true
        }
    });

    map.addControl(draw);

    // Show the modal with coordinates
    map.on('draw.create', (event) => {
        const polygon = event.features[0];
        const coordinates = polygon.geometry.coordinates[0];
        
        // Store coordinates in the hidden field
        document.getElementById('polygon-coordinates').value = JSON.stringify(coordinates);
        
        // Clear any previous values
        document.getElementById('house-number').value = '';
        document.getElementById('residents').value = '';
        
        // Show the modal
        document.getElementById('save-modal').style.display = 'flex';
    });
    map.on('draw.update', (event) => {
    const feature = event.features[0];
    const featureId = feature.id;
    const databaseId = polygonIdMap[featureId];

    if (databaseId) {
        const updatedCoordinates = feature.geometry.coordinates[0];

        $.ajax({
            url: `/polygons/${databaseId}/coordinates`,
            type: 'PUT',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                coordinates: JSON.stringify(updatedCoordinates)
            },
            success: function(response) {
                showNotification('Polygon coordinates updated successfully', 'success');

                // 🔁 Update the source for custom layer
                const sourceId = `polygon-source-${databaseId}`;
                if (map.getSource(sourceId)) {
                    const updatedSourceData = {
    type: 'Feature',
    properties: {
        // Use the latest data from the original source, or manually set it
        house_number: map.getSource(`polygon-source-${databaseId}`)._data.properties.house_number,
        residents: map.getSource(`polygon-source-${databaseId}`)._data.properties.residents
    },
    geometry: {
        type: 'Polygon',
        coordinates: feature.geometry.coordinates
    }
};

// Update source data
map.getSource(`polygon-source-${databaseId}`).setData(updatedSourceData);

                }
            },
            error: function(error) {
                console.error('Error updating polygon coordinates:', error);
                showNotification('Failed to update polygon coordinates', 'error');
            }
        });
    }
});



    // Handle polygon deletion
    map.on('draw.delete', (event) => {
        const features = event.features;
        
        features.forEach(feature => {
            const featureId = feature.id;
            const databaseId = polygonIdMap[featureId];
            
            if (databaseId) {
                // Send delete request to the server
                deletePolygon(databaseId);
                
                // Also remove any associated map layers
                if (map.getLayer(`polygon-layer-${databaseId}`)) {
    map.removeLayer(`polygon-layer-${databaseId}`);
}
if (map.getLayer(`polygon-label-${databaseId}`)) {
    map.removeLayer(`polygon-label-${databaseId}`);
}
if (map.getSource(`polygon-source-${databaseId}`)) {
    map.removeSource(`polygon-source-${databaseId}`);
}
                // Clean up the ID mapping
                delete polygonIdMap[featureId];
                
                // Reset info card content and hide edit button
                infoHouseNumber.textContent = '-';
                infoResidents.textContent = '-';
                editPropertyBtn.style.display = 'none';
                document.getElementById('info-message').style.display = 'block';
                
                // Reset current polygon ID
                currentPolygonId = null;
            }
        });
    });

    // Function to delete polygon from the database
    function deletePolygon(id) {
        $.ajax({
            url: `/polygons/${id}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Show success notification
                showNotification('Polygon deleted successfully', 'success');
            },
            error: function(error) {
                console.error('Error deleting polygon:', error);
                showNotification('Failed to delete polygon', 'error');
            }
        });
    }

    // Function to show notifications
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        // Auto-remove after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Hide modal on cancel
    document.getElementById('cancel-btn').addEventListener('click', () => {
        document.getElementById('save-modal').style.display = 'none';
        
        // Remove the drawn polygon if canceling
        const featureIds = draw.getSelectedIds();
        if (featureIds.length > 0) {
            draw.delete(featureIds);
        }
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

    // Load saved polygons from database when the map loads
    map.on('load', function() {
        // Get polygon data from the hidden container
        const polygonsContainer = document.getElementById('saved-polygons');
        const polygons = JSON.parse(polygonsContainer.getAttribute('data-polygons'));
        
        // Add each polygon to the map
        polygons.forEach(polygon => {
            try {
                const coordinates = JSON.parse(polygon.coordinates);
                
                // Create a feature collection for each polygon
                const feature = {
                    id: `polygon-${polygon.id}`,
                    type: 'Feature',
                    properties: {
                        house_number: polygon.house_number,
                        residents: polygon.residents
                    },
                    geometry: {
                        type: 'Polygon',
                        coordinates: [coordinates] // Wrap coordinates in an array for polygon format
                    }
                };
                
                // Add the feature to the draw plugin
                const addedFeature = draw.add(feature);
                
                // Store the mapping between MapboxDraw feature ID and database ID
                if (addedFeature && addedFeature.length > 0) {
                    polygonIdMap[addedFeature[0]] = polygon.id;
                }
                
                // Add the polygon as a map layer for better styling if needed
                if (!map.getSource(`polygon-source-${polygon.id}`)) {
                    map.addSource(`polygon-source-${polygon.id}`, {
                        'type': 'geojson',
                        'data': {
                            'type': 'Feature',
                            'properties': {
                                house_number: polygon.house_number,
                                residents: polygon.residents
                            },
                            'geometry': {
                                'type': 'Polygon',
                                'coordinates': [coordinates]
                            }
                        }
                    });
                    
                    map.addLayer({
                        'id': `polygon-layer-${polygon.id}`,
                        'type': 'fill',
                        'source': `polygon-source-${polygon.id}`,
                        'layout': {},
                        'paint': {
                            'fill-color': polygon.color || '#0080ff',
                            'fill-opacity': 0.5,
                            'fill-outline-color': '#000000'
                            

                        }
                    });
                    map.addLayer({
    'id': `polygon-label-${polygon.id}`,
    'type': 'symbol',
    'source': `polygon-source-${polygon.id}`,
    'layout': {
        'text-field': ['get', 'house_number'],
        'text-size': 14,
        'text-anchor': 'center'
    },
    'paint': {
        'text-color': '#000000',
        'text-halo-color': '#ffffff',
        'text-halo-width': 1
    }
});

                    
                    // For polygon click, show info in the card instead of a popup
                    map.on('click', `polygon-layer-${polygon.id}`, (e) => {
                        const properties = e.features[0].properties;
                        
                        // Display property info in the card and pass the polygon ID
                        showPropertyInfo(properties, polygon.id);
                    });
                    
                    // Change cursor to pointer when hovering over a polygon
                    map.on('mouseenter', `polygon-layer-${polygon.id}`, () => {
                        map.getCanvas().style.cursor = 'pointer';
                    });
                    
                    map.on('mouseleave', `polygon-layer-${polygon.id}`, () => {
                        map.getCanvas().style.cursor = '';
                    });
                }
            } catch (e) {
                console.error(`Failed to parse polygon coordinates: ${e}`);
            }
        });
    });
    </script>
</body>
</html>