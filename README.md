# Url

Helper class to work with url strings.

[![Build Status](https://travis-ci.org/urmaul/url.svg)](https://travis-ci.org/urmaul/url)

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

```php
echo Url::from('../html/')->absolute('http://urmaul.com/blog/tags/php/');
// http://urmaul.com/blog/tags/html/

echo Url::from('/blog/')->absolute('http://urmaul.com/blog/tags/php/');
// http://urmaul.com/blog/

echo Url::from('https://github.com/')->absolute('http://urmaul.com/blog/tags/php/');
// https://github.com/
```

* string **addParam($name, $value)** - adds get parameter to url.


```php
echo Url::from('http://urmaul.com/')->addParam('foo', 'bar');
// http://urmaul.com/?foo=bar

echo Url::from('http://urmaul.com/?foo=bar')->addParam('spam', 'ham');
// http://urmaul.com/?foo=bar&spam=ham

echo Url::from('http://urmaul.com/?foo=bar')->addParam('foo', 'spam');
// http://urmaul.com/?foo=spam
```

* string **addParams($addParams)** - adds get parameters to url.


```php
echo Url::from('http://urmaul.com/')->addParams(array('foo' => 'bar'));
// http://urmaul.com/?foo=bar

echo Url::from('http://urmaul.com/?foo=bar')->addParams(array('spam' => 'ham'));
// http://urmaul.com/?foo=bar&spam=ham

echo Url::from('http://urmaul.com/?foo=bar')->addParams(array('foo' => 'spam'));
// http://urmaul.com/?foo=spam
```

* string **removeParam($name, $value)** - removes get parameter from url.


```php
echo Url::from('http://urmaul.com/?foo=bar')->removeParam('foo');
// http://urmaul.com/

echo Url::from('http://urmaul.com/?foo=bar&spam=ham')->removeParam('spam');
// http://urmaul.com/?spam=ham
```

* string **removeParams($removeParams)** - removes get parameters from url.


```php
echo Url::from('http://urmaul.com/?foo=bar')->removeParams(array('foo', 'spam'));
// http://urmaul.com/

echo Url::from('http://urmaul.com/?foo=bar&spam=ham')->removeParams(array('foo', 'spam'));
// http://urmaul.com/

echo Url::from('http://urmaul.com/?foo=bar&foo=spam&ham=spam')->removeParams(array('foo', 'spam'));
// http://urmaul.com/?ham=spam
```
