<?php

declare(strict_types=1);

namespace Unit\Core;

use App\Core\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function test_Constructor_without_arguments()
    {
        $response = new Response();

        $headersSent = getProperty($response, 'headersSent');
        $statusSetter = getProperty($response, 'statusSetter');
        $headerSender = getProperty($response, 'headerSender');
        $terminator = getProperty($response, 'terminator');

        $this->assertSame('headers_sent', $headersSent);
        $this->assertSame('http_response_code', $statusSetter);
        $this->assertSame('header', $headerSender);
        $this->assertInstanceOf(\Closure::class, $terminator);
    }

    public function test_Constructor_with_arguments()
    {
        $headersSentCallable = function () { return 1; };
        $statusSetterCallable = function () { return 2; };
        $headerSenderCallable = function () { return 3; };
        $terminatorCallable = function () { return 4; };

        $response = new Response($headersSentCallable, $statusSetterCallable, $headerSenderCallable, $terminatorCallable);

        $headersSentProperty = getProperty($response, 'headersSent');
        $statusSetterProperty = getProperty($response, 'statusSetter');
        $headerSenderProperty = getProperty($response, 'headerSender');
        $terminatorProperty = getProperty($response, 'terminator');

        $this->assertSame($headersSentProperty(), $headersSentCallable());
        $this->assertSame($statusSetterProperty(), $statusSetterCallable());
        $this->assertSame($headerSenderProperty(), $headerSenderCallable());
        $this->assertSame($terminatorProperty(), $terminatorCallable());
    }

    public function test_getStatusCode()
    {
        $response = new Response();

        $result = $response->getStatusCode();

        $this->assertSame(200, $result);
    }

    public function test_setStatusCode_headers_already_sent()
    {
        $headersSentCallable = function () { return true; };

        $response = new Response($headersSentCallable);

        $result = $response->setStatusCode(404);

        $statusCode = $response->getStatusCode();

        $this->assertFalse($result);
        $this->assertSame(200, $statusCode);
    }

    public function test_setStatusCode_headers_not_sent()
    {
        $calls = 0;
        $headersSentCallable = function () { return false; };
        $statusSetterCallable = function () use (&$calls) { $calls++; };

        $response = new Response($headersSentCallable, $statusSetterCallable);

        $result = $response->setStatusCode(404);

        $statusCode = $response->getStatusCode();

        $this->assertTrue($result);
        $this->assertSame(404, $statusCode);
        $this->assertEquals(1, $calls);
    }

    public function test_redirect()
    {
        $calls = 0;

        $headersSentCallable = function () { return true; };
        $statusSetterCallable = function () { return true; };
        $headerSenderCallable = function () use (&$calls) { $calls++; };
        $terminatorCallable = function () use (&$calls) { $calls++; };

        $response = new Response($headersSentCallable, $statusSetterCallable, $headerSenderCallable, $terminatorCallable);

        $response->redirect('/home');

        $this->assertEquals(2, $calls);
    }
}
