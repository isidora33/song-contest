<?php

function resp_json($data, $status = 200)
{
    ob_start();
    ob_clean();

    http_response_code($status);
    if (gettype($data) == 'string') {
        $data = ['message' => $data];
    }
    echo json_encode($data);
    ob_flush();
}
