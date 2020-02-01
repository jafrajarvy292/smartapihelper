<?php

/**
 * @author David Tran <hunterr83@gmail.com>
 */

namespace jafrajarvy292\SmartAPIHelper\ResponseParser;

/**
 * This class is the base for all XML parser classes. It contains methods and initalizes variables that
 * are common to all SmartAPI products.
 */
abstract class ResponseParser
{
    /** @var string SmartAPI's default namespace */
    public const P1 = 'http://www.mismo.org/residential/2009/schemas';
    /** @var string Namespace used for xlinks */
    public const P2 = 'http://www.w3.org/1999/xlink';
    /** @var string Namespace used for MCL-specific extensions */
    public const P3 = 'inetapi/MISMO3_4_MCL_Extension.xsd';
    /** @var string XML schema namespace we probably won't use, but is declared in response */
    public const P4 = 'http://www.w3.org/2001/XMLSchema';
    /** @var string Another XML schema namespace we probably won't use, but is declared in response */
    public const P5 = 'http://www.w3.org/2001/XMLSchema-instance';
    /** @var array This array holds all the statuses that SmartAPI can communicate in its response. Only
     * a single status will be communicated in each response. Each product may only use a subset of this
     * list and each child class' STATUS array will reflect only what is used. The descriptions provided
     * here are general and may not apply to every child class. If a child class' description for a particular
     * status differs, then it will be pointed out.
     * - REQUEST_ERROR: This means an error was encountered with the request itself. More often than not,
     * this results from an authentication error (e.g. invalid user credentials).
     * - SERVICE_ERROR: This means an error was encountered related to the product you are ordering. For
     * example, trying to order a consumer credit report when your account isn't enabled for the product.
     * Practically speaking, you can treat a SERVICE_ERROR in the same manner you'd treat a REQUEST_ERROR:
     * display it to the end-user and they can contact their service provider if they aren't sure how
     * to resolve it.
     * - NEW: This means the system successfully received your request for a new order and was able to
     * create a unique file number for it. You should start polling for that file number in short intervals.
     * This can be accomplished using the sleep() function in a loop.
     * - PROCESSING: This means the system is still working on your request. Continue to poll for it in
     * short intervals.
     * - PENDING: This request is still being worked on, but will take from several minutes to several days
     * to complete. You should continue to poll for it, but in long intervals. Consider delegating the polling
     * for this file to a scheduled task or cron job.
     * - COMPLETED: Your order is completed and the report/details are included with the response.
     * - ERROR: Your order is completed, but the file is in an error status. A report may or may not be
     * available for viewing; if one is, it won't contain much more than the error message.
     * This scenario can happen for many reasons and one example would be if you ordered a SSA89,
     * but the Social Security Administration's server was offline. In such a case, our system would set the
     * order to error status with a description indicating a timeout, since we could not connect to them.
     */
    public const STATUS = [
        'REQUEST_ERROR' => 'REQUEST_ERROR',
        'SERVICE_ERROR' => 'SERVICE_ERROR',
        'NEW' => 'NEW',
        'PROCESSING' => 'PROCESSING',
        'PENDING' => 'PENDING',
        'COMPLETED' => 'COMPLETED',
        'ERROR' => 'ERROR'
    ];
    /** @var \DOMDocument This will hold the XML document we received from the server. It is protected
     * because child classes will need to access it.
     */
    protected $base;
    /** @var \DOMElement This will hold the root element.  It is protected because child classes will need to
     * access it. */
    protected $root;
    /** @var \DOMXPath The xpath that will help us jump around the XML doc as we parse it. It is protected
     * because child classes will need to access it.
     */
    protected $xpath;
    /** @var string This variable holds the status we pulled from the XML response to provide to the user */
    private $response_status = '';
    /** @var string If a message or description accompanies the response, it will be stored here. For error
     * statuses, a message will almost always be present and should be displayed to the end-user. */
    private $response_description = '';
    /** @var string Holds the VendorOrderIdentifer provided in the response */
    private $vendor_order_id = '';

    /**
     * Will load all our XML-related variables, declare necessary namespaces, and grab some commmonly-used
     * data from the XML response.
     *
     * @param string $xml_response The XML, as a string, that we received from the server
     * @param string $xml_ver XML version
     * @param string $encoding XML encoding language
     * @return void
     * @throws \Exception If the XML response is empty
     * @throws \Exception If the XML response doesn't contain MESSAGE for its root element
     */
    public function loadXMLResponse(
        string $xml_response,
        string $xml_ver = '1.0',
        string $encoding = 'utf-8'
    ): void {
        //Instantiate the DOMDocument
        if (trim($xml_response === '')) {
            throw new \Exception('XML response string is empty. Nothing to parse.');
        } else {
            $this->base = new \DOMDocument($xml_ver, $encoding);
            $this->base->loadXML($xml_response);
        }
        
        //Locate and label the root (i.e. MESSAGE element), else throw exception
        if ($this->base->getElementsByTagName('MESSAGE')->item(0) === null) {
            throw new \Exception('XML provided is not a valid SmartAPI response document.');
        } else {
            $this->root = $this->base->getElementsByTagName('MESSAGE')->item(0);
        }
        
        /* Declare DOM document namespaces. We probably don't need to do this, since we likely won't
        be editing the document, but just in case */
        $this->root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', self::P1);
        $this->root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:P2', self::P2);
        $this->root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:P3', self::P3);
        $this->root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:P4', self::P4);
        $this->root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:P5', self::P5);

        //Instantiate the XPath object and register all namespaces
        $this->xpath = new \DOMXPath($this->base);
        $this->xpath->registerNamespace('P1', $this::P1);
        $this->xpath->registerNamespace('P2', $this::P2);
        $this->xpath->registerNamespace('P3', $this::P3);
        $this->xpath->registerNamespace('P4', $this::P4);
        $this->xpath->registerNamespace('P5', $this::P5);

        //Extract the response status (and error message, if applicable) from the XML we received
        $this->parseStatus();
        /* Extract the VendorOrderIdentifier value */
        $this->parseVendorOrderID();
    }

