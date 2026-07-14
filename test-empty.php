<?php
require 'vendor/autoload.php';
try {
    $res = \Carbon\Carbon::parse("");
    echo "Type of empty string parse: " . gettype($res) . "\n";
} catch (\Throwable $e) {
    echo "Empty string parse error: " . $e->getMessage() . "\n";
}
try {
    $res = \Carbon\Carbon::parse(null);
    echo "Type of null parse: " . gettype($res) . "\n";
} catch (\Throwable $e) {
    echo "Null parse error: " . $e->getMessage() . "\n";
}
