<?php

namespace jafrajarvy292\SmartAPIHelper\Ancillary;

use PHPUnit\Framework\TestCase;

class PersonNameBlockTest extends TestCase
{
    public function testConstructorFirstLastOnly()
    {
        $this->assertInstanceOf(PersonNameBlock::class, new PersonNameBlock('David', 'Testcase'));
    }

    public function testConstructorFirstLastMiddleOnly()
    {
        $this->assertInstanceOf(
            PersonNameBlock::class,
            new PersonNameBlock('David', 'Testcase', 'Roberts')
        );
    }

    public function testConstructorFirstLastMiddleSuffix()
    {
        $this->assertInstanceOf(
            PersonNameBlock::class,
            new PersonNameBlock('David', 'Testcase', 'Roberts', 'JR')
        );
    }

    public function testGetFirst()
    {
        $test = new PersonNameBlock('David', 'Testcase', 'Roberts', 'JR');
        $this->assertRegExp('/^David$/', $test->getFirst());
    }

    public function testGetLast()
    {
        $test = new PersonNameBlock('David', 'Testcase', 'Roberts', 'JR');
        $this->assertRegExp('/^Testcase$/', $test->getLast());
    }

    public function testGetMiddle()
    {
        $test = new PersonNameBlock('David', 'Testcase', 'Roberts', 'JR');
        $this->assertRegExp('/^Roberts$/', $test->getMiddle());
    }

    public function testGetSuffix()
    {
        $test = new PersonNameBlock('David', 'Testcase', 'Roberts', 'JR');
        $this->assertRegExp('/^JR$/', $test->getSuffix());
    }

    public function testGetXML()
    {
        $test = new PersonNameBlock('David', 'Testcase', 'Roberts', 'JR');
        $document = new \DOMDocument();
        $this->assertInstanceOf(\DOMNode::class, $test->getXML($document));
    }

    public function testGetXMLFirstLast()
    {
        $test = new PersonNameBlock('David', 'Testcase');
        $base = new \DOMDocument();
        $base->appendChild($test->getXML($base));
        $name = $base->getElementsByTagName('NAME')->item(0);
        $first = $name->firstChild;
        $last = $first->nextSibling;
        $this->assertEquals('David', $first->textContent);
        $this->assertEquals('Testcase', $last->textContent);
    }

    public function testGetXMLFirstLastMiddle()
    {
        $test = new PersonNameBlock('David', 'Testcase', 'R');
        $base = new \DOMDocument();
        $base->appendChild($test->getXML($base));
        $name = $base->getElementsByTagName('NAME')->item(0);
        $first = $name->firstChild;
        $last = $first->nextSibling;
        $middle = $last->nextSibling;
        $this->assertEquals('David', $first->textContent);
        $this->assertEquals('Testcase', $last->textContent);
        $this->assertEquals('R', $middle->textContent);
    }

    public function testGetXMLFirstLastMiddleSuffix()
    {
        $test = new PersonNameBlock('David', 'Testcase', 'Roberts', 'JR');
        $base = new \DOMDocument();
        $base->appendChild($test->getXML($base));
        $name = $base->getElementsByTagName('NAME')->item(0);
        $first = $name->firstChild;
        $last = $first->nextSibling;
        $middle = $last->nextSibling;
        $suffix = $middle->nextSibling;
        $this->assertEquals('David', $first->textContent);
        $this->assertEquals('Testcase', $last->textContent);
        $this->assertEquals('Roberts', $middle->textContent);
        $this->assertEquals('JR', $suffix->textContent);
    }

    public function testGetXMLFirstLastSuffix()
    {
        $test = new PersonNameBlock('David', 'Testcase', '', 'JR');
        $base = new \DOMDocument();
        $base->appendChild($test->getXML($base));
        $name = $base->getElementsByTagName('NAME')->item(0);
        $first = $name->firstChild;
        $last = $first->nextSibling;
        $suffix = $last->nextSibling;
        $this->assertEquals('David', $first->textContent);
        $this->assertEquals('Testcase', $last->textContent);
        $this->assertEquals('JR', $suffix->textContent);
    }

    public function testValidateNameApostrophe()
    {
        $this->assertTrue(PersonNameBlock::validateName("O'Hare"));
    }

    public function testValidateNameSpace()
    {
        $this->assertTrue(PersonNameBlock::validateName('O Hare'));
    }

    public function testValidateNameHyphen()
    {
        $this->assertTrue(PersonNameBlock::validateName('Roberts-Anderson'));
    }

    public function testValidateNamePeriod()
    {
        $this->assertTrue(PersonNameBlock::validateName('R.'));
    }

    public function testInvalidName1()
    {
        $this->assertFalse(PersonNameBlock::validateName('James!'));
    }

    public function testInvalidName2()
    {
        $this->assertFalse(PersonNameBlock::validateName(''));
    }

    public function testInvalidName3()
    {
        $this->assertFalse(PersonNameBlock::validateName('Sara (Thomas)'));
    }

    public function testValidateValidSuffix()
    {
        $this->assertTrue(PersonNameBlock::validateSuffix('SR'));
        $this->assertTrue(PersonNameBlock::validateSuffix('JR'));
        $this->assertTrue(PersonNameBlock::validateSuffix('I'));
        $this->assertTrue(PersonNameBlock::validateSuffix('II'));
        $this->assertTrue(PersonNameBlock::validateSuffix('III'));
        $this->assertTrue(PersonNameBlock::validateSuffix('IV'));
        $this->assertTrue(PersonNameBlock::validateSuffix('V'));
        $this->assertTrue(PersonNameBlock::validateSuffix('VI'));
        $this->assertTrue(PersonNameBlock::validateSuffix('VII'));
        $this->assertTrue(PersonNameBlock::validateSuffix('VIII'));
        $this->assertTrue(PersonNameBlock::validateSuffix('IX'));
    }

    public function testValidateInvalidSuffix()
    {
        $this->assertFalse(PersonNameBlock::validateSuffix(''));
        $this->assertFalse(PersonNameBlock::validateSuffix('X'));
        $this->assertFalse(PersonNameBlock::validateSuffix(' '));
        $this->assertFalse(PersonNameBlock::validateSuffix(1));
    }
}
