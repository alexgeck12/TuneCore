<?php
namespace Core;

abstract class Remote
{
    protected $headers;
    protected $content;
    protected $useragent;
    protected $cookie;

    protected function getHTML($url, $params, $method = 'get')
    {
        $this->request($url, $params, $method);
        preg_match('/[1-5][0-9]+/is', $this->headers['http_code'], $http_code);
        if($http_code[0] == "200") {
            return $this->content;
        }
        else return false;
    }

    protected function getJSON($url, $params, $method = 'get')
    {
        if($data = $this->getHTML($url, $params, $method)) {
            return json_decode($data);
        }
        else return false;
    }

    protected function getJSONP($url, $params, $method = 'get')
    {
        $params['callback'] = 'jQuery'.rand(0, PHP_INT_MAX);
        if($data = $this->getHTML($url, $params, $method)) {
            return json_decode(str_ireplace($params['callback'], '', $data, strlen($params['callback'])));
        }
        else return false;
    }

    protected function getXML($url, $params, $method = 'get')
    {
        if($data = $this->getHTML($url, $params, $method)) {
            return simplexml_load_string($data);
        }
        else return false;
    }

    protected function getCSV($url, $params, $delimeter = ',', $pass_header = false, $method = 'get')
    {
        if($data = $this->getHTML($url, $params, $method)) {
            $rows = explode("\n", $data);
            foreach ($rows as $row) {
                $cells = explode($delimeter, $row);
                foreach ($cells as &$cell) {
                    if(substr($cell, 0, 1) == '"' && substr($cell, -1) == '"') {
                        $cell = substr($cell, 1, -1);
                    }
                    $cell = str_replace('""', '"', $cell);
                }
                $csv[] = $cells;
            }
            return $csv;
        }
        else return false;
    }

    protected function request($url, $data, $type = 'get')
    {
        $ch = curl_init();
        $options = array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 1,
            CURLOPT_FOLLOWLOCATION => 0,
            CURLOPT_BINARYTRANSFER => 1,
            CURLOPT_AUTOREFERER => 1,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0
        );

        if($this->cookies) {
            if(is_array($this->cookies)) {
                foreach ($this->cookies as $name => $value) {
                    $options[CURLOPT_COOKIE] .= $name.'='.$value.'; ';
                }
            }
            else {
                $options[CURLOPT_COOKIE] = $this->cookie;
            }
        }

        if($this->useragent) {
            $options[CURLOPT_USERAGENT] = $this->useragent;
        }

        if(!empty($data)) {
            switch(strtolower($type)) {
                case 'post':
                    $options[CURLOPT_POST]=1;
                    $options[CURLOPT_POSTFIELDS]=http_build_query($data);
                    break;

                case 'get':
                    $options[CURLOPT_HTTPGET]=1;
                    $url .= '?'.http_build_query($data);
                    break;
            }
        }

        $options[CURLOPT_URL] = $url;
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        $header = reset(explode("\r\n\r\n", $response));
        $this->content = trim(substr($response, strlen($header)));
        $headers = explode("\r\n", $header);
        foreach ($headers as $header) {
            $header = trim($header);
            if(stripos($header, 'Set-Cookie')!==false) {
                $cookie = explode("=", trim(reset(explode(";",substr($header, 11)))));
                $this->cookies[$cookie[0]] = $cookie[1];
            } else {
                $header = explode(":", trim($header));
                if(isset($header[1])) {
                    $this->headers[trim($header[0])] = trim($header[1]);
                } else {
                    $this->headers['http_code'] = trim($header[0]);
                }
            }
        }

        curl_close($ch);
    }

    public function getDomainUri($url)
    {
        if(preg_match('/https?:\/\/([^\/]+)(.*)/is', $url, $matches)) {
            if (!$matches[2]) {
                $matches[2] = '/';
            }
            return array('domain' => $matches[1], 'uri' => trim($matches[2]));
        }
        else return false;
    }
}