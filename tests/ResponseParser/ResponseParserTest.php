<?php

namespace jafrajarvy292\SmartAPIHelper\ResponseParser;

use PHPUnit\Framework\TestCase;

/**
 * The testing done in this class is to ensure our response parser class can process a variety of XML
 * responses--including corner cases--and not "break". The sample XML files we test against is a collection
 * of a wide variety of responses we can expect to receive. We need to test this against every product
 * that the library supports. At the time of this writing, this library supports the following products and
 * so that is what we've included sample XML response for to test against:
 *
 * - Consumer Credit
 */
class ResponseParserTest extends TestCase
{
    private $empty_file = '';
    private $bad_file = '<?xml version="1.0" encoding="utf-8" ?><root></root>';
    private $cc_file = [
        '01_' => '',
        '02_' => '',
        '03_' => '',
        '04_' => '',
        '05_' => '',
        '06_' => '',
        '07_' => '',
        '08_' => '',
        '09_' => '',
        '10_' => '',
        '11_' => '',
        '12_' => '',
        '13_' => '',
        '14_' => '',
        '15_' => ''
    ];

    public function setUp()
    {
        libxml_use_internal_errors(true);
        $this->cc_file['01_'] = file_get_contents(glob(__DIR__ . '/ConsumerCredit_Responses/01_*.xml')[0]);
        $this->cc_file['02_'] = file_get_contents(glob(__DIR__ . '/ConsumerCredit_Responses/02_*.xml')[0]);
        $this->cc_file['03_'] = file_get_contents(glob(__DIR__ . '/ConsumerCredit_Responses/03_*.xml')[0]);
        $this->cc_file['04_'] = file_get_contents(glob(__DIR__ . '/ConsumerCredit_Responses/04_*.xml')[0]);
        $this->cc_file['05_'] = file_get_contents(glob(__DIR__ . '/ConsumerCredit_Responses/05_*.xml')[0]);
        $this->cc_file['06_'] = file_get_contents(glob(__DIR__ . '/ConsumerCredit_Responses/06_*.xml')[0]);
        $this->cc_file['07_'] = file_get_contents(glob(__DIR__ . '/ConsumerCredit_Responses/07_*.xml')[0]);
        $this->cc_file['08_'] = file_get_contents(glob(__DIR__ . '/ConsumerCredit_Responses/08_*.xml')[0]);
        $this->cc_file['09_'] = file_get_contents(glob(__DIR__ . '/ConsumerCredit_Responses/09_*.xml')[0]);
        $this->cc_file['10_'] = file_get_contents(glob(__DIR__ . '/ConsumerCredit_Responses/10_*.xml')[0]);
        $this->cc_file['11_'] = file_get_contents(glob(__DIR__ . '/ConsumerCredit_Responses/11_*.xml')[0]);
        $this->cc_file['12_'] = file_get_contents(glob(__DIR__ . '/ConsumerCredit_Responses/12_*.xml')[0]);
        $this->cc_file['13_'] = file_get_contents(glob(__DIR__ . '/ConsumerCredit_Responses/13_*.xml')[0]);
        $this->cc_file['14_'] = file_get_contents(glob(__DIR__ . '/ConsumerCredit_Responses/14_*.xml')[0]);
        $this->cc_file['15_'] = file_get_contents(glob(__DIR__ . '/ConsumerCredit_Responses/15_*.xml')[0]);
    }
    
    public function testLoadXMLResponseGood()
    {
        foreach ($this->cc_file as $key => $value) {
            $parser = $this->getMockForAbstractClass(ResponseParser::class);
            $this->assertNull($parser->loadXMLResponse($value));
        }
    }

    public function testLoadXMLResponseBad()
    {
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $this->expectException(\Exception::class);
        $parser->loadXMLResponse($this->bad_file);
    }

    public function testLoadXMLResponseBad2()
    {
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $this->expectException(\Exception::class);
        $parser->loadXMLResponse($this->empty_file);
    }

    public function testGetStatus()
    {
        foreach ($this->cc_file as $key => $value) {
            $parser = $this->getMockForAbstractClass(ResponseParser::class);
            $parser->loadXMLResponse($value);
            $this->assertIsString($parser->getStatus());
        }
    }

    public function testGetStatusDescription()
    {
        foreach ($this->cc_file as $key => $value) {
            $parser = $this->getMockForAbstractClass(ResponseParser::class);
            $parser->loadXMLResponse($value);
            $this->assertIsString($parser->getStatusDescription());
        }
    }

    public function testGetVendorOrderIdentifier()
    {
        foreach ($this->cc_file as $key => $value) {
            $parser = $this->getMockForAbstractClass(ResponseParser::class);
            $parser->loadXMLResponse($value);
            $this->assertIsString($parser->getVendorOrderID());
        }
    }

