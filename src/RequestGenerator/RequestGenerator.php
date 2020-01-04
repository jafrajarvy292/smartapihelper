<?php

/**
 * @package SmartAPI Helper
 * @author David Tran <hunterr83@gmail.com>
 */

namespace jafrajarvy292\SmartAPIHelper\RequestGenerator;

/**
 * This class creates the base XML document used with all SmartAPI requests.
 *
 * This is an abstract class intended to be the base for all classes that create SmartAPI XML request
 * docs. Creating a DOM document and registering all the namespaces can be confusing and tedious;
 * this class handles that part.
 */
abstract class RequestGenerator
{
    /** @var string SmartAPI's default namespace */
    protected const P1 = 'http://www.mismo.org/residential/2009/schemas';
    /** @var string Namespace used for xlinks */
    protected const P2 = 'http://www.w3.org/1999/xlink';
    /** @var string Namespace used for MCL-specific extensions */
    protected const P3 = 'inetapi/MISMO3_4_MCL_Extension.xsd';
    /** @var \DOMDocument This will hold the XML document upon which we build the request file */
    protected $base;
    /** @var \DOMElement This will hold the root element */
    protected $root;
    /** @var \DOMXPath The xpath that will help us jump around the XML doc as we build it out */
    protected $xpath;

    /**
     * Instantiates the various XML objects, which is called from classes that extend this one.
     *
     * @param string $xml_version Standard XML declaration.
     * @param string $encoding Standard XML declaration.
     */
    protected function __construct(
        string $xml_version_input = '1.0',
        string $encoding_input = 'utf-8'
    ) {
        /* Disable XML errors. We turn this off because it will display even warnings, which tends to trigger
        because the 'inetapi/MISMO3_4_MCL_Extension.xsd' namespace isn't a fully qualified URI */
        libxml_clear_errors();
        libxml_use_internal_errors(true);

        if ($xml_version_input === null || $xml_version_input === '') {
            $xml_version_input = '';
        }

        if ($encoding_input === null || $encoding_input === '') {
            $encoding_input = '';
        }

        //Create the XML Document
        $this->base = new \DOMDocument($xml_version_input, $encoding_input);

        //Create the root element and register all namespaces
        $this->root = $this->base->createElement('MESSAGE');
        $this->root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', self::P1);
        $this->root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:P2', self::P2);
        $this->root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:P3', self::P3);
        $this->root->setAttribute('MessageType', 'Request');
        $this->base->appendChild($this->root);
        
        //Create the XPath object and register all namespaces
        $this->xpath = new \DOMXPath($this->base);
        $this->xpath->registerNamespace('P1', $this::P1);
        $this->xpath->registerNamespace('P2', $this::P2);
        $this->xpath->registerNamespace('P3', $this::P3);
    }
}
