# Url

[![Build Status](https://travis-ci.org/arnegroskurth/Url.svg?branch=master)](https://travis-ci.org/arnegroskurth/Url)
[![codecov](https://codecov.io/gh/arnegroskurth/Url/branch/master/graph/badge.svg)](https://codecov.io/gh/arnegroskurth/Url)
[![License](https://poser.pugx.org/arne-groskurth/url/license)](https://packagist.org/packages/arne-groskurth/url)

Url is a small PHP class that helps dealing with Urls.

## Setup

```bash
$ composer require arne-groskurth/url
```

## Examples

```php
<?php

use ArneGroskurth\Url\Url;

// construct url
$url = new Url();
$url->setHost('domain.tld');
$url->setScheme('ftps');

// parses given url
$url = new Url('http://username:password@www.test.com:8080/some/path.html?one=1&two=2#myfrag');

// modify parts
$url->setPort(80);
$url->removeQueryParameter('two');
$url->setQueryParameter('three', 3);

// get back url string
print $url->getUrl();

// avoid some parts on return
print $url->getUrl(Url::ALL - Url::CREDENTIALS);
print $url->getUrl(Url::SCHEME + Url::PORT);

// interpret link relative to some url
print $url->resolveRelativeUrl('../other/path.html')->getUrl();
print $url->resolveRelativeUrl('//domain.tld/')->getUrl();

```
