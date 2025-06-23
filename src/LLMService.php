<?php

namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Dompdf\Dompdf;
use Dompdf\Options;

class LLMService
{
    private $ollamaModel;
    private $client;

    public function __construct($ollamaModel = 'llama2')
    {
        $this->ollamaModel = $ollamaModel;
        $this->client = new Client([
            'base_uri' => 'http://localhost:11434',
            'timeout' => 5.0,
        ]);
    }

    /**
     * Send message to LLM and get response
     * @param string $message
     * @return string
     * @throws GuzzleException
     */
    public function chat(string $message): string
    {
        $response = $this->client->post('/api/generate', [
            'json' => [
                'model' => $this->ollamaModel,
                'prompt' => $message,
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        return $data['response'] ?? '';
    }

    /**
     * Generate PDF from HTML content
     * @param string $htmlContent
     * @param string $sessionId
     * @return string Path to generated PDF
     */
    public function generatePdf(string $htmlContent, string $sessionId): string
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($htmlContent);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $outputPath = __DIR__ . "/../storage/pdfs/{$sessionId}.pdf";
        file_put_contents($outputPath, $dompdf->output());

        return $outputPath;
    }

    /**
     * Get service health status
     * @return array
     */
    public function healthCheck(): array
    {
        try {
            $response = $this->client->get('/api/tags');
            return [
                'status' => 'healthy',
                'ollama_version' => json_decode($response->getBody(), true)
            ];
        } catch (GuzzleException $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}
