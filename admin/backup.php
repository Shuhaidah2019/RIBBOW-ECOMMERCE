<?php
date_default_timezone_set("Africa/Lagos");

// Database credentials
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "ribbowsite_db";

// Backup folder (directly in Ribbow/backups)
$backupDir = __DIR__ . "/../backups/";

// Create the folder if it doesn't exist
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}

// Create filename with timestamp
$filename = $backupDir . "backup_" . date("Y-m-d_H-i-s") . ".sql";

// Absolute path to mysqldump.exe
$mysqldump = "C:/xampp/mysql/bin/mysqldump.exe";

// Run mysqldump command
$command = "\"$mysqldump\" --user=$user --password=$pass --host=$host $dbname > \"$filename\"";
system($command, $output);

// Auto-clean: keep only the latest 4 backups
$files = glob($backupDir . "*.sql");
usort($files, function ($a, $b) {
    return filemtime($b) - filemtime($a);
});
if (count($files) > 4) {
    for ($i = 4; $i < count($files); $i++) {
        unlink($files[$i]);
    }
}

echo "✅ Backup created: " . basename($filename);
