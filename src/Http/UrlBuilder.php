<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Http;

class UrlBuilder
{
    /** @var string */
    protected $url;

    /** @var string */
    protected $scheme;

    /** @var string */
    protected $host;

    /** @var int */
    protected $port;

    /** @var string */
    protected $user;

    /** @var string */
    protected $password;

    /** @var string */
    protected $path;

    /** @var array */
    protected $query = [];

    /** @var string */
    protected $fragment;

    /**
     * @param string $url
     */
    public function __construct(string $url = null)
    {
        if ($url !== null) {
            $this->setUrl($url);
        }
    }

    /**
     * Set the base url.
     *
     * @param string $url
     * @return $this
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;
        $parts = parse_url($url);

        $this->scheme = $parts['scheme'] ?? null;
        $this->host = $parts['host'] ?? null;
        $this->port = $parts['port'] ?? null;
        $this->user = $parts['user'] ?? null;
        $this->password = $parts['pass'] ?? null;
        $this->path = $parts['path'] ?? null;
        $query = $parts['query'] ?? null;
        $this->fragment = $parts['fragment'] ?? null;

        if ($query) {
            parse_str($query, $this->query);
        }

        return $this;
    }

    /**
     * Get the compiled url.
     *
     * @return string
     */
    public function getUrl(): string
    {
        $url = '';
        if ($this->scheme) {
            $url .= $this->scheme . '://';
        }

        if ($this->user && $this->password) {
            $url .= $this->user . ':' . $this->password . '@';
        } elseif ($this->user) {
            $url .= $this->user . '@';
        }

        if ($this->host) {
            $url .= $this->host;
        }
        if ($this->port) {
            $url .= ':' . $this->port;
        }
        if ($this->path) {
            $url .= $this->path;
        }
        if ($this->query) {
            $url .= '?' . urldecode(http_build_query($this->query));
        }
        if ($this->fragment) {
            $url .= '#' . $this->fragment;
        }

        return $url;
    }

    /**
     * Get the GET query params.
     *
     * @return array
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * Set the GET query params.
     *
     * @param array $query
     * @return $this
     */
    public function setQuery(array $query): self
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Add GET query params.
     *
     * @param array $query
     * @return $this
     */
    public function addQuery(array $query): self
    {
        $this->query = array_merge($this->query, $query);

        return $this;
    }

    /**
     * Get the scheme of the url.
     *
     * @return string|null
     */
    public function getScheme(): ?string
    {
        return $this->scheme;
    }

    /**
     * Set the scheme of the url.
     *
     * @param string $scheme
     * @return $this
     */
    public function setScheme(string $scheme): self
    {
        $this->scheme = $scheme;

        return $this;
    }

    /**
     * Get the host of the url.
     *
     * @return string|null
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * Set the host of the url.
     *
     * @param string $host
     * @return $this
     */
    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Get the url port.
     *
     * @return int|null
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * Set the url port
     *
     * @param int $port
     * @return $this
     */
    public function setPort(int $port): self
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Get the HTTP auth user.
     *
     * @return string|null
     */
    public function getUser(): ?string
    {
        return $this->user;
    }

    /**
     * Set the HTTP auth user.
     *
     * @param string $user
     * @return $this
     */
    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the HTTP auth password.
     *
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Set the HTTP auth password.
     *
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the path in the url.
     *
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * Set the url path.
     *
     * @param string $path
     * @return $this
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get the string after `#` in the url.
     *
     * @return string|null
     */
    public function getFragment(): ?string
    {
        return $this->fragment;
    }

    /**
     * Set the string after `#` in the url.
     *
     * @param string $fragment
     * @return $this
     */
    public function setFragment(string $fragment): self
    {
        $this->fragment = $fragment;

        return $this;
    }

    /**
     * Get the compiled url string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getUrl();
    }
}
