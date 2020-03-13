<?php

/**
 * Created by phpstorm.
 * User: xzd
 * Date: 2020/3/13
 * Time: 14:12
 */

namespace testcomposer\core;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\ResponseInterface;

class request
{
    /**
     * 发送网络请求
     * @param $type
     * @param $url
     * @param $data
     * @param array $headers
     * @param array $cookies
     * @param array $auth
     * @return bool|string
     * @throws \Exception
     */
    public function request($type, $url, $data = [], $headers = [], $cookies = [], $auth = [])
    {
        $client = new Client();
        $option = [];
        if ($headers) {
            $option['headers'] = $headers;
        }
        if ($cookies) {
            $option['cookies'] = $cookies;
        }
        if ($auth) {
            $option['auth'] = $auth;
        }
        try {
            $type = strtolower($type);
            if ($data) {
                switch ($type) {
                    case 'get':
                        $option['query'] = $data;
                        break;
                    case 'post':
                        if (isset($headers['Content-Type']) && $headers['Content-Type'] == 'application/json') {
                            $option['json'] = $data;
                        } else {
                            $option['form_params'] = $data;
                        }
                        break;
                    case 'put':
                    case 'delete':
                        $option['json'] = $data;
                        break;
                    default:
                        throw new \Exception('undefined request type');
                }
            }
            /**
             * @var ResponseInterface $response
             */
            $response = $client->$type($url, $option);
        } catch (ServerException $e) {
            return false;
        } catch (ClientException $e) {
            return false;
        } catch (ConnectException $e) {
            return false;
        } catch (RequestException $e) {
            return false;
        }
        if ($response->getStatusCode() == 200 && strtolower($response->getReasonPhrase()) == 'ok') {
            return $response->getBody()->getContents();
        } else {
            return false;
        }
    }
}