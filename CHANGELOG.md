# Changelog

All notable changes to `laravel-akismet` will be documented in this file

## 2.2.0 - 2025-10-15

- Fix getResponseData header check for when an exception is thrown (#50)

## 2.1.0 - 2025-02-25

- Adding support for Laravel 12

## 2.0.3 - 2025-01-18

- Feature request: honeypot support (#49)

## 2.0.2 - 2024-11-24

- validateKey always returns Empty "api_key" value. (#47)

## 2.0.1 - 2024-11-09

- Properly submit data to Akismet (#45)

## 2.0.0 - 2024-11-08

- Refactor'd Guzzle HttpClient to Laravel's native Http Client
- setCommentAuthorUrl allowed to be null (#44)

## 1.13.0 - 2024-03-09

- Adding support for Laravel 11 (#35)

## 1.12.0 - 2023-06-08

- Adding support for Laravel 10 (#20)

## 1.11.0 - 2022-04-21

- Adding support for Laravel 9 (#19)

## 1.10.0 - 2020-12-06

- Adding support for PHP 8.0, ditched PHP 7.2 and PHP 7.3

## 1.9.0 - 2020-09-18

- Adding support for Laravel 8

## 1.8.0 - 2020-02-24

- Adding support for Laravel 7
- Dropping support for Laravel 5.8

## 1.7.1 - 2020-01-14

- Removed `nickurt\PwnedPasswords\PwnedPasswords` reference in `Akismet.php`

## 1.7.0 - 2019-12-02

- Added support for PHP 7.4

## 1.6.0 - 2019-09-04

- Added support for Laravel 6
