<?php

namespace App\Controllers;

use App\Services\LLMService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;
use Slim\Exception\HttpBadRequestException;

class ChatController
{
    private LLMService $llmService;

    public function __construct(LLMService $llmService)
    {
        $this->llmService = $llmService;
    }

    /**
     * Handle chat message
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function sendMessage(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $data = $request->getParsedBody();
            
            if (!isset($data['message']) || !isset($data['session_id'])) {
                throw new HttpBadRequestException($request, 'Missing required parameters');
            }

            $responseText = $this->llmService->chat($data['message']);
            
            $response->getBody()->write(json_encode([
                'response' => $responseText,
                'session_id' => $data['session_id']
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        } catch (\Exception $e) {
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500)
                ->getBody()
                ->write(json_encode([
                    'error' => [
                        'code' => 'INTERNAL_ERROR',
                        'message' => $e->getMessage()
                    ]
                ]));
        }
    }

    /**
     * Generate PDF from HTML
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function generatePdf(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $data = $request->getParsedBody();
            
            if (!isset($data['html_content']) || !isset($data['session_id'])) {
                throw new HttpBadRequestException($request, 'Missing required parameters');
            }

            $pdfPath = $this->llmService->generatePdf($data['html_content'], $data['session_id']);
            
            $response->getBody()->write(json_encode([
                'pdf_path' => $pdfPath
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        } catch (\Exception $e) {
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500)
                ->getBody()
                ->write(json_encode([
                    'error' => [
                        'code' => 'GENERATION_ERROR',
                        'message' => $e->getMessage()
                    ]
                ]));
        }
    }

    /**
     * Check service health
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function healthCheck(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $health = $this->llmService->healthCheck();
            
            $response->getBody()->write(json_encode($health));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        } catch (\Exception $e) {
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500)
                ->getBody()
                ->write(json_encode([
                    'error' => [
                        'code' => 'SERVICE_UNAVAILABLE',
                        'message' => $e->getMessage()
                    ]
                ]));
        }
    }
}
