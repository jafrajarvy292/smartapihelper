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
        $data->setSubjectPropAdd(new AddressBlock('4938 Subject Drive', 'Santa Ana', 'FL', '00093', 'CA'));
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

    public function testOutputXMLForStatusQueryXMLVersion()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setVendorOrderID('834323');
        $data->setRequestType('StatusQuery');
        $object = new ConsumerCreditRequestGenerator($data, '1.324');
        $string = $object->outputXMLString();
        $this->assertRegExp('/version="1.324"/', $string);
    }

    public function testOutputXMLForStatusQueryXMLEncoding()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('StatusQuery');
        $data->setVendorOrderID('834323');
        $object = new ConsumerCreditRequestGenerator($data, '1.0', 'WINDOWS-1252');
        $string = $object->outputXMLString();
        $this->assertRegExp('/encoding="WINDOWS-1252"/', $string);
    }

    public function testOutputXMLForStatusQueryDataVersion()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('StatusQuery');
        $data->setVendorOrderID('834323');
        $data->setDataVersion('98678');
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
        $this->assertEquals('98678', $node->textContent);
    }

    public function testOutputXMLForStatusQueryBorrowerParty()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('StatusQuery');
        $data->setVendorOrderID('4892349');
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

    public function testOutputXMLForStatusQueryBorrowerName()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('StatusQuery');
        $data->setVendorOrderID('4892349');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party1"]/P1:INDIVIDUAL/P1:NAME/P1:FirstName'
        )->item(0);
        $this->assertEquals('David', $node->textContent);
    }

    public function testOutputXMLForStatusQueryBorrowerNoName()
    {
        $data = new ConsumerCreditRequestData();
        $data->setSSN('b', '000000001');
        $data->setRequestType('StatusQuery');
        $data->setVendorOrderID('4892349');
        $object = new ConsumerCreditRequestGenerator($data);
        $this->expectException(\Exception::class);
        $object->outputXMLString();
    }

    public function testOutputXMLForStatusQueryBorrowerSSN()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('StatusQuery');
        $data->setVendorOrderID('4892349');
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

    public function testOutputXMLForStatusQueryBorrowerNoSSN()
    {
        $data = new ConsumerCreditRequestData();
        $data->setRequestType('StatusQuery');
        $data->setVendorOrderID('4892349');
        $object = new ConsumerCreditRequestGenerator($data);
        $this->expectException(\Exception::class);
        $object->outputXMLString();
    }

    public function testOutputXMLForStatusQueryCoborrowerParty()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setRequestType('StatusQuery');
        $data->setVendorOrderID('4892349');
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

    public function testOutputXMLForStatusQueryNoCoborrower()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('StatusQuery');
        $data->setVendorOrderID('4892349');
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
        $this->assertEquals(0, $node->length);
    }

    public function testOutputXMLForStatusQueryCoborrowerName()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setRequestType('StatusQuery');
        $data->setVendorOrderID('4892349');
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

    public function testOutputXMLForStatusQueryCoborrowerNoName()
    {
        /* No coborrower name simply means no coborrower. The presence of the name is what tell us whether
        a coborrower exists */
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setSSN('c', '000000002');
        $data->setRequestType('StatusQuery');
        $data->setVendorOrderID('4892349');
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
        $this->assertEquals(0, $node->length);
    }

    public function testOutputXMLForStatusQueryCoborrowerSSN()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setRequestType('StatusQuery');
        $data->setVendorOrderID('4892349');
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
        )->item(0);
        $this->assertEquals('000000002', $node->textContent);
    }

    public function testOutputXMLForStatusQueryCoborrowerNoSSN()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setRequestType('StatusQuery');
        $data->setVendorOrderID('4892349');
        $object = new ConsumerCreditRequestGenerator($data);
        $this->expectException(\Exception::class);
        $object->outputXMLString();
    }

    public function testOutputXMLForStatusQueryBorrowerRelationship()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('StatusQuery');
        $data->setVendorOrderID('4892349');
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

    public function testOutputXMLForStatusQueryCoborrowerRelationship()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setRequestType('StatusQuery');
        $data->setVendorOrderID('4892349');
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

    public function testOutputXMLForStatusQueryNoCoborrowerRelationship()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('StatusQuery');
        $data->setVendorOrderID('4892349');
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
        $this->assertEquals(0, $node->length);
    }

    public function testOutputXMLForStatusQueryServiceContainer()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('StatusQuery');
        $data->setVendorOrderID('4892349');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE[@P2:label="Service1"]'
        );
        $this->assertEquals(1, $node->length);
    }

    public function testOutputXMLForStatusQueryEquifaxTrueExperianTrueTransUnionTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('StatusQuery');
        $data->setVendorOrderID('4892349');
        $data->setEquifaxOptions(true);
        $data->setExperianOptions(true);
        $data->setTransUnionOptions(true);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE[@P2:label="Service1"]/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/' .
            'P1:CREDIT_REQUEST_DATA/P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedEquifaxIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedExperianIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedTransUnionIndicator')->item(0)->textContent
        );
    }

    public function testOutputXMLForStatusQueryEquifaxTrueExperianTrueTransUnionFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('StatusQuery');
        $data->setVendorOrderID('4892349');
        $data->setEquifaxOptions(true);
        $data->setExperianOptions(true);
        $data->setTransUnionOptions(false);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE[@P2:label="Service1"]/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/' .
            'P1:CREDIT_REQUEST_DATA/P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedEquifaxIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedExperianIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedTransUnionIndicator')->item(0)->textContent
        );
    }

    public function testOutputXMLForStatusQueryEquifaxTrueExperianFalseTransUnionFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('StatusQuery');
        $data->setVendorOrderID('4892349');
        $data->setEquifaxOptions(true);
        $data->setExperianOptions(false);
        $data->setTransUnionOptions(false);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE[@P2:label="Service1"]/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/' .
            'P1:CREDIT_REQUEST_DATA/P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedEquifaxIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedExperianIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedTransUnionIndicator')->item(0)->textContent
        );
    }

    public function testOutputXMLForStatusQueryEquifaxTrueExperianFalseTransUnionTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('StatusQuery');
        $data->setVendorOrderID('4892349');
        $data->setEquifaxOptions(true);
        $data->setExperianOptions(false);
        $data->setTransUnionOptions(true);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE[@P2:label="Service1"]/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/' .
            'P1:CREDIT_REQUEST_DATA/P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedEquifaxIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedExperianIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedTransUnionIndicator')->item(0)->textContent
        );
    }

    public function testOutputXMLForStatusQueryEquifaxFalseExperianTrueTransUnionTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('StatusQuery');
        $data->setVendorOrderID('4892349');
        $data->setEquifaxOptions(false);
        $data->setExperianOptions(true);
        $data->setTransUnionOptions(true);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE[@P2:label="Service1"]/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/' .
            'P1:CREDIT_REQUEST_DATA/P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedEquifaxIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedExperianIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedTransUnionIndicator')->item(0)->textContent
        );
    }

    public function testOutputXMLForStatusQueryEquifaxFalseExperianTrueTransUnionFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('StatusQuery');
        $data->setVendorOrderID('4892349');
        $data->setEquifaxOptions(false);
        $data->setExperianOptions(true);
        $data->setTransUnionOptions(false);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE[@P2:label="Service1"]/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/' .
            'P1:CREDIT_REQUEST_DATA/P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedEquifaxIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedExperianIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedTransUnionIndicator')->item(0)->textContent
        );
    }

    public function testOutputXMLForStatusQueryEquifaxFalseExperianFalseTransUnionFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('StatusQuery');
        $data->setVendorOrderID('4892349');
        $data->setEquifaxOptions(false);
        $data->setExperianOptions(false);
        $data->setTransUnionOptions(false);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE[@P2:label="Service1"]/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/' .
            'P1:CREDIT_REQUEST_DATA/P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedEquifaxIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedExperianIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedTransUnionIndicator')->item(0)->textContent
        );
    }

    public function testOutputXMLForStatusQueryEquifaxFalseExperianFalseTransUnionTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('StatusQuery');
        $data->setVendorOrderID('4892349');
        $data->setEquifaxOptions(false);
        $data->setExperianOptions(false);
        $data->setTransUnionOptions(true);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE[@P2:label="Service1"]/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/' .
            'P1:CREDIT_REQUEST_DATA/P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedEquifaxIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedExperianIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedTransUnionIndicator')->item(0)->textContent
        );
    }

    public function testOutputXMLForStatusQueryRequestType()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('StatusQuery');
        $data->setVendorOrderID('4892349');
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
        $this->assertEquals('StatusQuery', $node->textContent);
    }

    public function testOutputXMLForStatusQueryCreditCard()
    {
        $data = new ConsumerCreditRequestData();
        $card = new CreditCardBlock();
        $card->setAddress(new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $card->setName(new PersonNameBlock('David', 'Testcase'));
        $card->setCardNumber('4111111111111111');
        $card->setExpMonth('01');
        $card->setExpYear('2050');
        $data->setCreditCard($card);
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('StatusQuery');
        $data->setVendorOrderID('4892349');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE[@P2:label="Service1"]/P1:SERVICE_PAYMENTS/P1:SERVICE_PAYMENT'
        );
        $this->assertEquals(1, $node->length);
    }

    public function testOutputXMLForStatusQueryProductDescription()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('StatusQuery');
        $data->setVendorOrderID('4892349');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE[@P2:label="Service1"]/P1:SERVICE_PRODUCT/P1:SERVICE_PRODUCT_REQUEST/' .
            'P1:SERVICE_PRODUCT_DETAIL/P1:ServiceProductDescription'
        )->item(0);
        $this->assertEquals('CreditOrder', $node->textContent);
    }

    public function testOutputXMLForStatusQueryResponseFormats()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('StatusQuery');
        $data->setVendorOrderID('4892349');
        $data->setResponseFormats(new ResponseFormats(true, true, true));
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE[@P2:label="Service1"]/P1:SERVICE_PRODUCT/P1:SERVICE_PRODUCT_REQUEST/' .
            'P1:SERVICE_PRODUCT_DETAIL/P1:EXTENSION/P1:OTHER/P3:SERVICE_PREFERRED_RESPONSE_FORMATS'
        );
        $this->assertEquals(1, $node->length);
    }

    public function testOutputXMLForStatusQueryNoResponseFormats()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('StatusQuery');
        $data->setVendorOrderID('4892349');
        $data->setResponseFormats(new ResponseFormats(false, false, false));
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE[@P2:label="Service1"]/P1:SERVICE_PRODUCT/P1:SERVICE_PRODUCT_REQUEST/' .
            'P1:SERVICE_PRODUCT_DETAIL/P1:EXTENSION/P1:OTHER/P3:SERVICE_PREFERRED_RESPONSE_FORMATS'
        );
        $this->assertEquals(0, $node->length);
    }

    public function testOutputXMLForStatusQueryVendorOrderID()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('StatusQuery');
        $data->setVendorOrderID('4892349');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE[@P2:label="Service1"]/P1:SERVICE_PRODUCT_FULFILLMENT/' .
            'P1:SERVICE_PRODUCT_FULFILLMENT_DETAIL/P1:VendorOrderIdentifier'
        )->item(0);
        $this->assertEquals('4892349', $node->textContent);
    }

    public function testOutputXMLForStatusQueryNoVendorOrderID()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('StatusQuery');
        $object = new ConsumerCreditRequestGenerator($data);
        $this->expectException(\Exception::class);
        $object->outputXMLString();
    }

    public function testOutputXMLForUpgradeXMLVersion()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
        $object = new ConsumerCreditRequestGenerator($data, '2.44');
        $string = $object->outputXMLString();
        $this->assertRegExp('/version="2.44"/', $string);
    }

    public function testOutputXMLForUpgradeEncoding()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
        $object = new ConsumerCreditRequestGenerator($data, '1.0', 'WINDOWS-1252');
        $string = $object->outputXMLString();
        $this->assertRegExp('/encoding="WINDOWS-1252"/', $string);
    }

    public function testOutputXMLForUpgradeDataVersion()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
        $data->setDataVersion('5893894573');
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
        $this->assertEquals('5893894573', $node->textContent);
    }

    public function testOutputXMLForUpgradeBorrowerParty()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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

    public function testOutputXMLForUpgradeNoBorrower()
    {
        $data = new ConsumerCreditRequestData();
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
        $object = new ConsumerCreditRequestGenerator($data);
        $this->expectException(\Exception::class);
        $object->outputXMLString();
    }

    public function testOutputXMLForUpgradeBorrowerName()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party1"]/P1:INDIVIDUAL/P1:NAME'
        );
        $this->assertEquals(1, $node->length);
    }

    public function testOutputXMLForUpgradeBorrowerSSN()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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

    public function testOutputXMLForUpgradeBorrowerNoSSN()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
        $object = new ConsumerCreditRequestGenerator($data);
        $this->expectException(\Exception::class);
        $object->outputXMLString();
    }

    public function testOutputXMLForUpgradeCoborrowerParty()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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

    public function testOutputXMLForUpgradeCoborrowerName()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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

    public function testOutputXMLForUpgradeCoborrowerNoName()
    {
        /* If no coborrower name is provided, then the library processes as if no coborrower is present. Their
        name is the flag used to determine the presence of the coborrower */
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setSSN('c', '000000002');
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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
        $this->assertEquals(0, $node->length);
    }

    public function testOutputXMLForUpgradeCoborrowerYesPhoneNoEmail()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setPhone('c', new PhoneNumberBlock('3334445555'));
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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
        $this->assertEquals('3334445555', $node->textContent);
    }

    public function testOutputXMLForUpgradeCoborrowerNoPhoneNoEmail()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party2"]/P1:INDIVIDUAL/P1:CONTACT_POINTS'
        );
        $this->assertEquals(0, $node->length);
    }

    public function testOutputXMLForUpgradeCoborrowerNoPhoneYesEmail()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setEmail('c', 'jackie@example.com');
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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
        $this->assertEquals('jackie@example.com', $node->textContent);
    }

    public function testOutputXMLForUpgradeCoborrowerYesPhoneYesEmail()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setEmail('c', 'jackie@example.com');
        $data->setPhone('c', new PhoneNumberBlock('3332228888'));
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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
        )->item(0);
        $this->assertEquals('3332228888', $node->textContent);
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party2"]/P1:INDIVIDUAL/P1:CONTACT_POINTS/P1:CONTACT_POINT/' .
            'P1:CONTACT_POINT_EMAIL'
        )->item(0);
        $this->assertEquals('jackie@example.com', $node->textContent);
    }

    public function testOutputXMLForUpgradeCoborrowerDOB()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setDOB('c', '01-15-1990');
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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
        )->item(0);
        $this->assertEquals('1990-01-15', $node->textContent);
    }

    public function testOutputXMLForUpgradeCoborrowerNoDOB()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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

    public function testOutputXMLForUpgradeCoborrowerSSN()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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
        )->item(0);
        $this->assertEquals(000000002, $node->textContent);
    }

    public function testOutputXMLForUpgradeCoborrowerNoSSN()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
        $object = new ConsumerCreditRequestGenerator($data);
        $this->expectException(\Exception::class);
        $object->outputXMLString();
    }

    public function testOutputXMLForUpgradeBorrowerRelationship()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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

    public function testOutputXMLForUpgradeBorrowerNoCoborrowerRelationship()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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
        $node = $xpath->evaluate(
            '//P1:RELATIONSHIP[@P2:arcrole="urn:fdc:Meridianlink.com:2017:mortgage/' .
            'PARTY_IsVerifiedBy_SERVICE" and @P2:from="Party2" and @P2:to="Service1"]'
        );
        $this->assertEquals(0, $node->length);
    }

    public function testOutputXMLForUpgradeBorrowerCoborrowerRelationship()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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
        $node = $xpath->evaluate(
            '//P1:RELATIONSHIP[@P2:arcrole="urn:fdc:Meridianlink.com:2017:mortgage/' .
            'PARTY_IsVerifiedBy_SERVICE" and @P2:from="Party2" and @P2:to="Service1"]'
        );
        $this->assertEquals(1, $node->length);
    }

    public function testOutputXMLForUpgradeServiceContainer()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE[@P2:label="Service1"]'
        );
        $this->assertEquals(1, $node->length);
    }

    public function testOutputXMLForUpgradeEquifaxCreditTrueScoreFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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
    
    public function testOutputXMLForUpgradeEquifaxCreditTrueScoreTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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
    
    public function testOutputXMLForUpgradeEquifaxCreditFalseScoreTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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
    
    public function testOutputXMLForUpgradeEquifaxCreditFalseScoreFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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
    
    public function testOutputXMLForUpgradeExperianCreditTrueScoreTrueFraudTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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
    
    public function testOutputXMLForUpgradeExperianCreditTrueScoreTrueFraudFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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
    
    public function testOutputXMLForUpgradeExperianCreditTrueScoreFalseFraudTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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
    
    public function testOutputXMLForUpgradeExperianCreditTrueScoreFalseFraudFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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
    
    public function testOutputXMLForUpgradeExperianCreditFalseScoreTrueFraudTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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
    
    public function testOutputXMLForUpgradeExperianCreditFalseScoreTrueFraudFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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
    
    public function testOutputXMLForUpgradeExperianCreditFalseScoreFalseFraudTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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
    
    public function testOutputXMLForUpgradeExperianCreditCreditFalseScoreFalseFraudFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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
    
    public function testOutputXMLForUpgradeTransUnionCreditTrueScoreTrueFraudTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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
    
    public function testOutputXMLForUpgradeTransUnionCreditTrueScoreTrueFraudFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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
    
    public function testOutputXMLForUpgradeTransUnionCreditTrueScoreFalseFraudTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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
    
    public function testOutputXMLForUpgradeTransUnionCreditTrueScoreFalseFraudFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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
    
    public function testOutputXMLForUpgradeTransUnionCreditFalseScoreTrueFraudTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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
    
    public function testOutputXMLForUpgradeTransUnionCreditFalseScoreTrueFraudFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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
    
    public function testOutputXMLForUpgradeTransUnionCreditFalseScoreFalseFraudTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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
    
    public function testOutputXMLForUpgradeTransUnionCreditCreditFalseScoreFalseFraudFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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

    public function testOutputXMLForUpgradeRequestType()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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
        $this->assertEquals('Upgrade', $node->textContent);
    }

    public function testOutputXMLForUpgradeCreditCard()
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
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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

    public function testOutputXMLForUpgradeNoCreditCard()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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

    public function testOutputXMLForUpgradeProductDescription()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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
    
    public function testOutputXMLForUpgradeResponseFormats()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setResponseFormats(new ResponseFormats(true, true, true));
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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
    
    public function testOutputXMLForUpgradeNoResponseFormats()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setResponseFormats(new ResponseFormats(false, false, false));
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
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

    public function testOutputXMLForUpgradeVendorOrderID()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('Upgrade');
        $data->setVendorOrderID('9876546787');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE[@P2:label="Service1"]/P1:SERVICE_PRODUCT_FULFILLMENT/' .
            'P1:SERVICE_PRODUCT_FULFILLMENT_DETAIL/P1:VendorOrderIdentifier'
        )->item(0);
        $this->assertEquals('9876546787', $node->textContent);
    }

    public function testOutputXMLForUpgradeNoVendorOrderID()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('Upgrade');
        $object = new ConsumerCreditRequestGenerator($data);
        $this->expectException(\Exception::class);
        $object->outputXMLString();
    }

    public function testOutputXMLForRefreshXMLVersion()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
        $object = new ConsumerCreditRequestGenerator($data, '6.11');
        $string = $object->outputXMLString();
        $this->assertRegExp('/version="6.11"/', $string);
    }
    
    public function testOutputXMLForRefreshXMLEncoding()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
        $object = new ConsumerCreditRequestGenerator($data, '1.0', 'WINDOWS-1250');
        $string = $object->outputXMLString();
        $this->assertRegExp('/encoding="WINDOWS-1250"/', $string);
    }
    
    public function testOutputXMLForRefreshDataVersion()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshMissingRequestType()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $object = new ConsumerCreditRequestGenerator($data);
        $this->expectException(\Exception::class);
        $object->outputXMLString();
    }
    
    public function testOutputXMLForRefreshMissingBorrowerName()
    {
        $data = new ConsumerCreditRequestData();
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
        $object = new ConsumerCreditRequestGenerator($data);
        $this->expectException(\Exception::class);
        $object->outputXMLString();
    }
    
    public function testOutputXMLForRefreshMissingBorrowerSSN()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
        $object = new ConsumerCreditRequestGenerator($data);
        $this->expectException(\Exception::class);
        $object->outputXMLString();
    }
    
    public function testOutputXMLForRefreshMissingBorrowerAddress()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
        $object = new ConsumerCreditRequestGenerator($data);
        $this->expectException(\Exception::class);
        $object->outputXMLString();
    }
    
    public function testOutputXMLForRefreshBorrowerPartyNode()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshSubjectProperty()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
        $data->setSubjectPropAdd(new AddressBlock('4938 Subject Drive', 'Santa Ana', 'FL', '00093', 'CA'));
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
    
    public function testOutputXMLForRefreshNoSubjectProperty()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshLoanID()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshNoLoanID()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshBorrowerPhoneNoEmail()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshNoBorrowerPhoneNoEmail()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshBorrowerEmailNoPhone()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshBorrowerEmailBorrowerPhone()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshBorrowerFullName()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase', 'R', 'JR'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshBorrowerMinimumName()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshMailingAddress()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshNoMailingAddress()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshBorrowerDOB()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshNoBorrowerDOB()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshBorrowerCurrentAddress()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'), 'Current');
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshBorrowerNoCurrentAddress()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
        $object = new ConsumerCreditRequestGenerator($data);
        $this->expectException(\Exception::class);
        $object->outputXMLString();
    }
    
    public function testOutputXMLForRefreshBorrowerPriorAddress()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshBorrowerNoPriorAddress()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshBorrowerSSN()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshNoBorrowerSSN()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $this->expectException(\Exception::class);
        $object->outputXMLString();
    }
    
    public function testOutputXMLForRefreshCoborrowerPresent()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshCoborrowerMissingSSN()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
        $object = new ConsumerCreditRequestGenerator($data);
        $this->expectException(\Exception::class);
        $object->outputXMLString();
    }
    
    public function testOutputXMLForRefreshCoborrowerMissingCurrentAddress()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
        $object = new ConsumerCreditRequestGenerator($data);
        $this->expectException(\Exception::class);
        $object->outputXMLString();
    }
    
    public function testOutputXMLForRefreshCoborrowerMissingName()
    {
        /* There isn't such a thing as missing the coborrower name. The presence of the coborrower name is
        the flag to indicate whether they exist. No coborrower name means no coborrower is present */
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
        $object = new ConsumerCreditRequestGenerator($data);
        $this->assertIsString($object->outputXMLString());
    }
    
    public function testOutputXMLForRefreshCoborrowerPhone()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));
        $data->setPhone('c', new PhoneNumberBlock('8008882222'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshCoborrowerNoPhone()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshCoborrowerEmail()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));
        $data->setEmail('c', 'jane@example.com');
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshCoborrowerNoEmail()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshCoborrowerName()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshCoborrowerMailingAddress()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshCoborrowerNoMailingAddress()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshCoborrowerDOB()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshCoborrowerNoDOB()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshCoborrowerCurrentAddress()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshCoborrowerNoCurrentAddress()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
        $object = new ConsumerCreditRequestGenerator($data);
        $this->expectException(\Exception::class);
        $object->outputXMLString();
    }
    
    public function testOutputXMLForRefreshCoborrowerPreviousAddress()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));
        $data->setAddress('c', new AddressBlock('892 Prior Road', 'Santa Ana', 'NY', '92888'), 'Prior');
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshCoborrowerNoPreviousAddress()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshCoborrowerSSN()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshBorrowerRelationship()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshCoBorrowerRelationship()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setAddress('c', new AddressBlock('222 Cob Lane', 'Santa Ana', 'NY', '92888'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshSubjectPropertyAddressRelationship()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setSubjectPropAdd(new AddressBlock('5893 Property Court', 'Santa Ana', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshEquifaxCreditTrueScoreFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshEquifaxCreditTrueScoreTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshEquifaxCreditFalseScoreTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshEquifaxCreditFalseScoreFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshExperianCreditTrueScoreTrueFraudTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshExperianCreditTrueScoreTrueFraudFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshExperianCreditTrueScoreFalseFraudTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshExperianCreditTrueScoreFalseFraudFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshExperianCreditFalseScoreTrueFraudTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshExperianCreditFalseScoreTrueFraudFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshExperianCreditFalseScoreFalseFraudTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshExperianCreditCreditFalseScoreFalseFraudFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshTransUnionCreditTrueScoreTrueFraudTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshTransUnionCreditTrueScoreTrueFraudFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshTransUnionCreditTrueScoreFalseFraudTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshTransUnionCreditTrueScoreFalseFraudFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshTransUnionCreditFalseScoreTrueFraudTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshTransUnionCreditFalseScoreTrueFraudFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshTransUnionCreditFalseScoreFalseFraudTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshTransUnionCreditCreditFalseScoreFalseFraudFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshRequestType()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/P1:CREDIT_REQUEST_DATA/' .
            'P1:CREDIT_REQUEST_DATA_DETAIL/P1:CreditReportRequestActionTypeOtherDescription'
        )->item(0);
        $this->assertEquals('Refresh', $node->textContent);
    }
    
    public function testOutputXMLForRefreshCreditCard()
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
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshNoCreditCard()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshProductDescription()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshResponseFormats()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setResponseFormats(new ResponseFormats(true, true, true));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshNoResponseFormats()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setResponseFormats(new ResponseFormats(false, false, false));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
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
    
    public function testOutputXMLForRefreshVendorOrderID()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $data->setVendorOrderID('847437');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE[@P2:label="Service1"]/P1:SERVICE_PRODUCT_FULFILLMENT/' .
            'P1:SERVICE_PRODUCT_FULFILLMENT_DETAIL/P1:VendorOrderIdentifier'
        )->item(0);
        $this->assertEquals('847437', $node->textContent);
    }
    
    public function testOutputXMLForRefreshNoVendorOrderID()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('Refresh');
        $object = new ConsumerCreditRequestGenerator($data);
        $this->expectException(\Exception::class);
        $object->outputXMLString();
    }

    public function testOutputXMLForPermUnmergeXMLVersion()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setVendorOrderID('834323');
        $data->setRequestType('PermUnmerge');
        $object = new ConsumerCreditRequestGenerator($data, '5.3');
        $string = $object->outputXMLString();
        $this->assertRegExp('/version="5.3"/', $string);
    }
    
    public function testOutputXMLForPermUnmergeXMLEncoding()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('PermUnmerge');
        $data->setVendorOrderID('834323');
        $object = new ConsumerCreditRequestGenerator($data, '1.0', 'WINDOWS-1250');
        $string = $object->outputXMLString();
        $this->assertRegExp('/encoding="WINDOWS-1250"/', $string);
    }
    
    public function testOutputXMLForPermUnmergeDataVersion()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('PermUnmerge');
        $data->setVendorOrderID('834323');
        $data->setDataVersion('98678');
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
        $this->assertEquals('98678', $node->textContent);
    }
    
    public function testOutputXMLForPermUnmergeBorrowerParty()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('PermUnmerge');
        $data->setVendorOrderID('4892349');
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
    
    public function testOutputXMLForPermUnmergeBorrowerName()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('PermUnmerge');
        $data->setVendorOrderID('4892349');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:PARTY[@P2:label="Party1"]/P1:INDIVIDUAL/P1:NAME/P1:FirstName'
        )->item(0);
        $this->assertEquals('David', $node->textContent);
    }
    
    public function testOutputXMLForPermUnmergeBorrowerNoName()
    {
        $data = new ConsumerCreditRequestData();
        $data->setSSN('b', '000000001');
        $data->setRequestType('PermUnmerge');
        $data->setVendorOrderID('4892349');
        $object = new ConsumerCreditRequestGenerator($data);
        $this->expectException(\Exception::class);
        $object->outputXMLString();
    }
    
    public function testOutputXMLForPermUnmergeBorrowerSSN()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('PermUnmerge');
        $data->setVendorOrderID('4892349');
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
    
    public function testOutputXMLForPermUnmergeBorrowerNoSSN()
    {
        $data = new ConsumerCreditRequestData();
        $data->setRequestType('PermUnmerge');
        $data->setVendorOrderID('4892349');
        $object = new ConsumerCreditRequestGenerator($data);
        $this->expectException(\Exception::class);
        $object->outputXMLString();
    }
    
    public function testOutputXMLForPermUnmergeCoborrowerParty()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setRequestType('PermUnmerge');
        $data->setVendorOrderID('4892349');
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
    
    public function testOutputXMLForPermUnmergeNoCoborrower()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('PermUnmerge');
        $data->setVendorOrderID('4892349');
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
        $this->assertEquals(0, $node->length);
    }
    
    public function testOutputXMLForPermUnmergeCoborrowerName()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setRequestType('PermUnmerge');
        $data->setVendorOrderID('4892349');
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
    
    public function testOutputXMLForPermUnmergeCoborrowerNoName()
    {
        /* No coborrower name simply means no coborrower. The presence of the name is what tell us whether
        a coborrower exists */
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setSSN('c', '000000002');
        $data->setRequestType('PermUnmerge');
        $data->setVendorOrderID('4892349');
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
        $this->assertEquals(0, $node->length);
    }
    
    public function testOutputXMLForPermUnmergeCoborrowerSSN()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setRequestType('PermUnmerge');
        $data->setVendorOrderID('4892349');
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
        )->item(0);
        $this->assertEquals('000000002', $node->textContent);
    }
    
    public function testOutputXMLForPermUnmergeCoborrowerNoSSN()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setRequestType('PermUnmerge');
        $data->setVendorOrderID('4892349');
        $object = new ConsumerCreditRequestGenerator($data);
        $this->expectException(\Exception::class);
        $object->outputXMLString();
    }
    
    public function testOutputXMLForPermUnmergeBorrowerRelationship()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('PermUnmerge');
        $data->setVendorOrderID('4892349');
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
    
    public function testOutputXMLForPermUnmergeCoborrowerRelationship()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setName('c', new PersonNameBlock('Jackie', 'Tester'));
        $data->setSSN('c', '000000002');
        $data->setRequestType('PermUnmerge');
        $data->setVendorOrderID('4892349');
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
    
    public function testOutputXMLForPermUnmergeNoCoborrowerRelationship()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('PermUnmerge');
        $data->setVendorOrderID('4892349');
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
        $this->assertEquals(0, $node->length);
    }
    
    public function testOutputXMLForPermUnmergeServiceContainer()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('PermUnmerge');
        $data->setVendorOrderID('4892349');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE[@P2:label="Service1"]'
        );
        $this->assertEquals(1, $node->length);
    }
    
    public function testOutputXMLForPermUnmergeEquifaxTrueExperianTrueTransUnionTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('PermUnmerge');
        $data->setVendorOrderID('4892349');
        $data->setEquifaxOptions(true);
        $data->setExperianOptions(true);
        $data->setTransUnionOptions(true);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE[@P2:label="Service1"]/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/' .
            'P1:CREDIT_REQUEST_DATA/P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedEquifaxIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedExperianIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedTransUnionIndicator')->item(0)->textContent
        );
    }
    
    public function testOutputXMLForPermUnmergeEquifaxTrueExperianTrueTransUnionFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('PermUnmerge');
        $data->setVendorOrderID('4892349');
        $data->setEquifaxOptions(true);
        $data->setExperianOptions(true);
        $data->setTransUnionOptions(false);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE[@P2:label="Service1"]/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/' .
            'P1:CREDIT_REQUEST_DATA/P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedEquifaxIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedExperianIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedTransUnionIndicator')->item(0)->textContent
        );
    }
    
    public function testOutputXMLForPermUnmergeEquifaxTrueExperianFalseTransUnionFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('PermUnmerge');
        $data->setVendorOrderID('4892349');
        $data->setEquifaxOptions(true);
        $data->setExperianOptions(false);
        $data->setTransUnionOptions(false);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE[@P2:label="Service1"]/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/' .
            'P1:CREDIT_REQUEST_DATA/P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedEquifaxIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedExperianIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedTransUnionIndicator')->item(0)->textContent
        );
    }
    
    public function testOutputXMLForPermUnmergeEquifaxTrueExperianFalseTransUnionTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('PermUnmerge');
        $data->setVendorOrderID('4892349');
        $data->setEquifaxOptions(true);
        $data->setExperianOptions(false);
        $data->setTransUnionOptions(true);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE[@P2:label="Service1"]/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/' .
            'P1:CREDIT_REQUEST_DATA/P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedEquifaxIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedExperianIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedTransUnionIndicator')->item(0)->textContent
        );
    }
    
    public function testOutputXMLForPermUnmergeEquifaxFalseExperianTrueTransUnionTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('PermUnmerge');
        $data->setVendorOrderID('4892349');
        $data->setEquifaxOptions(false);
        $data->setExperianOptions(true);
        $data->setTransUnionOptions(true);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE[@P2:label="Service1"]/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/' .
            'P1:CREDIT_REQUEST_DATA/P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedEquifaxIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedExperianIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedTransUnionIndicator')->item(0)->textContent
        );
    }
    
    public function testOutputXMLForPermUnmergeEquifaxFalseExperianTrueTransUnionFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('PermUnmerge');
        $data->setVendorOrderID('4892349');
        $data->setEquifaxOptions(false);
        $data->setExperianOptions(true);
        $data->setTransUnionOptions(false);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE[@P2:label="Service1"]/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/' .
            'P1:CREDIT_REQUEST_DATA/P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedEquifaxIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedExperianIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedTransUnionIndicator')->item(0)->textContent
        );
    }
    
    public function testOutputXMLForPermUnmergeEquifaxFalseExperianFalseTransUnionFalse()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('PermUnmerge');
        $data->setVendorOrderID('4892349');
        $data->setEquifaxOptions(false);
        $data->setExperianOptions(false);
        $data->setTransUnionOptions(false);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE[@P2:label="Service1"]/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/' .
            'P1:CREDIT_REQUEST_DATA/P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedEquifaxIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedExperianIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedTransUnionIndicator')->item(0)->textContent
        );
    }
    
    public function testOutputXMLForPermUnmergeEquifaxFalseExperianFalseTransUnionTrue()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('PermUnmerge');
        $data->setVendorOrderID('4892349');
        $data->setEquifaxOptions(false);
        $data->setExperianOptions(false);
        $data->setTransUnionOptions(true);
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE[@P2:label="Service1"]/P1:CREDIT/P1:CREDIT_REQUEST/P1:CREDIT_REQUEST_DATAS/' .
            'P1:CREDIT_REQUEST_DATA/P1:CREDIT_REPOSITORY_INCLUDED'
        )->item(0);
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedEquifaxIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'false',
            $node->getElementsByTagName('CreditRepositoryIncludedExperianIndicator')->item(0)->textContent
        );
        $this->assertEquals(
            'true',
            $node->getElementsByTagName('CreditRepositoryIncludedTransUnionIndicator')->item(0)->textContent
        );
    }
    
    public function testOutputXMLForPermUnmergeRequestType()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $data->setRequestType('PermUnmerge');
        $data->setVendorOrderID('4892349');
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
        $this->assertEquals('PermUnmerge', $node->textContent);
    }
    
    public function testOutputXMLForPermUnmergeCreditCard()
    {
        $data = new ConsumerCreditRequestData();
        $card = new CreditCardBlock();
        $card->setAddress(new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $card->setName(new PersonNameBlock('David', 'Testcase'));
        $card->setCardNumber('4111111111111111');
        $card->setExpMonth('01');
        $card->setExpYear('2050');
        $data->setCreditCard($card);
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('PermUnmerge');
        $data->setVendorOrderID('4892349');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE[@P2:label="Service1"]/P1:SERVICE_PAYMENTS/P1:SERVICE_PAYMENT'
        );
        $this->assertEquals(1, $node->length);
    }
    
    public function testOutputXMLForPermUnmergeProductDescription()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('PermUnmerge');
        $data->setVendorOrderID('4892349');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE[@P2:label="Service1"]/P1:SERVICE_PRODUCT/P1:SERVICE_PRODUCT_REQUEST/' .
            'P1:SERVICE_PRODUCT_DETAIL/P1:ServiceProductDescription'
        )->item(0);
        $this->assertEquals('CreditOrder', $node->textContent);
    }
    
    public function testOutputXMLForPermUnmergeResponseFormats()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('PermUnmerge');
        $data->setVendorOrderID('4892349');
        $data->setResponseFormats(new ResponseFormats(true, true, true));
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE[@P2:label="Service1"]/P1:SERVICE_PRODUCT/P1:SERVICE_PRODUCT_REQUEST/' .
            'P1:SERVICE_PRODUCT_DETAIL/P1:EXTENSION/P1:OTHER/P3:SERVICE_PREFERRED_RESPONSE_FORMATS'
        );
        $this->assertEquals(1, $node->length);
    }
    
    public function testOutputXMLForPermUnmergeNoResponseFormats()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('PermUnmerge');
        $data->setVendorOrderID('4892349');
        $data->setResponseFormats(new ResponseFormats(false, false, false));
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE[@P2:label="Service1"]/P1:SERVICE_PRODUCT/P1:SERVICE_PRODUCT_REQUEST/' .
            'P1:SERVICE_PRODUCT_DETAIL/P1:EXTENSION/P1:OTHER/P3:SERVICE_PREFERRED_RESPONSE_FORMATS'
        );
        $this->assertEquals(0, $node->length);
    }
    
    public function testOutputXMLForPermUnmergeVendorOrderID()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('PermUnmerge');
        $data->setVendorOrderID('4892349');
        $object = new ConsumerCreditRequestGenerator($data);
        $base = new \DOMDocument();
        $base->loadXML($object->outputXMLString());
        $xpath = new \DOMXPath($base);
        $xpath->registerNamespace('P1', 'http://www.mismo.org/residential/2009/schemas');
        $xpath->registerNamespace('P2', 'http://www.w3.org/1999/xlink');
        $xpath->registerNamespace('P3', 'inetapi/MISMO3_4_MCL_Extension.xsd');
        $node = $xpath->evaluate(
            '//P1:SERVICE[@P2:label="Service1"]/P1:SERVICE_PRODUCT_FULFILLMENT/' .
            'P1:SERVICE_PRODUCT_FULFILLMENT_DETAIL/P1:VendorOrderIdentifier'
        )->item(0);
        $this->assertEquals('4892349', $node->textContent);
    }
    
    public function testOutputXMLForPermUnmergeNoVendorOrderID()
    {
        $data = new ConsumerCreditRequestData();
        $data->setName('b', new PersonNameBlock('David', 'Testcase'));
        $data->setSSN('b', '000000001');
        $data->setRequestType('PermUnmerge');
        $object = new ConsumerCreditRequestGenerator($data);
        $this->expectException(\Exception::class);
        $object->outputXMLString();
    }
}
