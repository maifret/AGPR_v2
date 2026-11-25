<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('AGPR_V2_SESSION');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.cookie_path', '/');
    session_start();
}
