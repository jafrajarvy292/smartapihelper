<?php

/**
 * Unit testing for the class that generates the Consumer Credit request files. Ideally, we'd want to use
 * xpath to test that each data point is populated under the correct element/attribute. However, this would
 * require a lot of work. Our compromise is that we'll only check that the data point is populated somewhere
 * in the XML document. In this case, we are assuming that if the data point is inserted into the document,
 * that it's been inserted into the correct attribute/element.
 */

namespace jafrajarvy292\SmartAPIHelper\RequestGenerator;

use jafrajarvy292\SmartAPIHelper\Ancillary\AddressBlock;
use jafrajarvy292\SmartAPIHelper\Ancillary\PersonNameBlock;
use jafrajarvy292\SmartAPIHelper\Ancillary\PhoneNumberBlock;
use jafrajarvy292\SmartAPIHelper\RequestData\ConsumerCreditRequestData;
use PHPUnit\Framework\TestCase;

class ConsumerCreditRequestGeneratorTest extends TestCase
{

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




    

}