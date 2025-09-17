<?php

namespace Mupy\BusinessCentral\QueryFilter;

enum QueryFilterEnum: string
{
    case EQUAL = 'eq';
    case NOT_EQUAL = 'ne';
    case GREATER_THAN = 'gt';
    case GREATER_OR_EQUAL = 'gte';
    case LESS_THAN = 'lt';
    case LESS_OR_EQUAL = 'lte';
}
