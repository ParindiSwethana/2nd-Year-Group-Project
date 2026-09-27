<?php
function csrfToken(): string
{
    if (empty($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }

    return $_SESSION["csrf_token"];
}

function verifyCsrfToken(): void
{
    $expected = $_SESSION["csrf_token"] ?? "";
    $sent = $_POST["csrf_token"] ?? "";

    if ($expected === "" || !hash_equals($expected, $sent)) {
        http_response_code(403);
        exit("Invalid request. Please go back, refresh the page and try again.");
    }
}
?>
