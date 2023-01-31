<?php

if (!function_exists('compile_response')) {
    function compile_response($message, $status_code, $data = null)
    {
        $response = compact('message', 'status_code');
        if ($data) {
            $response += compact('data');
        }

        return $response;
    }
}
