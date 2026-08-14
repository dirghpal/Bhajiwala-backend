<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected array $response = [
        'success' => true,
        'message' => '',
        'data' => null,
    ];
    
}