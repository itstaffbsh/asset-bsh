<?php
// Redirect langsung ke folder public
$script = $_SERVER['SCRIPT_NAME']; // /BSH FIX/index.php
$base = rtrim(dirname($script), '/\\'); // /BSH FIX
header('Location: ' . $base . '/public/');
exit;
