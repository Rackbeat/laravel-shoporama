<?php
/**
 * Created by PhpStorm.
 * User: nts
 * Date: 31.3.18.
 * Time: 16.53
 */

namespace KgBot\Shoporama\Utils;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use KgBot\Shoporama\Exceptions\ShoporamaClientException;
use KgBot\Shoporama\Exceptions\ShoporamaRequestException;

class Request
{
    /**
     * @var \GuzzleHttp\Client
     */
    public $client;

    /**
     * Request constructor.
     *
     * @param null $token
     * @param array $options
     * @param array $headers
     */
    public function __construct($token = null, $options = [], $headers = [])
    {
        $token = $token ?? config('shoporama.token');
        $headers = array_merge($headers, [

            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Shoporama ' . $token,
            'User-Agent' => 'Rackbeat Shoporama Integration (Stefan Ninic)',
            'X-Access-Token' => $token,
        ]);
        $options = array_merge($options, [

            'base_uri' => config('shoporama.base_uri'),
            'headers' => $headers,
        ]);
        $this->client = new Client($options);
    }

    /**
     * @param $callback
     *
     * @return mixed
     * @throws \KgBot\Shoporama\Exceptions\ShoporamaClientException
     * @throws \KgBot\Shoporama\Exceptions\ShoporamaRequestException
     */
    public function handleWithExceptions($callback)
    {
        try {
            return $callback();

        } catch (ClientException $exception) {

            $message = $exception->getMessage();
            $code = $exception->getCode();

            if ($exception->hasResponse()) {

                $message = (string)$exception->getResponse()->getBody();
                $code = $exception->getResponse()->getStatusCode();
            }

            throw new ShoporamaRequestException($message, $code);

        } catch (ServerException $exception) {

            $message = $exception->getMessage();
            $code = $exception->getCode();

            if ($exception->hasResponse()) {

                $message = (string)$exception->getResponse()->getBody();
                $code = $exception->getResponse()->getStatusCode();
            }

            throw new ShoporamaRequestException($message, $code);

        } catch (\Exception $exception) {

            $message = $exception->getMessage();
            $code = $exception->getCode();

            throw new ShoporamaClientException($message, $code);
        }
    }
}