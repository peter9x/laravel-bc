# Laravel Business Central

A Laravel package for integrating with the **Microsoft Business Central API**.

## Installation

1. Install the package via Composer:

```bash
composer require peter9x/laravel-bc
```

2. Publish the configuration file:

```bash
php artisan vendor:publish --provider="Mupy\\BusinessCentral\\BusinessCentralServiceProvider" --tag=config
```

3. Add the following to your `.env` file:

```env
BC_CLIENT_ID=your-client-id
BC_CLIENT_SECRET=your-client-secret
BC_TENANT_ID=your-tenant-id
BC_COMPANY_ID=your-company-id
BC_ENVIRONMENT=sandbox
```

## Usage

```php
use Mupy\BusinessCentral\Facades\BusinessCentral;
use Mupy\BusinessCentral\EndPoint\Company;
use Mupy\BusinessCentral\EndPoint\SalesInvoices;

$api = BusinessCentral::getClient();

// Change the environment dynamically if needed
$api->selectEnv('sandbox');

try {
    $result = $api->get(Company::class);

    if ($result->success()) {
        foreach ($result->data() as $entry) {
            $company = $api->useCompany($entry['id']);
            $company->get(SalesInvoices::class);
        }
    }
} catch (\Throwable $th) {
    // Handle exceptions as needed
}
```
