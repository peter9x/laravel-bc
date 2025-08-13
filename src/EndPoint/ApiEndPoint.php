<?php

namespace Mupy\BusinessCentral\EndPoint;

abstract class ApiEndPoint
{
    protected $APIGroup = '';
    protected $APIPublisher = '';
    protected $APIVersion = 'v2.0';
    protected $EntitySetName = null;
    protected $StaticPath = null;

    public static $select = [];

    /** @var array */
    private $filters = [];

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

    public function addFilter(string $string_filters)
    {
        $this->filters[] = $string_filters;
    }

    public function addFilters(array $filters)
    {
        foreach ($filters as $filter) {
            $this->addFilter($filter);
        }
    }

    public function select(array $select = [])
    {
        if (count($select) > 0) {
            $this->addFilter('$select=' . implode(',', $select));
        }
    }

    public function getQuery(): string
    {
        return count($this->filters) > 0 ? '?' . implode("&", $this->filters) : '';
    }

    public function __toString()
    {
        return $this->getQuery();
    }

    public function setStaticPath($path)
    {
        $this->StaticPath = $path;
    }

    public static function static($path)
    {
        $endpoint = new ApiEndPoint();
        $endpoint->setStaticPath($path);
        return $endpoint;
    }
}
