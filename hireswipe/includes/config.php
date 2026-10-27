<?php
/*
 * Google Maps key for the job location picker on the Post Job form.
 *
 * 1. In Google Cloud, enable "Maps JavaScript API" and "Geocoding API" and turn on billing.
 * 2. Create an API key and restrict it to your site (HTTP referrers, for example http://localhost:8000/*).
 * 3. Paste it below, or set the GOOGLE_MAPS_API_KEY environment variable.
 *
 * Leave it empty and the form falls back to a plain text field with an "Open Google Maps" link.
 */
define('GOOGLE_MAPS_API_KEY', getenv('GOOGLE_MAPS_API_KEY') ?: '');
