<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions\Interfaces;

/**
 * @see https://jsonapi.org/format/#error-objects
 */
interface JsonSchemaErrorInterface
{
    /**
     * Get the id for this occurrence of the exception.
     */
    public function getId(): ?string;

    /**
     * Set the id for this occurrence of the exception.
     */
    public function setId(string $id): self;

    /**
     * Get the links related to the exception.
     */
    public function getLinks(): array;

    /**
     * Set the links related to the exception.
     * Should contain an "about" link that leads to further details about this particular occurrence of the problem.
     */
    public function setLinks(array $links): self;

    /**
     * Return the http status for the request.
     */
    public function getStatus(): ?int;

    /**
     * Set the http status code for the request.
     */
    public function setStatus(int $status): self;

    /**
     * Get the application specific error code.
     */
    public function getErrorCode(): ?string;

    /**
     * Set the application specific error code.
     * This should be retrieved from one of the defined constants.
     */
    public function setErrorCode(string $errorCode): self;

    /**
     * Get the description linked to the code.
     */
    public function getTitle(): ?string;

    /**
     * Set the description linked to the code.
     * This should be retrieved from one of the defined constants.
     */
    public function setTitle(string $title): self;

    /**
     * Get the detailed message for the error.
     */
    public function getDetail(): ?string;

    /**
     * Set the detailed message for the server.
     */
    public function setDetail(string $detail): self;

    /**
     * Get an array containing references to the source of the error.
     */
    public function getSource(): array;

    /**
     * Set an array containing references to the source of the error.
     */
    public function setSource(array $source): self;

    /**
     * Get meta object containing non-standard meta-information about the error.
     */
    public function getMeta(): array;

    /**
     * Set meta object containing non-standard meta-information about the error.
     */
    public function setMeta(array $meta): self;
}
