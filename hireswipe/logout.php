<?php
require __DIR__ . '/includes/helpers.php';
$_SESSION = [];
session_destroy();
go('index.php');
