<?php
function flash($key, $msg = null) {
    if ($msg === null) {
        if (!empty($_SESSION['flash'][$key])) {
            $m = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $m;
        }
        return null;
    } else {
        $_SESSION['flash'][$key] = $msg;
    }
}

function h($str) { return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8'); }
?>