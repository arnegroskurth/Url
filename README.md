# Url

Url is a small PHP class that helps dealing with Urls.

## Installation

TempFile is available on [Packagist](https://packagist.org/packages/arne-groskurth/url) and can therefore be installed via Composer:

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

## License

MIT License

Copyright (c) 2016 Arne Groskurth

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
