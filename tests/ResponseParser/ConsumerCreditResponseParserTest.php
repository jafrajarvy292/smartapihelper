<?php

namespace jafrajarvy292\SmartAPIHelper\ResponseParser;

use PHPUnit\Framework\TestCase;

/**
 * Testing here is specific to responses we'll parse for the Consumer Credit product.
 */
class ConsumerCreditResponseParserTest extends TestCase
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
        $object = new ConsumerCreditResponseParser();
        $this->assertNull($object->loadXMLResponse($this->cc_file['01_']));
    }

    public function testLoadXMLResponseBad()
    {
        $object = new ConsumerCreditResponseParser();
        $this->expectException(\Exception::class);
        $object->loadXMLResponse($this->empty_file);
    }

    public function testLoadXMLResponseBad2()
    {
        $object = new ConsumerCreditResponseParser();
        $this->expectException(\Exception::class);
        $object->loadXMLResponse($this->bad_file);
    }

    public function testIsPersonPresent()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['01_']);
        $this->assertFalse($object->isPersonPresent('b'));
        $this->assertFalse($object->isPersonPresent('c'));

        unset($object);
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['02_']);
        $this->assertFalse($object->isPersonPresent('b'));
        $this->assertFalse($object->isPersonPresent('c'));

        unset($object);
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['03_']);
        $this->assertTrue($object->isPersonPresent('b'));
        $this->assertFalse($object->isPersonPresent('c'));

        unset($object);
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['04_']);
        $this->assertFalse($object->isPersonPresent('b'));
        $this->assertFalse($object->isPersonPresent('c'));

        unset($object);
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['05_']);
        $this->assertFalse($object->isPersonPresent('b'));
        $this->assertFalse($object->isPersonPresent('c'));

        unset($object);
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['06_']);
        $this->assertFalse($object->isPersonPresent('b'));
        $this->assertFalse($object->isPersonPresent('c'));

        unset($object);
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['07_']);
        $this->assertTrue($object->isPersonPresent('b'));
        $this->assertFalse($object->isPersonPresent('c'));

        unset($object);
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['08_']);
        $this->assertTrue($object->isPersonPresent('b'));
        $this->assertFalse($object->isPersonPresent('c'));

        unset($object);
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['09_']);
        $this->assertTrue($object->isPersonPresent('b'));
        $this->assertFalse($object->isPersonPresent('c'));

        unset($object);
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['10_']);
        $this->assertTrue($object->isPersonPresent('b'));
        $this->assertFalse($object->isPersonPresent('c'));

        unset($object);
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['11_']);
        $this->assertTrue($object->isPersonPresent('b'));
        $this->assertFalse($object->isPersonPresent('c'));

        unset($object);
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['12_']);
        $this->assertTrue($object->isPersonPresent('b'));
        $this->assertFalse($object->isPersonPresent('c'));

        unset($object);
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['13_']);
        $this->assertTrue($object->isPersonPresent('b'));
        $this->assertTrue($object->isPersonPresent('c'));

        unset($object);
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['14_']);
        $this->assertTrue($object->isPersonPresent('b'));
        $this->assertFalse($object->isPersonPresent('c'));

        unset($object);
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['15_']);
        $this->assertTrue($object->isPersonPresent('b'));
        $this->assertTrue($object->isPersonPresent('c'));
    }

    public function testGetBureauResponsesInvalidID()
    {
        $object = new ConsumerCreditResponseParser();
        $this->expectException(\Exception::class);
        $object->getBureauResponses('d');
    }

    public function testGetBureauResponsesInvalidID2()
    {
        $object = new ConsumerCreditResponseParser();
        $this->expectException(\Exception::class);
        $object->getBureauResponses(1);
    }

    public function testGetBureauResponsesInvalidID3()
    {
        $object = new ConsumerCreditResponseParser();
        $this->expectException(\Exception::class);
        $object->getBureauResponses('');
    }

    public function testGetBureauResponsesBorrower01()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['01_']);
        $this->expectException(\Exception::class);
        $object->getBureauResponses('b');
    }

    public function testGetBureauResponsesCoborrower01()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['01_']);
        $this->expectException(\Exception::class);
        $object->getBureauResponses('c');
    }

    public function testGetBureauResponsesBorrower02()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['02_']);
        $this->expectException(\Exception::class);
        $object->getBureauResponses('b');
    }

    public function testGetBureauResponsesCoborrower02()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['02_']);
        $this->expectException(\Exception::class);
        $object->getBureauResponses('c');
    }

    public function testGetBureauResponsesBorrower03()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['03_']);
        $results = $object->getBureauResponses('b');
        for ($i = 0; $i < count($results); $i++) {
            if ($results[$i]['BureauName'] === 'Experian') {
                $this->assertTrue($results[$i]['Result'] === 'FileReturned');
                $this->assertEquals('', $results[$i]['ErrorDescription']);
            } elseif ($results[$i]['BureauName'] === 'TransUnion') {
                $this->assertTrue($results[$i]['Result'] === 'FileReturned');
                $this->assertEquals('', $results[$i]['ErrorDescription']);
            } elseif ($results[$i]['BureauName'] === 'Equifax') {
                $this->assertTrue($results[$i]['Result'] === 'FileReturned');
                $this->assertEquals('', $results[$i]['ErrorDescription']);
            }
        }
    }

    public function testGetBureauResponsesCoborrower03()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['03_']);
        $this->expectException(\Exception::class);
        $object->getBureauResponses('c');
    }

    public function testGetBureauResponsesBorrower04()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['04_']);
        $this->expectException(\Exception::class);
        $object->getBureauResponses('b');
    }

    public function testGetBureauResponsesCoborrower04()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['04_']);
        $this->expectException(\Exception::class);
        $object->getBureauResponses('c');
    }

    public function testGetBureauResponsesBorrower05()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['05_']);
        $this->expectException(\Exception::class);
        $object->getBureauResponses('b');
    }

    public function testGetBureauResponsesCoborrower05()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['05_']);
        $this->expectException(\Exception::class);
        $object->getBureauResponses('c');
    }

    public function testGetBureauResponsesBorrower06()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['06_']);
        $this->expectException(\Exception::class);
        $object->getBureauResponses('b');
    }

    public function testGetBureauResponsesCoborrower06()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['06_']);
        $this->expectException(\Exception::class);
        $object->getBureauResponses('c');
    }

    public function testGetBureauResponsesBorrower07()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['07_']);
        $results =  $object->getBureauResponses('b');
        for ($i = 0; $i < count($results); $i++) {
            if ($results[$i]['BureauName'] === 'Experian') {
                $this->assertTrue($results[$i]['Result'] === 'FileReturned');
                $this->assertEquals('', $results[$i]['ErrorDescription']);
            } elseif ($results[$i]['BureauName'] === 'TransUnion') {
                $this->assertTrue($results[$i]['Result'] === 'FileReturned');
                $this->assertEquals('', $results[$i]['ErrorDescription']);
            } elseif ($results[$i]['BureauName'] === 'Equifax') {
                $this->assertTrue($results[$i]['Result'] === 'FileReturned');
                $this->assertEquals('', $results[$i]['ErrorDescription']);
            }
        }
    }

    public function testGetBureauResponsesCoborrower07()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['07_']);
        $this->expectException(\Exception::class);
        $object->getBureauResponses('c');
    }

    public function testGetBureauResponsesBorrower08()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['08_']);
        $results =  $object->getBureauResponses('b');
        $this->assertEquals(0, count($results));
    }

    public function testGetBureauResponsesCoborrower08()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['08_']);
        $this->expectException(\Exception::class);
        $object->getBureauResponses('c');
    }

    public function testGetBureauResponsesBorrower09()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['09_']);
        $results =  $object->getBureauResponses('b');
        for ($i = 0; $i < count($results); $i++) {
            if ($results[$i]['BureauName'] === 'TransUnion') {
                $this->assertTrue($results[$i]['Result'] === 'FileReturned');
                $this->assertEquals('', $results[$i]['ErrorDescription']);
            } elseif ($results[$i]['BureauName'] === 'Equifax') {
                $this->assertTrue($results[$i]['Result'] === 'FileReturned');
                $this->assertEquals('', $results[$i]['ErrorDescription']);
            }
        }
    }

    public function testGetBureauResponsesCoborrower09()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['09_']);
        $this->expectException(\Exception::class);
        $object->getBureauResponses('c');
    }

    public function testGetBureauResponsesBorrower10()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['10_']);
        $results =  $object->getBureauResponses('b');
        for ($i = 0; $i < count($results); $i++) {
            if ($results[$i]['BureauName'] === 'Experian') {
                $this->assertTrue($results[$i]['Result'] === 'NoFileReturnedError');
                $this->assertNotEquals('', $results[$i]['ErrorDescription']);
            } elseif ($results[$i]['BureauName'] === 'TransUnion') {
                $this->assertTrue($results[$i]['Result'] === 'FileReturned');
                $this->assertEquals('', $results[$i]['ErrorDescription']);
            } elseif ($results[$i]['BureauName'] === 'Equifax') {
                $this->assertTrue($results[$i]['Result'] === 'NoFileReturnedError');
                $this->assertNotEquals('', $results[$i]['ErrorDescription']);
            }
        }
    }

    public function testGetBureauResponsesCoborrower10()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['10_']);
        $this->expectException(\Exception::class);
        $object->getBureauResponses('c');
    }

    public function testGetBureauResponsesBorrower11()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['11_']);
        $results =  $object->getBureauResponses('b');
        for ($i = 0; $i < count($results); $i++) {
            if ($results[$i]['BureauName'] === 'Experian') {
                $this->assertTrue($results[$i]['Result'] === 'FileReturned');
                $this->assertEquals('', $results[$i]['ErrorDescription']);
            } elseif ($results[$i]['BureauName'] === 'TransUnion') {
                $this->assertTrue($results[$i]['Result'] === 'NoFileReturnedError');
                $this->assertNotEquals('', $results[$i]['ErrorDescription']);
            } elseif ($results[$i]['BureauName'] === 'Equifax') {
                $this->assertTrue($results[$i]['Result'] === 'FileReturned');
                $this->assertEquals('', $results[$i]['ErrorDescription']);
            }
        }
    }

    public function testGetBureauResponsesCoborrower11()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['11_']);
        $this->expectException(\Exception::class);
        $object->getBureauResponses('c');
    }

    public function testGetBureauResponsesBorrower12()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['12_']);
        $results =  $object->getBureauResponses('b');
        for ($i = 0; $i < count($results); $i++) {
            if ($results[$i]['BureauName'] === 'Experian') {
                $this->assertTrue($results[$i]['Result'] === 'FileReturned');
                $this->assertEquals('', $results[$i]['ErrorDescription']);
            } elseif ($results[$i]['BureauName'] === 'TransUnion') {
                $this->assertTrue($results[$i]['Result'] === 'FileReturned');
                $this->assertEquals('', $results[$i]['ErrorDescription']);
            } elseif ($results[$i]['BureauName'] === 'Equifax') {
                $this->assertTrue($results[$i]['Result'] === 'FileReturned');
                $this->assertEquals('', $results[$i]['ErrorDescription']);
            }
        }
    }

    public function testGetBureauResponsesCoborrower12()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['12_']);
        $this->expectException(\Exception::class);
        $object->getBureauResponses('c');
    }

    public function testGetBureauResponsesBorrower13()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['13_']);
        $results =  $object->getBureauResponses('b');
        for ($i = 0; $i < count($results); $i++) {
            if ($results[$i]['BureauName'] === 'Experian') {
                $this->assertTrue($results[$i]['Result'] === 'FileReturned');
                $this->assertEquals('', $results[$i]['ErrorDescription']);
            } elseif ($results[$i]['BureauName'] === 'TransUnion') {
                $this->assertTrue($results[$i]['Result'] === 'FileReturned');
                $this->assertEquals('', $results[$i]['ErrorDescription']);
            } elseif ($results[$i]['BureauName'] === 'Equifax') {
                $this->assertTrue($results[$i]['Result'] === 'FileReturned');
                $this->assertEquals('', $results[$i]['ErrorDescription']);
            }
        }
    }

    public function testGetBureauResponsesCoborrower13()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['13_']);
        $results =  $object->getBureauResponses('c');
        for ($i = 0; $i < count($results); $i++) {
            if ($results[$i]['BureauName'] === 'Experian') {
                $this->assertTrue($results[$i]['Result'] === 'FileReturned');
                $this->assertEquals('', $results[$i]['ErrorDescription']);
            } elseif ($results[$i]['BureauName'] === 'TransUnion') {
                $this->assertTrue($results[$i]['Result'] === 'FileReturned');
                $this->assertEquals('', $results[$i]['ErrorDescription']);
            } elseif ($results[$i]['BureauName'] === 'Equifax') {
                $this->assertTrue($results[$i]['Result'] === 'FileReturned');
                $this->assertEquals('', $results[$i]['ErrorDescription']);
            }
        }
    }

    public function testGetBureauResponsesBorrower14()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['14_']);
        $results =  $object->getBureauResponses('b');
        for ($i = 0; $i < count($results); $i++) {
            if ($results[$i]['BureauName'] === 'Experian') {
                $this->assertTrue($results[$i]['Result'] === 'FileReturned');
                $this->assertEquals('', $results[$i]['ErrorDescription']);
            } elseif ($results[$i]['BureauName'] === 'TransUnion') {
                $this->assertTrue($results[$i]['Result'] === 'FileReturned');
                $this->assertEquals('', $results[$i]['ErrorDescription']);
            } elseif ($results[$i]['BureauName'] === 'Equifax') {
                $this->assertTrue($results[$i]['Result'] === 'FileReturned');
                $this->assertEquals('', $results[$i]['ErrorDescription']);
            }
        }
    }

    public function testGetBureauResponsesCoborrower14()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['14_']);
        $this->expectException(\Exception::class);
        $object->getBureauResponses('c');
    }

    public function testGetBureauResponsesBorrower15()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['15_']);
        $results =  $object->getBureauResponses('b');
        for ($i = 0; $i < count($results); $i++) {
            if ($results[$i]['BureauName'] === 'Experian') {
                $this->assertTrue($results[$i]['Result'] === 'NoFileReturnedError');
                $this->assertEquals('*** NO RECORD FOUND ***', $results[$i]['ErrorDescription']);
            } elseif ($results[$i]['BureauName'] === 'TransUnion') {
                $this->assertTrue($results[$i]['Result'] === 'NoFileReturnedError');
                $this->assertEquals('*** NO RECORD FOUND ***', $results[$i]['ErrorDescription']);
            } elseif ($results[$i]['BureauName'] === 'Equifax') {
                $this->assertTrue($results[$i]['Result'] === 'NoFileReturnedError');
                $this->assertEquals('*** NO RECORD FOUND ***', $results[$i]['ErrorDescription']);
            }
        }
    }

    public function testGetBureauResponsesCoborrower15()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['15_']);
        $results =  $object->getBureauResponses('c');
        for ($i = 0; $i < count($results); $i++) {
            if ($results[$i]['BureauName'] === 'Experian') {
                $this->assertTrue($results[$i]['Result'] === 'FileReturned');
                $this->assertEquals('', $results[$i]['ErrorDescription']);
            } elseif ($results[$i]['BureauName'] === 'TransUnion') {
                $this->assertTrue($results[$i]['Result'] === 'FileReturned');
                $this->assertEquals('', $results[$i]['ErrorDescription']);
            } elseif ($results[$i]['BureauName'] === 'Equifax') {
                $this->assertTrue($results[$i]['Result'] === 'FileReturned');
                $this->assertEquals('', $results[$i]['ErrorDescription']);
            }
        }
    }

    public function testGetCreditScoresBorrower01()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['01_']);
        $this->expectException(\Exception::class);
        $object->getCreditScores('b');
    }

    public function testGetCreditScoresCoborrower01()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['01_']);
        $this->expectException(\Exception::class);
        $object->getCreditScores('c');
    }

    public function testGetCreditScoresBorrower02()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['02_']);
        $this->expectException(\Exception::class);
        $object->getCreditScores('b');
    }

    public function testGetCreditScoresCoborrower02()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['02_']);
        $this->expectException(\Exception::class);
        $object->getCreditScores('c');
    }

    public function testGetCreditScoresBorrower03()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['03_']);
        $results = $object->getCreditScores('b');
        for ($i = 0; $i < count($results); $i++) {
            if ($results[$i]['BureauName'] === 'Experian') {
                $this->assertEquals('2020-01-27', $results[$i]['DateGenerated']);
                $this->assertEquals('850', $results[$i]['MaximumValue']);
                $this->assertEquals('300', $results[$i]['MinimumValue']);
                $this->assertEquals('ExperianFairIsaac', $results[$i]['ModelName']);
                $this->assertEquals('34', $results[$i]['PercentileRank']);
                $this->assertEquals('0669', $results[$i]['ScoreValue']);
                $factors = $results[$i]['ScoreFactors'];
                for ($k = 0; $k < count($factors); $k++) {
                    $this->assertNotEquals('', $factors[$k]['Code']);
                    $this->assertNotEquals('', $factors[$k]['Text']);
                }
            } elseif ($results[$i]['BureauName'] === 'TransUnion') {
                $this->assertEquals('2020-01-27', $results[$i]['DateGenerated']);
                $this->assertEquals('839', $results[$i]['MaximumValue']);
                $this->assertEquals('309', $results[$i]['MinimumValue']);
                $this->assertEquals('FICORiskScoreClassic04', $results[$i]['ModelName']);
                $this->assertEquals('36', $results[$i]['PercentileRank']);
                $this->assertEquals('683', $results[$i]['ScoreValue']);
                $factors = $results[$i]['ScoreFactors'];
                for ($k = 0; $k < count($factors); $k++) {
                    $this->assertNotEquals('', $factors[$k]['Code']);
                    $this->assertNotEquals('', $factors[$k]['Text']);
                }
            } elseif ($results[$i]['BureauName'] === 'Equifax') {
                $this->assertEquals('2020-01-27', $results[$i]['DateGenerated']);
                $this->assertEquals('818', $results[$i]['MaximumValue']);
                $this->assertEquals('334', $results[$i]['MinimumValue']);
                $this->assertEquals('EquifaxBeacon5.0', $results[$i]['ModelName']);
                $this->assertEquals('30', $results[$i]['PercentileRank']);
                $this->assertEquals('00658', $results[$i]['ScoreValue']);
                $factors = $results[$i]['ScoreFactors'];
                for ($k = 0; $k < count($factors); $k++) {
                    $this->assertNotEquals('', $factors[$k]['Code']);
                    $this->assertNotEquals('', $factors[$k]['Text']);
                }
            }
        }
    }

    public function testGetCreditScoresCoborrower03()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['03_']);
        $this->expectException(\Exception::class);
        $object->getCreditScores('c');
    }

    public function testGetCreditScoresBorrower04()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['04_']);
        $this->expectException(\Exception::class);
        $object->getCreditScores('b');
    }

    public function testGetCreditScoresCoborrower04()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['04_']);
        $this->expectException(\Exception::class);
        $object->getCreditScores('c');
    }

    public function testGetCreditScoresBorrower05()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['05_']);
        $this->expectException(\Exception::class);
        $object->getCreditScores('b');
    }

    public function testGetCreditScoresCoborrower05()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['05_']);
        $this->expectException(\Exception::class);
        $object->getCreditScores('c');
    }

    public function testGetCreditScoresBorrower06()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['06_']);
        $this->expectException(\Exception::class);
        $object->getCreditScores('b');
    }

    public function testGetCreditScoresCoborrower06()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['06_']);
        $this->expectException(\Exception::class);
        $object->getCreditScores('c');
    }

    public function testGetCreditScoresBorrower07()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['07_']);
        $results = $object->getCreditScores('b');
        for ($i = 0; $i < count($results); $i++) {
            if ($results[$i]['BureauName'] === 'Experian') {
                $this->assertEquals('2020-01-27', $results[$i]['DateGenerated']);
                $this->assertEquals('850', $results[$i]['MaximumValue']);
                $this->assertEquals('300', $results[$i]['MinimumValue']);
                $this->assertEquals('ExperianFairIsaac', $results[$i]['ModelName']);
                $this->assertEquals('34', $results[$i]['PercentileRank']);
                $this->assertEquals('0669', $results[$i]['ScoreValue']);
                $factors = $results[$i]['ScoreFactors'];
                for ($k = 0; $k < count($factors); $k++) {
                    $this->assertNotEquals('', $factors[$k]['Code']);
                    $this->assertNotEquals('', $factors[$k]['Text']);
                }
            } elseif ($results[$i]['BureauName'] === 'TransUnion') {
                $this->assertEquals('2020-01-27', $results[$i]['DateGenerated']);
                $this->assertEquals('839', $results[$i]['MaximumValue']);
                $this->assertEquals('309', $results[$i]['MinimumValue']);
                $this->assertEquals('FICORiskScoreClassic04', $results[$i]['ModelName']);
                $this->assertEquals('36', $results[$i]['PercentileRank']);
                $this->assertEquals('683', $results[$i]['ScoreValue']);
                $factors = $results[$i]['ScoreFactors'];
                for ($k = 0; $k < count($factors); $k++) {
                    $this->assertNotEquals('', $factors[$k]['Code']);
                    $this->assertNotEquals('', $factors[$k]['Text']);
                }
            } elseif ($results[$i]['BureauName'] === 'Equifax') {
                $this->assertEquals('2020-01-27', $results[$i]['DateGenerated']);
                $this->assertEquals('818', $results[$i]['MaximumValue']);
                $this->assertEquals('334', $results[$i]['MinimumValue']);
                $this->assertEquals('EquifaxBeacon5.0', $results[$i]['ModelName']);
                $this->assertEquals('30', $results[$i]['PercentileRank']);
                $this->assertEquals('00658', $results[$i]['ScoreValue']);
                $factors = $results[$i]['ScoreFactors'];
                for ($k = 0; $k < count($factors); $k++) {
                    $this->assertNotEquals('', $factors[$k]['Code']);
                    $this->assertNotEquals('', $factors[$k]['Text']);
                }
            }
        }
    }

    public function testGetCreditScoresCoborrower07()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['07_']);
        $this->expectException(\Exception::class);
        $object->getCreditScores('c');
    }

    public function testGetCreditScoresBorrower08()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['08_']);
        $results = $object->getCreditScores('b');
        $this->assertEquals(0, count($results));
    }

    public function testGetCreditScoresCoborrower08()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['08_']);
        $this->expectException(\Exception::class);
        $object->getCreditScores('c');
    }

    public function testGetCreditScoresBorrower09()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['09_']);
        $results = $object->getCreditScores('b');
        for ($i = 0; $i < count($results); $i++) {
            if ($results[$i]['BureauName'] === 'TransUnion') {
                $this->assertEquals('2020-01-27', $results[$i]['DateGenerated']);
                $this->assertEquals('839', $results[$i]['MaximumValue']);
                $this->assertEquals('309', $results[$i]['MinimumValue']);
                $this->assertEquals('FICORiskScoreClassic04', $results[$i]['ModelName']);
                $this->assertEquals('87', $results[$i]['PercentileRank']);
                $this->assertEquals('804', $results[$i]['ScoreValue']);
                $factors = $results[$i]['ScoreFactors'];
                for ($k = 0; $k < count($factors); $k++) {
                    $this->assertNotEquals('', $factors[$k]['Code']);
                    $this->assertNotEquals('', $factors[$k]['Text']);
                }
            } elseif ($results[$i]['BureauName'] === 'Equifax') {
                $this->assertEquals('2020-01-27', $results[$i]['DateGenerated']);
                $this->assertEquals('818', $results[$i]['MaximumValue']);
                $this->assertEquals('334', $results[$i]['MinimumValue']);
                $this->assertEquals('EquifaxBeacon5.0', $results[$i]['ModelName']);
                $this->assertEquals('3', $results[$i]['PercentileRank']);
                $this->assertEquals('00497', $results[$i]['ScoreValue']);
                $factors = $results[$i]['ScoreFactors'];
                for ($k = 0; $k < count($factors); $k++) {
                    $this->assertNotEquals('', $factors[$k]['Code']);
                    $this->assertNotEquals('', $factors[$k]['Text']);
                }
            }
        }
    }

    public function testGetCreditScoresCoborrower09()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['09_']);
        $this->expectException(\Exception::class);
        $object->getCreditScores('c');
    }

    public function testGetCreditScoresBorrower10()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['10_']);
        $results = $object->getCreditScores('b');
        for ($i = 0; $i < count($results); $i++) {
            if ($results[$i]['BureauName'] === 'TransUnion') {
                $this->assertEquals('2020-01-27', $results[$i]['DateGenerated']);
                $this->assertEquals('839', $results[$i]['MaximumValue']);
                $this->assertEquals('309', $results[$i]['MinimumValue']);
                $this->assertEquals('FICORiskScoreClassic04', $results[$i]['ModelName']);
                $this->assertEquals('47', $results[$i]['PercentileRank']);
                $this->assertEquals('00720', $results[$i]['ScoreValue']);
                $factors = $results[$i]['ScoreFactors'];
                for ($k = 0; $k < count($factors); $k++) {
                    $this->assertNotEquals('', $factors[$k]['Code']);
                    $this->assertNotEquals('', $factors[$k]['Text']);
                }
            }
        }
    }

    public function testGetCreditScoresCoborrower10()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['10_']);
        $this->expectException(\Exception::class);
        $object->getCreditScores('c');
    }

    public function testGetCreditScoresBorrower11()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['11_']);
        $results = $object->getCreditScores('b');
        for ($i = 0; $i < count($results); $i++) {
            if (
                $results[$i]['BureauName'] === 'Experian' &&
                $results[$i]['ModelName'] === 'ExperianFairIsaac'
            ) {
                $this->assertEquals('2020-01-27', $results[$i]['DateGenerated']);
                $this->assertEquals('850', $results[$i]['MaximumValue']);
                $this->assertEquals('300', $results[$i]['MinimumValue']);
                $this->assertEquals('50', $results[$i]['PercentileRank']);
                $this->assertEquals('0728', $results[$i]['ScoreValue']);
                $factors = $results[$i]['ScoreFactors'];
                for ($k = 0; $k < count($factors); $k++) {
                    $this->assertNotEquals('', $factors[$k]['Code']);
                    $this->assertNotEquals('', $factors[$k]['Text']);
                }
            } elseif (
                $results[$i]['BureauName'] === 'Experian' &&
                $results[$i]['ModelName'] === 'ExperianVantageScoreV4'
            ) {
                $this->assertEquals('2020-01-27', $results[$i]['DateGenerated']);
                $this->assertEquals('850', $results[$i]['MaximumValue']);
                $this->assertEquals('300', $results[$i]['MinimumValue']);
                $this->assertEquals('56', $results[$i]['PercentileRank']);
                $this->assertEquals('0706', $results[$i]['ScoreValue']);
                $factors = $results[$i]['ScoreFactors'];
                for ($k = 0; $k < count($factors); $k++) {
                    $this->assertNotEquals('', $factors[$k]['Code']);
                    $this->assertNotEquals('', $factors[$k]['Text']);
                }
            } elseif ($results[$i]['BureauName'] === 'Equifax') {
                $this->assertEquals('2020-01-27', $results[$i]['DateGenerated']);
                $this->assertEquals('818', $results[$i]['MaximumValue']);
                $this->assertEquals('334', $results[$i]['MinimumValue']);
                $this->assertEquals('EquifaxBeacon5.0', $results[$i]['ModelName']);
                $this->assertEquals('', $results[$i]['PercentileRank']);
                $this->assertEquals('N/A', $results[$i]['ScoreValue']);
                $factors = $results[$i]['ScoreFactors'];
                for ($k = 0; $k < count($factors); $k++) {
                    $this->assertNotEquals('', $factors[$k]['Code']);
                    $this->assertNotEquals('', $factors[$k]['Text']);
                }
            }
        }
    }

    public function testGetCreditScoresCoborrower11()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['11_']);
        $this->expectException(\Exception::class);
        $object->getCreditScores('c');
    }

    public function testGetCreditScoresBorrower12()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['12_']);
        $results = $object->getCreditScores('b');
        for ($i = 0; $i < count($results); $i++) {
            if ($results[$i]['BureauName'] === 'Experian') {
                $this->assertEquals('2020-01-27', $results[$i]['DateGenerated']);
                $this->assertEquals('850', $results[$i]['MaximumValue']);
                $this->assertEquals('300', $results[$i]['MinimumValue']);
                $this->assertEquals('ExperianFairIsaac', $results[$i]['ModelName']);
                $this->assertEquals('55', $results[$i]['PercentileRank']);
                $this->assertEquals('0745', $results[$i]['ScoreValue']);
                $factors = $results[$i]['ScoreFactors'];
                for ($k = 0; $k < count($factors); $k++) {
                    $this->assertNotEquals('', $factors[$k]['Code']);
                    $this->assertNotEquals('', $factors[$k]['Text']);
                }
            } elseif ($results[$i]['BureauName'] === 'TransUnion') {
                $this->assertEquals('2020-01-27', $results[$i]['DateGenerated']);
                $this->assertEquals('843', $results[$i]['MaximumValue']);
                $this->assertEquals('336', $results[$i]['MinimumValue']);
                $this->assertEquals('FICORiskScoreClassic98', $results[$i]['ModelName']);
                $this->assertEquals('55', $results[$i]['PercentileRank']);
                $this->assertEquals('741', $results[$i]['ScoreValue']);
                $factors = $results[$i]['ScoreFactors'];
                for ($k = 0; $k < count($factors); $k++) {
                    $this->assertNotEquals('', $factors[$k]['Code']);
                    $this->assertNotEquals('', $factors[$k]['Text']);
                }
            } elseif ($results[$i]['BureauName'] === 'Equifax') {
                $this->assertEquals('2020-01-27', $results[$i]['DateGenerated']);
                $this->assertEquals('818', $results[$i]['MaximumValue']);
                $this->assertEquals('334', $results[$i]['MinimumValue']);
                $this->assertEquals('EquifaxBeacon5.0', $results[$i]['ModelName']);
                $this->assertEquals('55', $results[$i]['PercentileRank']);
                $this->assertEquals('00743', $results[$i]['ScoreValue']);
                $factors = $results[$i]['ScoreFactors'];
                for ($k = 0; $k < count($factors); $k++) {
                    $this->assertNotEquals('', $factors[$k]['Code']);
                    $this->assertNotEquals('', $factors[$k]['Text']);
                }
            }
        }
    }

    public function testGetCreditScoresCoborrower12()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['12_']);
        $this->expectException(\Exception::class);
        $object->getCreditScores('c');
    }

    public function testGetCreditScoresBorrower13()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['13_']);
        $results = $object->getCreditScores('b');
        for ($i = 0; $i < count($results); $i++) {
            if ($results[$i]['BureauName'] === 'Experian') {
                $this->assertEquals('2020-01-27', $results[$i]['DateGenerated']);
                $this->assertEquals('850', $results[$i]['MaximumValue']);
                $this->assertEquals('300', $results[$i]['MinimumValue']);
                $this->assertEquals('ExperianFairIsaac', $results[$i]['ModelName']);
                $this->assertEquals('18', $results[$i]['PercentileRank']);
                $this->assertEquals('0592', $results[$i]['ScoreValue']);
                $factors = $results[$i]['ScoreFactors'];
                for ($k = 0; $k < count($factors); $k++) {
                    $this->assertNotEquals('', $factors[$k]['Code']);
                    $this->assertNotEquals('', $factors[$k]['Text']);
                }
            } elseif ($results[$i]['BureauName'] === 'TransUnion') {
                $this->assertEquals('2020-01-27', $results[$i]['DateGenerated']);
                $this->assertEquals('839', $results[$i]['MaximumValue']);
                $this->assertEquals('309', $results[$i]['MinimumValue']);
                $this->assertEquals('FICORiskScoreClassic04', $results[$i]['ModelName']);
                $this->assertEquals('19', $results[$i]['PercentileRank']);
                $this->assertEquals('605', $results[$i]['ScoreValue']);
                $factors = $results[$i]['ScoreFactors'];
                for ($k = 0; $k < count($factors); $k++) {
                    $this->assertNotEquals('', $factors[$k]['Code']);
                    $this->assertNotEquals('', $factors[$k]['Text']);
                }
            } elseif ($results[$i]['BureauName'] === 'Equifax') {
                $this->assertEquals('2020-01-27', $results[$i]['DateGenerated']);
                $this->assertEquals('818', $results[$i]['MaximumValue']);
                $this->assertEquals('334', $results[$i]['MinimumValue']);
                $this->assertEquals('EquifaxBeacon5.0', $results[$i]['ModelName']);
                $this->assertEquals('13', $results[$i]['PercentileRank']);
                $this->assertEquals('00580', $results[$i]['ScoreValue']);
                $factors = $results[$i]['ScoreFactors'];
                for ($k = 0; $k < count($factors); $k++) {
                    $this->assertNotEquals('', $factors[$k]['Code']);
                    $this->assertNotEquals('', $factors[$k]['Text']);
                }
            }
        }
    }

    public function testGetCreditScoresCoborrower13()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['13_']);
        $results = $object->getCreditScores('c');
        for ($i = 0; $i < count($results); $i++) {
            if ($results[$i]['BureauName'] === 'Experian') {
                $this->assertEquals('2020-01-27', $results[$i]['DateGenerated']);
                $this->assertEquals('850', $results[$i]['MaximumValue']);
                $this->assertEquals('300', $results[$i]['MinimumValue']);
                $this->assertEquals('ExperianFairIsaac', $results[$i]['ModelName']);
                $this->assertEquals('33', $results[$i]['PercentileRank']);
                $this->assertEquals('0666', $results[$i]['ScoreValue']);
                $factors = $results[$i]['ScoreFactors'];
                for ($k = 0; $k < count($factors); $k++) {
                    $this->assertNotEquals('', $factors[$k]['Code']);
                    $this->assertNotEquals('', $factors[$k]['Text']);
                }
            } elseif ($results[$i]['BureauName'] === 'TransUnion') {
                $this->assertEquals('2020-01-27', $results[$i]['DateGenerated']);
                $this->assertEquals('839', $results[$i]['MaximumValue']);
                $this->assertEquals('309', $results[$i]['MinimumValue']);
                $this->assertEquals('FICORiskScoreClassic04', $results[$i]['ModelName']);
                $this->assertEquals('22', $results[$i]['PercentileRank']);
                $this->assertEquals('623', $results[$i]['ScoreValue']);
                $factors = $results[$i]['ScoreFactors'];
                for ($k = 0; $k < count($factors); $k++) {
                    $this->assertNotEquals('', $factors[$k]['Code']);
                    $this->assertNotEquals('', $factors[$k]['Text']);
                }
            } elseif ($results[$i]['BureauName'] === 'Equifax') {
                $this->assertEquals('2020-01-27', $results[$i]['DateGenerated']);
                $this->assertEquals('818', $results[$i]['MaximumValue']);
                $this->assertEquals('334', $results[$i]['MinimumValue']);
                $this->assertEquals('EquifaxBeacon5.0', $results[$i]['ModelName']);
                $this->assertEquals('29', $results[$i]['PercentileRank']);
                $this->assertEquals('00654', $results[$i]['ScoreValue']);
                $factors = $results[$i]['ScoreFactors'];
                for ($k = 0; $k < count($factors); $k++) {
                    $this->assertNotEquals('', $factors[$k]['Code']);
                    $this->assertNotEquals('', $factors[$k]['Text']);
                }
            }
        }
    }

    public function testGetCreditScoresBorrower14()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['14_']);
        $results = $object->getCreditScores('b');
        $this->assertEquals(0, count($results));
    }

    public function testGetCreditScoresCoborrower14()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['14_']);
        $this->expectException(\Exception::class);
        $object->getCreditScores('c');
    }

    public function testGetCreditScoresBorrower15()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['15_']);
        $results = $object->getCreditScores('b');
        $this->assertEquals(0, count($results));
    }

    public function testGetCreditScoresCoborrower15()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['15_']);
        $results = $object->getCreditScores('c');
        for ($i = 0; $i < count($results); $i++) {
            if ($results[$i]['BureauName'] === 'Experian') {
                $this->assertEquals('2020-01-31', $results[$i]['DateGenerated']);
                $this->assertEquals('850', $results[$i]['MaximumValue']);
                $this->assertEquals('300', $results[$i]['MinimumValue']);
                $this->assertEquals('ExperianFairIsaac', $results[$i]['ModelName']);
                $this->assertEquals('35', $results[$i]['PercentileRank']);
                $this->assertEquals('0672', $results[$i]['ScoreValue']);
                $factors = $results[$i]['ScoreFactors'];
                for ($k = 0; $k < count($factors); $k++) {
                    $this->assertNotEquals('', $factors[$k]['Code']);
                    $this->assertNotEquals('', $factors[$k]['Text']);
                }
            } elseif ($results[$i]['BureauName'] === 'TransUnion') {
                $this->assertEquals('2020-01-31', $results[$i]['DateGenerated']);
                $this->assertEquals('839', $results[$i]['MaximumValue']);
                $this->assertEquals('309', $results[$i]['MinimumValue']);
                $this->assertEquals('FICORiskScoreClassic04', $results[$i]['ModelName']);
                $this->assertEquals('87', $results[$i]['PercentileRank']);
                $this->assertEquals('804', $results[$i]['ScoreValue']);
                $factors = $results[$i]['ScoreFactors'];
                for ($k = 0; $k < count($factors); $k++) {
                    $this->assertNotEquals('', $factors[$k]['Code']);
                    $this->assertNotEquals('', $factors[$k]['Text']);
                }
            } elseif ($results[$i]['BureauName'] === 'Equifax') {
                $this->assertEquals('2020-02-01', $results[$i]['DateGenerated']);
                $this->assertEquals('818', $results[$i]['MaximumValue']);
                $this->assertEquals('334', $results[$i]['MinimumValue']);
                $this->assertEquals('EquifaxBeacon5.0', $results[$i]['ModelName']);
                $this->assertEquals('3', $results[$i]['PercentileRank']);
                $this->assertEquals('00497', $results[$i]['ScoreValue']);
                $factors = $results[$i]['ScoreFactors'];
                for ($k = 0; $k < count($factors); $k++) {
                    $this->assertNotEquals('', $factors[$k]['Code']);
                    $this->assertNotEquals('', $factors[$k]['Text']);
                }
            }
        }
    }

    public function testGetCreditFileLabelsBorrower01()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['01_']);
        $this->expectException(\Exception::class);
        $object->getCreditFileLabels('b');
    }

    public function testGetCreditFileLabelsCoborrower01()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['01_']);
        $this->expectException(\Exception::class);
        $object->getCreditFileLabels('c');
    }

    public function testGetCreditFileLabelsBorrower02()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['02_']);
        $this->expectException(\Exception::class);
        $object->getCreditFileLabels('b');
    }

    public function testGetCreditFileLabelsCoborrower02()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['02_']);
        $this->expectException(\Exception::class);
        $object->getCreditFileLabels('c');
    }

    public function testGetCreditFileLabelsBorrower03()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['03_']);
        $results = $object->getCreditFileLabels('b');
        $this->assertEquals(3, count($results));
        for ($i = 0; $i < count($results); $i++) {
            $this->assertNotEquals('', $results[$i]);
        }
    }

    public function testGetCreditFileLabelsCoborrower03()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['03_']);
        $this->expectException(\Exception::class);
        $object->getCreditFileLabels('c');
    }

    public function testGetCreditFileLabelsBorrower04()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['04_']);
        $this->expectException(\Exception::class);
        $object->getCreditFileLabels('c');
    }

    public function testGetCreditFileLabelsCoborrower04()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['04_']);
        $this->expectException(\Exception::class);
        $object->getCreditFileLabels('c');
    }

    public function testGetCreditFileLabelsBorrower05()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['05_']);
        $this->expectException(\Exception::class);
        $object->getCreditFileLabels('c');
    }

    public function testGetCreditFileLabelsCoborrower05()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['05_']);
        $this->expectException(\Exception::class);
        $object->getCreditFileLabels('c');
    }

    public function testGetCreditFileLabelsBorrower06()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['06_']);
        $this->expectException(\Exception::class);
        $object->getCreditFileLabels('b');
    }

    public function testGetCreditFileLabelsCoborrower06()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['06_']);
        $this->expectException(\Exception::class);
        $object->getCreditFileLabels('c');
    }

    public function testGetCreditFileLabelsBorrower07()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['07_']);
        $results = $object->getCreditFileLabels('b');
        $this->assertEquals(3, count($results));
        for ($i = 0; $i < count($results); $i++) {
            $this->assertNotEquals('', $results[$i]);
        }
    }

    public function testGetCreditFileLabelsCoborrower07()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['07_']);
        $this->expectException(\Exception::class);
        $object->getCreditFileLabels('c');
    }

    public function testGetCreditFileLabelsBorrower08()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['08_']);
        $results = $object->getCreditFileLabels('b');
        $this->assertEquals(0, count($results));
    }

    public function testGetCreditFileLabelsCoborrower08()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['08_']);
        $this->expectException(\Exception::class);
        $object->getCreditFileLabels('c');
    }

    public function testGetCreditFileLabelsBorrower09()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['09_']);
        $results = $object->getCreditFileLabels('b');
        $this->assertEquals(2, count($results));
        for ($i = 0; $i < count($results); $i++) {
            $this->assertNotEquals('', $results[$i]);
        }
    }

    public function testGetCreditFileLabelsCoborrower09()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['09_']);
        $this->expectException(\Exception::class);
        $object->getCreditFileLabels('c');
    }

    public function testGetCreditFileLabelsBorrower10()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['10_']);
        $results = $object->getCreditFileLabels('b');
        $this->assertEquals(3, count($results));
        for ($i = 0; $i < count($results); $i++) {
            $this->assertNotEquals('', $results[$i]);
        }
    }

    public function testGetCreditFileLabelsCoborrower10()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['10_']);
        $this->expectException(\Exception::class);
        $object->getCreditFileLabels('c');
    }

    public function testGetCreditFileLabelsBorrower11()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['11_']);
        $results = $object->getCreditFileLabels('b');
        $this->assertEquals(3, count($results));
        for ($i = 0; $i < count($results); $i++) {
            $this->assertNotEquals('', $results[$i]);
        }
    }

    public function testGetCreditFileLabelsCoborrower11()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['11_']);
        $this->expectException(\Exception::class);
        $object->getCreditFileLabels('c');
    }

    public function testGetCreditFileLabelsBorrower12()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['12_']);
        $results = $object->getCreditFileLabels('b');
        $this->assertEquals(3, count($results));
        for ($i = 0; $i < count($results); $i++) {
            $this->assertNotEquals('', $results[$i]);
        }
    }

    public function testGetCreditFileLabelsCoborrower12()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['12_']);
        $this->expectException(\Exception::class);
        $object->getCreditFileLabels('c');
    }

    public function testGetCreditFileLabelsBorrower13()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['13_']);
        $results = $object->getCreditFileLabels('b');
        $this->assertEquals(3, count($results));
        for ($i = 0; $i < count($results); $i++) {
            $this->assertNotEquals('', $results[$i]);
        }
    }

    public function testGetCreditFileLabelsCoborrower13()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['13_']);
        $results = $object->getCreditFileLabels('c');
        $this->assertEquals(3, count($results));
        for ($i = 0; $i < count($results); $i++) {
            $this->assertNotEquals('', $results[$i]);
        }
    }

    public function testGetCreditFileLabelsBorrower14()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['14_']);
        $results = $object->getCreditFileLabels('b');
        $this->assertEquals(3, count($results));
        for ($i = 0; $i < count($results); $i++) {
            $this->assertNotEquals('', $results[$i]);
        }
    }

    public function testGetCreditFileLabelsCoborrower14()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['14_']);
        $this->expectException(\Exception::class);
        $object->getCreditFileLabels('c');
    }

    public function testGetCreditFileLabelsBorrower15()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['15_']);
        $results = $object->getCreditFileLabels('b');
        $this->assertEquals(3, count($results));
        for ($i = 0; $i < count($results); $i++) {
            $this->assertNotEquals('', $results[$i]);
        }
    }

    public function testGetCreditFileLabelsCoborrower15()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['15_']);
        $results = $object->getCreditFileLabels('c');
        $this->assertEquals(3, count($results));
        for ($i = 0; $i < count($results); $i++) {
            $this->assertNotEquals('', $results[$i]);
        }
    }

    public function testGetLiabilitiesBorrower01()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['01_']);
        $this->expectException(\Exception::class);
        $object->getLiabilities('b');
    }

    public function testGetLiabilitiesCoborrower01()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['01_']);
        $this->expectException(\Exception::class);
        $object->getLiabilities('c');
    }

    public function testGetLiabilitiesAll01()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['01_']);
        $results = $object->getLiabilities();
        $this->assertEquals(0, count($results));
    }

    public function testGetLiabilitiesBorrower02()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['02_']);
        $this->expectException(\Exception::class);
        $object->getLiabilities('b');
    }

    public function testGetLiabilitiesCoborrower02()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['02_']);
        $this->expectException(\Exception::class);
        $object->getLiabilities('c');
    }

    public function testGetLiabilitiesAll02()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['01_']);
        $results = $object->getLiabilities();
        $this->assertEquals(0, count($results));
    }


    public function testGetLiabilitiesBorrower03()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['03_']);
        $results = $object->getLiabilities('b');
        $this->assertEquals(41, count($results));
        $this->assertEquals('Account888888888888888', $results[0]['FullName']);
        $this->assertEquals('7777777777', $results[1]['CreditLiabilityAccountIdentifier']);
        $this->assertEquals('Installment', $results[2]['CreditLiabilityAccountType']);
        $this->assertEquals('1798.00', $results[3]['CreditLiabilityUnpaidBalanceAmount']);
        $this->assertEquals('C', $results[4]['CreditLiabilityCurrentRatingCode']);
        $this->assertEquals('AsAgreed', $results[5]['CreditLiabilityCurrentRatingType']);
        $this->assertEquals('28.00', $results[6]['CreditLiabilityMonthlyPaymentAmount']);
        $this->assertEquals('Individual', $results[7]['CreditLiabilityAccountOwnershipType']);
        $this->assertEquals('CHARGE', $results[8]['AccountRemarks']);
        $this->assertEquals('Banking', $results[9]['CreditBusinessType']);
        $this->assertEquals('DepartmentAndMailOrderMiscellaneous', $results[10]['DetailCreditBusinessType']);
        $this->assertEquals('CreditCard', $results[11]['CreditLoanType']);
        $this->assertEquals('0', $results[12]['CreditLiability30DaysLateCount']);
        $this->assertEquals('0', $results[17]['CreditLiability60DaysLateCount']);
        $this->assertEquals('0', $results[30]['CreditLiability90DaysLateCount']);
        $this->assertEquals('2018-01-01', $results[31]['CreditLiabilityAccountOpenedDate']);
        $this->assertEquals('2018-09-01', $results[31]['CreditLiabilityAccountClosedDate']);
        $this->assertEquals('2018-07-01', $results[32]['CreditLiabilityAccountPaidDate']);
        $this->assertEquals('2017-10-01', $results[33]['CreditLiabilityAccountReportedDate']);
        $this->assertEquals('Paid', $results[34]['CreditLiabilityAccountStatusType']);
        $this->assertEquals('6000.00', $results[38]['CreditLiabilityCreditLimitAmount']);
        $this->assertEquals('4100.00', $results[40]['CreditLiabilityHighBalanceAmount']);
        $this->assertEquals('2019-11-01', $results[7]['CreditLiabilityHighestAdverseRatingDate']);
        $this->assertEquals('1', $results[27]['CreditLiabilityHighestAdverseRatingCode']);
        $this->assertEquals('Late30Days', $results[27]['CreditLiabilityHighestAdverseRatingType']);
        $this->assertEquals('2014-08-01', $results[29]['CreditLiabilityLastActivityDate']);
        $this->assertEquals('352', $results[31]['CreditLiabilityMonthsRemainingCount']);
        $this->assertEquals('92', $results[30]['CreditLiabilityMonthsReviewedCount']);
        $this->assertEquals('0.00', $results[40]['CreditLiabilityPastDueAmount']);
        $this->assertEquals('XCCCCCCCCCCCCCCCCC', $results[39]['CreditLiabilityPaymentPatternDataText']);
        $this->assertEquals('2017-10-01', $results[33]['CreditLiabilityPaymentPatternStartDate']);
        $this->assertEquals('', $results[33]['CreditLiabilityTermsDescription']);
        $this->assertEquals('360', $results[31]['CreditLiabilityTermsMonthsCount']);
        $this->assertEquals('Provided', $results[17]['CreditLiabilityTermsSourceType']);
        $this->assertEquals('PO BOX 8122', $results[17]['CreditorAddressStreet']);
        $this->assertEquals('MASON', $results[18]['CreditorAddressCity']);
        $this->assertEquals('CA', $results[19]['CreditorAddressState']);
        $this->assertEquals('43224', $results[21]['CreditorAddressZip']);
        $this->assertEquals('8008489136', $results[21]['ContactPointTelephoneValue']);
    }

    public function testGetLiabilitiesCoborrower03()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['03_']);
        $this->expectException(\Exception::class);
        $object->getLiabilities('c');
    }

    public function testGetLiabilitiesAll03()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['03_']);
        $results = $object->getLiabilities();
        $this->assertEquals(41, count($results));
    }

    public function testGetLiabilitiesBorrower04()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['04_']);
        $this->expectException(\Exception::class);
        $object->getLiabilities('b');
    }

    public function testGetLiabilitiesCoborrower04()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['04_']);
        $this->expectException(\Exception::class);
        $object->getLiabilities('c');
    }

    public function testGetLiabilitiesAll04()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['04_']);
        $results = $object->getLiabilities();
        $this->assertEquals(0, count($results));
    }

    public function testGetLiabilitiesBorrower05()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['05_']);
        $this->expectException(\Exception::class);
        $object->getLiabilities('b');
    }

    public function testGetLiabilitiesCoborrower05()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['05_']);
        $this->expectException(\Exception::class);
        $object->getLiabilities('c');
    }

    public function testGetLiabilitiesAll05()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['05_']);
        $results = $object->getLiabilities();
        $this->assertEquals(0, count($results));
    }

    public function testGetLiabilitiesBorrower06()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['06_']);
        $this->expectException(\Exception::class);
        $object->getLiabilities('b');
    }

    public function testGetLiabilitiesCoborrower06()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['06_']);
        $this->expectException(\Exception::class);
        $object->getLiabilities('c');
    }

    public function testGetLiabilitiesAll06()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['06_']);
        $results = $object->getLiabilities();
        $this->assertEquals(0, count($results));
    }

    public function testGetLiabilitiesBorrower07()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['07_']);
        $results = $object->getLiabilities('b');
        $this->assertEquals(41, count($results));
    }

    public function testGetLiabilitiesCoborrower07()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['07_']);
        $this->expectException(\Exception::class);
        $object->getLiabilities('c');
    }

    public function testGetLiabilitiesAll07()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['07_']);
        $results = $object->getLiabilities();
        $this->assertEquals(41, count($results));
    }

    public function testGetLiabilitiesBorrower08()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['08_']);
        $results = $object->getLiabilities('b');
        $this->assertEquals(0, count($results));
    }

    public function testGetLiabilitiesCoborrower08()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['08_']);
        $this->expectException(\Exception::class);
        $object->getLiabilities('c');
    }

    public function testGetLiabilitiesAll08()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['08_']);
        $results = $object->getLiabilities();
        $this->assertEquals(0, count($results));
    }

    public function testGetLiabilitiesBorrower09()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['09_']);
        $results = $object->getLiabilities('b');
        $this->assertEquals(32, count($results));
    }

    public function testGetLiabilitiesCoborrower09()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['09_']);
        $this->expectException(\Exception::class);
        $object->getLiabilities('c');
    }

    public function testGetLiabilitiesAll09()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['09_']);
        $results = $object->getLiabilities();
        $this->assertEquals(32, count($results));
    }

    public function testGetLiabilitiesBorrower10()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['10_']);
        $results = $object->getLiabilities('b');
        $this->assertEquals(2, count($results));
    }

    public function testGetLiabilitiesCoborrower10()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['10_']);
        $this->expectException(\Exception::class);
        $object->getLiabilities('c');
    }

    public function testGetLiabilitiesAll10()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['10_']);
        $results = $object->getLiabilities();
        $this->assertEquals(2, count($results));
    }

    public function testGetLiabilitiesBorrower11()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['11_']);
        $results = $object->getLiabilities('b');
        $this->assertEquals(19, count($results));
    }

    public function testGetLiabilitiesCoborrower11()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['11_']);
        $this->expectException(\Exception::class);
        $object->getLiabilities('c');
    }

    public function testGetLiabilitiesAll11()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['11_']);
        $results = $object->getLiabilities();
        $this->assertEquals(19, count($results));
    }

    public function testGetLiabilitiesBorrower12()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['12_']);
        $results = $object->getLiabilities('b');
        $this->assertEquals(14, count($results));
    }

    public function testGetLiabilitiesCoborrower12()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['12_']);
        $this->expectException(\Exception::class);
        $object->getLiabilities('c');
    }

    public function testGetLiabilitiesAll12()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['12_']);
        $results = $object->getLiabilities();
        $this->assertEquals(14, count($results));
    }

    public function testGetLiabilitiesBorrower13()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['13_']);
        $results = $object->getLiabilities('b');
        $this->assertEquals(42, count($results));
    }

    public function testGetLiabilitiesCoborrower13()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['13_']);
        $results = $object->getLiabilities('c');
        $this->assertEquals(22, count($results));
    }

    public function testGetLiabilitiesAll13()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['13_']);
        $results = $object->getLiabilities();
        $this->assertEquals(52, count($results));
    }

    public function testGetLiabilitiesBorrower14()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['14_']);
        $results = $object->getLiabilities('b');
        $this->assertEquals(33, count($results));
    }

    public function testGetLiabilitiesCoborrower14()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['14_']);
        $this->expectException(\Exception::class);
        $object->getLiabilities('c');
    }

    public function testGetLiabilitiesAll14()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['14_']);
        $results = $object->getLiabilities();
        $this->assertEquals(33, count($results));
    }

    public function testGetLiabilitiesBorrower15()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['15_']);
        $results = $object->getLiabilities('b');
        $this->assertEquals(0, count($results));
    }

    public function testGetLiabilitiesCoborrower15()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['15_']);
        $results = $object->getLiabilities('c');
        $this->assertEquals(37, count($results));
    }

    public function testGetLiabilitiesAll15()
    {
        $object = new ConsumerCreditResponseParser();
        $object->loadXMLResponse($this->cc_file['15_']);
        $results = $object->getLiabilities();
        $this->assertEquals(37, count($results));
    }

    public function testGetRatingText()
    {
        $this->assertEquals('NoDataAvailable', ConsumerCreditResponseParser::getRatingText('X'));
        $this->assertEquals('NoDataAvailable', ConsumerCreditResponseParser::getRatingText('x'));
        $this->assertEquals('NoDataAvailable', ConsumerCreditResponseParser::getRatingText('-'));
        $this->assertEquals('AsAgreed', ConsumerCreditResponseParser::getRatingText('C'));
        $this->assertEquals('AsAgreed', ConsumerCreditResponseParser::getRatingText('c'));
        $this->assertEquals('Late30Days', ConsumerCreditResponseParser::getRatingText('1'));
        $this->assertEquals('Late60Days', ConsumerCreditResponseParser::getRatingText('2'));
        $this->assertEquals('Late90Days', ConsumerCreditResponseParser::getRatingText('3'));
        $this->assertEquals('LateOver120Days', ConsumerCreditResponseParser::getRatingText('4'));
        $this->assertEquals('LateOver120Days', ConsumerCreditResponseParser::getRatingText('5'));
        $this->assertEquals('LateOver120Days', ConsumerCreditResponseParser::getRatingText('6'));
        $this->assertEquals('BankruptcyOrWageEarnerPlan', ConsumerCreditResponseParser::getRatingText('7'));
        $this->assertEquals('ForeclosureOrRepossession', ConsumerCreditResponseParser::getRatingText('8'));
        $this->assertEquals('CollectionOrChargeOff', ConsumerCreditResponseParser::getRatingText('9'));
    }

    public function testGetRatingTextBad()
    {
        $this->expectException(\Exception::class);
        ConsumerCreditResponseParser::getRatingText('Z');
    }

    public function testGetRatingTextBad2()
    {
        $this->expectException(\Exception::class);
        ConsumerCreditResponseParser::getRatingText('');
    }

    public function testGetRatingTextBadIgnore()
    {
        $this->assertEquals('', ConsumerCreditResponseParser::getRatingText('Z', true));
    }

    public function testGetRatingTextBadIgnore2()
    {
        $this->assertEquals('', ConsumerCreditResponseParser::getRatingText('', true));
    }
}
