<?php

namespace Mupy\BusinessCentral\EndPoint;

class Company extends ApiEndPoint
{
    protected string $APIGroup = '';

    protected string $APIPublisher = '';

    protected string $APIVersion = 'v2.0';

    protected string $EntitySetName = 'companies';
}