    /**
     * Returns the status indicated in the response XML file
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->response_status;
    }

    /**
     * Returns the status description indicated in the response XML file. Often populated if the status was
     * an error.
     *
     * @return string
     */
    public function getStatusDescription(): string
    {
        return $this->response_description;
    }

    /**
     * Returns the VendorOrderIdentifier found in the response file
     *
     * @return string
     */
    public function getVendorOrderID(): string
    {
        return $this->vendor_order_id;
    }

    /**
     * Returns an array containing the objects you could use if you wanted to parse or traverse the
     * response XML yourself. This would be used in cases where you want to extract information
     * that the SmartAPI Helper library doesn't specifically provide a method for retrieving. The below
     * outlines the associative array that is returned:
     *
     * - 'DOMDocument' => \DOMDocument (This is a clone of the XML document received from the server)
     * - 'DOMXPath' => \DOMXPath (This is the xpath object associated with the DOMDocument clone. This would be
     * used to navigate through the document.)
     * - 'Namespaces' => [] (This is an associative array of all the namespaces that have been registered
     * with the XML document and xpath object. The keys are the namespace prefix, the values are the
     * namespace URIs)
     *
     * @return array
     */
    public function getDOMObjects(): array
    {
        $return = [];
        /* Clone the DOMDocument. This allows the user to manipulate the data without editing the original
        object, which our class makes use of */
        $new_base = clone $this->base;
        //Since we've closed the DOMDocuemnt, we need to create a new xpath object associated with this one
        $new_xpath = new \DOMXPath($new_base);
        $new_namespaces = [];

        /* Register all namespaces to the xpath object. Namespace registration doesn't need to be done with
        the DOMDocument because the original one we've cloned already had that done */
        $new_xpath->registerNamespace('P1', $this::P1);
        $new_xpath->registerNamespace('P2', $this::P2);
        $new_xpath->registerNamespace('P3', $this::P3);
        $new_xpath->registerNamespace('P4', $this::P4);
        $new_xpath->registerNamespace('P5', $this::P5);

        /* Put all the namespaces registered into an array. It would be better to loop through some sort of
        array for this, but there is currently no way to see all namespaces registered to an xpath object.
        The drawback to manually including this information is if the SmartAPI response starts returning
        a new namespace in the future, we'll have to update this method to include it */
        $new_namespaces = [
            'P1' => $this::P1,
            'P2' => $this::P2,
            'P3' => $this::P3,
            'P4' => $this::P4,
            'P5' => $this::P5,
        ];

        //Add our data to the array we'll be returning
        $return['DOMDocument'] = $new_base;
        $return['DOMXpath'] = $new_xpath;
        $return['Namespaces'] = $new_namespaces;

        return $return;
    }

    /**
     * Returns the unique ID the SmartAPI interface generated for this specific transaction. If
     * troubleshooting with the service provider, providing this ID will allow the support person to
     * quickly locate your request and the server's response for reviewing.
     *
     * @return string
     */
    public function getTransactionID(): string
    {
        $transaction_id = '';
        $node = $this->xpath->evaluate(
            '/P1:MESSAGE/P1:DEAL_SETS/P1:DEAL_SET/P1:DEALS/P1:DEAL/P1:PARTIES/P1:PARTY/P1:ROLES/P1:ROLE/' .
            'P1:RESPONDING_PARTY/P1:RespondingPartyTransactionIdentifier'
        )->item(0);
        if ($node !== null) {
            $transaction_id = $node->textContent;
        }
        return $transaction_id;
    }

    /**
     * Returns the HTML version of the completed report as a literal string. If no HTML report exists,
     * returns an empty
     *
     * @return string
     */
    public function getHTMLDocString(): string
    {
        $document = '';
        $node = $this->xpath->evaluate(
            '/P1:MESSAGE/P1:DOCUMENT_SETS/P1:DOCUMENT_SET/P1:DOCUMENTS/' .
            'P1:DOCUMENT/P1:VIEWS/P1:VIEW/P1:VIEW_FILES/P1:VIEW_FILE/' .
            'P1:FOREIGN_OBJECT[P1:MIMETypeIdentifier[text()="text/html"]]/P1:EmbeddedContentXML'
        )->item(0);
        if ($node !== null) {
            $document = $node->textContent;
        }
        return $document;
    }

