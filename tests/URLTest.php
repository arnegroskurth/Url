<?php

namespace ArneGroskurth\Url\Tests;

use ArneGroskurth\Url\Url;


class UrlTest extends \PHPUnit_Framework_TestCase {

    public function testConstruction() {

        $url = new Url();
        $url->setScheme('http');
        $url->setUser('username');
        $url->setPass('password');
        $url->setHost('www.test.com');
        $url->setPort(8080);
        $url->setPath('/some/path.html');
        $url->setQueryParameter('one', 1);
        $url->setQueryParameter('two', 2);
        $url->setFragment('myfrag');

        $urlString = 'http://username:password@www.test.com:8080/some/path.html?one=1&two=2#myfrag';
        static::assertEquals($urlString, $url->getUrl());
        static::assertEquals($urlString, (string)$url);

        static::assertEquals('http', $url->getScheme());
        static::assertEquals('username', $url->getUser());
        static::assertEquals('password', $url->getPass());
        static::assertEquals('www.test.com', $url->getHost());
        static::assertEquals(8080, $url->getPort());
        static::assertEquals('/some/path.html', $url->getPath());
        static::assertEquals(1, $url->getQueryParameter('one'));
        static::assertEquals(2, $url->getQueryParameter('two'));
        static::assertEquals('myfrag', $url->getFragment());
    }

    public function testReassembly() {

        $originalUrl = 'http://username:password@www.test.com:8080/some/path.html?one=1&two=2#myfrag';

        $url = new Url($originalUrl);

        static::assertEquals($originalUrl, $url->getUrl());
    }


    public function testPartialReassembly() {

        $originalUrl = 'http://username:password@www.test.com:8080/some/path.html?one=1&two=2#myfrag';

        $url = new Url($originalUrl);

        static::assertEquals(
            '//username:password@www.test.com:8080/some/path.html?one=1&two=2#myfrag',
            $url->getUrl(Url::ALL - Url::SCHEME)
        );
        static::assertEquals(
            'http://username@www.test.com:8080/some/path.html?one=1&two=2#myfrag',
            $url->getUrl(Url::ALL - Url::PASS)
        );
        static::assertEquals(
            'http://www.test.com:8080/some/path.html?one=1&two=2#myfrag',
            $url->getUrl(Url::ALL - Url::CREDENTIALS)
        );
        static::assertEquals(
            'http://username:password@www.test.com/some/path.html?one=1&two=2#myfrag',
            $url->getUrl(Url::ALL - Url::PORT)
        );
        static::assertEquals(
            'http://username:password@www.test.com:8080/?one=1&two=2#myfrag',
            $url->getUrl(Url::ALL - Url::PATH)
        );
        static::assertEquals(
            'http://username:password@www.test.com:8080/some/path.html#myfrag',
            $url->getUrl(Url::ALL - Url::QUERY)
        );
        static::assertEquals(
            'http://username:password@www.test.com:8080/some/path.html?one=1&two=2',
            $url->getUrl(Url::ALL - Url::FRAGMENT)
        );
    }


    public function testDefaultPortSkipping() {

        $originalUrl = 'http://username:password@www.test.com:80/some/path.html?one=1&two=2#myfrag';

        $url = new Url($originalUrl);

        static::assertEquals(
            'http://username:password@www.test.com/some/path.html?one=1&two=2#myfrag',
            $url->getUrl()
        );
    }


    public function testRelativeUrlResolving() {

        $originalUrl = 'http://username:password@www.test.com:8080/some/path.html?one=1&two=2#myfrag';

        $url = new Url($originalUrl);

        static::assertEquals(
            '//domain.tld/',
            $url->resolveRelativeUrl('//domain.tld')->getUrl()
        );
        static::assertEquals(
            'http://username:password@www.test.com:8080/some/path.html?one=1&two=2#myfrag2',
            $url->resolveRelativeUrl('#myfrag2')->getUrl()
        );
        static::assertEquals(
            'http://username:password@www.test.com:8080/some/path.html?another=parameter',
            $url->resolveRelativeUrl('?another=parameter')->getUrl()
        );
        static::assertEquals(
            'http://username:password@www.test.com:8080/some/path2.html',
            $url->resolveRelativeUrl('path2.html')->getUrl()
        );
        static::assertEquals(
            'http://username:password@www.test.com:8080/test.ext',
            $url->resolveRelativeUrl('../test.ext')->getUrl()
        );
        static::assertEquals(
            'http://username:password@www.test.com:8080/test.ext',
            $url->resolveRelativeUrl('../../test.ext')->getUrl()
        );
        static::assertEquals(
            'http://username:password@www.test.com:8080/another/path.ext',
            $url->resolveRelativeUrl('/another/path.ext')->getUrl()
        );
    }


    public function testPathCanonicalization() {

        $originalUrl = 'http://username:password@www.test.com:8080/some/../relative/path.html?one=1&two=2#myfrag';

        $url = new Url($originalUrl);

        static::assertEquals(
            'http://username:password@www.test.com:8080/relative/path.html?one=1&two=2#myfrag',
            $url->getUrl()
        );
    }


    public function testQueryModification() {

        $originalUrl = 'http://username:password@www.test.com:8080/some/path.html?one=1&two=2#myfrag';

        $url = new Url($originalUrl);
        $url->removeQueryParameter('one');
        $url->setQueryParameter('two', 3);
        $url->setQueryParameter('three', 2);

        static::assertEquals(
            'http://username:password@www.test.com:8080/some/path.html?two=3&three=2#myfrag',
            $url->getUrl()
        );
    }


    public function testValidation() {

        static::assertTrue(Url::validate('http://username:password@www.test.com:8080/some/path.html?one=1&two=2#myfrag'));
        static::assertFalse(Url::validate(''));
        static::assertFalse(Url::validate(':80'));
        static::assertFalse(Url::validate('path'));
        static::assertFalse(Url::validate('?param=value'));
        static::assertFalse(Url::validate('#frag'));
    }
}
