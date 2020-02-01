<?php

namespace jafrajarvy292\SmartAPIHelper\HTTPHandler;

use PHPUnit\Framework\TestCase;

class HTTPHandlerTest extends TestCase
{
    public function testConstructor()
    {
        $this->assertInstanceOf(HTTPHandler::class, new HTTPHandler());
    }

    public function testSetUserLoginValid()
    {
        $http = new HTTPHandler();
        $this->assertNull($http->setUserLogin('login_name'));
    }

    public function testSetUserLoginInvalid()
    {
        $http = new HTTPHandler();
        $this->expectException(\Exception::class);
        $http->setUserLogin('login:name');
    }

    public function testSetUserLoginInvalid2()
    {
        $http = new HTTPHandler();
        $this->expectException(\Exception::class);
        $http->setUserLogin('');
    }

    public function testSetUserLoginInvalid3()
    {
        $http = new HTTPHandler();
        $this->assertNull($http->setUserLogin('login:name', true));
    }

    public function testSetUserPasswordValid()
    {
        $http = new HTTPHandler();
        $this->assertNull($http->setUserPassword('password'));
    }

    public function testSetUserPasswordInvalid()
    {
        $http = new HTTPHandler();
        $this->expectException(\Exception::class);
        $http->setUserPassword('');
    }

    public function testSetHTTPEndpointValid()
    {
        $http = new HTTPHandler();
        $this->assertNull($http->setHTTPEndpoint('https://www.google.com'));
    }

    public function testSetHTTPEndpointInvalid()
    {
        $http = new HTTPHandler();
        $this->expectException(\Exception::class);
        $http->setHTTPEndpoint('');
    }

    public function testSetMCLInterfaceValid()
    {
        $http = new HTTPHandler();
        $this->assertNull($http->setMCLInterface('Interface_Value'));
    }

    public function testSetMCLInterfaceInvalid()
    {
        $http = new HTTPHandler();
        $this->expectException(\Exception::class);
        $http->setMCLInterface('');
    }

    public function testSetMCLSurrogatedLoginValid()
    {
        $http = new HTTPHandler();
        $this->assertNull($http->setMCLSurrogatedLogin('login2'));
    }

    public function testSetCURLCertFileValid()
    {
        $http = new HTTPHandler();
        $this->assertNull($http->setCURLCertFile(__DIR__ . '/test_cert.pem'));
    }

    public function testSetCURLCertFileInvalid()
    {
        $http = new HTTPHandler();
        $this->expectException(\Exception::class);
        $http->setCURLCertFile(__DIR__ . '/dl4823jldkl489dp23j4jksdf.pem');
    }

    public function testLoadXMLStringValid()
    {
        $http = new HTTPHandler();
        $this->assertNull($http->loadXMLString('string'));
    }

    public function testSetHTTPTimeoutValid()
    {
        $http = new HTTPHandler();
        $this->assertNull($http->setHTTPTimeout(90));
    }

    public function testGetUserLogin()
    {
        $http = new HTTPHandler();
        $http->setUserLogin('login_name');
        $this->assertEquals('login_name', $http->getUserLogin());
    }

    public function testGetUserPassword()
    {
        $http = new HTTPHandler();
        $http->setUserPassword('my_password');
        $this->assertEquals('my_password', $http->getUserPassword());
    }

    public function testGetHTTPEndpoint()
    {
        $http = new HTTPHandler();
        $http->setHTTPEndpoint('https://www.google.com');
        $this->assertEquals('https://www.google.com', $http->getHTTPEndpoint());
    }

    public function testGetMCLInterface()
    {
        $http = new HTTPHandler();
        $http->setMCLInterface('InterfaceValue');
        $this->assertEquals('InterfaceValue', $http->getMCLInterface());
    }

    public function testGetMCLSurrogatedLogin()
    {
        $http = new HTTPHandler();
        $http->setMCLSurrogatedLogin('login2');
        $this->assertEquals('login2', $http->getMCLSurrogatedLogin());
    }

    public function testGetXMLString()
    {
        $http = new HTTPHandler();
        $http->loadXMLString('xml_string');
        $this->assertEquals('xml_string', $http->getXMLString());
    }

    public function testGetHTTPTimeout()
    {
        $http = new HTTPHandler();
        $http->setHTTPTimeout(90);
        $this->assertEquals(90, $http->getHTTPTimeout());
    }

