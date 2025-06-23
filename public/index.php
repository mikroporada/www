<?php

use App\App;
use DI\ContainerBuilder;
use DI\Definition\Source\DefinitionSource;
use DI\Definition\Source\ArrayDefinitionSource;
use DI\Definition\Definition;
use Psr\Container\ContainerInterface;

require __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Create container builder
$containerBuilder = new ContainerBuilder();

// Define dependencies
$containerBuilder->addDefinitions([
    App\Services\LLMService::class => function () {
        return new App\Services\LLMService(
            getenv('OLLAMA_MODEL') ?: 'llama2'
        );
    },
    App\Controllers\ChatController::class => function (ContainerInterface $container) {
        return new App\Controllers\ChatController(
            $container->get(App\Services\LLMService::class)
        );
    }
]);

// Build container
$container = $containerBuilder->build();

// Create app instance
$app = new App($container);

// Add routes
$app->get('/', function () {
    return 'Welcome to mikroporada.pl API';
});

$app->post('/api/chat', function (Psr\Http\Message\ServerRequestInterface $request, 
                                Psr\Http\Message\ResponseInterface $response) use ($app) {
    return $app->getContainer()->get(App\Controllers\ChatController::class)
        ->sendMessage($request, $response);
});

$app->post('/api/generate-pdf', function (Psr\Http\Message\ServerRequestInterface $request, 
                                        Psr\Http\Message\ResponseInterface $response) use ($app) {
    return $app->getContainer()->get(App\Controllers\ChatController::class)
        ->generatePdf($request, $response);
});

$app->get('/api/health', function (Psr\Http\Message\ServerRequestInterface $request, 
                                 Psr\Http\Message\ResponseInterface $response) use ($app) {
    return $app->getContainer()->get(App\Controllers\ChatController::class)
        ->healthCheck($request, $response);
});

// Run app
$app->run();
