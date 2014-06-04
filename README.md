# Url

Helper class to work with url strings.

## Installing

``
composer require urmaul/url dev-master
``

## Creating

```php
use urmaul\url\Url;

$url = new Url('http://urmaul.com/');
// or
$url = Url::from('http://urmaul.com/');
```

## Functions

* string **absolute($baseUrl)** - converts url from relative to absolute using base url.

* string **addParam($name, $value)** - adds get parameter to url.
