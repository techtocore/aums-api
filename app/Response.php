<?php
namespace Aums;

/**
 * A response class to return the response info
 * Class Response
 * @package Aums
 */
class Response {

    private $body;
    private $code;
    private $effectiveUrl;

    /**
     * Response constructor.
     * @param int $code HTTP Status code
     * @param string $body Response body
     * @param string $effectiveUrl Effective URL after request
     */

    public function __construct($code, $body = "", $effectiveUrl = "") {
        $this->body = $body;
        $this->code = $code;
        $this->effectiveUrl = $effectiveUrl;
    }

    /**
     * Get the response body
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Get the HTTP status code
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get the effective URL after the request
     * @return string
     */
    public function getEffectiveUrl()
    {
        return $this->effectiveUrl;
    }


}