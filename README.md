## Laravel Akismet

[![Latest Stable Version](https://poser.pugx.org/nickurt/laravel-akismet/v/stable?format=flat-square)](https://packagist.org/packages/nickurt/laravel-akismet)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/nickurt/laravel-akismet/master.svg?style=flat-square)](https://travis-ci.org/nickurt/laravel-akismet)
[![Total Downloads](https://img.shields.io/packagist/dt/nickurt/laravel-akismet.svg?style=flat-square)](https://packagist.org/packages/nickurt/laravel-akismet)

### Installation
Install this package with composer:
```
composer require nickurt/laravel-akismet
```

Add the provider to `config/app.php` file

```php
'nickurt\Akismet\ServiceProvider',
```

and the facade in the file

```php
'Akismet' => 'nickurt\Akismet\Facade',
```

Copy the config files for the api

```
php artisan vendor:publish --provider="nickurt\Akismet\ServiceProvider" --tag="config"
```
### Configuration
The Akismet information can be set with environment values in the `.env` file (or directly in the `config/akismet.php` file)
```
AKISMET_APIKEY=MY_UNIQUE_APIKEY
AKISMET_BLOGURL=https://my-custom-blogurl.dev
```
### Examples

#### Validation Rule
You can use a hidden-field `akismet` in your Form-Request to validate if the request is valid
```php
// FormRequest ...

public function rules()
{
    return [
        'akismet' => [new \nickurt\Akismet\Rules\AkismetRule(
            request()->input('email'), request()->input('name')
        )]
    ];
}

// Manually ...

$validator = validator()->make(['akismet' => 'akismet'], ['akismet' => [new \nickurt\Akismet\Rules\AkismetRule(
    request()->input('email'), request()->input('name')
)]]);
```
The `AkismetRule` requires a `email` and `name` parameter to validate the request.
#### Events
You can listen to the `IsSpam`, `ReportSpam` and  `ReportHam` events, e.g. if you want to log all the `IsSpam`-requests in your application
##### IsSpam Event
This event will be fired when the request contains spam
`nickurt\Akismet\Events\IsSpam`
##### ReportSpam Event
This event will be fired when you succesfully reported spam
`nickurt\Akismet\Events\ReportSpam`
##### ReportHam Event
This event will be fired when you succesfully reported ham
`nickurt\Akismet\Events\ReportHam`

#### Custom Implementation
##### Validate Key
```php
if( \Akismet::validateKey() ) {
    // valid
} else {
    // invalid
}
```
##### Set CommentAuthor Information
```php
\Akismet::setCommentAuthor("John Doe")
    ->setCommentAuthorUrl("https://www.google.com")
    ->setCommentContent("It's me, John!")
    ->setCommentType('registration');
    // etc
    
// or
\Akismet::fill([
    'comment_author' => 'John Doe',
    'comment_author_url' => 'https://www.google.com',
    'comment_content' => 'It's me, John!'
]);
// etc
```
##### Is it Spam?
```php
if( \Akismet::isSpam() ) {
    // yes, i'm spam!
}
```
##### Submit Spam (missed spam)
```php
if( \Akismet::reportSpam() ) {
    // yes, thanks!
}
```
##### Submit Ham (false positives)
```php
if( \Akismet::reportHam() ) {
    // yes, thanks!
}
```

### Tests
```sh
composer test
```

- - - 