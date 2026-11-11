<?php

declare(strict_types=1);

namespace App\Core;

final class Response
{
    private int $statusCode = 200;
    private $headersSent;
    private $statusSetter;
    private $headerSender;
    private $terminator;

    /**
     * @param callable|null $headersSent  Returns whether headers have been sent (injectable for tests)
     * @param callable|null $statusSetter Receives the status code to set (injectable for tests)
     * @param callable|null $headerSender Receives a raw header string to send (injectable for tests)
     * @param callable|null $terminator   Ends the request after a redirect (injectable for tests)
     */
    public function __construct(
        ?callable $headersSent = null,
        ?callable $statusSetter = null,
        ?callable $headerSender = null,
        ?callable $terminator = null
    ) {
        $this->headersSent = $headersSent ?? 'headers_sent';
        $this->statusSetter = $statusSetter ?? 'http_response_code';
        $this->headerSender = $headerSender ?? 'header';
        $this->terminator = $terminator ?? function () {
            exit; // @codeCoverageIgnore
        };
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $code): bool
    {
        // Can only set this if headers not already sent
        if (($this->headersSent)()) {
            return false;
        }

        ($this->statusSetter)($code);

        $this->statusCode = $code;

        return true;
    }

    public function redirect(string $path)
    {
        ($this->headerSender)("Location: $path");
        ($this->terminator)();
    }
}
