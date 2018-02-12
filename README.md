## Laravel Akismet

### Installation
Install this package with composer:
```
php composer.phar require nickurt/laravel-akismet:1.*
```

Add the provider to config/app.php file

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
The Akismet information can be set with environment values in the .env file (or directly in the config/akismet.php file)
```
AKISMET_APIKEY=MY_UNIQUE_APIKEY
AKISMET_BLOGURL=https://my-custom-blogurl.dev
```
### Examples

#### Validation Rule
You can use a hidden-field `akismet` in your Form-Request to validate if the request is valid
```php
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
\Akismet::setCommentAuthor("John Doe");
\Akismet::setCommentAuthorUrl("https://www.google.com");
\Akismet::setCommentContent("It's me, John!");
// etc
```
##### Get CommentAuthor Information
```php
if( \Akismet::getCommentAuthor() == 'John Doe' ) {
    // it's me John!
}
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
##### Submit ham (false positives)
```php
if( \Akismet::reportHam() ) {
    // yes, thanks!
}
```

### Tests
```sh
vendor/bin/phpunit -c vendor/nickurt/laravel-akismet/tests
```

- - - 