<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Exception;
use MyParcelCom\JsonApi\Exceptions\Interfaces\ExceptionInterface;
use MyParcelCom\JsonApi\Exceptions\Interfaces\JsonSchemaErrorInterface;

abstract class AbstractException extends Exception implements ExceptionInterface
{
    /** @var string */
    protected $id;

    /** @var array */
    protected $links;

    /** @var int */
    protected $status;

    /** @var string */
    protected $errorCode;

    /** @var string */
    protected $title;

    /** @var string */
    protected $detail;

    /** @var array */
    protected $source;

    /** @var array */
    protected $meta;

    /**
     * @param string          $detail
     * @param array           $errorType
     * @param int             $status
     * @param \Throwable|null $previous
     */
    public function __construct(string $detail, array $errorType, int $status, \Throwable $previous = null)
    {
        $this->setDetail($detail);
        $this->setErrorCode($errorType['code']);
        $this->setTitle($errorType['title']);
        $this->setStatus($status);

        parent::__construct($detail, (int)$this->getErrorCode(), $previous);
    }

    /**
     * Get the id for this occurrence of the exception.
     *
     * @return string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Set the id for this occurrence of the exception.
     *
     * @param string $id
     * @return $this
     */
    public function setId(string $id): JsonSchemaErrorInterface
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the links related to the exception.
     *
     * @return array
     */
    public function getLinks(): ?array
    {
        return $this->links;
    }

    /**
     * Add a link to the existing links array.
     *
     * @param string $name
     * @param mixed  $url
     * @return $this
     */
    public function addLink(string $name, $url): JsonSchemaErrorInterface
    {
        $this->links[$name] = $url;

        return $this;
    }

    /**
     * Set the links related to the exception.
     *
     * @param array $links Should contain an about link that leads to further details about this particular occurrence of the problem.
     * @return $this
     */
    public function setLinks(array $links): JsonSchemaErrorInterface
    {
        $this->links = $links;

        return $this;
    }

    /**
     * Return the http status for the request.
     *
     * @return int
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * Set the http status code for the request.
     *
     * @param int $status
     * @return $this
     */
    public function setStatus(int $status): JsonSchemaErrorInterface
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the application specific error code.
     *
     * @return string
     */
    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    /**
     * Set the application specific error code.
     * This should be retrieved from one of the defined constants.
     *
     * @param string $errorCode
     * @return $this
     */
    public function setErrorCode(string $errorCode): JsonSchemaErrorInterface
    {
        $this->errorCode = $errorCode;

        return $this;
    }

    /**
     * Get the description linked to the code.
     *
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set the description linked to the code.
     * This should be retrieved from one of the defined constants.
     *
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): JsonSchemaErrorInterface
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the detailed message for the exception.
     *
     * @return string
     */
    public function getDetail(): ?string
    {
        return $this->detail;
    }

    /**
     * Set the detailed message for the exception.
     *
     * @param string $detail
     * @return $this
     */
    public function setDetail(string $detail): JsonSchemaErrorInterface
    {
        $this->detail = $detail;

        return $this;
    }

    /**
     * Get an array containing references to the source of the exception.
     *
     * @return array
     */
    public function getSource(): ?array
    {
        return $this->source;
    }

    /**
     * Set an array containing references to the source of the exception.
     *
     * @param array $source
     * @return $this
     */
    public function setSource(array $source): JsonSchemaErrorInterface
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get meta object containing non-standard meta-information about the exception.
     *
     * @return array
     */
    public function getMeta(): ?array
    {
        return $this->meta;
    }

    /**
     * Set meta object containing non-standard meta-information about the exception.
     *
     * @param array $meta
     * @return $this
     */
    public function setMeta(array $meta): JsonSchemaErrorInterface
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * Add meta values to the existing meta object.
     *
     * @param string $key
     * @param mixed  $value
     * @return $this
     */
    public function addMeta(string $key, $value): JsonSchemaErrorInterface
    {
        $this->meta[$key] = $value;

        return $this;
    }
}
