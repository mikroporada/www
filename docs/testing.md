# Testing Guide

## Test Structure

```
tests/
├── unit/           # Unit tests
├── integration/    # Integration tests
├── api/           # API tests
└── e2e/           # End-to-end tests
```

## Unit Tests

### Location
```php
tests/unit/App/Services/LLMServiceTest.php
```

### Example
```php
<?php

declare(strict_types=1);

namespace Tests\Unit\App\Services;

use App\Services\LLMService;
use PHPUnit\Framework\TestCase;

class LLMServiceTest extends TestCase
{
    private LLMService $llmService;

    protected function setUp(): void
    {
        $this->llmService = new LLMService('test-model');
    }

    public function testChat(): void
    {
        $response = $this->llmService->chat('Hello');
        $this->assertIsString($response);
        $this->assertNotEmpty($response);
    }

    public function testGeneratePdf(): void
    {
        $html = '<h1>Hello</h1>';
        $sessionId = uniqid();
        
        $pdfPath = $this->llmService->generatePdf($html, $sessionId);
        $this->assertFileExists($pdfPath);
    }
}
```

## Integration Tests

### Location
```php
tests/integration/Controllers/ChatControllerTest.php
```

### Example
```php
<?php

declare(strict_types=1);

namespace Tests\Integration\Controllers;

use App\Controllers\ChatController;
use PHPUnit\Framework\TestCase;

class ChatControllerTest extends TestCase
{
    public function testSendMessage(): void
    {
        $controller = new ChatController();
        $response = $controller->sendMessage('Hello');
        
        $this->assertArrayHasKey('response', $response);
        $this->assertIsString($response['response']);
    }
}
```

## API Tests

### Location
```php
tests/api/Endpoints/ChatEndpointTest.php
```

### Example
```php
<?php

declare(strict_types=1);

namespace Tests\Api\Endpoints;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class ChatEndpointTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $this->client = new Client([
            'base_uri' => 'http://localhost:8000',
        ]);
    }

    public function testChatEndpoint(): void
    {
        $response = $this->client->post('/chat', [
            'json' => [
                'message' => 'Hello',
                'session_id' => uniqid()
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('response', $data);
    }
}
```

## Test Coverage

```bash
composer test --coverage
```

### Expected Coverage
- Unit tests: 90%
- Integration tests: 80%
- API tests: 70%

## Test Data

### Test Data Location
```php
tests/_data/
├── fixtures/      # Test fixtures
├── samples/      # Sample data
└── mocks/        # Mock responses
```

### Example Fixture
```php
tests/_data/fixtures/chat_message.php
```

```php
<?php

return [
    'message' => 'Hello',
    'session_id' => uniqid(),
    'expected_response' => 'Hi there!'
];
```

## Test Environment

### Environment Variables
```bash
TEST_ENV=true
TEST_DATABASE_URL=mysql://test_user:test_pass@localhost/test_db
```

### Test Database
- Separate test database
- Database migrations
- Test data seeding

## Best Practices

1. Write tests before code
2. Keep tests focused
3. Use meaningful test names
4. Mock external services
5. Test edge cases
6. Keep tests independent
7. Clean up after tests
8. Use descriptive assertions

## Running Tests

### All Tests
```bash
make test
```

### Specific Test
```bash
phpunit tests/unit/App/Services/LLMServiceTest
```

### Test Coverage
```bash
composer test --coverage
```

### Debugging Tests
```bash
phpunit --debug tests/unit/App/Services/LLMServiceTest
```
