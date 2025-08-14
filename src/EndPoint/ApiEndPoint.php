<?php

namespace Mupy\BusinessCentral\EndPoint;

abstract class ApiEndPoint
{
    protected string $APIGroup = '';

    protected string $APIPublisher = '';

    protected string $APIVersion = 'v2.0';

    protected string $EntitySetName;

    protected string $StaticPath;

    /** @var array<string> */
    public static array $select = [];

    /** @var array<string> */
    private array $filters = [];

    public function getPath(): string
    {
        return $this->EntitySetName;
    }

    public function publisher(): string
    {
        return $this->APIPublisher;
    }

    public function group(): string
    {
        return $this->APIGroup;
    }

    public function version(): string
    {
        return $this->APIVersion;
    }

    public function addFilter(string $stringFilter): void
    {
        $this->filters[] = $stringFilter;
    }

    /**
     * @param  array<string>  $filters
     */
    public function addFilters(array $filters): void
    {
        foreach ($filters as $filter) {
            $this->addFilter($filter);
        }
    }

    /**
     * @param  array<string>  $select
     */
    public function select(array $select = []): void
    {
        if (count($select) > 0) {
            $this->addFilter('$select='.implode(',', $select));
        }
    }

    public function getQuery(): string
    {
        return count($this->filters) > 0 ? '?'.implode('&', $this->filters) : '';
    }

    public function __toString(): string
    {
        return $this->getQuery();
    }

    public function setStaticPath(string $path): void
    {
        $this->StaticPath = $path;
    }

    /**
     * Cria um endpoint estático
     */
    public static function static(string $path): self
    {
        $endpoint = new class($path) extends ApiEndPoint
        {
            public function __construct(string $path)
            {
                $this->setStaticPath($path);
            }
        };

        return $endpoint;
    }
}
