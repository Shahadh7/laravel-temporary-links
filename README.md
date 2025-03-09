# Laravel Temporary Access Links

A Laravel package for generating temporary, expiring access links for users to view/download content securely.

## Features

- ✅ **Time-Limited Links** – Links expire after a set time (e.g., 24 hours)
- ✅ **One-Time Use Links** – Restrict access to a single open/download
- ✅ **IP & Device Restrictions** – Limit access to specific IPs or devices
- ✅ **Webhook Integration** – Get notified when a link is accessed

## Installation

You can install the package via composer:

```bash
composer require shahadh/laravel-temporary-links
```

Publish the migrations and config:

```bash
php artisan vendor:publish --tag="temporary-links-migrations"
php artisan vendor:publish --tag="temporary-links-config"
```

Run the migrations:

```bash
php artisan migrate
```

## Usage

### Basic Usage

```php
// Add the trait to your model
use Shahadh\TemporaryLinks\Traits\HasTemporaryLinks;

class Document extends Model
{
    use HasTemporaryLinks;
}

// Create a temporary link
$document = Document::find(1);
$link = $document->createTemporaryLink();

// Get the URL
$url = $link->getUrl();
```

### Link Options

```php
// Create a link that expires in 1 hour
$link = $document->createExpiringTemporaryLink(60);

// Create a single-use link
$link = $document->createSingleUseTemporaryLink();

// Create a link with IP restriction
$link = $document->createTemporaryLink([
    'restrict_ip' => $request->ip()
]);

// Create a link with device restriction
$link = $document->createTemporaryLink([
    'restrict_device' => true
]);

// Create a link with custom path
$link = $document->createTemporaryLinkForPath('/downloads/secret-file.pdf');
```

### Handling Access

By default, accessing a link will redirect to the model or path. You can customize this behavior by adding a `handleTemporaryAccess` method to your model:

```php
public function handleTemporaryAccess($link, $request)
{
    // Custom access logic here
    return Storage::download($this->file_path);
}
```

### Webhook Integration

Configure webhooks in the config file to receive notifications when links are accessed or expire.

## Configuration

See the `config/temporary-links.php` file for all configuration options.

## License

The MIT License (MIT). Please see License File for more information.