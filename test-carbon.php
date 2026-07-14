<?php
require 'vendor/autoload.php';
$c = \Illuminate\Support\Carbon::now();
$res = \Carbon\Carbon::parse($c);
echo "Type: " . gettype($res) . "\n";
echo "Class: " . (is_object($res) ? get_class($res) : 'not an object') . "\n";
