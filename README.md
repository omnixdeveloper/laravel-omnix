<h2 align="center">
    Ethereum Package for Laravel
</h2>

<p align="center">
    <a href="https://packagist.org/packages/omnixdeveloper/laravel-omnix"><img src="https://poser.pugx.org/omnixdeveloper/laravel-omnix/v/stable?format=flat-square" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/omnixdeveloper/laravel-omnix"><img src="https://poser.pugx.org/omnixdeveloper/laravel-omnix/v/unstable?format=flat-square" alt="Latest Unstable Version"></a>    
    <a href="https://packagist.org/packages/omnixdeveloper/laravel-omnix"><img src="https://poser.pugx.org/omnixdeveloper/laravel-omnix/license?format=flat-square" alt="License"></a>
    <a href="https://packagist.org/packages/omnixdeveloper/laravel-omnix"><img src="https://poser.pugx.org/omnixdeveloper/laravel-omnix/downloads" alt="Total Downloads"></a>
</p>

## Introduction

This is a simple Laravel Service Provider providing for <a href="https://github.com/ethereum/wiki/wiki/JSON-RPC">Generic JSON RPC</a>

and <a href="https://github.com/ethereum/go-ethereum/wiki/Management-APIs">Management API</a>

Installation
------------

To install the PHP client library using Composer:

```bash
composer require omnixdeveloper/laravel-omnix
```

Alternatively, add these two lines to your composer require section:

```json
{
    "require": {
        "omnixdeveloper/laravel-omnix": "dev-master"
    }
}
```

### Laravel 5.5+

If you're using Laravel 5.5 or above, the package will automatically register the `Ethereum` provider and facade.

### Laravel 5.4 and below

Add `omnixdeveloper\LaravelOmnix\OmnixServiceProvider` to the `providers` array in your `config/app.php`:

```php
'providers' => [
    // Other service providers...

    omnixdeveloper\LaravelOmnix\OmnixServiceProvider::class,
],
```

If you want to use the facade interface, you can `use` the facade class when needed:

```php
use omnixdeveloper\LaravelOmnix\Facade\Ethereum;
```

Or add an alias in your `config/app.php`:

```php
'aliases' => [
    ...
    'Ethereum' => omnixdeveloper\LaravelOmnix\Facade\Ethereum::class,
],
```

### Using laravel-omnix with Lumen

laravel-omnix works with Lumen too! You'll need to do a little work by hand
to get it up and running. First, install the package using composer:


```bash
composer require omnixdeveloper/laravel-omnix
```

Next, we have to tell Lumen that our library exists. Update `bootstrap/app.php`
and register the `OmnixServiceProvider`:

```php
$app->register(omnixdeveloper\LaravelOmnix\OmnixServiceProvider::class);
```

Finally, we need to configure the library. Unfortunately Lumen doesn't support
auto-publishing files so you'll have to create the config file yourself by creating
a config directory and copying the config file out of the package in to your project:

```bash
mkdir config
cp vendor/omnixdeveloper/laravel-omnix/config/ethereum.php config/ethereum.php
```

At this point, set `ETH_HOST` and `ETH_PORT` in your `.env` file and it should
be working for you. You can test this with the following route:

```php
try{
        $ret = \omnixdeveloper\LaravelOmnix\Facade\Ethereum::eth_protocolVersion();
        print_r($ret);
    }catch (Exception $e){
        echo $e->getMessage();
    }
```

Configuration
-------------

You can use `artisan vendor:publish` to copy the distribution configuration file to your app's config directory:

```bash
php artisan vendor:publish
```

Then update `config/ethereum.php` with your credentials. Alternatively, you can update your `.env` file with the following:

```dotenv
ETH_HOST=http://localhost
ETH_PORT=8545
```

Usage
-----

To use the Ethereum Client Library you can use the facade, or request the instance from the service container:

```php
try{
        $ret = \omnixdeveloper\LaravelOmnix\Facade\Ethereum::eth_protocolVersion();
        print_r($ret);
    }catch (Exception $e){
        echo $e->getMessage();
    }
```

Or

```php
$thereum = app('Ethereum');

$result=$thereum->eth_protocolVersion();
```
