<?php

declare(strict_types=1);

namespace Unit\Core;

use App\Core\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    private $uri = '/test';

    public function test_Constructor_request_URI_is_set()
    {
        $_SERVER['REQUEST_URI'] = $this->uri;

        $request = new Request();

        $requestUriProperty = getProperty($request, 'requestUri');

        $this->assertInternalType('array', $requestUriProperty);
        $this->assertArrayHasKey('path', $requestUriProperty);
        $this->assertEquals('/test', $requestUriProperty['path']);
    }

    public function test_Constructor_cannot_parse_URL()
    {
        $this->uri = 'http:///test';

        $_SERVER['REQUEST_URI'] = $this->uri;

        $calls = 0;

        $headerSenderCallable = function () use (&$calls) { $calls++; };
        $terminatorCallable = function () use (&$calls) { $calls++; };

        $request = new Request($headerSenderCallable, $terminatorCallable);

        $this->assertSame(2, $calls);
    }

    public function test_Constructor_add_query_to_request_array()
    {
        $this->uri = 'http:/test?var1=123&var2=abc';

        $_SERVER['REQUEST_URI'] = $this->uri;

        $request = new Request();

        $requestUriProperty = getProperty($request, 'requestUri');

        $this->assertArrayHasKey('query', $requestUriProperty);
        $this->assertInternalType('array', $requestUriProperty['query']);
        $this->assertEquals(['var1' => '123', 'var2' => 'abc'], $requestUriProperty['query']);
    }

    public function test_Constructor_trim_final_slash_from_path()
    {
        $this->uri = '/test/';

        $_SERVER['REQUEST_URI'] = $this->uri;

        $request = new Request();

        $requestUriProperty = getProperty($request, 'requestUri');

        $this->assertArrayHasKey('path', $requestUriProperty);
        $this->assertEquals('/test', $requestUriProperty['path']);
    }

    public function test_getPath()
    {
        $_SERVER['REQUEST_URI'] = $this->uri;

        $request = new Request();
        $result = $request->getPath();

        $this->assertEquals($this->uri, $result);
    }

    public function test_getQuery_query_is_present()
    {
        $this->uri = '/test?var=abc';

        $_SERVER['REQUEST_URI'] = $this->uri;

        $request = new Request();
        $result = $request->getQuery();

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('var', $result);
        $this->assertEquals(['var' => 'abc'], $result);
    }

    public function test_getQuery_query_is_not_present()
    {
        $_SERVER['REQUEST_URI'] = $this->uri;

        $request = new Request();
        $result = $request->getQuery();

        $this->assertNull($result);
    }

    public function test_getContentType_content_type_is_present()
    {
        $_SERVER['REQUEST_URI'] = $this->uri;
        $_SERVER['CONTENT_TYPE'] = 'application/json';

        $request = new Request();
        $result = $request->getContentType();

        $this->assertEquals('application/json', $result);
    }

    public function test_getContentType_content_type_is_not_present()
    {
        $_SERVER['REQUEST_URI'] = $this->uri;

        $request = new Request();
        $result = $request->getContentType();

        $this->assertNull($result);
    }

    public function test_getMethod()
    {
        $requestMethod = 'POST';

        $_SERVER['REQUEST_METHOD'] = $requestMethod;

        $request = new Request();
        $result = $request->getMethod();

        $this->assertEquals(strtolower($requestMethod), $result);
    }

    public function test_isGet_is_get()
    {
        $requestMethod = 'GET';

        $_SERVER['REQUEST_METHOD'] = $requestMethod;

        $request = new Request();
        $result = $request->isGet();

        $this->assertTrue($result);
    }

    public function test_isGet_is_not_get()
    {
        $requestMethod = 'POST';

        $_SERVER['REQUEST_METHOD'] = $requestMethod;

        $request = new Request();
        $result = $request->isGet();

        $this->assertFalse($result);
    }

    public function test_isPost_is_post()
    {
        $requestMethod = 'POST';

        $_SERVER['REQUEST_METHOD'] = $requestMethod;

        $request = new Request();
        $result = $request->isPost();

        $this->assertTrue($result);
    }

    public function test_isPost_is_not_post()
    {
        $requestMethod = 'GET';

        $_SERVER['REQUEST_METHOD'] = $requestMethod;

        $request = new Request();
        $result = $request->isPost();

        $this->assertFalse($result);
    }

    public function test_getBody_get_request_filtering_special_characters()
    {
        $var1 = '<abc>';
        $var2 = '&123"';

        $_SERVER = [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => $this->uri,
        ];

        $_GET = [
            'var1' => $var1,
            'var2' => $var2,
        ];

        $request = new Request();
        $result = $request->getBody();

        $this->assertSame(filter_var($var1, FILTER_SANITIZE_SPECIAL_CHARS), $result['var1']);
        $this->assertSame(filter_var($var2, FILTER_SANITIZE_SPECIAL_CHARS), $result['var2']);
    }

    public function test_getBody_get_request_not_filtering_special_characters()
    {
        $var1 = '<abc>';
        $var2 = '&123"';

        $_SERVER = [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => $this->uri,
        ];

        $_GET = [
            'var1' => $var1,
            'var2' => $var2,
        ];

        $request = new Request();
        $result = $request->getBody(false);

        $this->assertSame($var1, $result['var1']);
        $this->assertSame($var2, $result['var2']);
    }

    public function test_getBody_JSON_post_request()
    {
        $_SERVER = [
            'REQUEST_URI' => $this->uri,
            'REQUEST_METHOD' => 'POST',
            'CONTENT_TYPE' => 'application/json',
        ];

        $postArray = [
            'var1' => 'abc',
            'var2' => '123',
        ];

        $inputReaderCallable = function () use ($postArray) {
            return json_encode($postArray);
        };

        $request = new Request(null, null, $inputReaderCallable);
        $result = $request->getBody(false);

        $this->assertSame($result, $postArray);
    }

    public function test_getBody_form_post_request_filtering_special_characters()
    {
        $var1 = '<abc>';
        $var2 = '&123"';
        $var3 = [
            'key1' => 'value1',
        ];

        $_SERVER = [
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => $this->uri,
        ];

        $_POST = [
            'var1' => $var1,
            'var2' => $var2,
            'var3' => $var3,
        ];

        $expected = [
            'var1' => filter_var($var1, FILTER_SANITIZE_SPECIAL_CHARS),
            'var2' => filter_var($var2, FILTER_SANITIZE_SPECIAL_CHARS),
            'var3' => $var3,
        ];

        $request = new Request();
        $result = $request->getBody();

        $this->assertSame($result, $expected);
    }

    public function test_getBody_form_post_request_not_filtering_special_characters()
    {
        $var1 = '<abc>';
        $var2 = '&123"';
        $var3 = [
            'key1' => 'value1',
        ];

        $_SERVER = [
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => $this->uri,
        ];

        $_POST = [
            'var1' => $var1,
            'var2' => $var2,
            'var3' => $var3,
        ];

        $request = new Request();
        $result = $request->getBody(false);

        $this->assertSame($result, $_POST);
    }

    public function test_getHeaders()
    {
        $host = 'www.example.com';
        $userAgent = 'Firefox';

        $_SERVER['LANG'] = "GREEK";

        $_SERVER['HTTP_HOST'] = $host;
        $_SERVER['HTTP_USER_AGENT'] = $userAgent;

        $request = new Request();
        $result = $request->getHeaders();

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('Host', $result);
        $this->assertArrayHasKey('User-Agent', $result);
        $this->assertEquals($host, $result['Host']);
        $this->assertEquals($userAgent, $result['User-Agent']);
    }
}
