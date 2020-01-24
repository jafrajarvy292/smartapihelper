<?php

/**
 * Unit testing for the class that generates the Consumer Credit request files.
 */

namespace jafrajarvy292\SmartAPIHelper\RequestGenerator;

use jafrajarvy292\SmartAPIHelper\Ancillary\AddressBlock;
use jafrajarvy292\SmartAPIHelper\Ancillary\CreditCardBlock;
use jafrajarvy292\SmartAPIHelper\Ancillary\PersonNameBlock;
use jafrajarvy292\SmartAPIHelper\Ancillary\PhoneNumberBlock;
use jafrajarvy292\SmartAPIHelper\Ancillary\ResponseFormats;
use jafrajarvy292\SmartAPIHelper\RequestData\ConsumerCreditRequestData;
use PHPUnit\Framework\TestCase;

class ConsumerCreditRequestGeneratorTest extends TestCase
{
    public function testOutputXMLForSubmitXMLVersion()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data, '2.32');
        $string = $object->outputXMLString();
        $this->assertRegExp('/version="2.32"/', $string);
    }

    public function testOutputXMLForSubmitXMLEncoding()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data, '1.0', 'WINDOWS-1250');
        $string = $object->outputXMLString();
        $this->assertRegExp('/encoding="WINDOWS-1250"/', $string);
    }

    public function testOutputXMLForSubmitDataVersion()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $data->setDataVersion('098765234567');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '/P1:MESSAGE/P1:ABOUT_VERSIONS/P1:ABOUT_VERSION/P1:DataVersionIdentifier'
        )->item(0);
        $this->assertEquals('098765234567', $node->textContent);
    }

    public function testConstructorDefault()
    {
        $data = new ConsumerCreditRequestData();
        $object = new ConsumerCreditRequestGenerator($data);
        $this->assertInstanceOf(ConsumerCreditRequestGenerator::class, $object);
    }

    public function testOutputXMLForSubmitMissingRequestType()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $object = new ConsumerCreditRequestGenerator($data);
        $this->expectException(\Exception::class);
        $object->outputXMLString();
    }

    public function testOutputXMLForSubmitMissingBorrowerName()
    {
        $data = new ConsumerCreditRequestData();
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $this->expectException(\Exception::class);
        $object->outputXMLString();
    }

    public function testOutputXMLForSubmitMissingBorrowerSSN()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $this->expectException(\Exception::class);
        $object->outputXMLString();
    }

    public function testOutputXMLForSubmitMissingBorrowerAddress()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $this->expectException(\Exception::class);
        $object->outputXMLString();
    }

    public function testOutputXMLForSubmitBorrowerPartyNode()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party1"]'
        );
        $this->assertEquals(1, $node->length);
    }

    public function testOutputXMLForSubmitSubjectProperty()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $data->setSubjectPropAdd(new AddressBlock('4938 Subject Drive','Santa Ana', 'FL', '00093', 'CA'));
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '/P1:MESSAGE/P1:DEAL_SETS/P1:DEAL_SET/P1:DEALS/P1:DEAL/P1:COLLATERALS/P1:COLLATERAL/' .
            'P1:SUBJECT_PROPERTY/P1:ADDRESS'
        )->item(0);
        $this->assertEquals(
            '4938 Subject Drive',
            $node->getElementsByTagName('AddressLineText')->item(0)->textContent
        );
        $this->assertEquals(
            'Santa Ana',
            $node->getElementsByTagName('CityName')->item(0)->textContent
        );
        $this->assertEquals(
            'FL',
            $node->getElementsByTagName('StateCode')->item(0)->textContent
        );
        $this->assertEquals(
            '00093',
            $node->getElementsByTagName('PostalCode')->item(0)->textContent
        );
        $this->assertEquals(
            'CA',
            $node->getElementsByTagName('CountryCode')->item(0)->textContent
        );
    }

    public function testOutputXMLForSubmitNoSubjectProperty()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '/P1:MESSAGE/P1:DEAL_SETS/P1:DEAL_SET/P1:DEALS/P1:DEAL/P1:COLLATERALS/P1:COLLATERAL/' .
            'P1:SUBJECT_PROPERTY/P1:ADDRESS'
        )->item(0);
        $this->assertNull($node);
    }

    public function testOutputXMLForSubmitLoanID()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $data->setLoanID('56ifj934td3');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '/P1:MESSAGE/P1:DEAL_SETS/P1:DEAL_SET/P1:DEALS/P1:DEAL/P1:LOANS/P1:LOAN/P1:LOAN_IDENTIFIERS/' .
            'P1:LOAN_IDENTIFIER/P1:LoanIdentifier'
        )->item(0);
        $this->assertEquals('56ifj934td3', $node->textContent);
    }

    public function testOutputXMLForSubmitNoLoanID()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '/P1:MESSAGE/P1:DEAL_SETS/P1:DEAL_SET/P1:DEALS/P1:DEAL/P1:LOANS/P1:LOAN/P1:LOAN_IDENTIFIERS/' .
            'P1:LOAN_IDENTIFIER/P1:LoanIdentifier'
        )->item(0);
        $this->assertNull($node);
    }

    public function testOutputXMLForSubmitBorrowerPhoneNoEmail()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $data->setPhone('b', new PhoneNumberBlock('5556667777', '4399', 'Work', 'DescriptionHere'));
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party1"]/P1:INDIVIDUAL/P1:CONTACT_POINTS/P1:CONTACT_POINT'
        )->item(0);
        $number = $node->getElementsByTagName('ContactPointTelephoneValue')->item(0);
        $extension = $node->getElementsByTagName('ContactPointTelephoneExtensionValue')->item(0);
        $type = $node->getElementsByTagName('ContactPointRoleType')->item(0);
        $description = $node->getElementsByTagName('ContactPointRoleTypeOtherDescription')->item(0);
        $this->assertEquals('5556667777', $number->textContent);
        $this->assertEquals('4399', $extension->textContent);
        $this->assertEquals('DescriptionHere', $description->textContent);
    }

    public function testOutputXMLForSubmitNoBorrowerPhoneNoEmail()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party1"]/P1:INDIVIDUAL/P1:CONTACT_POINTS'
        )->item(0);
        $this->assertNull($node);
    }

    public function testOutputXMLForSubmitBorrowerEmailNoPhone()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $data->setEmail('b', 'testing@test.com');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party1"]/P1:INDIVIDUAL/P1:CONTACT_POINTS/P1:CONTACT_POINT/' .
            'P1:CONTACT_POINT_EMAIL/P1:ContactPointEmailValue'
        )->item(0);
        $this->assertEquals('testing@test.com', $node->textContent);
    }

    public function testOutputXMLForSubmitBorrowerEmailBorrowerPhone()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $data->setEmail('b', 'testing@test.com');
        $data->setPhone('b', new PhoneNumberBlock('5556667777', '4399', 'Work', 'DescriptionHere'));
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party1"]/P1:INDIVIDUAL/P1:CONTACT_POINTS/P1:CONTACT_POINT'
        );
        $this->assertEquals(2, $node->length);
    }

    public function testOutputXMLForSubmitBorrowerFullName()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase', 'R', 'JR'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party1"]/P1:INDIVIDUAL/P1:NAME'
        )->item(0);
        $first = $node->getElementsByTagName('FirstName')->item(0);
        $last = $node->getElementsByTagName('LastName')->item(0);
        $middle = $node->getElementsByTagName('MiddleName')->item(0);
        $suffix = $node->getElementsByTagName('SuffixName')->item(0);
        $this->assertEquals('David', $first->textContent);
        $this->assertEquals('Testcase', $last->textContent);
        $this->assertEquals('R', $middle->textContent);
        $this->assertEquals('JR', $suffix->textContent);
    }

    public function testOutputXMLForSubmitBorrowerMinimumName()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party1"]/P1:INDIVIDUAL/P1:NAME'
        )->item(0);
        $first = $node->getElementsByTagName('FirstName')->item(0);
        $last = $node->getElementsByTagName('LastName')->item(0);
        $middle = $node->getElementsByTagName('MiddleName')->item(0);
        $suffix = $node->getElementsByTagName('SuffixName')->item(0);
        $this->assertEquals('David', $first->textContent);
        $this->assertEquals('Testcase', $last->textContent);
        $this->assertNull($middle);
        $this->assertNull($suffix);
    }

    public function testOutputXMLForSubmitMailingAddress()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $data->setAddress('b', new AddressBlock('3892 Mailing Drive', 'New York', 'NY', '66885'), 'Mailing');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party1"]/P1:ADDRESSES/P1:ADDRESS[P1:AddressType/text()="Mailing"]'
        )->item(0);
        $street = $node->getElementsByTagName('AddressLineText')->item(0);
        $city = $node->getElementsByTagName('CityName')->item(0);
        $country = $node->getElementsByTagName('CountryCode')->item(0);
        $zip = $node->getElementsByTagName('PostalCode')->item(0);
        $state = $node->getElementsByTagName('StateCode')->item(0);
        $this->assertEquals('3892 Mailing Drive', $street->textContent);
        $this->assertEquals('New York', $city->textContent);
        $this->assertEquals('US', $country->textContent);
        $this->assertEquals('66885', $zip->textContent);
        $this->assertEquals('NY', $state->textContent);
    }

    public function testOutputXMLForSubmitNoMailingAddress()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party1"]/P1:ADDRESSES/P1:ADDRESS[P1:AddressType/text()="Mailing"]'
        )->item(0);
        $this->assertNull($node);
    }

    public function testOutputXMLForSubmitBorrowerDOB()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $data->setDOB('b', '01-02-1980');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party1"]/P1:ROLES/P1:ROLE/P1:BORROWER/P1:BORROWER_DETAIL/' .
            'P1:BorrowerBirthDate'
        )->item(0);
        $this->assertEquals('1980-01-02', $node->textContent);
    }

    public function testOutputXMLForSubmitNoBorrowerDOB()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party1"]/P1:ROLES/P1:ROLE/P1:BORROWER/P1:BORROWER_DETAIL/' .
            'P1:BorrowerBirthDate'
        )->item(0);
        $this->assertNull($node);
    }

    public function testOutputXMLForSubmitBorrowerCurrentAddress()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'), 'Current');
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party1"]/P1:ROLES/P1:ROLE/P1:BORROWER/P1:RESIDENCES/P1:RESIDENCE/' .
            'P1:RESIDENCE_DETAIL/P1:BorrowerResidencyType[text()="Current"]'
        );
        $this->assertEquals(1, $node->length);
    }

    public function testOutputXMLForSubmitBorrowerNoCurrentAddress()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $this->expectException(\Exception::class);
        $object->outputXMLString();
    }

    public function testOutputXMLForSubmitBorrowerPriorAddress()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $data->setAddress('b', new AddressBlock('2938 Prior Lane', 'Santa Ana', 'CA', '92705'), 'Prior');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party1"]/P1:ROLES/P1:ROLE/P1:BORROWER/P1:RESIDENCES/P1:RESIDENCE/' .
            'P1:RESIDENCE_DETAIL/P1:BorrowerResidencyType[text()="Prior"]'
        );
        $this->assertEquals(1, $node->length);
    }

    public function testOutputXMLForSubmitBorrowerNoPriorAddress()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party1"]/P1:ROLES/P1:ROLE/P1:BORROWER/P1:RESIDENCES/P1:RESIDENCE/' .
            'P1:RESIDENCE_DETAIL/P1:BorrowerResidencyType[text()="Prior"]'
        );
        $this->assertEquals(0, $node->length);
    }

    public function testOutputXMLForSubmitBorrowerSSN()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party1"]/P1:TAXPAYER_IDENTIFIERS/P1:TAXPAYER_IDENTIFIER/' .
            'P1:TaxpayerIdentifierValue'
        )->item(0);
        $this->assertEquals('000000001', $node->textContent);
    }

    public function testOutputXMLForSubmitNoBorrowerSSN()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $this->expectException(\Exception::class);
        $object->outputXMLString();
    }

    public function testOutputXMLForSubmitCoborrowerPresent()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party2"]'
        );
        $this->assertEquals(1, $node->length);
    }

    public function testOutputXMLForSubmitCoborrowerMissingSSN()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $this->expectException(\Exception::class);
        $object->outputXMLString();
    }

    public function testOutputXMLForSubmitCoborrowerMissingCurrentAddress()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $this->expectException(\Exception::class);
        $object->outputXMLString();
    }

    public function testOutputXMLForSubmitCoborrowerMissingName()
    {
        /* There isn't such a thing as missing the coborrower name. The presence of the coborrower name is
        the flag to indicate whether they exist. No coborrower name means no coborrower is present */
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $this->assertIsString($object->outputXMLString());
    }

    public function testOutputXMLForSubmitCoborrowerPhone()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));    
        $data->setPhone('c', new PhoneNumberBlock('8008882222'));
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party2"]/P1:INDIVIDUAL/P1:CONTACT_POINTS/P1:CONTACT_POINT/' .
            'P1:CONTACT_POINT_TELEPHONE/P1:ContactPointTelephoneValue'
        )->item(0);
        $this->assertEquals('8008882222', $node->textContent);
    }

    public function testOutputXMLForSubmitCoborrowerNoPhone()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));    
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party2"]/P1:INDIVIDUAL/P1:CONTACT_POINTS/P1:CONTACT_POINT/' .
            'P1:CONTACT_POINT_TELEPHONE'
        );
        $this->assertEquals(0, $node->length);
    }

    public function testOutputXMLForSubmitCoborrowerEmail()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));    
        $data->setEmail('c', 'jane@example.com');
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party2"]/P1:INDIVIDUAL/P1:CONTACT_POINTS/P1:CONTACT_POINT/' .
            'P1:CONTACT_POINT_EMAIL/P1:ContactPointEmailValue'
        )->item(0);
        $this->assertEquals('jane@example.com', $node->textContent);
    }

    public function testOutputXMLForSubmitCoborrowerNoEmail()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));    
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party2"]/P1:INDIVIDUAL/P1:CONTACT_POINTS/P1:CONTACT_POINT/' .
            'P1:CONTACT_POINT_EMAIL'
        );
        $this->assertEquals(0, $node->length);
    }

    public function testOutputXMLForSubmitCoborrowerName()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));    
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party2"]/P1:INDIVIDUAL/P1:NAME'
        );
        $this->assertEquals(1, $node->length);
    }

    public function testOutputXMLForSubmitCoborrowerMailingAddress()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));    
        $data->setRequestType('Submit');
        $data->setAddress('c', new AddressBlock('3892 Mailing Drive', 'New York', 'NY', '66885'), 'Mailing');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party2"]/P1:ADDRESSES/P1:ADDRESS[P1:AddressType/text()="Mailing"]'
        );
        $this->assertEquals(1, $node->length);
    }

    public function testOutputXMLForSubmitCoborrowerNoMailingAddress()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));    
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party2"]/P1:ADDRESSES/P1:ADDRESS[P1:AddressType/text()="Mailing"]'
        );
        $this->assertEquals(0, $node->length);
    }

    public function testOutputXMLForSubmitCoborrowerDOB()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));    
        $data->setRequestType('Submit');
        $data->setDOB('c', '01-15-2000');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party2"]/P1:ROLES/P1:ROLE/P1:BORROWER/P1:BORROWER_DETAIL/' .
            'P1:BorrowerBirthDate'
        );
        $this->assertEquals(1, $node->length);
    }

    public function testOutputXMLForSubmitCoborrowerNoDOB()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));    
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party2"]/P1:ROLES/P1:ROLE/P1:BORROWER/P1:BORROWER_DETAIL/' .
            'P1:BorrowerBirthDate'
        );
        $this->assertEquals(0, $node->length);
    }

    public function testOutputXMLForSubmitCoborrowerCurrentAddress()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));    
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party2"]/P1:ROLES/P1:ROLE/P1:BORROWER/P1:RESIDENCES/P1:RESIDENCE/' .
            'P1:RESIDENCE_DETAIL/P1:BorrowerResidencyType[text()="Current"]'
        );
        $this->assertEquals(1, $node->length);
    }

    public function testOutputXMLForSubmitCoborrowerNoCurrentAddress()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $this->expectException(\Exception::class);
        $object->outputXMLString();
    }

    public function testOutputXMLForSubmitCoborrowerPreviousAddress()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));
        $data->setAddress('c', new AddressBlock('892 Prior Road', 'Santa Ana', 'NY', '92888'), 'Prior');
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party2"]/P1:ROLES/P1:ROLE/P1:BORROWER/P1:RESIDENCES/P1:RESIDENCE/' .
            'P1:RESIDENCE_DETAIL/P1:BorrowerResidencyType[text()="Prior"]'
        );
        $this->assertEquals(1, $node->length);
    }

    public function testOutputXMLForSubmitCoborrowerNoPreviousAddress()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party2"]/P1:ROLES/P1:ROLE/P1:BORROWER/P1:RESIDENCES/P1:RESIDENCE/' .
            'P1:RESIDENCE_DETAIL/P1:BorrowerResidencyType[text()="Prior"]'
        );
        $this->assertEquals(0, $node->length);
    }

    public function testOutputXMLForSubmitCoborrowerSSN()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party2"]/P1:TAXPAYER_IDENTIFIERS/P1:TAXPAYER_IDENTIFIER/' .
            'P1:TaxpayerIdentifierValue'
        );
        $this->assertEquals(1, $node->length);
    }

    public function testOutputXMLForSubmitBorrowerRelationship()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:RELATIONSHIP[@P2:arcrole="urn:fdc:Meridianlink.com:2017:mortgage/' .
            'PARTY_IsVerifiedBy_SERVICE" and @P2:from="Party1" and @P2:to="Service1"]'
        );
        $this->assertEquals(1, $node->length);
    }

    public function testOutputXMLForSubmitCoBorrowerRelationship()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:RELATIONSHIP[@P2:arcrole="urn:fdc:Meridianlink.com:2017:mortgage/' .
            'PARTY_IsVerifiedBy_SERVICE" and @P2:from="Party2" and @P2:to="Service1"]'
        );
        $this->assertEquals(1, $node->length);
    }

    public function testOutputXMLForSubmitSubjectPropertyAddressRelationship()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setSubjectPropAdd(new AddressBlock('5893 Property Court', 'Santa Ana', 'CA', '92843'));
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:RELATIONSHIP[@P2:arcrole="urn:fdc:Meridianlink.com:2017:mortgage/' .
            'PROPERTY_IsVerifiedBy_SERVICE" and @P2:from="Property1" and @P2:to="Service1"]'
        );
        $this->assertEquals(1, $node->length);
    }

    public function testOutputXMLForSubmitEquifaxCreditTrueScoreFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $data->setEquifaxOptions(true, false);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/P1:CREDIT_REQUEST_DATA/' .
            'P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedEquifaxIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('RequestEquifaxScore')->item(0)->textContent
        );
    }

    public function testOutputXMLForSubmitEquifaxCreditTrueScoreTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $data->setEquifaxOptions(true, true);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/P1:CREDIT_REQUEST_DATA/' .
            'P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedEquifaxIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('RequestEquifaxScore')->item(0)->textContent
        );
    }

    public function testOutputXMLForSubmitEquifaxCreditFalseScoreTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $data->setEquifaxOptions(false, true);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/P1:CREDIT_REQUEST_DATA/' .
            'P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedEquifaxIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('RequestEquifaxScore')->item(0)->textContent
        );
    }

    public function testOutputXMLForSubmitEquifaxCreditFalseScoreFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $data->setEquifaxOptions(false, false);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/P1:CREDIT_REQUEST_DATA/' .
            'P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedEquifaxIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('RequestEquifaxScore')->item(0)->textContent
        );
    }

    public function testOutputXMLForSubmitExperianCreditTrueScoreTrueFraudTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $data->setExperianOptions(true, true, true);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/P1:CREDIT_REQUEST_DATA/' .
            'P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedExperianIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('RequestExperianScore')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('RequestExperianFraud')->item(0)->textContent
        );
    }
    
    public function testOutputXMLForSubmitExperianCreditTrueScoreTrueFraudFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $data->setExperianOptions(true, true, false);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/P1:CREDIT_REQUEST_DATA/' .
            'P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedExperianIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('RequestExperianScore')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('RequestExperianFraud')->item(0)->textContent
        );
    }

    public function testOutputXMLForSubmitExperianCreditTrueScoreFalseFraudTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $data->setExperianOptions(true, false, true);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/P1:CREDIT_REQUEST_DATA/' .
            'P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedExperianIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('RequestExperianScore')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('RequestExperianFraud')->item(0)->textContent
        );
    }

    public function testOutputXMLForSubmitExperianCreditTrueScoreFalseFraudFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $data->setExperianOptions(true, false, false);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/P1:CREDIT_REQUEST_DATA/' .
            'P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedExperianIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('RequestExperianScore')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('RequestExperianFraud')->item(0)->textContent
        );
    }

    public function testOutputXMLForSubmitExperianCreditFalseScoreTrueFraudTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $data->setExperianOptions(false, true, true);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/P1:CREDIT_REQUEST_DATA/' .
            'P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedExperianIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('RequestExperianScore')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('RequestExperianFraud')->item(0)->textContent
        );
    }

    public function testOutputXMLForSubmitExperianCreditFalseScoreTrueFraudFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $data->setExperianOptions(false, true, false);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/P1:CREDIT_REQUEST_DATA/' .
            'P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedExperianIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('RequestExperianScore')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('RequestExperianFraud')->item(0)->textContent
        );
    }

    public function testOutputXMLForSubmitExperianCreditFalseScoreFalseFraudTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $data->setExperianOptions(false, true, true);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/P1:CREDIT_REQUEST_DATA/' .
            'P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedExperianIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('RequestExperianScore')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('RequestExperianFraud')->item(0)->textContent
        );
    }

    public function testOutputXMLForSubmitExperianCreditCreditFalseScoreFalseFraudFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $data->setExperianOptions(false, false, false);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/P1:CREDIT_REQUEST_DATA/' .
            'P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedExperianIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('RequestExperianScore')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('RequestExperianFraud')->item(0)->textContent
        );
    }

    public function testOutputXMLForSubmitTransUnionCreditTrueScoreTrueFraudTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $data->setTransUnionOptions(true, true, true);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/P1:CREDIT_REQUEST_DATA/' .
            'P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedTransUnionIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('RequestTransUnionScore')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('RequestTransUnionFraud')->item(0)->textContent
        );
    }
    
    public function testOutputXMLForSubmitTransUnionCreditTrueScoreTrueFraudFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $data->setTransUnionOptions(true, true, false);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/P1:CREDIT_REQUEST_DATA/' .
            'P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedTransUnionIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('RequestTransUnionScore')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('RequestTransUnionFraud')->item(0)->textContent
        );
    }

    public function testOutputXMLForSubmitTransUnionCreditTrueScoreFalseFraudTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $data->setTransUnionOptions(true, false, true);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/P1:CREDIT_REQUEST_DATA/' .
            'P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedTransUnionIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('RequestTransUnionScore')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('RequestTransUnionFraud')->item(0)->textContent
        );
    }

    public function testOutputXMLForSubmitTransUnionCreditTrueScoreFalseFraudFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $data->setTransUnionOptions(true, false, false);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/P1:CREDIT_REQUEST_DATA/' .
            'P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedTransUnionIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('RequestTransUnionScore')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('RequestTransUnionFraud')->item(0)->textContent
        );
    }

    public function testOutputXMLForSubmitTransUnionCreditFalseScoreTrueFraudTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $data->setTransUnionOptions(false, true, true);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/P1:CREDIT_REQUEST_DATA/' .
            'P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedTransUnionIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('RequestTransUnionScore')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('RequestTransUnionFraud')->item(0)->textContent
        );
    }

    public function testOutputXMLForSubmitTransUnionCreditFalseScoreTrueFraudFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $data->setTransUnionOptions(false, true, false);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/P1:CREDIT_REQUEST_DATA/' .
            'P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedTransUnionIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('RequestTransUnionScore')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('RequestTransUnionFraud')->item(0)->textContent
        );
    }

    public function testOutputXMLForSubmitTransUnionCreditFalseScoreFalseFraudTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $data->setTransUnionOptions(false, true, true);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/P1:CREDIT_REQUEST_DATA/' .
            'P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedTransUnionIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('RequestTransUnionScore')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('RequestTransUnionFraud')->item(0)->textContent
        );
    }

    public function testOutputXMLForSubmitTransUnionCreditCreditFalseScoreFalseFraudFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $data->setTransUnionOptions(false, false, false);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/P1:CREDIT_REQUEST_DATA/' .
            'P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedTransUnionIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('RequestTransUnionScore')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('RequestTransUnionFraud')->item(0)->textContent
        );
    }

    public function testOutputXMLForSubmitRequestType()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/P1:CREDIT_REQUEST_DATA/' .
            'P1:CREDIT_REQUEST_DATA_DETAIL/P1:CreditReportRequestActionType'
        )->item(0);
        $this->assertEquals('Submit', $node->textContent);
    }

    public function testOutputXMLForSubmitCreditCard()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $card = new CreditCardBlock();
        $card->setCardNumber('4111111111111111');
        $card->setName(new PersonNameBlock('David', 'Testcase'));
        $card->setAddress(new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $card->setExpMonth('01');
        $card->setExpYear('2020');
        $data->setCreditCard($card);
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE/P1:SERVICE_PAYMENTS/P1:SERVICE_PAYMENT'
        );
        $this->assertEquals(1, $node->length);
    }

    public function testOutputXMLForSubmitNoCreditCard()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE/P1:SERVICE_PAYMENTS/P1:SERVICE_PAYMENT'
        );
        $this->assertEquals(0, $node->length);
    }

    public function testOutputXMLForSubmitProductDescription()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE/P1:SERVICE_PRODUCT/P1:SERVICE_PRODUCT_REQUEST/P1:SERVICE_PRODUCT_DETAIL/' .
            'P1:ServiceProductDescription'
        )->item(0);
        $this->assertEquals('CreditOrder', $node->textContent);
    }

    public function testOutputXMLForSubmitResponseFormats()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setResponseFormats(new ResponseFormats(true, true, true));
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE/P1:SERVICE_PRODUCT/P1:SERVICE_PRODUCT_REQUEST/P1:SERVICE_PRODUCT_DETAIL/' .
            'P1:EXTENSION/P1:OTHER/P3:SERVICE_PREFERRED_RESPONSE_FORMATS'
        );
        $this->assertEquals(1, $node->length);
    }

    public function testOutputXMLForSubmitNoResponseFormats()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setResponseFormats(new ResponseFormats(false, false, false));
        $data->setRequestType('Submit');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE/P1:SERVICE_PRODUCT/P1:SERVICE_PRODUCT_REQUEST/P1:SERVICE_PRODUCT_DETAIL/' .
            'P1:EXTENSION/P1:OTHER/P3:SERVICE_PREFERRED_RESPONSE_FORMATS'
        );
        $this->assertEquals(0, $node->length);
    }



    

}