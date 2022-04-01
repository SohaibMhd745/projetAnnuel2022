<?php
function formatResponse(int $statusCode, array $headers, $body): string{
    header("Content-Type: application/json");

    foreach ($headers as $headerName => $headerValue) {
        header("$headerName: $headerValue");
    }

    http_response_code($statusCode);

    return json_encode($body);
}