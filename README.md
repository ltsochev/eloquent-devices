# Allow multiple remember me tokens per user

This package allows your users to be remembered by Laravel across multiple devices. 

By default laravel stores the "remember" token in the users table as a singular column, which is an issue when more than one device stores a "remember token"

## Installation

### Laravel

This package can be used in Laravel 5.4 or higher. It's not tested on older versions as of yet.

You can install the package via composer:

``` bash
composer require ltsochev/eloquent-devices
```
Afterwards you'll need to register the service provider in your `config/app.php` file as follows:

```php
'providers' => [
    // ...
    Ltsochev\Auth\ServiceProvider::class,
]
```
*make sure it's added after `Illuminate\Auth\AuthServiceProvider` and `Illuminate\Session\SessionServiceProvider` so that we are certain the Laravel Auth driver is already loaded.*

Two more settings need to be added to `config/auth.php` file. 

```php
'token_table' => 'user_tokens',
'driver_name' => 'eloquentdevices',
```

Once done, you'll have to run migrations for the package

```bash
php artisan vendor:publish --provider="Ltsochev\Auth\ServiceProvider" --tag="migrations"
php artisan migrate
```

And finally, you'll have to replace the auth driver in your auth config file. 
It's easily done by changing the `config/auth.php` file accordingly:

```php
//...
'driver' => 'eloquentdevices',
```

You can change the name of the abstraction by changing the value of `driver_name` in your config file.

And that's it! Your users will be remembered across multiple devices. 

**IMPORTANT! The package does not have automated garbage collect as of yet. You'll have to bake in something on your own for this one.**
