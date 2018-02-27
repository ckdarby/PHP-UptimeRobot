<?php namespace UptimeRobot;

/**
 * Class API
 * @package UptimeRobot
 */
class API
{
    private $url;
    private $contents;

    private $args;
    private $options;

    public $debug;

    /**
     * Initializes the API.
     *
     * @param array $config An array of configuration
     * @param array $options An array of options for curl
     *
     * @throws \Exception Configuration is missing
     */
    public function __construct($config = array(), $options = array())
    {
        if (empty($config['apiKey'])) {
            throw new \Exception('Missing API Key');
        }

        if (empty($config['url'])) {
            throw new \Exception('Missing API url');
        }

        // Setting apiKey, Format & noJsonCallBack
        $this->args['apiKey'] = $config['apiKey'];
        $this->args['format'] = 'json';
        $this->args['noJsonCallback'] = 1;

        $this->url = $config['url'];

        // Set options for curl
        $this->options = $this->getOptions($options);
    }

    /**
     * Makes curl call to the url & returns output.
     *
     * @param string $resource The resource of the api
     * @param array $args Array of options for the query query
     *
     * @return array json_decoded contents
     * @throws \Exception If the curl request fails
     */
    public function request($resource, $args = array())
    {

        $url = $this->buildUrl($resource, $args);
        $curl = curl_init($url);

        curl_setopt_array($curl, $this->options);
        $this->contents = curl_exec($curl);
        $this->setDebug($curl);

        if (curl_errno($curl) > 0) {
            throw new \Exception('There was an error while making the request');
        }

        $jsonDecodeContent = json_decode($this->contents, true);

        if(is_null($jsonDecodeContent)) {
            throw new \Exception('Unable to decode JSON response');
        }

        return $jsonDecodeContent;
    }

    /**
     * Get options for curl.
     *
     * @param $options
     *
     * @return array
     */
    private function getOptions($options)
    {
        $conf = [
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true
        ];

        if (isset($options['timeout'])) {
            $conf[CURLOPT_TIMEOUT_MS] = $options['timeout'] * 1000;
        }
        if (isset($options['connect_timeout'])) {
            $conf[CURLOPT_CONNECTTIMEOUT_MS] = $options['connect_timeout'] * 1000;
        }

        return $conf;
    }

    /**
     * Builds the url for the curl request.
     *
     * @param string $resource The resource of the api
     * @param array $args Array of options for the query query
     *
     * @return string  Finalized Url
     */
    private function buildUrl($resource, $args)
    {
        //Merge args(apiKey, Format, noJsonCallback)
        $args = array_merge($args, $this->args);
        $query = http_build_query($args);

        $url = $this->url;
        $url .= $resource . '?' . $query;

        return $url;
    }

    /**
     * Sets debug information from last curl.
     *
     * @param resource $curl Curl handle
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
