<?php

namespace Mupy\BusinessCentral;

use Psr\Http\Message\ResponseInterface;
use RuntimeException;

/**
 * Represents the response received from an API request,
 * containing the HTTP status code and decoded response body.
 */
class ApiResponse
{
    /**
     * @var int The HTTP status code returned by the API.
     */
    private int $statusCode;

    /**
     * @var array The content of the API response as an associative array.
     */
    private array $body;

    /**
     * @var string The OData context string, if present in the response.
     */
    private string $context = '';

    /**
     * ApiResponse constructor.
     *
     * @param ResponseInterface $response The HTTP response object.
     *
     * @throws RuntimeException If the response body is not valid JSON.
     */
    public function __construct(ResponseInterface $response)
    {
        $this->statusCode = $response->getStatusCode();

        $this->body = json_decode((string)$response->getBody(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException(
                "Invalid JSON response: " . json_last_error_msg()
            );
        }

        // Safely set context if present
        if (isset($this->body['@odata.context'])) {
            $this->context = (string)$this->body['@odata.context'];
        }
    }

    /**
     * Get the HTTP status code of the response.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get the decoded response body as an associative array.
     *
     * @return array
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * Get the main data array from the response body (from the 'value' key).
     *
     * @return array The array of items from the response or empty array if not present.
     */
    public function data(): array
    {
        return $this->body['value'] ?? [];
    }

    /**
     * Get the OData context string from the response, if available.
     *
     * @return string
     */
    public function getContext(): string
    {
        return $this->context;
    }

    /**
     * Check if the response indicates a successful request.
     *
     * @return bool True if the status code is 200, false otherwise.
     */
    public function success(): bool
    {
        return $this->statusCode === 200;
    }
}
