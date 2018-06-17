<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

//Cors Support
$app->add(function ($req, $res, $next) {
    //$this->logger->info('app->add');
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

$app->add(new \Slim\Middleware\JwtAuthentication([
    "secure" => false, //To allow non https calls.
    "secret" => "4037227320180606",
    "rules" => [
            new \Slim\Middleware\JwtAuthentication\RequestPathRule([
                "path" => "/",
                "passthrough" => ['/tasks', '/file'] //exclude by path
            ]),
            new \Slim\Middleware\JwtAuthentication\RequestMethodRule([
                "passthrough" => ["OPTIONS"] //Don't use tokens for options calls CORS
            ])
        ]    
]));