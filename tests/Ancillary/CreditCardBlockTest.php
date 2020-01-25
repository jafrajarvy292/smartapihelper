<?php

namespace jafrajarvy292\SmartAPIHelper\Ancillary;

use PHPUnit\Framework\TestCase;

class CreditCardBlockTest extends TestCase
{
    public function testSetName()
    {
        $card = new CreditCardBlock();
        $this->assertNull($card->setName(new PersonNameBlock('David', 'Testcase')));
    }

    public function testSetAddress()
    {
        $card = new CreditCardBlock();
        $this->assertNull($card->setAddress(new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843')));
    }

    public function testSetValidCardNumber()
    {
        $card = new CreditCardBlock();
        $this->assertNull($card->setCardNumber('4111111111111111'));
    }

    public function testSetInvalidCardNumber()
    {
        $card = new CreditCardBlock();
        $this->expectException(\Exception::class);
        $card->setCardNumber('4111111111111112');
    }

    public function testSetInvalidCardNumber2()
    {
        $card = new CreditCardBlock();
        $this->expectException(\Exception::class);
        $card->setCardNumber('4111111111sadfd112');
    }

    public function testSetInvalidCardNumber3()
    {
        $card = new CreditCardBlock();
        $this->expectException(\Exception::class);
        $card->setCardNumber('4111-1111-1111-1111');
    }

    public function testSetValidExpMonth()
    {
        $card = new CreditCardBlock();
        $this->assertNull($card->setExpMonth(01));
    }

    public function testSetValidExpMonth2()
    {
        $card = new CreditCardBlock();
        $this->assertNull($card->setExpMonth(1));
    }

    public function testSetValidExpMonth3()
    {
        $card = new CreditCardBlock();
        $this->assertNull($card->setExpMonth('01'));
    }

    public function testSetValidExpMonth4()
    {
        $card = new CreditCardBlock();
        $this->assertNull($card->setExpMonth('1'));
    }

    public function testSetInvalidExpMonth()
    {
        $card = new CreditCardBlock();
        $this->expectException(\Exception::class);
        $card->setExpMonth('13');
    }

    public function testSetInvalidExpMonth2()
    {
        $card = new CreditCardBlock();
        $this->expectException(\Exception::class);
        $card->setExpMonth('a');
    }

    public function testSetInvalidExpMonth3()
    {
        $card = new CreditCardBlock();
        $this->expectException(\Exception::class);
        $card->setExpMonth(15);
    }

    public function testSetValidExpYear()
    {
        $card = new CreditCardBlock();
        $this->assertNull($card->setExpYear(2000));
    }

    public function testSetValidExpYear2()
    {
        $card = new CreditCardBlock();
        $this->assertNull($card->setExpYear('2000'));
    }

    public function testSetInvalidExpYear()
    {
        $card = new CreditCardBlock();
        $this->expectException(\Exception::class);
        $card->setExpYear('a');
    }

    public function testSetInvalidExpYear2()
    {
        $card = new CreditCardBlock();
        $this->expectException(\Exception::class);
        $card->setExpYear('3016');
    }

    public function testSetValidCVV()
    {
        $card = new CreditCardBlock();
        $this->assertNull($card->setCVV('382'));
    }

    public function testSetValidCVV2()
    {
        $card = new CreditCardBlock();
        $this->assertNull($card->setCVV('3882'));
    }

    public function testSetInvalidCVV()
    {
        $card = new CreditCardBlock();
        $this->expectException(\Exception::class);
        $card->setCVV('33382');
    }

    public function testSetInvalidCVV2()
    {
        $card = new CreditCardBlock();
        $this->expectException(\Exception::class);
        $card->setCVV('32');
    }

    public function testSetInvalidCVV3()
    {
        $card = new CreditCardBlock();
        $this->expectException(\Exception::class);
        $card->setCVV('3a2');
    }

    public function testGetCardholderName()
    {
        $card = new CreditCardBlock();
        $card->setName(new PersonNameBlock('David', 'Testcase'));
        $this->assertInstanceOf(PersonNameBlock::class, $card->getCardholderName());
    }

    public function testGetCardholderNameNull()
    {
        $card = new CreditCardBlock();
        $this->assertNull($card->getCardholderName());
    }

    public function testGetCardholderAddress()
    {
        $card = new CreditCardBlock();
        $card->setAddress(new AddressBlock('123 Main St #3892', 'Garden Grove', 'CA', '92843'));
        $this->assertInstanceOf(AddressBlock::class, $card->getCardholderAddress());
    }

    public function testGetCardholderAddressNull()
    {
        $card = new CreditCardBlock();
        $this->assertNull($card->getCardholderAddress());
    }

    public function testGetCardNumber()
    {
        $card = new CreditCardBlock();
        $card->setCardNumber('4111111111111111');
        $this->assertIsString($card->getCardNumber());
    }

    public function testGetExpMonth()
    {
        $card = new CreditCardBlock();
        $card->setExpMonth('1');
        $this->assertRegExp('/^\d{2}$/', $card->getExpMonth());
    }

    public function testGetExpYear()
    {
        $card = new CreditCardBlock();
        $card->setExpYear(2020);
        $this->assertRegExp('/^\d{4}$/', $card->getExpYear());
    }

    public function testGetCVV()
    {
        $card = new CreditCardBlock();
        $card->setCVV('123');
        $this->assertRegExp('/^\d{3,4}$/', $card->getCVV());
    }

    public function testGetXMLAllFields()
    {
        $card = new CreditCardBlock();
        $card->setName(new PersonNameBlock('David', 'Testcase'));
        $card->setAddress(new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $card->setCardNumber('4111111111111111');
        $card->setExpMonth('12');
        $card->setExpYear('2030');
        $card->setCVV('123');
        $document = new \DOMDocument();
        $this->assertInstanceOf(\DOMNode::class, $card->getXML($document));
    }

    public function testGetXMLAllFields2()
    {
        $card = new CreditCardBlock();
        $card->setName(new PersonNameBlock('David', 'Testcase', 'Richardson', 'SR'));
        $card->setAddress(new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $card->setCardNumber('4111111111111111');
        $card->setExpMonth('12');
        $card->setExpYear('2030');
        $card->setCVV('123');
        $document = new \DOMDocument();
        $document->appendChild($card->getXML($document));
        $service_payment = $document->firstChild;
        
        $address = $service_payment->firstChild;
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

        $name = $address->nextSibling;
        $first = $name->firstChild;
        $last = $first->nextSibling;
        $middle = $last->nextSibling;
        $suffix = $middle->nextSibling;
        $this->assertEquals('David', $first->textContent);
        $this->assertEquals('Testcase', $last->textContent);
        $this->assertEquals('Richardson', $middle->textContent);
        $this->assertEquals('SR', $suffix->textContent);

        $card = $name->nextSibling;
        $number = $card->firstChild;
        $expiration = $number->nextSibling;
        $cvv = $expiration->nextSibling;
        $this->assertEquals('4111111111111111', $number->textContent);
        $this->assertEquals('2030-12', $expiration->textContent);
        $this->assertEquals('123', $cvv->textContent);
    }

    public function testGetXMLMissingName()
    {
        $card = new CreditCardBlock();
        $card->setAddress(new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $card->setCardNumber('4111111111111111');
        $card->setExpMonth('12');
        $card->setExpYear('2030');
        $card->setCVV('123');
        $document = new \DOMDocument();
        $document->appendChild($card->getXML($document));
        $this->assertEquals(0, $document->getElementsByTagName('NAME')->length);
    }

    public function testGetXMLMissingAddress()
    {
        $card = new CreditCardBlock();
        $card->setName(new PersonNameBlock('David', 'Testcase'));
        $card->setCardNumber('4111111111111111');
        $card->setExpMonth('12');
        $card->setExpYear('2030');
        $card->setCVV('123');
        $document = new \DOMDocument();
        $document->appendChild($card->getXML($document));
        $this->assertEquals(0, $document->getElementsByTagName('ADDRESS')->length);
    }

    public function testGetXMLMissingCardNumber()
    {
        $card = new CreditCardBlock();
        $card->setName(new PersonNameBlock('David', 'Testcase'));
        $card->setAddress(new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $card->setExpMonth('12');
        $card->setExpYear('2030');
        $card->setCVV('123');
        $document = new \DOMDocument();
        $document->appendChild($card->getXML($document));
        $this->assertEquals(0, $document->getElementsByTagName('ServicePaymentAccountIdentifier')->length);
    }

    public function testGetXMLMissingExpMonth()
    {
        $card = new CreditCardBlock();
        $card->setName(new PersonNameBlock('David', 'Testcase'));
        $card->setAddress(new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $card->setCardNumber('4111111111111111');
        $card->setExpYear('2030');
        $card->setCVV('123');
        $document = new \DOMDocument();
        $document->appendChild($card->getXML($document));
        $this->assertEquals(0, $document->
            getElementsByTagName('ServicePaymentCreditAccountExpirationDate')->length);
    }

    public function testGetXMLMissingExpYear()
    {
        $card = new CreditCardBlock();
        $card->setName(new PersonNameBlock('David', 'Testcase'));
        $card->setAddress(new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $card->setCardNumber('4111111111111111');
        $card->setExpMonth('12');
        $card->setCVV('123');
        $document = new \DOMDocument();
        $document->appendChild($card->getXML($document));
        $this->assertEquals(0, $document->
            getElementsByTagName('ServicePaymentCreditAccountExpirationDate')->length);
    }

    public function testGetXMLMissingCVV()
    {
        $card = new CreditCardBlock();
        $card->setName(new PersonNameBlock('David', 'Testcase'));
        $card->setAddress(new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $card->setCardNumber('4111111111111111');
        $card->setExpMonth('12');
        $card->setExpYear('2030');
        $document = new \DOMDocument();
        $document->appendChild($card->getXML($document));
        $this->assertEquals(0, $document->
            getElementsByTagName('ServicePaymentSecondaryCreditAccountIdentifier')->length);
    }

    public function testValidateCardNumberPass()
    {
        $this->assertTrue(CreditCardBlock::validateCardNumber('4111111111111111'));
    }

    public function testValidateCardNumberFail()
    {
        $this->assertFalse(CreditCardBlock::validateCardNumber('41111116456'));
    }

    public function testValidateCardNumberFail2()
    {
        $this->assertFalse(CreditCardBlock::validateCardNumber('dasdf2343'));
    }

    public function testValidateCardNumberFail3()
    {
        $this->assertFalse(CreditCardBlock::validateCardNumber(''));
    }
}
