# Laravel Business Central

Laravel package for Microsoft Business Central API

## Installation

```bash
composer require peter9x/laravel-bc
```

```bash
php artisan vendor:publish --provider="Mupy\BusinessCentral\BusinessCentralServiceProvider" --tag=config
```

```bash
BC_CLIENT_ID=your-client-id
BC_CLIENT_SECRET=your-client-secret
BC_TENANT_ID=your-tenant-id
BC_COMPANY_ID=your-company-id
BC_ENVIRONMENT=sandbox
```

```php
use Mupy\BusinessCentral\Facades\BusinessCentral;

$customers = BusinessCentral::getCustomers();
´´´
```
