<?php

/**
 * @package SmartAPI Helper
 * @author David Tran <hunterr83@gmail.com>
 */

namespace jafrajarvy292\SmartAPIHelper\HTTPHandler;

/**
 * This is the base class for all HTTP handler classes. HTTP handler classes handle the sending of SmartAPI
 * request XML files and the receiving of corresponding responses.
 */
class HTTPHandler
{
    /** @var string The SmartAPI Helper version */
    private const SMARTAPI_HELPER_VERSION = '1.0.0';
    /** @var string User's login name */
    private $user_login;
    /** @var string User's password */
    private $user_password;
    /** @var string The URL to where the API request is being submitted */
    private $http_endpoint = '';
    /** @var string The MCL-Interface HTTP header value */
    private $mcl_interface = '';
    /** @var string The MCL-SurrogatedLogin HTTP header value, if applicable */
    private $mcl_surrogated_login = '';
    /** @var string The XML document, as plain text string, that's going to submitted to SmartAPI service */
    private $xml_string = '';
    /** @var resource The cURL resource that we'll be using for HTTP communication */
    private $ch;
    /** @var string Holds the response body from the latest cURL execution */
    private $ch_response = '';
    /** @var int The time, in seconds, until the connection to the server is timed out. This encompasses
     * the time we allow from the sending of the request to when the server is done streaming us its response.
     * This method applies the timeout to both the cURL resource and PHP's set_time_limit().
     */
    private $http_timeout = 60;
    /** @var bool Tell us if the most recent cURL execution resulted in a successful response from server */
    private $ch_successful = null;
    /** @var string If the cURL execution was not successful, this will hold the corresponding details */
    private $ch_err_message = '';
    /** @var bool Holds flag to indicate if logging is eanbled. */
    private $logging_enabled = false;
    /** @var string If logging is enabled, we store the output path here */
    private $log_path = '';

    /**
     * Constructor will intialize the cURL resource
     */
    public function __construct()
    {
        //Initialize cURL resource
        $this->ch = curl_init();
    }

    /**
     * Set user's login name
     *
     * @param string $login The login name
     * @return void
     */
    public function setUserLogin(string $login, bool $colon_exception = false): void
    {
        if ($login === null || $login === '') {
            throw new \Exception('User login name cannot be blank.');
        } elseif (preg_match('/:/', $login) === 1 && $colon_exception === false) {
            throw new \Exception('User login name contains a colon, which violates Basic Auth scheme. ' .
             'Change login name or resubmit with exception flag set to true to ignore.');
        } else {
            $this->user_login = $login;
        }
    }

    /**
     * Set user's password
     *
     * @param string $password The password value
     * @return void
     */
    public function setUserPassword(string $password): void
    {
        if ($password === null || $password === '') {
            throw new \Exception('User password cannot be blank.');
        } else {
            $this->user_password = $password;
        }
    }

    /**
     * Set the HTTP endpoint for where the request is being submitted
     *
     * @param string $url The URL
     * @return void
     */
    public function setHTTPEndpoint(string $url): void
    {
        $url = trim($url);
        if ($url === null || $url === '') {
            throw new \Exception('HTTP endpoint cannot be blank.');
        } else {
            $this->http_endpoint = $url;
        }
    }

    /**
     * Set the MCL-Interface HTTP header value value
     *
     * @param string $interface The MCL-Inteface value
     * @return void
     */
    public function setMCLInterface(string $interface): void
    {
        if ($interface === null || $interface === '') {
            throw new \Exception('MCL-Interface value cannot be blank.');
        } else {
            $this->mcl_interface = $interface;
        }
    }

    /**
     * Set the MCL-SurrogatedLogin value
     *
     * @param string $login The MCL-SurrogatedLogin value
     * @return void
     */
    public function setMCLSurrogatedLogin(string $login): void
    {
        if ($login === null || $login === '') {
            $this->mcl_surrogated_login = '';
        } else {
            $this->mcl_surrogated_login = $login;
        }
    }

    /**
     * Stores the XML request string to the corresponding object property
     *
     * @param string $document The full XML request string to be sent to SmartAPI service
     * @return void
     */
    public function loadXMLString(string $document): void
    {
        $this->xml_string = $document;
    }

    /**
     * Set the amount of time we wait for the submission to complete, in seconds.
     *
     * @param integer $duration The timeout, in seconds
     * @return void
     */
    public function setHTTPTimeout(int $duration): void
    {
        $this->http_timeout = $duration;
    }