    public function testGetDOMObjects()
    {
        foreach ($this->cc_file as $key => $value) {
            $parser = $this->getMockForAbstractClass(ResponseParser::class);
            $parser->loadXMLResponse($value);
            $output = $parser->getDOMObjects();
            $this->assertInstanceOf(\DOMDocument::class, $output['DOMDocument']);
            $this->assertInstanceOf(\DOMXPath::class, $output['DOMXpath']);
            $this->assertEquals('http://www.mismo.org/residential/2009/schemas', $output['Namespaces']['P1']);
            $this->assertEquals('http://www.w3.org/1999/xlink', $output['Namespaces']['P2']);
            $this->assertEquals('inetapi/MISMO3_4_MCL_Extension.xsd', $output['Namespaces']['P3']);
            $this->assertEquals('http://www.w3.org/2001/XMLSchema', $output['Namespaces']['P4']);
            $this->assertEquals('http://www.w3.org/2001/XMLSchema-instance', $output['Namespaces']['P5']);
        }
    }

    public function testGetTransactionID()
    {
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['01_']);
        $this->assertEquals('d8c125ec-7c82-4c14-91a6-af5635b5df63', $parser->getTransactionID());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['02_']);
        $this->assertEquals('3bdd1dd0-e1a2-45f7-979a-6734bd825c80', $parser->getTransactionID());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['03_']);
        $this->assertEquals('3c2defef-6af9-48cb-abfd-113f2d8e13e9', $parser->getTransactionID());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['04_']);
        $this->assertEquals('e53b2e14-64e6-4316-b3e8-606b42596404', $parser->getTransactionID());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['05_']);
        $this->assertEquals('645870a9-bcdf-4ea2-84ae-8cc9c90f7e6a', $parser->getTransactionID());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['06_']);
        $this->assertEquals('e5486033-68bf-4dd1-a801-6a8171bdd0ba', $parser->getTransactionID());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['07_']);
        $this->assertEquals('3c2defef-6af9-48cb-abfd-113f2d8e13e9', $parser->getTransactionID());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['08_']);
        $this->assertEquals('9cfe5397-9832-4fe7-b4ae-6e55ef91fb08', $parser->getTransactionID());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['09_']);
        $this->assertEquals('3e4f5348-72a0-4431-8a1d-5c9849eddcc8', $parser->getTransactionID());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['10_']);
        $this->assertEquals('daa1d049-cab5-43fd-aa86-fcbc31a487c8', $parser->getTransactionID());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['11_']);
        $this->assertEquals('0238b8c8-b068-49a9-a665-17a7acac8c0e', $parser->getTransactionID());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['12_']);
        $this->assertEquals('cfab41a4-562d-4e75-9425-6b032696eb3f', $parser->getTransactionID());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['13_']);
        $this->assertEquals('61e85a69-b5f1-4202-a27c-c7cf77854f71', $parser->getTransactionID());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['14_']);
        $this->assertEquals('446f50c0-62ac-4c21-b53d-54e8c195a9da', $parser->getTransactionID());

        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['15_']);
        $this->assertEquals('007927a3-a277-43ad-87ad-356d829593a1', $parser->getTransactionID());
    }

    public function testGetHTMLDocString()
    {
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['01_']);
        $this->assertEquals('', $parser->getHTMLDocString());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['02_']);
        $this->assertEquals('', $parser->getHTMLDocString());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['03_']);
        $this->assertEquals('', $parser->getHTMLDocString());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['04_']);
        $this->assertEquals('', $parser->getHTMLDocString());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['05_']);
        $this->assertEquals('', $parser->getHTMLDocString());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['06_']);
        $this->assertNotEquals('', $parser->getHTMLDocString());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['07_']);
        $this->assertEquals('', $parser->getHTMLDocString());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['08_']);
        $this->assertNotEquals('', $parser->getHTMLDocString());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['09_']);
        $this->assertEquals('', $parser->getHTMLDocString());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['10_']);
        $this->assertEquals('', $parser->getHTMLDocString());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['11_']);
        $this->assertEquals('', $parser->getHTMLDocString());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['12_']);
        $this->assertEquals('', $parser->getHTMLDocString());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['13_']);
        $this->assertEquals('', $parser->getHTMLDocString());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['14_']);
        $this->assertEquals('', $parser->getHTMLDocString());

        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['15_']);
        $this->assertNotEquals('', $parser->getHTMLDocString());
    }

    public function testGetPDFDocString()
    {
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['01_']);
        $this->assertEquals('', $parser->getPDFDocString());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['02_']);
        $this->assertEquals('', $parser->getPDFDocString());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['03_']);
        $this->assertEquals('', $parser->getPDFDocString());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['04_']);
        $this->assertEquals('', $parser->getPDFDocString());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['05_']);
        $this->assertEquals('', $parser->getPDFDocString());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['06_']);
        $this->assertNotEquals('', $parser->getPDFDocString());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['07_']);
        $this->assertEquals('', $parser->getPDFDocString());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['08_']);
        $this->assertNotEquals('', $parser->getPDFDocString());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['09_']);
        $this->assertEquals('', $parser->getPDFDocString());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['10_']);
        $this->assertEquals('', $parser->getPDFDocString());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['11_']);
        $this->assertEquals('', $parser->getPDFDocString());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['12_']);
        $this->assertEquals('', $parser->getPDFDocString());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['13_']);
        $this->assertEquals('', $parser->getPDFDocString());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['14_']);
        $this->assertEquals('', $parser->getPDFDocString());

        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['15_']);
        $this->assertNotEquals('', $parser->getPDFDocString());
    }

    /* Test the parseStatus() method, which will actually parse the StatusCode and the StatusDescription. We
    shall check both */
    public function testParseStatus()
    {
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['01_']);
        $this->assertEquals(ConsumerCreditResponseParser::STATUS['NEW'], $parser->getStatus());
        $this->assertEquals('NOT READY', $parser->getStatusDescription());

        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['02_']);
        $this->assertEquals(ConsumerCreditResponseParser::STATUS['PROCESSING'], $parser->getStatus());
        $this->assertEquals('NOT READY', $parser->getStatusDescription());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['03_']);
        $this->assertEquals(ConsumerCreditResponseParser::STATUS['COMPLETED'], $parser->getStatus());
        $this->assertEquals('READY', $parser->getStatusDescription());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['04_']);
        $this->assertEquals(ConsumerCreditResponseParser::STATUS['ERROR'], $parser->getStatus());
        $this->assertEquals(
            'User is not authorized to pull new credit orders via an LOS interface.',
            $parser->getStatusDescription()
        );
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['05_']);
        $this->assertEquals(ConsumerCreditResponseParser::STATUS['REQUEST_ERROR'], $parser->getStatus());
        $this->assertNotEquals('', $parser->getStatusDescription());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['06_']);
        $this->assertEquals(ConsumerCreditResponseParser::STATUS['COMPLETED'], $parser->getStatus());
        $this->assertEquals('READY', $parser->getStatusDescription());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['07_']);
        $this->assertEquals(ConsumerCreditResponseParser::STATUS['COMPLETED'], $parser->getStatus());
        $this->assertEquals('READY', $parser->getStatusDescription());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['08_']);
        $this->assertEquals(ConsumerCreditResponseParser::STATUS['COMPLETED'], $parser->getStatus());
        $this->assertEquals('READY', $parser->getStatusDescription());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['09_']);
        $this->assertEquals(ConsumerCreditResponseParser::STATUS['COMPLETED'], $parser->getStatus());
        $this->assertEquals('READY', $parser->getStatusDescription());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['10_']);
        $this->assertEquals(ConsumerCreditResponseParser::STATUS['COMPLETED'], $parser->getStatus());
        $this->assertEquals('READY', $parser->getStatusDescription());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['11_']);
        $this->assertEquals(ConsumerCreditResponseParser::STATUS['COMPLETED'], $parser->getStatus());
        $this->assertEquals('READY', $parser->getStatusDescription());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['12_']);
        $this->assertEquals(ConsumerCreditResponseParser::STATUS['COMPLETED'], $parser->getStatus());
        $this->assertEquals('READY', $parser->getStatusDescription());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['13_']);
        $this->assertEquals(ConsumerCreditResponseParser::STATUS['COMPLETED'], $parser->getStatus());
        $this->assertEquals('READY', $parser->getStatusDescription());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['14_']);
        $this->assertEquals(ConsumerCreditResponseParser::STATUS['COMPLETED'], $parser->getStatus());
        $this->assertEquals('READY', $parser->getStatusDescription());

        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['15_']);
        $this->assertEquals(ConsumerCreditResponseParser::STATUS['COMPLETED'], $parser->getStatus());
        $this->assertEquals('READY', $parser->getStatusDescription());
    }

    public function testParseVendorOrderID()
    {
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['01_']);
        $this->assertEquals('815859', $parser->getVendorOrderID());

        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['02_']);
        $this->assertEquals('815859', $parser->getVendorOrderID());

        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['03_']);
        $this->assertEquals('815867', $parser->getVendorOrderID());

        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['04_']);
        $this->assertEquals('', $parser->getVendorOrderID());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['05_']);
        $this->assertEquals('', $parser->getVendorOrderID());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['06_']);
        $this->assertEquals('815868', $parser->getVendorOrderID());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['07_']);
        $this->assertEquals('815867', $parser->getVendorOrderID());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['08_']);
        $this->assertEquals('815871', $parser->getVendorOrderID());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['09_']);
        $this->assertEquals('815872', $parser->getVendorOrderID());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['10_']);
        $this->assertEquals('815874', $parser->getVendorOrderID());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['11_']);
        $this->assertEquals('815876', $parser->getVendorOrderID());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['12_']);
        $this->assertEquals('815879', $parser->getVendorOrderID());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['13_']);
        $this->assertEquals('815881', $parser->getVendorOrderID());
        
        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['14_']);
        $this->assertEquals('815883', $parser->getVendorOrderID());

        unset($parser);
        $parser = $this->getMockForAbstractClass(ResponseParser::class);
        $parser->loadXMLResponse($this->cc_file['15_']);
        $this->assertEquals('820197', $parser->getVendorOrderID());
    }
}
