<?php

namespace MyParcelCom\JsonApi\Exceptions\Interfaces;

interface JsonSchemaErrorInterface
{
    /**
     * Get the id for this occurrence of the exception.
     *
     * @return string
     */
    public function getId(): ?string;

    /**
     * Set the id for this occurrence of the exception.
     *
     * @param string $id
     * @return $this
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
     * @param array $links Should contain an about link that leads to further details about this particular occurrence of the problem.
     * @return $this
     */
    public function setLinks(array $links): self;

    /**
     * Return the http status for the request.
     *
     * @return int
     */
    public function getStatus(): ?int;

    /**
     * Set the http status code for the request.
     *
     * @param int $status
     * @return $this
     */
    public function setStatus(int $status): self;

    /**
     * Get the application specific error code.
     *
     * @return string|null
     */
    public function getErrorCode(): ?string;

    /**
     * Set the application specific error code.
     * This should be retrieved from one of the defined constants.
     *
     * @param string $errorCode
     * @return $this
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
     * @param string $title
     * @return $this
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
     * @param string $detail
     * @return $this
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
     * @param array $source
     * @return $this
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
     * @param array $meta
     * @return $this
     */
    public function setMeta(array $meta): self;

}
