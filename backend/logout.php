<?php

session_start();
session_unset();
session_destroy();
header("Location: /RIBBOW/public/auth.html");
exit();
