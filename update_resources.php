<?php
$files = glob(__DIR__ . '/app/Filament/Admin/Resources/*/*Resource.php');
$exclude = ['JurnalUmumResource.php', 'LaporanKonsinyasiResource.php', 'UserResource.php'];
foreach ($files as $file) {
    if (in_array(basename($file), $exclude)) continue;
    $content = file_get_contents($file);
    if (strpos($content, 'function canAccess') === false) {
        $content = preg_replace(
            '/public static function form\(/',
            "public static function canAccess(): bool\n    {\n        return auth()->user()?->isAdmin() ?? false;\n    }\n\n    public static function form(",
            $content
        );
        file_put_contents($file, $content);
        echo 'Updated: ' . $file . "\n";
    }
}