    public function testSumbitCURLRequest()
    {
        $http = new HTTPHandler();
        $http->setUserLogin('login');
        $http->setUserPassword('password');
        $http->setMCLInterface('Interface_value');
        $http->loadXMLString('xml_string');
        $this->assertNull($http->setHTTPEndpoint('http://www.dkljkl4i4jo4kj3iu23rjkl34tkl.com'));
    }

    public function testCURLPrepMissingLogin()
    {
        $http = new HTTPHandler();
        $http->setUserPassword('password');
        $http->setMCLInterface('Interface_value');
        $http->loadXMLString('xml_string');
        $http->setHTTPEndpoint('http://www.dkljkl4i4jo4kj3iu23rjkl34tkl.com');
        $this->expectException(\Exception::class);
        $http->submitCURLRequest();
    }

    public function testCURLPrepMissingPassword()
    {
        $http = new HTTPHandler();
        $http->setUserLogin('login_name');
        $http->setMCLInterface('Interface_value');
        $http->loadXMLString('xml_string');
        $http->setHTTPEndpoint('http://www.dkljkl4i4jo4kj3iu23rjkl34tkl.com');
        $this->expectException(\Exception::class);
        $http->submitCURLRequest();
    }

    public function testCURLPrepMissingMCLInterface()
    {
        $http = new HTTPHandler();
        $http->setUserLogin('login_name');
        $http->setUserPassword('password');
        $http->loadXMLString('xml_string');
        $http->setHTTPEndpoint('http://www.dkljkl4i4jo4kj3iu23rjkl34tkl.com');
        $this->expectException(\Exception::class);
        $http->submitCURLRequest();
    }

    public function testCURLPrepMissingXMLPayload()
    {
        $http = new HTTPHandler();
        $http->setUserLogin('login_name');
        $http->setUserPassword('password');
        $http->setMCLInterface('Interface_value');
        $http->setHTTPEndpoint('http://www.dkljkl4i4jo4kj3iu23rjkl34tkl.com');
        $this->expectException(\Exception::class);
        $http->submitCURLRequest();
    }

    public function testCURLPrepMissingHTTPEndpoint()
    {
        $http = new HTTPHandler();
        $http->setUserLogin('login_name');
        $http->setUserPassword('password');
        $http->setMCLInterface('Interface_value');
        $http->loadXMLString('xml_string');
        $this->expectException(\Exception::class);
        $http->submitCURLRequest();
    }

    public function testWasCURLSuccessful()
    {
        $http = new HTTPHandler();
        $this->expectException(\Exception::class);
        $http->wasCURLSuccessful();
    }

    public function testWasCURLSuccessful2()
    {
        $http = new HTTPHandler();
        $http->setUserLogin('login');
        $http->setUserPassword('password');
        $http->setMCLInterface('Interface_value');
        $http->loadXMLString('xml_string');
        $http->setHTTPEndpoint('http://www.dkljkl4i4jo4kj3iu23rjkl34tkl.com');
        $http->submitCURLRequest();
        $this->assertFalse($http->wasCURLSuccessful());
    }

    public function testGetCURLErrorMessage()
    {
        $http = new HTTPHandler();
        $http->setUserLogin('login');
        $http->setUserPassword('password');
        $http->setMCLInterface('Interface_value');
        $http->loadXMLString('xml_string');
        $http->setHTTPEndpoint('http://www.dkljkl4i4jo4kj3iu23rjkl34tkl.com');
        $http->submitCURLRequest();
        $this->assertTrue('' !== $http->getCURLErrorMessage());
    }

    public function testGetCURLResponse()
    {
        $http = new HTTPHandler();
        $http->setUserLogin('login');
        $http->setUserPassword('password');
        $http->setMCLInterface('Interface_value');
        $http->loadXMLString('xml_string');
        $http->setHTTPEndpoint('http://www.dkljkl4i4jo4kj3iu23rjkl34tkl.com');
        $http->submitCURLRequest();
        $this->assertEquals('', $http->getCURLResponse());
    }

    public function testEnableLoggingValid()
    {
        $http = new HTTPHandler();
        $this->assertNull($http->enableLogging(__DIR__ . '/logging_test/'));
    }

    public function testEnableLoggingInvalid()
    {
        $http = new HTTPHandler();
        $this->expectException(\Exception::class);
        $http->enableLogging(__DIR__ . '/nonexistent_folder/');
    }
}
