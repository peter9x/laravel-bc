<?php

namespace Mupy\BusinessCentral\EndPoint;

use Mupy\BusinessCentral\QueryFilter\QueryFilterEnum;

abstract class ApiEndPoint
{
    protected string $APIGroup = '';

    protected string $APIPublisher = '';

    protected string $APIVersion = 'v2.0';

    protected ?string $EntitySetName = null;

    protected ?string $StaticPath = null;

    public static $select = [];

    /** @var array */
    private $_filters = [];

    /** @var array */
    private $_select = [];

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
        $this->_filters[] = $string_filters;
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
            $this->_select = $select;
        }
    }

    public function getQuery(): array
    {
        $query = [];

        if (! empty($this->_select)) {
            $query[] = '$select='.implode(',', $this->_select);
        }

        if (! empty($this->_filters)) {
            $query[] = '$filter='.implode(',', $this->_filters);
        }

        return $query;
    }

    public function __toString()
    {
        $query = $this->getQuery();

        return count($query) > 0 ? '?'.implode('&', $query) : '';
    }

    public function setStaticPath($path)
    {
        $this->StaticPath = $path;
    }

    public static function static($path)
    {
        $endpoint = new ApiEndPoint;
        $endpoint->setStaticPath($path);

        return $endpoint;
    }

    public static function filter(string $key, mixed $value, QueryFilterEnum $operator): string
    {
        if (is_string($value)) {
            return "{$key} {$operator->value} '{$value}'";
        } else {
            return "{$key} {$operator->value} {$value}";
        }
    }
}
