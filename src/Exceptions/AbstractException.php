<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Exception;
use MyParcelCom\JsonApi\Exceptions\Interfaces\ExceptionInterface;
use MyParcelCom\JsonApi\Exceptions\Interfaces\JsonSchemaErrorInterface;
use Throwable;

abstract class AbstractException extends Exception implements ExceptionInterface
{
    protected ?string $id = null;

    protected array $links = [];

    protected ?string $errorCode = null;

    protected ?string $title = null;

    protected array $source = [];

    protected array $meta = [];

    public function __construct(
        protected string $detail,
        array $errorType,
        protected int $status,
        Throwable $previous = null,
    ) {
        $this->setErrorCode($errorType['code']);
        $this->setTitle($errorType['title']);

        parent::__construct($detail, (int) $this->getErrorCode(), $previous);
    }

    /**
     * Get the id for this occurrence of the exception.
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Set the id for this occurrence of the exception.
     */
    public function setId(string $id): JsonSchemaErrorInterface
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the links related to the exception.
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * Add a link to the existing links array.
     */
    public function addLink(string $name, string $url): JsonSchemaErrorInterface
    {
        $this->links[$name] = $url;

        return $this;
    }

    /**
     * Set the links related to the exception.
     * Should contain an about link that leads to further details about this particular occurrence of the problem.
     */
    public function setLinks(array $links): JsonSchemaErrorInterface
    {
        $this->links = $links;

        return $this;
    }

    /**
     * Return the http status for the request.
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Set the http status code for the request.
     */
    public function setStatus(int $status): JsonSchemaErrorInterface
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the application specific error code.
     */
    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    /**
     * Set the application specific error code.
     * This should be retrieved from one of the defined constants.
     */
    public function setErrorCode(string $errorCode): JsonSchemaErrorInterface
    {
        $this->errorCode = $errorCode;

        return $this;
    }

    /**
     * Get the description linked to the code.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set the description linked to the code.
     * This should be retrieved from one of the defined constants.
     */
    public function setTitle(string $title): JsonSchemaErrorInterface
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the detailed message for the exception.
     */
    public function getDetail(): string
    {
        return $this->detail;
    }

    /**
     * Set the detailed message for the exception.
     */
    public function setDetail(string $detail): JsonSchemaErrorInterface
    {
        $this->detail = $detail;

        return $this;
    }

    /**
     * Get an array containing references to the source of the exception.
     */
    public function getSource(): array
    {
        return $this->source;
    }

    /**
     * Set an array containing references to the source of the exception.
     */
    public function setSource(array $source): JsonSchemaErrorInterface
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get meta object containing non-standard meta-information about the exception.
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    /**
     * Set meta object containing non-standard meta-information about the exception.
     */
    public function setMeta(array $meta): JsonSchemaErrorInterface
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * Add meta values to the existing meta object.
     */
    public function addMeta(string $key, mixed $value): JsonSchemaErrorInterface
    {
        $this->meta[$key] = $value;

        return $this;
    }
}
