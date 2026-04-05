<?php
$course = "IT";
$course = preg_replace('/[^A-Z0-9]/', '', strtoupper($course));
$aliases = [
    'IT' => 'BSIT'
];
echo $course . " => " . ($aliases[$course] ?? 'none') . "\n";
