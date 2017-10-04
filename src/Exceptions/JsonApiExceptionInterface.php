<?php declare(strict_types=1);

namespace MyParcelCom\Exceptions;

interface JsonApiExceptionInterface
{
    // General errors *salute* 10000 - 10999
    const NOT_FOUND = [
        'code'  => '10000',
        'title' => 'Not Found',
    ];

    const INTERNAL_SERVER_ERROR = [
        'code'  => '10001',
        'title' => 'Internal Server Error',
    ];

    const RESOURCE_NOT_FOUND = [
        'code'  => '10002',
        'title' => 'Resource Not Found',
    ];

    const INVALID_JSON_SCHEMA = [
        'code'  => '10003',
        'title' => 'Invalid JSON Schema',
    ];

    const INVALID_REQUEST_HEADER = [
        'code'  => '10004',
        'title' => 'Invalid Request Header',
    ];

    const RESOURCE_CANNOT_BE_MODIFIED = [
        'code'  => '10005',
        'title' => 'Resource Cannot Be Modified',
    ];

    // External API related errors 13000 - 13999
    const EXTERNAL_REQUEST_ERROR = [
        'code'  => '13001',
        'title' => 'External Request Error',
    ];

    const CARRIER_API_ERROR = [
        'code'  => '13002',
        'title' => 'Carrier API Error',
    ];

    // Auth related errors 14000 - 14999
    const AUTH_INVALID_CLIENT = [
        'code'  => '14000',
        'title' => 'Invalid OAuth Client',
    ];

    const AUTH_INVALID_SCOPE = [
        'code'  => '14001',
        'title' => 'Scope Not Available To Client',
    ];

    const AUTH_INVALID_TOKEN = [
        'code'  => '14002',
        'title' => 'Access token is invalid',
    ];

    const AUTH_SERVER_EXCEPTION = [
        'code'  => '14050',
        'title' => 'Unable To Process OAuth Request',
    ];

    /**
     * Get the id for this occurrence of the exception.
     *
     * @return string
     */
    public function getId(): ?string;

    /**
     * Set the id for this occurrence of the exception.
     *
     * @param  string $id
     * @return JsonApiExceptionInterface
     */
    public function setId(string $id): self;

    /**
     * Get the links related to the exception.
     *
     * @return array
     */
    public function getLinks(): ?array;

    /**
     * Set the links related to the exception.
     *
     * @param  array $links Should contain an about link that leads to further details about this particular occurrence of the problem.
     * @return JsonApiExceptionInterface
     */
    public function setLinks(array $links): self;

    /**
     * Return the http status for the request.
     *
     * @return string
     */
    public function getStatus(): ?string;

    /**
     * Set the http status code for the request.
     *
     * @param  string $status
     * @return JsonApiExceptionInterface
     */
    public function setStatus(string $status): self;

    /**
     * Get the application specific error code.
     *
     * @return string
     */
    public function getErrorCode(): ?string;

    /**
     * Set the application specific error code.
     * This should be retrieved from one of the defined constants.
     *
     * @param  string $errorCode
     * @return JsonApiExceptionInterface
     */
    public function setErrorCode(string $errorCode): self;

    /**
     * Get the description linked to the code.
     *
     * @return string
     */
    public function getTitle(): ?string;

    /**
     * Set the description linked to the code.
     * This should be retrieved from one of the defined constants.
     *
     * @param  string $title
     * @return JsonApiExceptionInterface
     */
    public function setTitle(string $title): self;

    /**
     * Get the detailed message for the error.
     *
     * @return string
     */
    public function getDetail(): ?string;

    /**
     * Set the detailed message for the server.
     *
     * @param  string $detail
     * @return JsonApiExceptionInterface
     */
    public function setDetail(string $detail): self;

    /**
     * Get an array containing references to the source of the error.
     *
     * @return array
     */
    public function getSource(): ?array;

    /**
     * Set an array containing references to the source of the error.
     *
     * @param  array $source
     * @return JsonApiExceptionInterface
     */
    public function setSource(array $source): self;

    /**
     * Get meta object containing non-standard meta-information about the error.
     *
     * @return array
     */
    public function getMeta(): ?array;

    /**
     * Set meta object containing non-standard meta-information about the error.
     *
     * @param  array $meta
     * @return JsonApiExceptionInterface
     */
    public function setMeta(array $meta): self;
}
