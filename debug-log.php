<?php
$logContent = file_get_contents('storage/logs/laravel.log');
$parts = explode('[2026-', $logContent);
$lastPart = end($parts);

$lines = explode("\n", $lastPart);
$first20Lines = array_slice($lines, 0, 25);
echo "[2026-" . implode("\n", $first20Lines) . "\n";
