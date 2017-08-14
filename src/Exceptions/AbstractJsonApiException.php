<?php declare(strict_types=1);

namespace MyParcelCom\Exceptions;

use Exception;

abstract class AbstractJsonApiException extends Exception implements JsonApiExceptionInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var array
     */
    protected $links;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var string
     */
    protected $errorCode;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $detail;

    /**
     * @var array
     */
    protected $source;

    /**
     * @var array
     */
    protected $meta;

    /**
     * AbstractJsonApiException constructor.
     *
     * @param string         $detail
     * @param array          $errorType
     * @param int            $status
     * @param Throwable|null $previous
     */
    public function __construct(string $detail, array $errorType, int $status, Throwable $previous = null)
    {
        $this->setDetail($detail);
        $this->setErrorCode($errorType['code']);
        $this->setTitle($errorType['title']);
        $this->setStatus((string)$status);

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
     * @param  string $id
     * @return JsonApiExceptionInterface
     */
    public function setId(string $id): JsonApiExceptionInterface
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
     * Set the links related to the exception.
     *
     * @param  array $links Should contain an about link that leads to further details about this particular occurrence of the problem.
     * @return JsonApiExceptionInterface
     */
    public function setLinks(array $links): JsonApiExceptionInterface
    {
        $this->links = $links;

        return $this;
    }

    /**
     * Return the http status for the request.
     *
     * @return string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Set the http status code for the request.
     *
     * @param  string $status
     * @return JsonApiExceptionInterface
     */
    public function setStatus(string $status): JsonApiExceptionInterface
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
     * @param  string $errorCode
     * @return JsonApiExceptionInterface
     */
    public function setErrorCode(string $errorCode): JsonApiExceptionInterface
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
     * @param  string $title
     * @return JsonApiExceptionInterface
     */
    public function setTitle(string $title): JsonApiExceptionInterface
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the detailed message for the error.
     *
     * @return string
     */
    public function getDetail(): ?string
    {
        return $this->detail;
    }

    /**
     * Set the detailed message for the server.
     *
     * @param  string $detail
     * @return JsonApiExceptionInterface
     */
    public function setDetail(string $detail): JsonApiExceptionInterface
    {
        $this->detail = $detail;

        return $this;
    }

    /**
     * Get an array containing references to the source of the error.
     *
     * @return array
     */
    public function getSource(): ?array
    {
        return $this->source;
    }

    /**
     * Set an array containing references to the source of the error.
     *
     * @param  array $source
     * @return JsonApiExceptionInterface
     */
    public function setSource(array $source): JsonApiExceptionInterface
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get meta object containing non-standard meta-information about the error.
     *
     * @return array
     */
    public function getMeta(): ?array
    {
        return $this->meta;
    }

    /**
     * Set meta object containing non-standard meta-information about the error.
     *
     * @param  array $meta
     * @return JsonApiExceptionInterface
     */
    public function setMeta(array $meta): JsonApiExceptionInterface
    {
        $this->meta = $meta;

        return $this;
    }
}