    /**
     * Return the PDF version of the completed report as a base64 encoded string. If no PDF report
     * exits, returns an empty string
     *
     * @return string
     */
    public function getPDFDocString(): string
    {
        $document = '';
        $node = $this->xpath->evaluate(
            '/P1:MESSAGE/P1:DOCUMENT_SETS/P1:DOCUMENT_SET/P1:DOCUMENTS/' .
            'P1:DOCUMENT/P1:VIEWS/P1:VIEW/P1:VIEW_FILES/P1:VIEW_FILE/' .
            'P1:FOREIGN_OBJECT[P1:MIMETypeIdentifier[text()="application/pdf"]]/P1:EmbeddedContentXML'
        )->item(0);
        if ($node !== null) {
            $document = $node->textContent;
        }
        return $document;
    }

    /**
     * This extracts the status and status description from the XML response. Intended to be called from
     * the loadXMLResponse() method as a way of "automatically" getting this data for the user as soon
     * as the response XML is loaded to the object.
     *
     * @return void
     * @throws \Exception If a status could not be determined after checking various parts of the response
     * @throws \Exception If a StatusCode was returned, but isn't one of the expected enumerations
     */
    private function parseStatus(): void
    {
        //Check to see if a request error was encountered. The presence of the referenced node would tell us
        $request_error = $this->xpath->evaluate(
            '/P1:MESSAGE/P1:DEAL_SETS/P1:DEAL_SET_SERVICES/' .
            'P1:DEAL_SET_SERVICE/P1:ERRORS/P1:ERROR/P1:ERROR_MESSAGES/P1:ERROR_MESSAGE'
        )->item(0);
        if ($request_error !== null) {
            //If request error is present, then set that value to class property
            $this->response_status = static::STATUS['REQUEST_ERROR'];
            //Extract the error code and/or description, concatenate if applicable, then store the string
            $error_description = '';
            //Extact error code
            if ($request_error->getElementsByTagName('ErrorMessageCategoryCode')->length === 1) {
                $error_description = $request_error->getElementsByTagName('ErrorMessageCategoryCode')->
                    item(0)->textContent;
            }
            //Extract error description
            if ($request_error->getElementsByTagName('ErrorMessageText')->length === 1) {
                $error_description .= ' ' . $request_error->getElementsByTagName('ErrorMessageText')->
                item(0)->textContent;
            }
            $this->response_description = trim($error_description);
            return;
        }
        /* Locate the STATUS element under the SERVICE container. This will contain the status code and
        description */
        $status_container = $this->xpath->evaluate(
            '/P1:MESSAGE/P1:DEAL_SETS/P1:DEAL_SET/P1:DEALS/' .
            'P1:DEAL/P1:SERVICES/P1:SERVICE/P1:STATUSES/P1:STATUS'
        )->item(0);
        //If the STATUS element does not exist, throw exception
        if ($status_container === null) {
            throw new \Exception('No status could be determined from the server\'s response. ' .
            'Contact service provider for troubleshooting.');
        }
        //Locate the StatusCode node under the SERVICE container to get the status code
        $status_code = $status_container->getElementsByTagName('StatusCode')->item(0);
        //If StatusCode element is not present, throw exception
        if ($status_code === null) {
            throw new \Exception('No status could be determined from the server\'s response. ' .
            'Contact service provider for troubleshooting.');
        //If StatusCode exists, match to the enumeration and store it to the object
        } else {
            $status_code = $status_code->textContent;
            foreach (static::STATUS as $key => $value) {
                if (strtoupper($status_code) === strtoupper($value)) {
                    $this->response_status = $value;
                    break;
                }
            }
            //If the code returned by the server doesn't match any enumeration, throw error
            if ($this->response_status === '') {
                throw new \Exception('StatusCode "' . $status_code . '" was returned by server, but is not ' .
                    'a recognized status for this product. Contact service provider for troubleshooting.');
            }
            //Save the response description, if one was provided
            $status_description = $status_container->getElementsByTagName('StatusDescription')->item(0);
            if ($status_description !== null) {
                $this->response_description = $status_description->textContent;
            }
        }
    }

    /**
     * This will extract the VendorOrderIdentifier from the response XML, if present. Intended to be called
     * from the loadXMLResponse() method as a way of "automatically" getting this data for the user as soon
     * as the response XML is loaded to the object.
     *
     * @return void
     */
    private function parseVendorOrderID(): void
    {
        $id = $this->xpath->evaluate(
            '/P1:MESSAGE/P1:DEAL_SETS/P1:DEAL_SET/P1:DEALS/P1:DEAL/P1:SERVICES/' .
            'P1:SERVICE/P1:SERVICE_PRODUCT_FULFILLMENT/P1:SERVICE_PRODUCT_FULFILLMENT_DETAIL/
            P1:VendorOrderIdentifier'
        )->item(0);
        if ($id !== null) {
            $this->vendor_order_id = $id->textContent;
        }
    }
}
