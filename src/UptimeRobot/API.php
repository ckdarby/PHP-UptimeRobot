<?php

namespace UptimeRobot;

class API
{

    private $url, $contents;
    public $debug;

    /**
     * Initializes the API
     *
     * @param  array  $config  An array of configuration
     * @throws Exception Configuration is missing
     */
    public function __construct($config = array()) {

        if(empty($config['apiKey'])) {
            throw new \Exception('Missing API Key');
        }

        if(empty($config['url'])) {
            throw new \Exception('Missing API url');
        }

        //Setting apiKey, Format & noJsonCallBack
        $this->args['apiKey'] = $config['apiKey'];
        $this->args['format'] = 'json';
        $this->args['noJsonCallback'] = 1;

        $this->url = $config['url'];
    }

    /**
     * Makes curl call to the url & returns output
     *
     * @param  string  $resource  The resource of the api
     * @param  array  $args  Array of options for the query query
     * @throws Exception If the curl request fails
     * @return  array  json_decoded contents
     */
    public function request($resource, $args=array())
    {

        $url = $this->buildUrl($resource, $args);
        $curl = curl_init($url);

        $options = array(
            CURLOPT_HEADER         => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
        );

        curl_setopt_array($curl, $options);
        $this->contents = curl_exec($curl);
        $this->setDebug($curl);

        if(curl_errno($curl) > 0) {
            throw new \Exception('There was an error while making the request');
        }

        return json_decode($this->contents, true);
    }

    /**
     * Builds the url for the curl request
     *
     * @param  string  $resource  The resource of the api
     * @param  array  $args  Array of options for the query query
     * @return  string  Finalized Url
     */
    private function buildUrl($resource, $args)
    {
        //Merge args(apiKey, Format, noJsonCallback)
        $args = array_merge($args, $this->args);
        $query = http_build_query($args);

        $url = $this->url;
        $url .= $resource.'?'.$query;

        return $url;
    }

    /**
     * Sets debug information from last curl
     *
     * @param  resource  $curl  Curl handle
     */
    private function setDebug($curl)
    {
        $this->debug = [
            'errorNum' => curl_errno($curl),
            'error' => curl_error($curl),
            'info' => curl_getinfo($curl),
            'raw' => $this->contents,
        ];

    }
}
