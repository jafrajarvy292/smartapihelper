<?php

namespace jafrajarvy292\SmartAPIHelper\Ancillary;

use PHPUnit\Framework\TestCase;

class PhoneNumberBlockTest extends TestCase
{
    public function testConstructorValid()
    {
        $this->assertInstanceOf(
            PhoneNumberBlock::class,
            new PhoneNumberBlock('8004445555')
        );

        $this->assertInstanceOf(
            PhoneNumberBlock::class,
            new PhoneNumberBlock('800-444-5555')
        );

        $this->assertInstanceOf(
            PhoneNumberBlock::class,
            new PhoneNumberBlock('(800) 444-5555')
        );

        $this->assertInstanceOf(
            PhoneNumberBlock::class,
            new PhoneNumberBlock('800.444.5555')
        );

        $this->assertInstanceOf(
            PhoneNumberBlock::class,
            new PhoneNumberBlock('800 444 5555')
        );

        $this->assertInstanceOf(
            PhoneNumberBlock::class,
            new PhoneNumberBlock('8004445555', '4893')
        );

        $this->assertInstanceOf(
            PhoneNumberBlock::class,
            new PhoneNumberBlock('8004445555', '4892', 'Work')
        );

        $this->assertInstanceOf(
            PhoneNumberBlock::class,
            new PhoneNumberBlock('8004445555', '489', 'Other', 'Pay Phone')
        );
    }

    public function testConstructorInvalid1()
    {
        $this->expectException(\Exception::class);
        new PhoneNumberBlock('489234');
    }

    public function testConstructorInvalid2()
    {
        $this->expectException(\Exception::class);
        new PhoneNumberBlock('');
    }

    public function testConstructorInvalid3()
    {
        $this->expectException(\Exception::class);
        new PhoneNumberBlock('ddddd');
    }

    public function testConstructorInvalid4()
    {
        $this->expectException(\Exception::class);
        new PhoneNumberBlock('555667777', 'x4334');
    }

    public function testConstructorInvalid5()
    {
        $this->expectException(\Exception::class);
        new PhoneNumberBlock('555667777', '4334', 'PayPhone');
    }

    public function testGetNumber()
    {
        $phone = new PhoneNumberBlock('4445556666');
        $this->assertRegExp('/^4445556666$/', $phone->getNumber());
    }

    public function testGetNumber2()
    {
        $phone = new PhoneNumberBlock('(444) 555-6666');
        $this->assertRegExp('/^4445556666$/', $phone->getNumber());
    }

    public function testGetExt()
    {
        $phone = new PhoneNumberBlock('4445556666', '333');
        $this->assertRegExp('/^333$/', $phone->getExt());
    }

    public function testGetType()
    {
        $phone = new PhoneNumberBlock('4445556666', '333');
        $this->assertRegExp('/^Home$/', $phone->getType());
    }

    public function testGetType2()
    {
        $phone = new PhoneNumberBlock('4445556666', '333', 'Mobile');
        $this->assertRegExp('/^Mobile$/', $phone->getType());
    }

    public function testGetDescription()
    {
        $phone = new PhoneNumberBlock('4445556666', '333', 'Other', 'Pay Phone');
        $this->assertRegExp('/^Pay Phone$/', $phone->getDescription());
    }

    public function testGetXML()
    {
        $phone = new PhoneNumberBlock('4445556666', '333', 'Other', 'Pay Phone');
        $document = new \DOMDocument();
        $this->assertInstanceOf(\DOMNode::class, $phone->getXML($document));
    }

    public function testGetXMLNumber()
    {
        $phone = new PhoneNumberBlock('4445556666');
        $base = new \DOMDocument();
        $base->appendChild($phone->getXML($base));
        $contact_point = $base->getElementsByTagName('CONTACT_POINT')->item(0);
        $contact_point_phone = $contact_point->firstChild;
        $number = $contact_point_phone->firstChild;
        $type = $contact_point_phone->nextSibling->firstChild;
        $this->assertEquals('4445556666', $number->textContent);
        $this->assertEquals('Home', $type->textContent);
    }

    public function testGetXMLNumberExt()
    {
        $phone = new PhoneNumberBlock('4445556666', '48934');
        $base = new \DOMDocument();
        $base->appendChild($phone->getXML($base));
        $contact_point = $base->getElementsByTagName('CONTACT_POINT')->item(0);
        $contact_point_phone = $contact_point->firstChild;
        $ext = $contact_point_phone->firstChild;
        $number = $ext->nextSibling;
        $type = $contact_point_phone->nextSibling->firstChild;
        $this->assertEquals('48934', $ext->textContent);
        $this->assertEquals('4445556666', $number->textContent);
        $this->assertEquals('Home', $type->textContent);
    }

    public function testGetXMLNumberExtType()
    {
        $phone = new PhoneNumberBlock('4445556666', '48934', 'Mobile');
        $base = new \DOMDocument();
        $base->appendChild($phone->getXML($base));
        $contact_point = $base->getElementsByTagName('CONTACT_POINT')->item(0);
        $contact_point_phone = $contact_point->firstChild;
        $ext = $contact_point_phone->firstChild;
        $number = $ext->nextSibling;
        $type = $contact_point_phone->nextSibling->firstChild;
        $this->assertEquals('48934', $ext->textContent);
        $this->assertEquals('4445556666', $number->textContent);
        $this->assertEquals('Mobile', $type->textContent);
    }

