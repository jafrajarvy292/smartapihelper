<?php

namespace jafrajarvy292\SmartAPIHelper\Ancillary;

use PHPUnit\Framework\TestCase;

class AddressBlockTest extends TestCase
{
    public function testConstructorGoodAddress()
    {
        $this->assertInstanceOf(
            AddressBlock::class,
            new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843')
        );
    }

    public function testConstructorBadStreet()
    {
        $this->expectException(\Exception::class);
        new AddressBlock('123 M*ain St', 'Garden Grove', 'CA', '92843');
    }

    public function testConstructorEmptyStreet()
    {
        $this->expectException(\Exception::class);
        new AddressBlock('', 'Garden Grove', 'CA', '92843');
    }

    public function testConstructorEmptyCity()
    {
        $this->expectException(\Exception::class);
        new AddressBlock('123 Main St', '', 'CA', '92843');
    }

    public function testConstructorBadState()
    {
        $this->expectException(\Exception::class);
        new AddressBlock('123 Main St', 'Garden Grove', 'PL', '92843');
    }

    public function testConstructorEmptyState()
    {
        $this->expectException(\Exception::class);
        new AddressBlock('123 Main St', 'Garden Grove', '', '92843');
    }

    public function testConstructorBadZip()
    {
        $this->expectException(\Exception::class);
        new AddressBlock('123 Main St', 'Garden Grove', 'CA', '9843');
    }

    public function testConstructorEmptyZip()
    {
        $this->expectException(\Exception::class);
        new AddressBlock('123 Main St', 'Garden Grove', 'CA', '');
    }

    public function testConstructorBadCountry()
    {
        $this->expectException(\Exception::class);
        new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843', 'CH');
    }

    public function testGetStreet()
    {
        $test = new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843');
        $this->assertIsString($test->getStreet());
    }

    public function testGetCity()
    {
        $test = new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843');
        $this->assertIsString($test->getCity());
    }

    public function testGetState()
    {
        $test = new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843');
        $this->assertIsString($test->getState());
    }

    public function testGetZip()
    {
        $test = new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843');
        $this->assertIsString($test->getZip());
    }

    public function testGetCountry()
    {
        $test = new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843');
        $this->assertIsString($test->getCountry());
    }

    public function testGetXML()
    {
        $test = new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843');
        $this->assertInstanceOf(
            \DOMNode::class,
            $test->getXML(new \DOMDocument())
        );
    }

    public function testGetXMLFullUS()
    {
        $test = new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843', 'US');
        $base = new \DOMDocument();
        $base->appendChild($test->getXML($base));
        $address = $base->getElementsByTagName('ADDRESS')->item(0);
        $street = $address->firstChild;
        $city = $street->nextSibling;
        $country = $city->nextSibling;
        $zip = $country->nextSibling;
        $state = $zip->nextSibling;
        $this->assertEquals('123 Main St', $street->textContent);
        $this->assertEquals('Garden Grove', $city->textContent);
        $this->assertEquals('CA', $state->textContent);
        $this->assertEquals('92843', $zip->textContent);
        $this->assertEquals('US', $country->textContent);
    }

    public function testGetXMLFullCA()
    {
        $test = new AddressBlock('123 Main St', 'Garden Grove', 'ON', 'L8N1L4', 'CA');
        $base = new \DOMDocument();
        $base->appendChild($test->getXML($base));
        $address = $base->getElementsByTagName('ADDRESS')->item(0);
        $street = $address->firstChild;
        $city = $street->nextSibling;
        $country = $city->nextSibling;
        $zip = $country->nextSibling;
        $state = $zip->nextSibling;
        $this->assertEquals('123 Main St', $street->textContent);
        $this->assertEquals('Garden Grove', $city->textContent);
        $this->assertEquals('ON', $state->textContent);
        $this->assertEquals('L8N1L4', $zip->textContent);
        $this->assertEquals('CA', $country->textContent);
    }

    public function testValidateStreetApostrophe()
    {
        $this->assertTrue(AddressBlock::validateStreet("123 L'Chateau St"));
    }

    public function testValidateStreetHyphen()
    {
        $this->assertTrue(AddressBlock::validateStreet('123 Marie-Dean St'));
    }

    public function testValidateStreetPeriod()
    {
        $this->assertTrue(AddressBlock::validateStreet('123 Main St Ste. 7'));
    }

    public function testValidateStreetPoundSign()
    {
        $this->assertTrue(AddressBlock::validateStreet('123 Main St #4'));
    }

    public function testValidateStreetForwardSlash()
    {
        $this->assertTrue(AddressBlock::validateStreet('123 1/4 Fantasy Lane'));
    }

    public function testValidateStreetAmpersand()
    {
        $this->assertTrue(AddressBlock::validateStreet('123 Jack & Jill Lane'));
    }

    public function testValidateGoodState()
    {
        $this->assertTrue(AddressBlock::validateState('PR'));
    }

    public function testValidateBadState()
    {
        $this->assertFalse(AddressBlock::validateState('PZ'));
    }

    public function testValidateGoodZip()
    {
        $this->assertTrue(AddressBlock::validateZip('92843'));
    }

    public function testValidateGoodZip4()
    {
        $this->assertTrue(AddressBlock::validateZip('92843-7777'));
    }

    public function testValidateGoodCanadianZip()
    {
        $this->assertTrue(AddressBlock::validateZip('A4K6I9'));
    }

    public function testValidateGoodCanadianZipWithSpace()
    {
        $this->assertTrue(AddressBlock::validateZip('A4K 6I9'));
    }

    public function testValidateBadZip1()
    {
        $this->assertFalse(AddressBlock::validateZip('A4K-6I9'));
    }

    public function testValidateBadZip2()
    {
        $this->assertFalse(AddressBlock::validateZip('9482'));
    }

    public function testValidateBadZip3()
    {
        $this->assertFalse(AddressBlock::validateZip('92843-333'));
    }

    public function testValidateGoodCountryUS()
    {
        $this->assertTrue(AddressBlock::validateCountry('US'));
    }
    
    public function testValidateGoodCountryCA()
    {
        $this->assertTrue(AddressBlock::validateCountry('CA'));
    }

    public function testValidateBadCountry()
    {
        $this->assertFalse(AddressBlock::validateCountry('AU'));
    }
}