    /**
     * This prepares the cURL resource for execution. It's the final step before submitting the request.
     *
     * @return void
     */
    public function curlPrep(): void
    {
        //Check for user login
        if ($this->user_login === null || $this->user_login === '') {
            throw new \Exception('User login name is required.');
        }
        //Check for user password
        if ($this->user_password === null || $this->user_password === '') {
            throw new \Exception('User password is required.');
        }
        //Check for HTTP endpoint
        if ($this->http_endpoint === null || $this->http_endpoint == '') {
            throw new \Exception('HTTP endpoint is required. If testing, use ' .
                '"https://demo.mortgagecreditlink.com/inetapi/request_products.aspx"');
        }
        //Check for MCL-Interface header
        if ($this->mcl_interface === null || $this->mcl_interface === '') {
            throw new \Exception('MCL-Interface is required. If testing, use "SmartAPITestingIdentifier"');
        }
        //Check for XML payload
        if ($this->xml_string === null || $this->xml_string === '') {
            throw new \Exception('XML request string is required.');
        }

        //Check that cURL library is installed, since that is what we'll be using
        if (extension_loaded('curl') === false) {
            throw new \Exception('cURL library is required, but does not appear to be installed.');
        }

        /* Set PHP's timeout to match the one set for cURL. PHP's default timeout is 30 seconds, which
        will cause early timeouts if our cURL value is set higher and we fail to increase the PHP one */
        set_time_limit($this->http_timeout);
        /* Ignore user connection abort; this ensures the application will wait for and process the server's
        response, even if the user disconnects */
        ignore_user_abort(true);

        //Set cURL timeout
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->http_timeout);
        //Set response to be stored as a string
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        //Set POST method
        curl_setopt($this->ch, CURLOPT_POST, true);
        //Enable redirect following
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        //Enable cURL failure if HTTP status code indicates error
        curl_setopt($this->ch, CURLOPT_FAILONERROR, true);
        //Set user agent
        $curl_agent = 'cURL/' . curl_version()['version'];
        $php_agent = 'PHP/' . PHP_VERSION;
        $smartapi_helper_agent = 'SmartAPIHelper/' . self::SMARTAPI_HELPER_VERSION;
        $full_agent = $smartapi_helper_agent . ' ' . $curl_agent . ' ' . $php_agent;
        curl_setopt($this->ch, CURLOPT_USERAGENT, $full_agent);

        //Set relevant HTTP headers for SmartAPI
        $headers = [];
        $headers[] = 'Content-Type: application/xml';
        $headers[] = 'MCL-Interface: ' . $this->mcl_interface;
        $headers[] = 'Authorization: Basic ' . base64_encode($this->user_login . ':' . $this->user_password);
        if ($this->mcl_surrogated_login !== null && $this->mcl_surrogated_login !== '') {
            $headers[] = 'MCL-SurrogatedLogin: ' . $this->mcl_surrogated_login;
        }
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
        
        //Set URL
        curl_setopt($this->ch, CURLOPT_URL, $this->http_endpoint);
        