    public function testGetXMLNumberExtTypeDesc()
    {
        $phone = new PhoneNumberBlock('4445556666', '48934', 'Mobile', 'Description Here');
        $base = new \DOMDocument();
        $base->appendChild($phone->getXML($base));
        $contact_point = $base->getElementsByTagName('CONTACT_POINT')->item(0);
        $contact_point_phone = $contact_point->firstChild;
        $ext = $contact_point_phone->firstChild;
        $number = $ext->nextSibling;
        $type = $contact_point_phone->nextSibling->firstChild;
        $description = $type->nextSibling;
        $this->assertEquals('48934', $ext->textContent);
        $this->assertEquals('4445556666', $number->textContent);
        $this->assertEquals('Mobile', $type->textContent);
        $this->assertEquals('Description Here', $description->textContent);
    }

    public function testGetXMLNumberExtTypeDesc2()
    {
        $phone = new PhoneNumberBlock('4445556666', '48934', 'Other', 'Description Here');
        $base = new \DOMDocument();
        $base->appendChild($phone->getXML($base));
        $contact_point = $base->getElementsByTagName('CONTACT_POINT')->item(0);
        $contact_point_phone = $contact_point->firstChild;
        $ext = $contact_point_phone->firstChild;
        $number = $ext->nextSibling;
        $type = $contact_point_phone->nextSibling->firstChild;
        $description = $type->nextSibling;
        $this->assertEquals('48934', $ext->textContent);
        $this->assertEquals('4445556666', $number->textContent);
        $this->assertEquals('Other', $type->textContent);
        $this->assertEquals('Description Here', $description->textContent);
    }

    public function testGetXMLNumberType()
    {
        $phone = new PhoneNumberBlock('4445556666', '', 'Work');
        $base = new \DOMDocument();
        $base->appendChild($phone->getXML($base));
        $contact_point = $base->getElementsByTagName('CONTACT_POINT')->item(0);
        $contact_point_phone = $contact_point->firstChild;
        $number = $contact_point_phone->firstChild;
        $type = $contact_point_phone->nextSibling->firstChild;
        $this->assertEquals('4445556666', $number->textContent);
        $this->assertEquals('Work', $type->textContent);
    }

    public function testGetXMLNumberTypeDesc()
    {
        $phone = new PhoneNumberBlock('4445556666', '', 'Work', 'DescriptionHere');
        $base = new \DOMDocument();
        $base->appendChild($phone->getXML($base));
        $contact_point = $base->getElementsByTagName('CONTACT_POINT')->item(0);
        $contact_point_phone = $contact_point->firstChild;
        $number = $contact_point_phone->firstChild;
        $type = $contact_point_phone->nextSibling->firstChild;
        $description = $type->nextSibling;
        $this->assertEquals('4445556666', $number->textContent);
        $this->assertEquals('Work', $type->textContent);
        $this->assertEquals('DescriptionHere', $description->textContent);
    }

    public function testValidateTypeValid()
    {
        $this->assertTrue(PhoneNumberBlock::validateType('Home'));
        $this->assertTrue(PhoneNumberBlock::validateType('Mobile'));
        $this->assertTrue(PhoneNumberBlock::validateType('Work'));
        $this->assertTrue(PhoneNumberBlock::validateType('Other'));
    }

    public function testValidateTypeInvalid()
    {
        $this->assertFalse(PhoneNumberBlock::validateType('Fax'));
    }

    public function testValidateNumberValid()
    {
        $this->assertTrue(PhoneNumberBlock::validateNumber('6667778888'));
        $this->assertTrue(PhoneNumberBlock::validateNumber('(666) 777-8888'));
        $this->assertTrue(PhoneNumberBlock::validateNumber('666-777-8888'));
        $this->assertTrue(PhoneNumberBlock::validateNumber('666 777 8888'));
        $this->assertTrue(PhoneNumberBlock::validateNumber('666.777.8888'));
    }

    public function testValidateNumberInvalid()
    {
        $this->assertFalse(PhoneNumberBlock::validateNumber(''));
        $this->assertFalse(PhoneNumberBlock::validateNumber('a'));
        $this->assertFalse(PhoneNumberBlock::validateNumber(' '));
        $this->assertFalse(PhoneNumberBlock::validateNumber('444-444-555'));
    }

    public function testValidateExtValid()
    {
        $this->assertTrue(PhoneNumberBlock::validateExt('348934'));
        $this->assertTrue(PhoneNumberBlock::validateExt('34893344'));
    }

    public function testValidateExtInvalid()
    {
        $this->assertFalse(PhoneNumberBlock::validateExt(''));
        $this->assertFalse(PhoneNumberBlock::validateExt(' '));
        $this->assertFalse(PhoneNumberBlock::validateExt('x434'));
    }
}
