// Job location picker: ties a Google Map to the location field and the hidden lat/lng fields.
// Type a place and press "Find on map", click the map, or move the map so the pin sits on the spot.
(function () {
    var picker = document.getElementById('location-picker');
    if (!picker) { return; }

    var input = document.getElementById('location');
    var latField = document.getElementById('lat');
    var lngField = document.getElementById('lng');
    var status = document.getElementById('map-status');
    var findBtn = document.getElementById('find-on-map');
    var mapEl = document.getElementById('map');

    var DEFAULT_CENTER = { lat: 22.9734, lng: 78.6569 };
    var map = null;
    var geocoder = null;
    var userMoved = false;

    findBtn.disabled = true;

    function say(message) { status.textContent = message; }

    function savePoint(point) {
        latField.value = point.lat.toFixed(6);
        lngField.value = point.lng.toFixed(6);
    }

    function label(result) {
        return result.formatted_address.slice(0, 120);
    }

    // Pin to address: fill the text field from the map position.
    function reverse(point) {
        say('Looking up this spot...');
        geocoder.geocode({ location: point }, function (results, code) {
            if (code === 'OK' && results && results[0]) {
                input.value = label(results[0]);
                say('Pinned: ' + input.value);
            } else {
                say('Pin saved, but no address was found here. Type a name for this location.');
            }
        });
    }

    // Address to pin: move the map to what was typed.
    function search() {
        var query = input.value.trim();
        if (!query) { say('Type a place first.'); input.focus(); return; }
        say('Searching...');
        geocoder.geocode({ address: query }, function (results, code) {
            if (code !== 'OK' || !results || !results[0]) {
                say('No match found. Try a fuller address.');
                return;
            }
            var spot = results[0].geometry.location;
            var point = { lat: spot.lat(), lng: spot.lng() };
            map.setCenter(point);
            map.setZoom(15);
            savePoint(point);
            input.value = label(results[0]);
            say('Pinned: ' + input.value);
        });
    }

    function pickCenter() {
        var c = map.getCenter();
        var point = { lat: c.lat(), lng: c.lng() };
        savePoint(point);
        reverse(point);
    }

    window.initJobMap = function () {
        geocoder = new google.maps.Geocoder();

        var lat = parseFloat(latField.value);
        var lng = parseFloat(lngField.value);
        var hasPin = isFinite(lat) && isFinite(lng);

        map = new google.maps.Map(mapEl, {
            center: hasPin ? { lat: lat, lng: lng } : DEFAULT_CENTER,
            zoom: hasPin ? 15 : 5,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: false,
            clickableIcons: false
        });

        map.addListener('dragstart', function () { userMoved = true; });
        map.addListener('click', function (e) {
            var point = { lat: e.latLng.lat(), lng: e.latLng.lng() };
            map.panTo(point);
            savePoint(point);
            reverse(point);
        });
        map.addListener('idle', function () {
            if (!userMoved) { return; }
            userMoved = false;
            pickCenter();
        });
        mapEl.addEventListener('keydown', function (e) {
            if (['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].indexOf(e.key) !== -1) { userMoved = true; }
        });

        findBtn.disabled = false;
    };

    window.gm_authFailure = function () {
        say('Google Maps could not load. Check the API key, and that billing, the Maps JavaScript API and the Geocoding API are enabled.');
    };

    findBtn.addEventListener('click', search);

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); if (geocoder) { search(); } }
    });

    // Editing the text makes the old pin stale, so clear it until the location is found again.
    input.addEventListener('input', function () {
        latField.value = '';
        lngField.value = '';
        say('Choose Find on map to pin this location.');
    });

    var script = document.createElement('script');
    script.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(picker.dataset.key)
        + '&callback=initJobMap&loading=async&v=weekly';
    script.async = true;
    script.onerror = function () { say('Google Maps could not be loaded. Check your internet connection.'); };
    document.head.appendChild(script);
})();
