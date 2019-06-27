<?php

class Newsletter2goApiService
{
    const GRANT_TYPE = 'https://nl2go.com/jwt';
    const REFRESH_GRANT_TYPE = 'https://nl2go.com/jwt_refresh';
    const API_BASE_URL = 'https://api-staging.newsletter2go.com';

    private $apiKey;
    private $accessToken;
    private $refreshToken;
    private $lastStatusCode;

    /**
     * ApiService constructor.
     * @param $authKey
     * @param $accessToken
     * @param $refreshToken
     */
    public function __construct()
    {
        $this->apiKey =   Configuration::get('NEWSLETTER2GO_AUTH_KEY');
        $this->accessToken =    Configuration::get('NEWSLETTER2GO_ACCESS_TOKEN');
        $this->refreshToken =    Configuration::get('NEWSLETTER2GO_REFRESH_TOKEN');
    }

    /**
     *
     * @param string $method
     * @param string $endpoint
     * @param array $params
     * @param array $headers
     * @param bool $authorize
     * @return array
     */
    public function httpRequest($method, $endpoint, $params = [], $headers = ['Content-Type: application/json'], $authorize = false)
    {
        $response = [];
        $response['status'] = 0;

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            if ($authorize) {
                // this is needed for refresh token
                curl_setopt($ch, CURLOPT_USERPWD, $this->apiKey);
            }

            switch ($method) {
                case 'PATCH':
                case 'POST':
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                    break;
                case 'GET':
                    $encodedParams = array();
                    if (count($params) > 0) {
                        foreach ($params as $key => $value) {
                            $encodedParams[] = urlencode($key) . '=' . urlencode($value);
                        }

                        $getParams = "?" . http_build_query($params);
                        $endpoint .= $endpoint . $getParams;
                    }
                    break;
                default:
                    return null;
            }
            curl_setopt($ch, CURLOPT_URL, self::API_BASE_URL . $endpoint);

            $response = json_decode(curl_exec($ch), true);
            $this->setLastStatusCode(curl_getinfo($ch, CURLINFO_HTTP_CODE));

            curl_close($ch);

        } catch (\Exception $exception) {
            $response['error'] = $exception->getMessage();
        }

        return $response;
    }

    public function refreshToken()
    {
        if ($this->getRefreshToken()) {

            $data = [
                'refresh_token' => $this->getRefreshToken(),
                'grant_type' => self::REFRESH_GRANT_TYPE
            ];

            $result = $this->httpRequest('POST', '/oauth/v2/token', $data, [], true);

            if (isset($result['access_token'])) {
                $this->setAccessToken($result['access_token']);
                $this->setRefreshToken($result['refresh_token']);
            }
        } else {
            $this->setLastStatusCode(203);
            $result = ['status' => 203, 'error' => 'no refresh token found'];
        }

        return $result;
    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        $this->accessToken =  Configuration::get('NEWSLETTER2GO_ACCESS_TOKEN');

        return $this->accessToken;
    }

    /**
     * @param mixed $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken =    Configuration::updateValue('NEWSLETTER2GO_ACCESS_TOKEN', $accessToken);
    }

    /**
     * @return mixed
     */
    public function getRefreshToken()
    {
        $this->refreshToken =  Configuration::get('NEWSLETTER2GO_REFRESH_TOKEN');

        return $this->refreshToken;
    }

    /**
     * @param mixed $refreshToken
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken =  Configuration::updateValue('NEWSLETTER2GO_REFRESH_TOKEN', $refreshToken);;
    }

    public function testConnection()
    {
        $refreshResult = $this->refreshToken();

        if ($this->getLastStatusCode() === 200) {
            $headers = ['Content-Type: application/json', 'Authorization: Bearer ' . $this->getAccessToken()];

            $companyResult =  $this->httpRequest('GET', '/companies', [], $headers);

            return [
                'status' => $companyResult['status'],
                'account_id' => $refreshResult['account_id'],
                'company_id' => $companyResult['value'][0]['id']
            ];

        } else {
            $response['error'] = $refreshResult['error'];
        }

        $response['status'] = $this->getLastStatusCode();

        return $response;
    }

    /**
     * @return mixed
     */
    public function getLastStatusCode()
    {
        return $this->lastStatusCode;
    }

    /**
     * @param mixed $lastStatusCode
     */
    public function setLastStatusCode($lastStatusCode)
    {
        $this->lastStatusCode = $lastStatusCode;
    }

}