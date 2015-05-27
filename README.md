## Laravel Akismet

### Installation
Install this package with composer:
```
php composer.phar require nickurt/laravel-akismet:dev-master
```

Add the provider to config/app.php file

```
'nickurt\Akismet\ServiceProvider',
```

and the facade in the file

```
'Akismet' => 'nickurt\Akismet\Facade',
```

Copy the config files for the api

```
php artisan vendor:publish
```

- - - 