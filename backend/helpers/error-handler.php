<?php
// /backend/helpers/error-handler.php

// Turn off PHP default error display in production
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/errors.log'); // log file path

// Ensure logs directory exists
if (!is_dir(__DIR__ . '/../logs')) {
    mkdir(__DIR__ . '/../logs', 0777, true);
}

// Custom error handler
function ribbowErrorHandler($errno, $errstr, $errfile, $errline) {
    $message = "[" . date('Y-m-d H:i:s') . "] Error: {$errstr} in {$errfile} on line {$errline}\n";
    error_log($message);

    // For fatal errors or production mode, show a user-friendly page
    if ($errno === E_USER_ERROR || $errno === E_ERROR) {
        http_response_code(500);
        echo "<h2>Oops! Something went wrong on our end. Please try again later.</h2>";
        exit();
    }

    return true; // Don't execute PHP internal handler
}

// Custom exception handler
function ribbowExceptionHandler($exception) {
    $message = "[" . date('Y-m-d H:i:s') . "] Uncaught Exception: {$exception->getMessage()} in {$exception->getFile()} on line {$exception->getLine()}\n";
    error_log($message);

    http_response_code(500);
    echo "<h2>Oops! Something went wrong. Please try again later.</h2>";
    exit();
}

// Register handlers
set_error_handler('ribbowErrorHandler');
set_exception_handler('ribbowExceptionHandler');

// Optional: catch shutdown fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        $message = "[" . date('Y-m-d H:i:s') . "] Fatal Error: {$error['message']} in {$error['file']} on line {$error['line']}\n";
        error_log($message);
        http_response_code(500);
        echo "<h2>Oops! A fatal error occurred. Please try again later.</h2>";
    }
});





function handleError($message = "Something went wrong!") {
    http_response_code(500);
    include __DIR__ . '/../public/error.php';
    exit();
}

function handleNotFound($message = "Page not found!") {
    http_response_code(404);
    include __DIR__ . '/../public/404.php';
    exit();
}
?>
