<?php
$context = stream_context_create([
    'http' => [
        'ignore_errors' => true
    ]
]);
$html = file_get_contents('http://127.0.0.1:8000/saldo-awal/create', false, $context);
echo "HTTP Headers:\n";
print_r($http_response_header);
echo "\nBody snippet:\n";
echo substr($html, 0, 1000) . "\n";
