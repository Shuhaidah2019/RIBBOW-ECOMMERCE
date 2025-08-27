<?php
// db_backup.php

// Database credentials
$host = "localhost";
$user = "root";
$pass = ""; // your MySQL password
$db   = "ribbowsite_db";

// Where to save backup
$backupDir = __DIR__ . "/../backups/";
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}

// File name like backup_2025-08-18.sql
$filename = $backupDir . "backup_" . date("Y-m-d") . ".sql";

// Command for mysqldump
$command = "mysqldump -h $host -u $user " . ($pass ? "-p$pass " : "") . "$db > $filename";

// Run command
system($command, $retval);

if ($retval === 0) {
    echo "✅ Backup successful: $filename";
} else {
    echo "❌ Backup failed!";
}
