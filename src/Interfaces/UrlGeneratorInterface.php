<?php declare(strict_types=1);

namespace MyParcelCom\JsonApi\Interfaces;

interface UrlGeneratorInterface
{
    /**
     * Generates an absolute URL for given route name and optional parameters.
     *
     * @param string $name
     * @param array  $parameters
     * @return string
     */
    public function route($name, $parameters = []);
}