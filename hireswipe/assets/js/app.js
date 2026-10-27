// Shows a preview of the chosen image before the form is submitted.
document.querySelectorAll('input[type="file"][data-preview]').forEach(function (input) {
    var preview = document.getElementById(input.dataset.preview);
    if (!preview) { return; }
    input.addEventListener('change', function () {
        var file = input.files && input.files[0];
        if (file && file.type.indexOf('image/') === 0) {
            preview.src = URL.createObjectURL(file);
        }
    });
});

// Keeps the "Open Google Maps" link in step with what is typed in the location field.
document.querySelectorAll('a[data-maps-input]').forEach(function (link) {
    var input = document.getElementById(link.dataset.mapsInput);
    if (!input) { return; }
    input.addEventListener('input', function () {
        var place = input.value.trim();
        link.href = place
            ? 'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(place)
            : 'https://www.google.com/maps';
    });
});
