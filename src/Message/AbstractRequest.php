<?php
/**
 * Trexle Abstract REST Request
 */

namespace Omnipay\Trexle\Message;

use Guzzle\Http\Message\RequestInterface;

/**
 * Trexle Abstract REST Request
 *
 * This is the parent class for all Trexle REST requests.
 *
 * ### Test modes
 *
 * To enable test/sandbox/development mode you need to use the Trexle Sandbox Gateway in your dashboard at trexle.com
 * against the same API end point https://core.trexle.com/api/v1
 *
 * @see \Omnipay\Trexle\Gateway
 * @link https://docs.trexle.com
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    const API_VERSION = 'v1';

    /**
     * Test Endpoint URL
     *
     * @var string URL
     */
    protected $testEndpoint = 'https://core.trexle.com/api/';

    /**
     * Live Endpoint URL
     *
     * @var string URL
     */
    protected $liveEndpoint = 'https://core.trexle.com/api/';

    /**
     * Get secret key
     *
     * Calls to the Trexle Payments API must be authenticated using HTTP
     * basic authentication, with your API key as the username, and
     * a blank string as the password.
     *
     * @return string
     */
    public function getSecretKey()
    {
        return $this->getParameter('secretKey');
    }

    /**
     * Set secret key
     *
     * Calls to the Trexle Payments API must be authenticated using HTTP
     * basic authentication, with your API key as the username, and
     * a blank string as the password.
     *
     * @param string $value
     * @return AbstractRequest implements a fluent interface
     */
    public function setSecretKey($value)
    {
        return $this->setParameter('secretKey', $value);
    }

    /**
     * Get the request email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->getParameter('email');
    }

    /**
     * Sets the request email.
     *
     * @param string $value
     * @return AbstractRequest Provides a fluent interface
     */
    public function setEmail($value)
    {
        return $this->setParameter('email', $value);
    }

    /**
     * Get API endpoint URL
     *
     * @return string
     */
    protected function getEndpoint()
    {
        $base = $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
        return $base . self::API_VERSION;
    }

    /**
     * Send a request to the gateway.
     *
     * @param string $action
     * @param array  $data
     * @param string $method
     *
     * @return HttpResponse
     */
    public function sendRequest($action, $data = null, $method = RequestInterface::POST)
    {
        // don't throw exceptions for 4xx errors
        $this->httpClient->getEventDispatcher()->addListener(
            'request.error',
            function ($event) {
                if ($event['response']->isClientError()) {
                    $event->stopPropagation();
                }
            }
        );

        // Return the response we get back from Trexle Payments
        return $this->httpClient->createRequest(
            $method,
            $this->getEndpoint() . $action,
            array('Authorization' => 'Basic ' . base64_encode($this->getSecretKey() . ':')),
            $data
        )->send();
    }
}
