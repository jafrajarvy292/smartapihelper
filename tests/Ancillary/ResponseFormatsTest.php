<?php

namespace jafrajarvy292\SmartAPIHelper\Ancillary;

use PHPUnit\Framework\TestCase;

class ResponseFormatsTest extends TestCase
{
    public function testConstructorValid()
    {
        $this->assertInstanceOf(ResponseFormats::class, new ResponseFormats(true, true, true));
        $this->assertInstanceOf(ResponseFormats::class, new ResponseFormats(true, true));
        $this->assertInstanceOf(ResponseFormats::class, new ResponseFormats(true, false, true));
        $this->assertInstanceOf(ResponseFormats::class, new ResponseFormats());
    }

    public function testSetCustomFormatValid()
    {
        $response = new ResponseFormats();
        $this->assertNull($response->setCustomFormat('JSON', true));
        $this->assertNull($response->setCustomFormat('TEXT', false));
    }

    public function testSetCustomFormatInvalid()
    {
        $response = new ResponseFormats();
        $this->expectException(\Exception::class);
        $response->setCustomFormat('HTML', true);
    }

    public function testSetXMLFormat()
    {
        $response = new ResponseFormats();
        $this->assertNull($response->setXMLFormat(false));
        $this->assertNull($response->setXMLFormat(true));
    }

    public function testSetHTMLFormat()
    {
        $response = new ResponseFormats();
        $this->assertNull($response->setHTMLFormat(false));
        $this->assertNull($response->setHTMLFormat(true));
    }

    public function testSetPDFFormat()
    {
        $response = new ResponseFormats();
        $this->assertNull($response->setPDFFormat(false));
        $this->assertNull($response->setPDFFormat(true));
    }

    public function testGetFormats()
    {
        $response = new ResponseFormats(true, true, false);
        $count = count($response->getFormats());
        $this->assertEquals(2, $count);
    }

    public function testGetAllFormats()
    {
        $response = new ResponseFormats(true, true, false);
        $count = count($response->getAllFormats());
        $this->assertEquals(3, $count);
    }

    public function testGetCount()
    {
        $response = new ResponseFormats(true, true, false);
        $this->assertEquals(2, $response->getCount());
    }

    public function testGetXML()
    {
        $response = new ResponseFormats();
        $document = new \DOMDocument();
        $this->assertInstanceOf(\DOMNode::class, $response->getXML($document));
    }

    public function testGetXMLNoHTML()
    {
        $response = new ResponseFormats(true, false, true);
        $document = new \DOMDocument();
        $document->appendChild($response->getXML($document));
        $string = $document->textContent;
        $this->assertEquals(0, preg_match('/Html/', $string));
    }

    public function testGetXMLNoXML()
    {
        $response = new ResponseFormats(false, true, true);
        $document = new \DOMDocument();
        $document->appendChild($response->getXML($document));
        $string = $document->textContent;
        $this->assertEquals(0, preg_match('/Xml/', $string));
    }

    public function testGetXMLNoPDF()
    {
        $response = new ResponseFormats(true, true, false);
        $document = new \DOMDocument();
        $document->appendChild($response->getXML($document));
        $string = $document->textContent;
        $this->assertEquals(0, preg_match('/Pdf/', $string));
    }

    public function testGetCustomFormat()
    {
        $response = new ResponseFormats(true, true, false);
        $response->setCustomFormat('Json', true);
        $document = new \DOMDocument();
        $document->appendChild($response->getXML($document));
        $string = $document->textContent;
        $this->assertEquals(1, preg_match('/Json/', $string));
    }

    public function testGetCustomAndAllFormats()
    {
        $response = new ResponseFormats(true, true, true);
        $response->setCustomFormat('Json', true);
        $document = new \DOMDocument();
        $document->appendChild($response->getXML($document));
        $string = $document->textContent;
        $this->assertEquals(1, preg_match('/Xml/', $string));
        $this->assertEquals(1, preg_match('/Html/', $string));
        $this->assertEquals(1, preg_match('/Pdf/', $string));
        $this->assertEquals(1, preg_match('/Json/', $string));
    }
}
