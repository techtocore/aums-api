<?php

namespace Aums;

/**
 * A micro HTTP client for interacting with the AUMS
 * Class Client.
 */
class Client
{
    private $baseURI = '';
    private $cookieDir = '';
    private $curl;
    private $cookieFilename;

    /**
     * Client constructor.
     *
     * @param string $baseURI
     * @param string $cookieDir
     */
    public function __construct($baseURI = '', $cookieDir = '../storage/cookies/')
    {
        $this->baseURI = $baseURI;
        $this->cookieDir = $cookieDir;

        $this->cookieFilename = md5(time().mt_rand(10, 99)).'.cookies';

        touch($this->cookieDir.$this->cookieFilename);

        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curl, CURLOPT_COOKIEJAR, $this->cookieDir.$this->cookieFilename);
        curl_setopt($this->curl, CURLOPT_COOKIEFILE, $this->cookieDir.$this->cookieFilename);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_PORT, 8444);
        curl_setopt($this->curl, CURLOPT_COOKIESESSION, true);
        curl_setopt($this->curl, CURLOPT_VERBOSE, true);
        curl_setopt($this->curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3');
    }

    /**
     * Create a GET Request.
     *
     * @param string $path
     * @param array  $params
     * @param bool   $includeHeaders
     *
     * @return Response
     */
    public function get($path, $params, $includeHeaders = false)
    {
        curl_setopt($this->curl, CURLOPT_POST, false);
        curl_setopt($this->curl, CURLOPT_HEADER, $includeHeaders);
        curl_setopt($this->curl, CURLOPT_URL, $this->getURL($path).'?'.http_build_query($params));
        $data = curl_exec($this->curl);
        $requestInfo = curl_getinfo($this->curl);

        return new Response($requestInfo['http_code'], $data, $requestInfo['url']);
    }

    /**
     * Create a POST Request.
     *
     * @param string $path
     * @param array  $params
     * @param bool   $includeHeaders
     *
     * @return Response
     */
    public function post($path, $params, $includeHeaders = false)
    {
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_HEADER, $includeHeaders);
        curl_setopt($this->curl, CURLOPT_URL, $this->getURL($path));
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($params));
        $data = curl_exec($this->curl);
        $requestInfo = curl_getinfo($this->curl);

        return new Response($requestInfo['http_code'], $data, $requestInfo['url']);
    }

    /**
     * Create full URL from path and base URL.
     *
     * @param string $path
     *
     * @return string Full URL
     */
    private function getURL($path)
    {
        return $this->baseURI.$path;
    }

    /**
     * Get the cookie filename.
     *
     * @return string
     */
    public function getCookieFilename()
    {
        return $this->cookieFilename;
    }

    /**
     * Get the cookie filename.
     *
     * @return string
     */
    public function getCookieFileLocation()
    {
        return $this->cookieDir.$this->cookieFilename;
    }

    /**
     * @param string $cookieDir
     */
    public function setCookieDir($cookieDir)
    {
        $this->cookieDir = $cookieDir;
        curl_setopt($this->curl, CURLOPT_COOKIEJAR, $this->cookieDir.$this->cookieFilename);
        curl_setopt($this->curl, CURLOPT_COOKIEFILE, $this->cookieDir.$this->cookieFilename);
    }

    /**
     * Buggy function to get image from an old request.
     *
     * @param string $baseURI             Base URI of the Original request
     * @param string $encodedEnrollmentId Encoded Student enrollment ID
     * @param string $cookieFilename      Cookie filename
     *
     * @return mixed
     */
    public static function getImageData($baseURI, $encodedEnrollmentId, $cookieFilename)
    {
        $cookieDir = __DIR__.'/../storage/cookies/';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_COOKIEJAR, $cookieDir.$cookieFilename);
        curl_setopt($curl, CURLOPT_COOKIEFILE, $cookieDir.$cookieFilename);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_PORT, 8444);
        curl_setopt($curl, CURLOPT_COOKIESESSION, true);
        curl_setopt($curl, CURLOPT_VERBOSE, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3');

        $params = [
            'action'              => 'SHOW_STUDENT_PHOTO',
            'encodedenrollmentId' => $encodedEnrollmentId,
            'flag'                => 'photo',
        ];

        echo $baseURI.'/aums/FileUploadServlet?'.http_build_query($params);
        curl_setopt($curl, CURLOPT_URL, $baseURI.'/aums/FileUploadServlet?'.http_build_query($params));

        return curl_exec($curl);
    }
}
