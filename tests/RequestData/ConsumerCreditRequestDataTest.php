<?php

namespace jafrajarvy292\SmartAPIHelper\RequestData;

use jafrajarvy292\SmartAPIHelper\Ancillary\AddressBlock;
use jafrajarvy292\SmartAPIHelper\Ancillary\PersonNameBlock;
use jafrajarvy292\SmartAPIHelper\Ancillary\PhoneNumberBlock;
use jafrajarvy292\SmartAPIHelper\Ancillary\CreditCardBlock;
use jafrajarvy292\SmartAPIHelper\Ancillary\ResponseFormats;
use PHPUnit\Framework\TestCase;

class ConsumerCreditRequestDataTest extends TestCase
{
    public function testConstructor()
    {
        $this->assertInstanceOf(ConsumerCreditRequestData::class, new ConsumerCreditRequestData());
    }

    public function testSetNameValid()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertNull($object->setName('b', new PersonNameBlock('David', 'Testcase')));
        $this->assertNull($object->setName('c', new PersonNameBlock('Jane', 'Testcase')));
    }

    public function testSetNameInvalid()
    {
        $object = new ConsumerCreditRequestData();
        $this->expectException(\Exception::class);
        $object->setName('f', new PersonNameBlock('David', 'Testcase'));
    }

    public function testSetSSNValid()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertNull($object->setSSN('b', '000000001'));
        $this->assertNull($object->setSSN('c', '000000002'));
    }

    public function testSetSSNInvalid()
    {
        $object = new ConsumerCreditRequestData();
        $this->expectException(\Exception::class);
        $object->setSSN('b', '00003');
    }

    public function testSetSSNInvalid2()
    {
        $object = new ConsumerCreditRequestData();
        $this->expectException(\Exception::class);
        $object->setSSN('b', 'adsdf');
    }

    public function testSetSSNInvalid3()
    {
        $object = new ConsumerCreditRequestData();
        $this->expectException(\Exception::class);
        $object->setSSN('b', '000-00-0002');
    }

    public function testSetSSNInvalid4()
    {
        $object = new ConsumerCreditRequestData();
        $this->expectException(\Exception::class);
        $object->setSSN('f', '000000002');
    }

    public function testSetAddressValid()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertNull($object->setAddress(
            'b',
            new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843')
        ));
        $this->assertNull($object->setAddress(
            'b',
            new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'),
            'Current'
        ));
        $this->assertNull($object->setAddress(
            'b',
            new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'),
            'Prior'
        ));
        $this->assertNull($object->setAddress(
            'b',
            new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'),
            'Mailing'
        ));
        $this->assertNull($object->setAddress('b'));
        $this->assertNull($object->setAddress('b'), 'Current');
        $this->assertNull($object->setAddress('b'), 'Prior');
        $this->assertNull($object->setAddress('b'), 'Mailing');
        $this->assertNull($object->setAddress(
            'c',
            new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843')
        ));
        $this->assertNull($object->setAddress(
            'c',
            new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'),
            'Current'
        ));
        $this->assertNull($object->setAddress(
            'c',
            new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'),
            'Prior'
        ));
        $this->assertNull($object->setAddress(
            'c',
            new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'),
            'Mailing'
        ));
        $this->assertNull($object->setAddress('c'));
        $this->assertNull($object->setAddress('c'), 'Current');
        $this->assertNull($object->setAddress('c'), 'Prior');
        $this->assertNull($object->setAddress('c'), 'Mailing');
    }

    public function testSetAddressInvalid()
    {
        $object = new ConsumerCreditRequestData();
        $this->expectException(\Exception::class);
        $object->setAddress(
            'b',
            new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'),
            'Previous'
        );
        $this->expectException(\Exception::class);
    }

    public function testSetDOBValid()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertNull($object->setDOB('b', '01-20-1950'));
        $this->assertNull($object->setDOB('b', '1950-01-20'));
        $this->assertNull($object->setDOB('b', ''));
        $this->assertNull($object->setDOB('c', '01-20-1950'));
        $this->assertNull($object->setDOB('c', '1950-01-20'));
        $this->assertNull($object->setDOB('c', ''));
    }

    public function testSetDOBInvalid()
    {
        $object = new ConsumerCreditRequestData();
        $this->expectException(\Exception::class);
        $object->setDOB('b', 'dklsdf');
    }

    public function testSetDOBInvalid2()
    {
        $object = new ConsumerCreditRequestData();
        $this->expectException(\Exception::class);
        $object->setDOB('f', '01-20-1950');
    }

    public function testSetDOBInvalid3()
    {
        $object = new ConsumerCreditRequestData();
        $this->expectException(\Exception::class);
        $object->setDOB('b', '01/02/1980');
    }

    public function testSetDOBInvalid4()
    {
        $object = new ConsumerCreditRequestData();
        $this->expectException(\Exception::class);
        $object->setDOB('b', '01021980');
    }

    public function testSetPhoneValid()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertNull($object->setPhone('b', new PhoneNumberBlock('3335556666')));
        $this->assertNull($object->setPhone('b'));
        $this->assertNull($object->setPhone('c', new PhoneNumberBlock('3335556666')));
        $this->assertNull($object->setPhone('c'));
    }

    public function testSetEmailValid()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertNull($object->setEmail('b', 'test@test.com'));
        $this->assertNull($object->setEmail('b', ''));
        $this->assertNull($object->setEmail('c', 'test@test.com'));
        $this->assertNull($object->setEmail('c', ''));
    }

    public function testSetEmailInvalid()
    {
        $object = new ConsumerCreditRequestData();
        $this->expectException(\Exception::class);
        $object->setEmail('f', 'test@test.com');
    }

    public function testSetEmailInvalid2()
    {
        $object = new ConsumerCreditRequestData();
        $this->expectException(\Exception::class);
        $object->setEmail('b', 'test@test.com;test2@test.com');
    }

    public function testSetEmailInvalid3()
    {
        $object = new ConsumerCreditRequestData();
        $this->expectException(\Exception::class);
        $object->setEmail('b', 'testtest.com');
    }

    public function testSetEmailInvalid4()
    {
        $object = new ConsumerCreditRequestData();
        $this->expectException(\Exception::class);
        $object->setEmail('b', 'test@testcom');
    }

    public function testSetSubjectPropAddValid()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertNull($object->setSubjectPropAdd(
            new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843')
        ));
        $this->assertNull($object->setSubjectPropAdd());
    }

    public function testSetSubjectPropAddInvalid()
    {
        $object = new ConsumerCreditRequestData();
        $this->expectException(\Exception::class);
        $object->setSubjectPropAdd(new AddressBlock('123 Main St', 'Garden Grove', 'CA', '333'));
    }

    public function testSetLoanIDValid()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertNull($object->setLoanID('48923uhj49u2%$#@FR'));
        $this->assertNull($object->setLoanID(''));
    }

    public function testSetLoanType()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertNull($object->setLoanType('Auto'));
        $this->assertNull($object->setLoanType(''));
        $this->assertNull($object->setLoanType('Other'));
        $this->assertNull($object->setLoanType('987654oiuytrjuhytjhgf'));
    }

    public function testSetCreditCardValid()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertNull($object->setCreditCard(new CreditCardBlock()));
        $this->assertNull($object->setCreditCard());
    }

    public function testSetEquifaxOptionsValid()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertNull($object->setEquifaxOptions(true, true));
        $this->assertNull($object->setEquifaxOptions(true, false));
        $this->assertNull($object->setEquifaxOptions(false, true));
        $this->assertNull($object->setEquifaxOptions(false, false));
        $this->assertNull($object->setEquifaxOptions(true));
        $this->assertNull($object->setEquifaxOptions(false));
    }

    public function testSetExperianOptionsValid()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertNull($object->setExperianOptions(true, true, true));
        $this->assertNull($object->setExperianOptions(false, true, true));
        $this->assertNull($object->setExperianOptions(true));
        $this->assertNull($object->setExperianOptions(false));
        $this->assertNull($object->setExperianOptions(true, false));
    }

    public function testSetTransUnionOptionsValid()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertNull($object->setTransUnionOptions(true, true, true));
        $this->assertNull($object->setTransUnionOptions(false, true, true));
        $this->assertNull($object->setTransUnionOptions(true));
        $this->assertNull($object->setTransUnionOptions(false));
        $this->assertNull($object->setTransUnionOptions(true, false));
    }

    public function testSetRequestTypeValid()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertNull($object->setRequestType('Submit'));
        $this->assertNull($object->setRequestType('subMit'));
        $this->assertNull($object->setRequestType('StatusQuery'));
        $this->assertNull($object->setRequestType('staTusQUery'));
        $this->assertNull($object->setRequestType('Upgrade'));
        $this->assertNull($object->setRequestType('UpgrAde'));
        $this->assertNull($object->setRequestType('Refresh'));
        $this->assertNull($object->setRequestType('RefresH'));
        $this->assertNull($object->setRequestType('PermUnmerge'));
        $this->assertNull($object->setRequestType('PerMunmeRge'));
    }

    public function testSetRequestTypeInvalid()
    {
        $object = new ConsumerCreditRequestData();
        $this->expectException(\Exception::class);
        $object->setRequestType('');
    }

    public function testSetRequestTypeInvalid2()
    {
        $object = new ConsumerCreditRequestData();
        $this->expectException(\Exception::class);
        $object->setRequestType('renew');
    }

    public function testSetResponseFormatsValid()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertNull($object->setResponseFormats(new ResponseFormats(true, true, true)));
        $this->assertNull($object->setResponseFormats(new ResponseFormats(true, false)));
        $this->assertNull($object->setResponseFormats(new ResponseFormats(false)));
        $this->assertNull($object->setResponseFormats(new ResponseFormats(false, false, false)));
        $this->assertNull($object->setResponseFormats(new ResponseFormats()));
    }

    public function testSetVendorOrderID()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertNull($object->setVendorOrderID('489234'));
        $this->assertNull($object->setVendorOrderID('do58923jflkj3'));
        $this->assertNull($object->setVendorOrderID(''));
    }

    public function testSetDataVersionValid()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertNull($object->setDataVersion('48923498234'));
        $this->assertNull($object->setDataVersion('d54jrhd3'));
    }

    public function testSetDataVersionInvalid()
    {
        $object = new ConsumerCreditRequestData();
        $this->expectException(\Exception::class);
        $object->setDataVersion('');
    }

    public function testGetBorrowerID()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertEquals('b', $object->getBorrowerID());
    }

    public function testGetCoborrowerID()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertEquals('c', $object->getCoborrowerID());
    }

    public function testGetDataVersionDefault()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertEquals('201703', $object->getDataVersion());
    }

    public function testGetDataVersion()
    {
        $object = new ConsumerCreditRequestData();
        $object->setDataVersion('1234567890');
        $this->assertEquals('1234567890', $object->getDataVersion());
    }

    public function testGetSubjectPropAddValid()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertEquals(null, $object->getSubjectPropAdd());
    }

    public function testGetSubjectPropAddValid2()
    {
        $object = new ConsumerCreditRequestData();
        $object->setSubjectPropAdd(new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $this->assertInstanceOf(AddressBlock::class, $object->getSubjectPropAdd());
    }

    public function testGetNameValid()
    {
        $object = new ConsumerCreditRequestData();
        $object->setName('b', new PersonNameBlock('David', 'Testcase'));
        $this->assertInstanceOf(PersonNameBlock::class, $object->getName('b'));
        $object->setName('c', new PersonNameBlock('Julie', 'Testcase'));
        $this->assertInstanceOf(PersonNameBlock::class, $object->getName('c'));
    }

    public function testGetNameValid2()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertEquals(null, $object->getName('b'));
        $this->assertEquals(null, $object->getName('c'));
    }

    public function testGetSSNValid()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertEquals('', $object->getSSN('b'));
        $this->assertEquals('', $object->getSSN('c'));
    }

    public function testGetSSNValid2()
    {
        $object = new ConsumerCreditRequestData();
        $object->setSSN('b', '000000001');
        $object->setSSN('c', '000000002');
        $this->assertEquals('000000001', $object->getSSN('b'));
        $this->assertEquals('000000002', $object->getSSN('c'));
    }

    public function testGetAddressValidNull()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertEquals(null, $object->getAddress('b'));
        $this->assertEquals(null, $object->getAddress('b', 'Current'));
        $this->assertEquals(null, $object->getAddress('b', 'curRent'));
        $this->assertEquals(null, $object->getAddress('b', 'Prior'));
        $this->assertEquals(null, $object->getAddress('b', 'prioR'));
        $this->assertEquals(null, $object->getAddress('b', 'Mailing'));
        $this->assertEquals(null, $object->getAddress('b', 'MaiLing'));
        $this->assertEquals(null, $object->getAddress('c'));
        $this->assertEquals(null, $object->getAddress('c', 'Current'));
        $this->assertEquals(null, $object->getAddress('c', 'CUrrent'));
        $this->assertEquals(null, $object->getAddress('c', 'Prior'));
        $this->assertEquals(null, $object->getAddress('c', 'pRior'));
        $this->assertEquals(null, $object->getAddress('c', 'Mailing'));
        $this->assertEquals(null, $object->getAddress('c', 'mailing'));
    }

    public function testGetAddressValidDefault()
    {
        $object = new ConsumerCreditRequestData();
        $object->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $this->assertEquals('123 Main St', $object->getAddress('b')->getStreet());
        $this->assertEquals('Garden Grove', $object->getAddress('b')->getCity());
        $this->assertEquals('CA', $object->getAddress('b')->getState());
        $this->assertEquals('92843', $object->getAddress('b')->getZip());
        $this->assertEquals('US', $object->getAddress('b')->getCountry());
        $this->assertEquals('123 Main St', $object->getAddress('b', 'CurrEnt')->getStreet());
        $this->assertEquals('Garden Grove', $object->getAddress('b', 'CurrenT')->getCity());
        $this->assertEquals('CA', $object->getAddress('b', 'current')->getState());
        $this->assertEquals('92843', $object->getAddress('b', 'CURRENT')->getZip());
        $this->assertEquals('US', $object->getAddress('b', 'Current')->getCountry());

        $object->setAddress('c', new AddressBlock('234 Main St', 'Santa Ana', 'NY', '00222'));
        $this->assertEquals('234 Main St', $object->getAddress('c')->getStreet());
        $this->assertEquals('Santa Ana', $object->getAddress('c')->getCity());
        $this->assertEquals('NY', $object->getAddress('c')->getState());
        $this->assertEquals('00222', $object->getAddress('c')->getZip());
        $this->assertEquals('US', $object->getAddress('c')->getCountry());
        $this->assertEquals('234 Main St', $object->getAddress('c', 'CuRRent')->getStreet());
        $this->assertEquals('Santa Ana', $object->getAddress('c', 'CurrenT')->getCity());
        $this->assertEquals('NY', $object->getAddress('c', 'current')->getState());
        $this->assertEquals('00222', $object->getAddress('c', 'CURRENT')->getZip());
        $this->assertEquals('US', $object->getAddress('c', 'CurreNT')->getCountry());
    }

    public function testGetAddressValidCurrent()
    {
        $object = new ConsumerCreditRequestData();
        $object->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'), 'CuRrent');
        $this->assertEquals('123 Main St', $object->getAddress('b', 'Current')->getStreet());
        $this->assertEquals('Garden Grove', $object->getAddress('b', 'Current')->getCity());
        $this->assertEquals('CA', $object->getAddress('b', 'Current')->getState());
        $this->assertEquals('92843', $object->getAddress('b', 'Current')->getZip());
        $this->assertEquals('US', $object->getAddress('b', 'Current')->getCountry());
        $this->assertEquals('123 Main St', $object->getAddress('b')->getStreet());
        $this->assertEquals('Garden Grove', $object->getAddress('b')->getCity());
        $this->assertEquals('CA', $object->getAddress('b')->getState());
        $this->assertEquals('92843', $object->getAddress('b')->getZip());
        $this->assertEquals('US', $object->getAddress('b')->getCountry());

        $object->setAddress('c', new AddressBlock('234 Main St', 'Santa Ana', 'NY', '00222'), 'CurrenT');
        $this->assertEquals('234 Main St', $object->getAddress('c', 'Current')->getStreet());
        $this->assertEquals('Santa Ana', $object->getAddress('c', 'Current')->getCity());
        $this->assertEquals('NY', $object->getAddress('c', 'Current')->getState());
        $this->assertEquals('00222', $object->getAddress('c', 'Current')->getZip());
        $this->assertEquals('US', $object->getAddress('c', 'Current')->getCountry());
        $this->assertEquals('234 Main St', $object->getAddress('c')->getStreet());
        $this->assertEquals('Santa Ana', $object->getAddress('c')->getCity());
        $this->assertEquals('NY', $object->getAddress('c')->getState());
        $this->assertEquals('00222', $object->getAddress('c')->getZip());
        $this->assertEquals('US', $object->getAddress('c')->getCountry());
    }

    public function testGetAddressValidPrior()
    {
        $object = new ConsumerCreditRequestData();
        $object->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'), 'prior');
        $this->assertEquals('123 Main St', $object->getAddress('b', 'PrIor')->getStreet());
        $this->assertEquals('Garden Grove', $object->getAddress('b', 'Prior')->getCity());
        $this->assertEquals('CA', $object->getAddress('b', 'PriOR')->getState());
        $this->assertEquals('92843', $object->getAddress('b', 'Prior')->getZip());
        $this->assertEquals('US', $object->getAddress('b', 'Prior')->getCountry());

        $object->setAddress('c', new AddressBlock('234 Main St', 'Santa Ana', 'NY', '00222'), 'prior');
        $this->assertEquals('234 Main St', $object->getAddress('c', 'Prior')->getStreet());
        $this->assertEquals('Santa Ana', $object->getAddress('c', 'PrioR')->getCity());
        $this->assertEquals('NY', $object->getAddress('c', 'PriOr')->getState());
        $this->assertEquals('00222', $object->getAddress('c', 'PRIOR')->getZip());
        $this->assertEquals('US', $object->getAddress('c', 'prior')->getCountry());
    }

    public function testGetAddressValidMailing()
    {
        $object = new ConsumerCreditRequestData();
        $object->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'), 'mailing');
        $this->assertEquals('123 Main St', $object->getAddress('b', 'MailiNg')->getStreet());
        $this->assertEquals('Garden Grove', $object->getAddress('b', 'MailinG')->getCity());
        $this->assertEquals('CA', $object->getAddress('b', 'mailing')->getState());
        $this->assertEquals('92843', $object->getAddress('b', 'MAILING')->getZip());
        $this->assertEquals('US', $object->getAddress('b', 'Mailing')->getCountry());

        $object->setAddress('c', new AddressBlock('234 Main St', 'Santa Ana', 'NY', '00222'), 'Mailing');
        $this->assertEquals('234 Main St', $object->getAddress('c', 'Mailing')->getStreet());
        $this->assertEquals('Santa Ana', $object->getAddress('c', 'mailing')->getCity());
        $this->assertEquals('NY', $object->getAddress('c', 'MAILING')->getState());
        $this->assertEquals('00222', $object->getAddress('c', 'MaIling')->getZip());
        $this->assertEquals('US', $object->getAddress('c', 'Mailing')->getCountry());
    }

    public function testGetAddressInvalid()
    {
        $object = new ConsumerCreditRequestData();
        $this->expectException(\Exception::class);
        $object->getAddress('b', 'future');
    }

    public function testGetAddressInvalid2()
    {
        $object = new ConsumerCreditRequestData();
        $this->expectException(\Exception::class);
        $object->getAddress('f', 'Current');
    }

    public function testGetDOBValid()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertEquals('', $object->getDOB('b'));
        $this->assertEquals('', $object->getDOB('c'));
    }

    public function testGetDOBValid2()
    {
        $object = new ConsumerCreditRequestData();
        $object->setDOB('b', '01-02-1980');
        $object->setDOB('c', '02-12-1985');
        $this->assertEquals('1980-01-02', $object->getDOB('b'));
        $this->assertEquals('1985-02-12', $object->getDOB('c'));
    }

    public function testGetPhoneValid()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertNull($object->getPhone('b'));
        $this->assertNull($object->getPhone('c'));
    }

    public function testGetPhoneValid2()
    {
        $object = new ConsumerCreditRequestData();
        $object->setPhone('b', new PhoneNumberBlock('4445556666', '123', 'Mobile', 'Description here'));
        $this->assertEquals('4445556666', $object->getPhone('b')->getNumber());
        $this->assertEquals('123', $object->getPhone('b')->getExt());
        $this->assertEquals('Mobile', $object->getPhone('b')->getType());
        $this->assertEquals('Description here', $object->getPhone('b')->getDescription());

        $object->setPhone('c', new PhoneNumberBlock('7778889999', '986', 'Work', 'Description goes here'));
        $this->assertEquals('7778889999', $object->getPhone('c')->getNumber());
        $this->assertEquals('986', $object->getPhone('c')->getExt());
        $this->assertEquals('Work', $object->getPhone('c')->getType());
        $this->assertEquals('Description goes here', $object->getPhone('c')->getDescription());
    }

    public function testGetEmailValid()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertEquals('', $object->getEmail('b'));
        $this->assertEquals('', $object->getEmail('c'));
    }

    public function testGetEmailValid2()
    {
        $object = new ConsumerCreditRequestData();
        $object->setEmail('b', '');
        $object->setEmail('c', '');
        $this->assertEquals('', $object->getEmail('b'));
        $this->assertEquals('', $object->getEmail('c'));
    }

    public function testGetEmailValid3()
    {
        $object = new ConsumerCreditRequestData();
        $object->setEmail('b', 'borrower@test.com');
        $object->setEmail('c', 'coborrower@test.com');
        $this->assertEquals('borrower@test.com', $object->getEmail('b'));
        $this->assertEquals('coborrower@test.com', $object->getEmail('c'));
    }

    public function testGetEmailInvalid()
    {
        $object = new ConsumerCreditRequestData();
        $this->expectException(\Exception::class);
        $object->getEmail('f');
    }

    public function testGetLoanIDValid()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertEquals('', $object->getLoanID());
    }

    public function testGetLoanIDValid2()
    {
        $object = new ConsumerCreditRequestData();
        $object->setLoanID('482jo45thu9f3');
        $this->assertEquals('482jo45thu9f3', $object->getLoanID());
    }

    public function testGetLoanTypeValid()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertEquals('', $object->getLoanType());
    }

    public function testGetLoanTypeValid2()
    {
        $object = new ConsumerCreditRequestData();
        $object->setLoanType('Automobile');
        $this->assertEquals('Automobile', $object->getLoanType());
    }

    public function testGetCreditCard()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertNull($object->getCreditCard());
    }

    public function testGetCreditCard2()
    {
        $object = new ConsumerCreditRequestData();
        $object->setCreditCard(new CreditCardBlock());
        $this->assertInstanceOf(CreditCardBlock::class, $object->getCreditCard());
    }

    public function testGetEquifaxOptions()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertIsArray($object->getEquifaxOptions());
    }

    public function testGetEquifaxOptions2()
    {
        $object = new ConsumerCreditRequestData();
        $object->setEquifaxOptions(false, true);
        $this->assertEquals(['credit' => false, 'score' => true], $object->getEquifaxOptions());
        $object->setEquifaxOptions(true, false);
        $this->assertEquals(['credit' => true, 'score' => false], $object->getEquifaxOptions());
    }

    public function testGetExperianOptions()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertIsArray($object->getExperianOptions());
    }

    public function testGetExperianOptions2()
    {
        $object = new ConsumerCreditRequestData();
        $object->setExperianOptions(false, true, false);
        $this->assertEquals(
            ['credit' => false, 'score' => true, 'fraud' => false],
            $object->getExperianOptions()
        );
        $object->setExperianOptions(true, false, true);
        $this->assertEquals(
            ['credit' => true, 'score' => false, 'fraud' => true],
            $object->getExperianOptions()
        );
    }

    public function testGetTransUnionOptions()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertIsArray($object->getTransUnionOptions());
    }

    public function testGetTransUnionOptions2()
    {
        $object = new ConsumerCreditRequestData();
        $object->setTransUnionOptions(false, true, false);
        $this->assertEquals(
            ['credit' => false, 'score' => true, 'fraud' => false],
            $object->getTransUnionOptions()
        );
        $object->setTransUnionOptions(true, false, true);
        $this->assertEquals(
            ['credit' => true, 'score' => false, 'fraud' => true],
            $object->getTransUnionOptions()
        );
    }

    public function testGetRequestType()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertEquals('', $object->getRequestType());
    }

    public function testGetRequestType2()
    {
        $object = new ConsumerCreditRequestData();
        $object->setRequestType('Submit');
        $this->assertEquals('Submit', $object->getRequestType());
    }

    public function testGetResponseFormats()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertInstanceOf(ResponseFormats::class, $object->getResponseFormats());
    }

    public function testGetVendorOrderID()
    {
        $object = new ConsumerCreditRequestData();
        $this->assertEquals('', $object->getVendorOrderID());
    }

    public function testGetVendorOrderID2()
    {
        $object = new ConsumerCreditRequestData();
        $object->setVendorOrderID('589fhjio34tr');
        $this->assertEquals('589fhjio34tr', $object->getVendorOrderID());
    }

    public function testGetXMLString()
    {
        $object = new ConsumerCreditRequestData();
        $object->setRequestType('Submit');
        $object->setName('b', new PersonNameBlock('David', 'Testcase'));
        $object->setSSN('b', '000000001');
        $object->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $this->assertIsString($object->getXMLString());
    }

    public function testGetXMLStringMissingRequestType()
    {
        $object = new ConsumerCreditRequestData();
        $object->setName('b', new PersonNameBlock('David', 'Testcase'));
        $object->setSSN('b', '000000001');
        $object->setAddress('b', new AddressBlock('123 Main St', 'Garden Grove', 'CA', '92843'));
        $this->expectException(\Exception::class);
        $object->getXMLString();
    }
}
