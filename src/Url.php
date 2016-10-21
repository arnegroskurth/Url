<?php

namespace ArneGroskurth\Url;


class Url {

    /*
     * The following values can be subtracted or added together to get a mask identifying all parts that has to be included.
     */
    const NONE = 0;

    const SCHEME = 1;
    const USER = 2;
    const PASS = 4;
    const CREDENTIALS = 6;
    const PORT = 8;
    const PATH = 16;
    const QUERY = 32;
    const FRAGMENT = 64;

    const ALL = 127;


    /**
     * @var string
     */
    protected $scheme;

    /**
     * @var string
     */
    protected $user;

    /**
     * @var string
     */
    protected $pass;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $query = array();

    /**
     * @var string
     */
    protected $fragment;


    /**
     * @return string
     */
    public function getScheme() {

        return $this->scheme;
    }


    /**
     * @param string $scheme
     *
     * @return Url
     */
    public function setScheme($scheme) {

        $this->scheme = $scheme;

        return $this;
    }


    /**
     * @return string
     */
    public function getUser() {

        return $this->user;
    }


    /**
     * @param string $user
     *
     * @return Url
     */
    public function setUser($user) {

        $this->user = $user;

        return $this;
    }


    /**
     * @return string
     */
    public function getPass() {

        return $this->pass;
    }


    /**
     * @param string $pass
     *
     * @return Url
     */
    public function setPass($pass) {

        $this->pass = $pass;

        return $this;
    }


    /**
     * @return string
     */
    public function getHost() {

        return $this->host;
    }


    /**
     * @param string $host
     *
     * @return Url
     */
    public function setHost($host) {

        $this->host = $host;

        return $this;
    }

    /**
     * @param bool $canonicalized
     *
     * @return string
     */
    public function getPath($canonicalized = false) {

        return $canonicalized ? ('/' . $this->getCanonicalizedPath($this->path)) : $this->path;
    }


    /**
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path) {

        $path = $path ?: '/';

        if($path[0] !== '/') {

            $path = '/' . $path;
        }

        $this->path = $path;

        return $this;
    }


    /**
     * @return int
     */
    public function getPort() {

        return $this->port;
    }


    /**
     * @param int $port
     *
     * @return Url
     * @throws UrlException
     */
    public function setPort($port) {

        if(!is_int($port) || $port < 1 || $port > 65535) {

            throw new UrlException('Invalid port given.');
        }

        $this->port = $port;

        return $this;
    }


    /**
     * @return array
     */
    public function getQuery() {

        return $this->query;
    }


    /**
     * @param array $query
     *
     * @return Url
     */
    public function setQuery(array $query) {

        $this->query = $query;

        return $this;
    }


    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasQueryParameter($name) {

        return isset($this->query[$name]);
    }


    /**
     * @param string $name
     *
     * @return string
     */
    public function getQueryParameter($name) {

        return isset($this->query[$name]) ? $this->query[$name] : null;
    }


    /**
     * @param string $name
     * @param string $value
     *
     * @return $this
     */
    public function setQueryParameter($name, $value) {

        $this->query[$name] = $value;

        return $this;
    }


    /**
     * @param string $name
     *
     * @return $this
     */
    public function removeQueryParameter($name) {

        if(isset($this->query[$name])) {

            unset($this->query[$name]);
        }

        return $this;
    }


    /**
     * @return string
     */
    public function getQueryString() {

        $return = array();

        foreach($this->query as $name => $value) {

            $return[] = $name . '=' . urlencode($value);
        }

        return implode('&', $return);
    }


    /**
     * @param string $queryString
     *
     * @return $this
     */
    public function setQueryString($queryString) {

        foreach(explode('&', $queryString) as $parameter) {

            list($name, $value) = explode('=', $parameter);

            $this->query[$name] = urldecode($value);
        }

        return $this;
    }


    /**
     * @return string
     */
    public function getFragment() {

        return $this->fragment;
    }


    /**
     * @param string $fragment
     *
     * @return Url
     */
    public function setFragment($fragment) {

        $this->fragment = $fragment;

        return $this;
    }


