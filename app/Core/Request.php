<?php

declare(strict_types=1);

namespace App\Core;

final class Request
{
    private array $requestUri;
    private $terminator;
    private $inputReader;

    /**
     * @param callable|null $headerSender Receives a raw header string to send (injectable for tests)
     * @param callable|null $terminator   Ends the request after a redirect (injectable for tests)
     */
    public function __construct(
        ?callable $headerSender = null,
        ?callable $terminator = null,
        ?callable $inputReader = null
    )
    {
        $this->headerSender = $headerSender ?? 'header';
        $this->terminator = $terminator ?? function () {
            exit; // @codeCoverageIgnore
        };
        $this->inputReader = $inputReader ?? function () {
            return file_get_contents('php://input'); // @codeCoverageIgnore
        };

        // Get request URI
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';

        // Remove illegal characters
        $requestUri = filter_var($requestUri, FILTER_SANITIZE_URL);

        // Parse the URI
        // If the request URI cannot be parsed, output is false - default to empty array
        $requestArray = parse_url($requestUri) ?: [];

        // Stop now if we cannot parse the URL
        // example: http://api///xxx
        if (!array_key_exists('path', $requestArray)) {
            // Redirect to general error page
            ($this->headerSender)("Location: /error");

            // Terminate
            ($this->terminator)();
        }

        // Update the query value to an array
        if (array_key_exists('query', $requestArray)) {
            parse_str($requestArray['query'],$requestArray['query']);
        }

        // Check for final / character and remove it
        // Don't do this for the homepage
        if ( array_key_exists('path', $requestArray) && $requestArray['path'] != '/' && substr($requestArray['path'], -1) === '/') {
            $requestArray['path'] = rtrim($requestArray['path'], '/');
        }

        // Update property
        $this->requestUri = $requestArray;
    }

    public function getPath(): string
    {
        return $this->requestUri['path'];
    }

    public function getQuery(): ?array
    {
        return $this->requestUri['query'] ?? null;
    }

    public function getContentType(): ?string
    {
        return $_SERVER['CONTENT_TYPE'] ?? null;
    }

    public function getMethod(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function isGet(): bool
    {
        return $this->getMethod() === 'get';
    }

    public function isPost(): bool
    {
        return $this->getMethod() === 'post';
    }

    public function getBody($filterSpecialChars = true): ?array
    {
        $body = [];

        // Get request
        if ($this->getMethod() === 'get') {
            foreach ($_GET as $key => $value) {
                if ($filterSpecialChars) {
                    $body[$key] = trim(filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS));
                } else {
                    $body[$key] = trim(filter_var($value));
                }
            }
        }

        // JSON post request
        elseif ($this->getMethod() === 'post' && $this->getContentType() == 'application/json') {
            $json = ($this->inputReader)();

            $body = json_decode($json, true);
        }

        // Form post request
        elseif ($this->getMethod() === 'post') {
            foreach ($_POST as $key => $value) {
                // Check for array first - used for checkbox arrays
                if (is_array($value)) {
                    $body[$key] = $value;
                }
                elseif ($filterSpecialChars) {
                    $body[$key] = trim(filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS));
                } else {
                    $body[$key] = trim(filter_var($value));
                }
            }
        }

        return $body;
    }

    public function getHeaders(): array
    {
        return getallheaders();
    }

    public function getCookies(): array
    {
        return array_change_key_case($_COOKIE, CASE_LOWER);
    }
}
