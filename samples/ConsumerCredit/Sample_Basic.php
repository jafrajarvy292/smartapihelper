<?php

/**
 * This is a basic sample of a consumer credit order request. It showcases the minimal amount of information
 * needed for ordering and uses very few configuration options, leaving most to their default values.
 */

 //Include the autoloader
require '..\\jafrajarvy292\\smartapihelper\\autoload.php';

//Include classes that are used for this request
use jafrajarvy292\SmartAPIHelper\Ancillary\AddressBlock;
use jafrajarvy292\SmartAPIHelper\Ancillary\PersonNameBlock;
use jafrajarvy292\SmartAPIHelper\RequestData\ConsumerCreditRequestData;
use jafrajarvy292\SmartAPIHelper\HTTPHandler\HTTPHandler;
use jafrajarvy292\SmartAPIHelper\ResponseParser\ConsumerCreditResponseParser;

/* Create a new ConsumerCreditRequestData object, which holds the info we'll be passing to SmartAPI for
ordering */
$request = new ConsumerCreditRequestData();
//Set the borrower's name
$request->setName('b', new PersonNameBlock('Bill', 'Testcase', 'C'));
//Set the coborrower's name
$request->setName('c', new PersonNameBlock('Beth', 'Testcase', 'C', 'SR'));
//Set the borrower's SSN
$request->setSSN('b', '000000015');
//Set the coborrower's SSN
$request->setSSN('c', '000000016');
//Set the borrower's current address
$request->setAddress('b', new AddressBlock('8842 48th Ave', 'Anthill', 'MO', '65488'));
//Set the coborrower's current address
$request->setAddress('c', new AddressBlock('8842 48th Ave', 'Anthill', 'MO', '65488'));
//Set the request type. Submit = new order
$request->setRequestType('Submit');

//Create a new HTTPHandler object to manage the HTTP POST request
$manager = new HTTPHandler();
//Set the login name. This is something the service provider would have issued to the end-user
$manager->setUserLogin('login');
//Set the login name's corresponding password
$manager->setUserPassword('password');
//Set the POST URL. This one is the SmartAPI testing URL
$manager->setHTTPEndpoint('https://demo.mortgagecreditlink.com/inetapi/request_products.aspx');
//Set the MCL-Interface value. This is the value for testing.
$manager->setMCLInterface('SmartAPITestingIdentifier');
//Generate the XML payload from the ConsumerCreditRequestData object and load it into our HTTPHandler object
$manager->loadXMLString($request->getXMLString());
//Submit the cURL request
$manager->submitCURLRequest();

//Variables to prep the polling phase
//Set the timeout. Recommended is 90 seconds.
$timeout = 90;
//Set the polling interval. Recommend polling every 1 - 10 seconds.
$polling_interval = 1;
/* Flag that we will set to false once we receive an 'end' type status that indicates the file is done, so
polling can stop */
$keep_polling = true;
/* Instantiate a new ConsumerCreditResponseParser object. As its name states, you load the XML response into
this and you can parse data using its various methods */
$response = new ConsumerCreditResponseParser();

//Polling should continue until we either hit the timeout or the polling flag is set to false
while ($timeout > 0 && $keep_polling === true) {
    //Ensure the cURL response was at least successful. If not, exit the loop and display the error message
    if ($manager->wasCURLSuccessful() === false) {
        echo 'Submission failed... ' . $manager->getCURLErrorMessage();
        $keep_polling = false;
        break;
    }
    //If the cURL was successful, load the response into our ConsumerCreditResponseParser object for parsing
    $response->loadXMLResponse($manager->getCURLResponse());

    //Determine what the response status was, which dicatates what we do next
    switch ($response->getStatus()) {
        //If status was either REQUEST_ERROR or SERVICE_ERROR, stop polling and display the error
        case $response::STATUS['REQUEST_ERROR']:
        case $response::STATUS['SERVICE_ERROR']:
            $keep_polling = false;
            echo $response->getStatus() . ' - ' . $response->getStatusDescription();
            break;
        //If status was either NEW or PROCESSING, then continue polling
        case $response::STATUS['NEW']:
        case $response::STATUS['PROCESSING']:
            /* Update the request type to StatusQuery to indicate we want to poll for the status, as
            opposed to ordering a brand new file */
            $request->setRequestType('StatusQuery');
            /* Grab the VendorOrderIdentifier from the response and indicate that in our polling submission;
            this is the file we want to poll for */
            $request->setVendorOrderID($response->getVendorOrderID());
            /* Regenerate the XML request to reflect the updated request type and insertion of
            VendorOrderIdentifier, then load it back into the HTTPHandler */
            $manager->loadXMLString($request->getXMLString());
            //Pause for the interval indicated before resubmitting
            sleep($polling_interval);
            //Decrement the timeout by the the amount of the polling interval that we've paused
            $timeout = $timeout - $polling_interval;
            //Finally, resubmit the request
            $manager->submitCURLRequest();
            break;
        //If status is completed, stop polling
        case $response::STATUS['COMPLETED']:
            $keep_polling = false;
            //In our example, we'll display the completed PDF document to the user.
            header('Content-Type: application/pdf');
            print(base64_decode($response->getPDFDocString()));
            break;
        /* If status is error, stop polling. The file has gone into an error status, though not ideal, it's
        technically an "end" status. A description will accompany this type of status */
        case $response::STATUS['ERROR']:
            $keep_polling = false;
            echo 'ERROR - ' . $response->getStatusDescription();
            break;
    }
}

//If polling timed out, stop polling and display relevant message to user.
if ($keep_polling === true) {
    echo 'The order too longer than expected to complete. Please contact service provider to notify them ' .
    'of the issue.';
}
