<?php

function e($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function csrfToken(): string
{
    if (
        empty($_SESSION['agpr_csrf']) ||
        !is_string($_SESSION['agpr_csrf'])
    ) {
        $_SESSION['agpr_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['agpr_csrf'];
}

function csrfValido($token): bool
{
    return is_string($token)
        && isset($_SESSION['agpr_csrf'])
        && is_string($_SESSION['agpr_csrf'])
        && hash_equals($_SESSION['agpr_csrf'], $token);
}

function campoCsrf(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrfToken()) . '">';
}

function flash(string $mensaje, string $tipo = 'ok'): void
{
    $_SESSION['agpr_flash'] = [
        'mensaje' => $mensaje,
        'tipo' => $tipo,
    ];
}

function tomarFlash(): ?array
{
    if (empty($_SESSION['agpr_flash']) || !is_array($_SESSION['agpr_flash'])) {
        return null;
    }

    $flash = $_SESSION['agpr_flash'];
    unset($_SESSION['agpr_flash']);

    return $flash;
}

function agprEnCarpeta(string $carpeta): bool
{
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');

    return strpos($script, '/' . trim($carpeta, '/') . '/') !== false;
}

function urlLogin(): string
{
    return agprEnCarpeta('admin') ? '../login.php' : 'login.php';
}

function urlAdmin(string $ruta = 'index.php'): string
{
    $ruta = ltrim($ruta, '/');

    return agprEnCarpeta('admin') ? $ruta : 'admin/' . $ruta;
}