        //Set payload, which is the XML request string
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->xml_string);
    }

    /**
     * Return the user login in plain text
     *
     * @return string
     */
    public function getUserLogin(): string
    {
        return $this->user_login;
    }

    /**
     * Return the user password in plain text
     *
     * @return string
     */
    public function getUserPassword(): string
    {
        return $this->user_password;
    }

    /**
     * Get the HTTP endpoint to where the ordering is being submitted
     *
     * @return string Full URL
     */
    public function getHTTPEndpoint(): string
    {
        return $this->http_endpoint;
    }

    /**
     * Get the MCL-Interface value
     *
     * @return string
     */
    public function getMCLInterface(): string
    {
        return $this->mcl_interface;
    }

    /**
     * Get the MCL-SurrogatedLogin value in plain text
     *
     * @return string
     */
    public function getMCLSurrogatedLogin(): string
    {
        return $this->mcl_surrogated_login;
    }

    /**
     * Get the XML string that will be sent to the server
     *
     * @return string
     */
    public function getXMLString(): string
    {
        return $this->xml_string;
    }

    /**
     * Get the timeout, in seconds, that is currently configured
     *
     * @return integer
     */
    public function getHTTPTimeout(): int
    {
        return $this->http_timeout;
    }

    /**
     * Submit the XML request
     *
     * @return void
     */
    public function submitCURLRequest(): void
    {
        //Run the preparation function
        $this->curlPrep();

        //If logging is enabled, submit the cURL request and log the relevant data
        if ($this->logging_enabled === true) {
            /* Generate ID for this, we use it to label the request and response file pair. We prefix the
            ID with a unix timestamp so the files will appear in a somewhat ordered manner when viewing the
            directory in alphabetical sort. We then append a random hex value to allow the user to quickly
            find the file */
            $time = (string)(microtime(true) * 10000);
            /* Take the last 10 digits of the timestamp, since that's sufficient for keeping the files
            in a sorted order */
            $time = substr($time, -10);
            $log_id = $time . '_' . sprintf('%06x', mt_rand(0, 0xffffff));

            //Create the request and response log files and their resource pointers
            $send_file_name = $log_id . '_REQUEST.log';
            $response_file_name = $log_id . '_RESPONSE.log';
            $send_file = fopen($this->log_path . $send_file_name, 'a');
            $response_file = fopen($this->log_path . $response_file_name, 'a');

            //Prep the cURL for logging response headers
            $response_headers = '';
            curl_setopt(
                $this->ch,
                CURLOPT_HEADERFUNCTION,
                function ($ch, $header) use (&$response_headers) {
                    $response_headers .= $header;
                    return strlen($header);
                }
            );

            //Prep the cURL for logging verbose request info to our request log file
            curl_setopt($this->ch, CURLOPT_VERBOSE, true);
            curl_setopt($this->ch, CURLOPT_STDERR, $send_file);

            //Grab the timestamp just before sending
            $send_time = date('m-d-Y h:i:sA');

            //Execute the cURL request
            $this->ch_response = curl_exec($this->ch);

            //Grab the timestamp right after we receive a response
            $response_time = date('m-d-Y h:i:sA');
            
            //Log the request body
            fwrite($send_file, PHP_EOL . $this->xml_string);

            //Log the response headers
            fwrite($response_file, $response_headers);
            //Log the response body
            fwrite($response_file, $this->ch_response);

            //Write to the transactions log
            $log_name = 'transactions.log';
            $log_file = fopen($this->log_path . $log_name, 'a');
            fwrite($log_file, PHP_EOL . PHP_EOL . $send_time . ' Sending request: ' . $send_file_name);
            fwrite($log_file, PHP_EOL . $response_time . ' Receiving response: ' . $response_file_name);
        //If logging is not enabled, then just submit the cURL request
        } else {
            //Execute the cURL request
            $this->ch_response = curl_exec($this->ch);
        }

        //Briefly parse the response to determine if it was successful or not
        //Store the HTTP response code
        $http_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        //If HTTP response was not 200, then return error and corresponding message
        if ($http_code !== 200) {
            $this->ch_successful = false;
            $this->ch_err_message = curl_error($this->ch);
        //If HTTP response was 200, then check to see if the respone we received is the correct one
        } else {
            $response = substr($this->ch_response, 0, 250);
            /* If "<MESSAGE " isn't found near the beginning of response, then we didn't get back the
            expected XML response, this is an error */
            if (preg_match('/<MESSAGE /', $response) !== 1) {
                $this->ch_successful = false;
                $this->ch_err_message = 'Unexpected response: ' . $this->ch_response;
            } else {
                $this->ch_successful = true;
            }
        }
    }

    /**
     * Returns the status of the most recent cURL execution
     *
     * @return bool Returns true if latest execution was successful, else false
     */
    public function wasCURLSuccessful(): bool
    {
        if ($this->ch_successful === null) {
            throw new \Exception('cURL needs to be executed before a result can be determined.');
        } else {
            return $this->ch_successful;
        }
    }

    /**
     * If latest cURL execution resulted in an error, this returns the message/details
     *
     * @return string
     */
    public function getCURLErrorMessage(): string
    {
        return htmlspecialchars($this->ch_err_message);
    }

    /**
     * Get the cURL response. If the cURL was successful, this should be an XML document string
     *
     * @return string
     */
    public function getCURLResponse(): string
    {
        return $this->ch_response;
    }

    /**
     * This will set the logging flag to true, which will log communication with the server. The output
     * will be placed in the directory provided in the argument.
     * The files are not stripped of sensitive data; they should be deleted when no longer needed.
     *
     * @param string $path The folder where you want the log files output.
     * @return void
     * @example $object->enableLogging('./TempFolder/');
     */
    public function enableLogging(string $output_folder): void
    {
        $this->log_path = $output_folder;
        //Check that the folder actually exists.
        if (!is_dir($this->log_path)) {
            throw new \Exception('Unable to enable logging: the path "' . $output_folder .
                '" does not appear to exist. Try creating it first.');
        }
        $id = sprintf('%06x', mt_rand(0, 0xffffff));
        /* Create a test file to the specified logging directory, write to it, then delete it. If this fails,
        an exception will be thrown */
        $file_path = $this->log_path . $id . '_testing.txt';
        $temp_file = fopen($file_path, 'a');
        fwrite($temp_file, 'Test file to confirm needed access for logging; this file should delete itself.' .
            ' If it does not, notify development team.');
        fclose($temp_file);
        unlink($file_path);

        //If we get this far, then enable the logging flag.
        $this->logging_enabled = true;
    }
}