    /**
     * @param string $url
     *
     * @throws UrlException
     */
    public function __construct($url = null) {

        if($url === null) {
            return;
        }


        // http://php.net/manual/de/function.parse-url.php#114817
        $encodedUrl = preg_replace_callback('%[^:/@?&=#]+%usD', function ($matches) {

            return urlencode($matches[0]);

        }, $url);

        $parts = parse_url($encodedUrl);

        if($parts === false) {

            throw new UrlException('Invalid Url given.');
        }

        foreach($parts as $name => $value) {

            $parts[$name] = urldecode($value);
        }

        if(!isset($parts['host'])) {

            throw new UrlException('Invalid Url given.');
        }

        $this->scheme = isset($parts['scheme']) ? $parts['scheme'] : null;
        $this->user = isset($parts['user']) ? $parts['user'] : null;
        $this->pass = isset($parts['pass']) ? $parts['pass'] : null;
        $this->host = $parts['host'];
        $this->port = isset($parts['port']) ? (int)$parts['port'] : null;
        $this->path = isset($parts['path']) ? $parts['path'] : null;
        $this->fragment = isset($parts['fragment']) ? $parts['fragment'] : null;

        if(isset($parts['query'])) {

            $this->setQueryString($parts['query']);
        }
    }


    /**
     * @param int $includeParts
     * @param bool $skipDefaultPort
     * @param bool $canonicalizePath
     *
     * @return string
     * @throws UrlException
     */
    public function getUrl($includeParts = self::ALL, $skipDefaultPort = true, $canonicalizePath = true) {

        if(!is_int($includeParts) || $includeParts < static::NONE || $includeParts > static::ALL) {

            throw new UrlException('Invalid part inclusion mask given.');
        }


        $return = '';

        if($includeParts & self::SCHEME && $this->scheme) {

            $return .= $this->scheme . ':';
        }

        $return .= '//';

        if($includeParts & self::USER && $this->user) {

            $return .= $this->user;

            if($includeParts & self::PASS && $this->pass) {

                $return .= ':' . $this->pass;
            }

            $return .= '@';
        }

        $return .= $this->host;

        if($includeParts & self::PORT && $this->port) {

            $defaultPort = $this->scheme ? static::getDefaultPort($this->scheme) : null;

            if($this->port !== $defaultPort || !$skipDefaultPort) {

                $return .= ':' . $this->port;
            }
        }

        if($includeParts & self::PATH && $this->path) {

            $return .= $this->getPath($canonicalizePath);
        }

        else {

            $return .= '/';
        }

        if($includeParts & self::QUERY && $this->query) {

            $return .= '?' . $this->getQueryString();
        }

        if($includeParts & self::FRAGMENT && $this->fragment) {

            $return .= '#' . $this->fragment;
        }

        return $return;
    }


    /**
     * Build absolute url of given path in given context
     *
     * @param string $url
     *
     * @return Url
     */
    public function resolveRelativeUrl($url) {

        try {

            // return absolute url
            return new static($url);
        }
        catch(UrlException $exception) {}

        // anchor
        if($url[0] === '#') {

            return new static($this->getUrl(self::ALL - self::FRAGMENT) . $url);
        }

        // query
        if($url[0] === '?') {

            return new static($this->getUrl(self::ALL - self::FRAGMENT - self::QUERY) . $url);
        }

        // relative path from domain root
        if($url[0] === '/') {

            return new static($this->getUrl(self::ALL - self::FRAGMENT - self::QUERY - self::PATH) . substr($url, 1));
        }

        // relative path from current path
        else {

            $currentPath = $this->path ?: '/';

            // cut last path fragment
            if(substr($currentPath, -1) !== '/') {

                $currentPath = substr($currentPath, 0, strrpos($currentPath, '/') + 1);
            }

            return new static($this->getUrl(self::ALL - self::FRAGMENT - self::QUERY - self::PATH) . $this->getCanonicalizedPath($currentPath . $url));
        }
    }


    /**
     * @return string
     */
    public function __toString() {

        return $this->getUrl();
    }


    /**
     * @param string $path
     *
     * @return string
     */
    protected function getCanonicalizedPath($path) {

        $newPath = array();
        foreach(explode('/', $path) as $segment) {

            if($segment === '' || $segment === '.') {

                continue;
            }

            elseif($segment === '..') {

                array_pop($newPath);
            }

            else {

                array_push($newPath, $segment);
            }
        }

        return implode('/', $newPath);
    }


    /**
     * Returns true if the given Url is valid.
     *
     * @param string $url
     *
     * @return bool
     */
    public static function validate($url) {

        try {

            new self($url);

            return true;
        }
        catch(UrlException $exception) {

            return false;
        }
    }


    /**
     * Returns the default port the given scheme usually can be contacted on.
     *
     * @param string $scheme
     *
     * @return int
     */
    public static function getDefaultPort($scheme) {

        switch(strtolower($scheme)) {

            case 'dav': return 9800;
            case 'ftp': return 21;
            case 'ftps': return 990;
            case 'git': return 9418;
            case 'http': return 80;
            case 'https': return 443;
            case 'svn': return 3690;

            // sub-protocol of http
            case 'ws': return 80;
            case 'wss': return 443;

            default: return null;
        }
    }
}