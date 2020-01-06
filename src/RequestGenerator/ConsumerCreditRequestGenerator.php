<?php

/**
 * @package SmartAPI Helper
 * @author David Tran <hunterr83@gmail.com>
 */

namespace jafrajarvy292\SmartAPIHelper\RequestGenerator;

use jafrajarvy292\SmartAPIHelper\RequestData\ConsumerCreditRequestData;

/**
 * This class handles the creation of the request XML file for Consumer Credit requests
 */
class ConsumerCreditRequestGenerator extends RequestGenerator
{
    /** @var ConsumerCreditRequestData Holds the data we'll need to build out the XML request file */
    private $data;

    /**
     * As soon as this object is instantiated, it will generate the root element with namespace declarations.
     *
     * @param ConsumerCreditRequestData $data The pertinent information for generating the full request
     * document
     * @param string $xml_version_input The XML version
     * @param string $encoding_input The encoding language
     */
    public function __construct(
        ConsumerCreditRequestData $data,
        string $xml_version_input = '1.0',
        string $encoding_input = 'utf-8'
    ) {
        //Parent constructor will build out the base XML doc and a few other variables we'll use
        parent::__construct($xml_version_input, $encoding_input);

        //Store the credit object for use with other class methods
        $this->data = $data;
    }
    
    /**
     * Generate the full request XML file and return it as a string
     *
     * @return string The full XML request document as a string
     */
    public function outputXMLString(): string
    {
        /* We call a helper function to do the actual XML file generation. The XML file will have varying
        nodes based off the request type. Overall, the helper functions will have a lot of
        code in common, but also a lot that is not. Instead of inserting a bunch of if statements
        to generate the code based off the request type in a single function, it's easier to just break
        them each into their own. This will create more redundant code, but reduces complexity. */
        switch ($this->data->getRequestType()) {
            case 'Submit':
                return $this->outputXMLforSubmit();
                break;
            case 'StatusQuery':
                return $this->outputXMLforStatusQuery();
                break;
            case 'Upgrade':
                return $this->outputXMLforUpgrade();
                break;
            case 'Refresh':
                return $this->outputXMLforRefresh();
                break;
            case 'PermUnmerge':
                return $this->outputXMLforPermUnmerge();
                break;
            default:
                throw new \Exception('Request type is required.');
        }
    }

    /**
     * Generates an XML for a new order request
     *
     * @return string
     */
    private function outputXMLforSubmit(): string
    {
        /* Disable XML errors. We turn this off because it will display even warnings, which tends to trigger
        because the 'inetapi/MISMO3_4_MCL_Extension.xsd' namespace isn't a fully qualified URI */
        libxml_clear_errors();
        libxml_use_internal_errors(true);

        /* Create aliases for the object properties. This is strictly so that we don't have to constantly
        type $this or self:: */
        $base = $this->base;
        $root = $this->root;
        $xpath = $this->xpath;
        $data = $this->data;
        $p1 = self::P1;
        $p2 = self::P2;
        $p3 = self::P3;

        //ABOUT_VERSIONS container insertion
        $root->appendChild($base->createElement('ABOUT_VERSIONS'))->
            appendChild($base->createElement('ABOUT_VERSION'))->
            appendChild($base->createElement('DataVersionIdentifier'))->
            appendChild($base->createTextNode($data->getDataVersion()));

        //DEAL container insertion
        $deal = $root->appendChild($base->createElement('DEAL_SETS'))->
            appendChild($base->createElement('DEAL_SET'))->
            appendChild($base->createElement('DEALS'))->
            appendChild($base->createElement('DEAL'));

        //COLLATERALS container insertion if subject property was provided
        if ($data->getSubjectPropAdd() !== null) {
            $subject_prop = $deal->appendChild($base->createElement('COLLATERALS'))->
                appendChild($base->createElement('COLLATERAL'))->
                appendChild($base->createElement('SUBJECT_PROPERTY'));
            $subject_prop->setAttributeNS($p2, 'label', 'Property1');
            $address = $subject_prop->appendChild($data->getSubjectPropAdd()->getXML($base));
        }

        //LOANS container insertion if loan identifier was provided
        if ($data->getLoanID() !== null && $data->getLoanID() !== '') {
            $deal->appendChild($base->createElement('LOANS'))->
                appendChild($base->createElement('LOAN'))->
                appendChild($base->createElement('LOAN_IDENTIFIERS'))->
                appendChild($base->createElement('LOAN_IDENTIFIER'))->
                appendChild($base->createElement('LoanIdentifier'))->
                appendChild($base->createTextNode($data->getLoanID()));
        }

        //PARTY container insertion for primary borrower
        $borr = $deal->appendChild($base->createElement('PARTIES'))->
            appendChild($base->createElement('PARTY'));
        $borr->setAttribute('SequenceNumber', '1');
        $borr->setAttributeNS($p2, 'label', 'Party1');
        $indiv = $borr->appendChild($base->createElement('INDIVIDUAL'));
        //If phone or email is present, create the CONTACT_POINTS container
        if ($data->getPhone('b') !== null || $data->getEmail('b') !== null && $data->getEmail('b') !== '') {
            $contact_points = $indiv->appendChild($base->createElement('CONTACT_POINTS'));
            //If phone is present, add it
            if ($data->getPhone('b') !== null) {
                $contact_points->appendChild($data->getPhone('b')->getXML($base));
            }
            //If email is present, add it
            if ($data->getEmail('b') !== null && $data->getEmail('b') !== '') {
                $contact_points->appendChild($base->createElement('CONTACT_POINT'))->
                    appendChild($base->createElement('CONTACT_POINT_EMAIL'))->
                    appendChild($base->createElement('ContactPointEmailValue'))->
                    appendChild($base->createTextNode($data->getEmail('b')));
            }
        }
        //NAME block insertion
        if ($data->getName('b') === null) {
            throw new \Exception('Borrower name is missing.');
        } else {
            $indiv->appendChild($data->getName('b')->getXML($base));
        }

        //Mailing address insertion if one is present
        if ($data->getAddress('b', 'Mailing') !== null) {
            //Insert address block and set temp pointer to ADDRESS element
            $temp = $borr->appendChild($base->createElement('ADDRESSES'))->
                appendChild($data->getAddress('b', 'Mailing')->getXML($base));
            /* Address object's getXML() doesn't insert an AddressType field, which is required to
            indicate this as a mailing address, so we target the CityName element and insert the needed node
            just before it */
            $ref_node = $temp->getElementsByTagName('CityName')->item(0);
            $temp->insertBefore($base->createElement('AddressType', 'Mailing'), $ref_node);
        }

        //Insert the [...]ROLES/ROLE/BORROWER node to hold more info for the borrower
        $borr_borr = $borr->appendChild($base->createElement('ROLES'))->
            appendChild($base->createElement('ROLE'))->
            appendChild($base->createElement('BORROWER'));
        if ($data->getDOB('b') !== null && $data->getDOB('b') !== '') {
            $borr_borr->appendChild($base->createElement('BORROWER_DETAIL'))->
                appendChild($base->createElement('BorrowerBirthDate'))->
                appendChild($base->createTextNode($data->getDOB('b')));
        }
        //RESIDENCES node insertion to hold residential addresses
        $residences = $borr_borr->appendChild($base->createElement('RESIDENCES'));
        //Insert Current residential address
        if ($data->getAddress('b', 'Current') === null) {
            throw new \Exception('Borrower\'s current address cannot be empty.');
        } else {
            $residences->appendChild($base->createElement('RESIDENCE'))->
                appendChild($data->getAddress('b', 'Current')->getXML($base))->
                parentNode->
                appendChild($base->createElement('RESIDENCE_DETAIL'))->
                appendChild($base->createElement('BorrowerResidencyType', 'Current'));
        }

        //Insert Prior residential address
        if ($data->getAddress('b', 'Prior') !== null) {
            $residences->appendChild($base->createElement('RESIDENCE'))->
                appendChild($data->getAddress('b', 'Prior')->getXML($base))->
                parentNode->
                appendChild($base->createElement('RESIDENCE_DETAIL'))->
                appendChild($base->createElement('BorrowerResidencyType', 'Prior'));
        }

        //Insert PartyRoleType for borrower
        $borr->getElementsByTagName('ROLES')->item(0)->getElementsByTagName('ROLE')->item(0)->
            appendChild($base->createElement('ROLE_DETAIL'))->
            appendChild($base->createElement('PartyRoleType', 'Borrower'));
        
        //Insert borrower SSN
        if ($data->getSSN('b') === null || $data->getSSN('b') === '') {
            throw new \Exception('Borrower SSN cannot be empty.');
        } else {
            $borr->appendChild($base->createElement('TAXPAYER_IDENTIFIERS'))->
                appendChild($base->createElement('TAXPAYER_IDENTIFIER'))->
                appendChild($base->createElement('TaxpayerIdentifierType', 'SocialSecurityNumber'))->
                parentNode->
                appendChild($base->createElement('TaxpayerIdentifierValue'))->
                appendChild($base->createTextNode($data->getSSN('b')));
        }

        //If coborrower name is present, then generate the PARTY node for the coborrower
        if ($data->getName('c') !== null) {
            $coborr = $deal->getElementsByTagName('PARTIES')->item(0)->
                appendChild($base->createElement('PARTY'));
            $coborr->setAttribute('SequenceNumber', '2');
            $coborr->setAttributeNS($p2, 'label', 'Party2');

            $coborr->appendChild($base->createElement('INDIVIDUAL'));
            //If coborrower has a phone or email present, then generate CONTACT_POINTS node
            if (
                $data->getPhone('c') !== null ||
                $data->getEmail('c') !== null &&
                $data->getEmail('c') !== ''
            ) {
                $contact_points = $coborr->getElementsByTagName('INDIVIDUAL')->item(0)->
                    appendChild($base->createElement('CONTACT_POINTS'));
                //If coborrower has a phone, then insert it
                if ($data->getPhone('c') !== null) {
                    $contact_points->appendChild($data->getPhone('c')->getXML($base));
                }
                //If coborrower has an email, then insert it
                if ($data->getEmail('c') !== null) {
                    $contact_points->appendChild($base->createElement('CONTACT_POINT'))->
                        appendChild($base->createElement('CONTACT_POINT_EMAIL'))->
                        appendChild($base->createElement('ContactPointEmailValue'))->
                        appendChild($base->createTextNode($data->getEmail('c')));
                }
            }

            //Insert coborrower NAME block
            $coborr->getElementsByTagName('INDIVIDUAL')->item(0)->
                appendChild($data->getName('c')->getXML($base));

            //Insert coborrower mailing address block
            if ($data->getAddress('c', 'Mailing') !== null) {
                $addresses = $coborr->appendChild($base->createElement('ADDRESSES'))->
                    appendChild($data->getAddress('c', 'Mailing')->getXML($base));
                $ref_node = $addresses->getElementsByTagName('CityName')->item(0);
                $addresses->insertBefore($base->createElement('AddressType', 'Mailing'), $ref_node);
            }

            //ROLE container insertion
            $role = $coborr->appendChild($base->createElement('ROLES'))->
                appendChild($base->createElement('ROLE'));

            //BORROWER container insertion
            $coborr_coborr = $role->appendChild($base->createElement('BORROWER'));

            //If coborrower DOB is present, insert it
            if ($data->getDOB('c') !== null && $data->getDOB('c') !== '') {
                $coborr_coborr->appendChild($base->createElement('BORROWER_DETAIL'))->
                    appendChild($base->createElement('BorrowerBirthDate'))->
                    appendChild($base->createTextNode($data->getDOB('c')));
            }

            //Insert current residencial address
            if ($data->getAddress('c', 'Current') === null) {
                throw new \Exception('Coborrower current address cannot be empty');
            } else {
                $coborr_coborr->appendChild($base->createElement('RESIDENCES'))->
                    appendChild($base->createElement('RESIDENCE'))->
                    appendChild($data->getAddress('c', 'Current')->getXML($base))->
                    parentNode->
                    appendChild($base->createElement('RESIDENCE_DETAIL'))->
                    appendChild($base->createElement('BorrowerResidencyType', 'Current'));
            }

            //Insert prior residential address, if provided
            if ($data->getAddress('c', 'Prior') !== null) {
                $coborr_coborr->getElementsByTagName('RESIDENCES')->item(0)->
                    appendChild($base->createElement('RESIDENCE'))->
                    appendChild($data->getAddress('c', 'Prior')->getXML($base))->
                    parentNode->
                    appendChild($base->createElement('RESIDENCE_DETAIL'))->
                    appendChild($base->createElement('BorrowerResidencyType', 'Prior'));
            }

            //Insert role type
            $role->appendChild($base->createElement('ROLE_DETAIL'))->
                appendChild($base->createElement('PartyRoleType', 'Borrower'));
            
            //Insert coborrower SSN
            if ($data->getSSN('c') === null || $data->getSSN('c') === '') {
                throw new \Exception('Coborrower SSN cannot be empty.');
            } else {
                $coborr->appendChild($base->createElement('TAXPAYER_IDENTIFIERS'))->
                    appendChild($base->createElement('TAXPAYER_IDENTIFIER'))->
                    appendChild($base->createElement('TaxpayerIdentifierType', 'SocialSecurityNumber'))->
                    parentNode->appendChild($base->createElement('TaxpayerIdentifierValue'))->
                    appendChild($base->createTextNode($data->getSSN('c')));
            }
        }

        //RELATIONSHIPS container insertion
        $relationships = $deal->appendChild($base->createElement('RELATIONSHIPS'));
        //Insert service xlink for borrower
        $relationship1 = $relationships->appendChild($base->createElement('RELATIONSHIP'));
        $relationship1->setAttributeNS(
            $p2,
            'arcrole',
            'urn:fdc:Meridianlink.com:2017:mortgage/PARTY_IsVerifiedBy_SERVICE'
        );
        $relationship1->setAttributeNS($p2, 'from', 'Party1');
        $relationship1->setAttributeNS($p2, 'to', 'Service1');
        //If coborrower exists, insert xlink for coborrower
        if ($data->getName('c') !== null) {
            $relationship2 = $relationships->appendChild($base->createElement('RELATIONSHIP'));
            $relationship2->setAttributeNS(
                $p2,
                'arcrole',
                'urn:fdc:Meridianlink.com:2017:mortgage/PARTY_IsVerifiedBy_SERVICE'
            );
            $relationship2->setAttributeNS($p2, 'from', 'Party2');
            $relationship2->setAttributeNS($p2, 'to', 'Service1');
        }
        //If subject property exists, insert xlink for this
        if ($data->getSubjectPropAdd() !== null) {
            $relationship3 = $relationships->appendChild($base->createElement('RELATIONSHIP'));
            $relationship3->setAttributeNS(
                $p2,
                'arcrole',
                'urn:fdc:Meridianlink.com:2017:mortgage/PROPERTY_IsVerifiedBy_SERVICE'
            );
            $relationship3->setAttributeNS($p2, 'from', 'Property1');
            $relationship3->setAttributeNS($p2, 'to', 'Service1');
        }

        //SERVICES container build-out
        $service = $deal->appendChild($base->createElement('SERVICES'))->
            appendChild($base->createElement('SERVICE'));
        $service->setAttributeNS($p2, 'label', 'Service1');
        $credit_request = $service->appendChild($base->createElement('CREDIT'))->
            appendChild($base->createElement('CREDIT_REQUEST'));
        
        //Insert loan type if one was provided
        if ($data->getLoanType() !== null && $data->getLoanType() !== '') {
            $credit_request->appendChild($base->createElement('CREDIT_INQUIRIES'))->
                appendChild($base->createElement('CREDIT_INQUIRY'))->
                appendChild($base->createElement('CREDIT_INQUIRY_DETAIL'))->
                appendChild($base->createElement('CreditLoanType', 'Other'))->parentNode->
                appendChild($base->createElement('CreditLoanTypeOtherDescription'))->
                appendChild($base->createTextNode($data->getLoanType()));
        }

        //Insert credit bureau option flags
        $credit_req_data = $credit_request->appendChild($base->createElement('CREDIT_REQUEST_DATAS'))->
            appendChild($base->createElement('CREDIT_REQUEST_DATA'));
        $credit_req_data->appendChild($base->createElement('CREDIT_REPOSITORY_INCLUDED'))->
            appendChild($base->createElement(
                'CreditRepositoryIncludedEquifaxIndicator',
                var_export($data->getEquifaxOptions()['credit'], true)
            ))->parentNode->
            appendChild($base->createElement(
                'CreditRepositoryIncludedExperianIndicator',
                var_export($data->getExperianOptions()['credit'], true)
            ))->parentNode->
            appendChild($base->createElement(
                'CreditRepositoryIncludedTransUnionIndicator',
                var_export($data->getTransUnionOptions()['credit'], true)
            ))->parentNode->
            appendChild($base->createElement('EXTENSION'))->
            appendChild($base->createElement('OTHER'))->
            appendChild($base->createElementNS(
                $p3,
                'RequestEquifaxScore',
                var_export($data->getEquifaxOptions()['score'], true)
            ))->parentNode->
            appendChild($base->createElementNS(
                $p3,
                'RequestExperianFraud',
                var_export($data->getExperianOptions()['fraud'], true)
            ))->parentNode->
            appendChild($base->createElementNS(
                $p3,
                'RequestExperianScore',
                var_export($data->getExperianOptions()['score'], true)
            ))->parentNode->
            appendChild($base->createElementNS(
                $p3,
                'RequestTransUnionFraud',
                var_export($data->getTransUnionOptions()['fraud'], true)
            ))->parentNode->
            appendChild($base->createElementNS(
                $p3,
                'RequestTransUnionScore',
                var_export($data->getTransUnionOptions()['score'], true)
            ));

        //Insert request action type
        $credit_req_data->appendChild($base->createElement('CREDIT_REQUEST_DATA_DETAIL'))->
            appendChild($base->createElement('CreditReportRequestActionType'))->
            appendChild($base->createTextNode($data->getRequestType()));
        
        //If credit card info is provided, insert it
        if ($data->getCreditCard() !== null) {
            $service->appendChild($base->createElement('SERVICE_PAYMENTS'))->
                appendChild($data->getCreditCard()->getXML($base));
        }

        //Insert service product description
        $service_detail = $service->appendChild($base->createElement('SERVICE_PRODUCT'))->
            appendChild($base->createElement('SERVICE_PRODUCT_REQUEST'))->
            appendChild($base->createElement('SERVICE_PRODUCT_DETAIL'));
        $service_detail->appendChild($base->createElement('ServiceProductDescription', 'CreditOrder'));
        //If any preferred response formats are requested, include the corresponding nodes
        if ($data->getResponseFormats()->getCount() !== 0) {
            $service_detail->appendChild($base->createElement('EXTENSION'))->
                appendChild($base->createElement('OTHER'))->
                appendChild($data->getResponseFormats()->getXML($base, $p3));
        }
        return $base->saveXML();
    }

    /**
     * Generates an XML file for a status/reissue request
     *
     * @return string
     */
    private function outputXMLforStatusQuery(): string
    {
        /* Disable XML errors. We turn this off because it will display even warnings, which tends to trigger
        because the 'inetapi/MISMO3_4_MCL_Extension.xsd' namespace isn't a fully qualified URI */
        libxml_clear_errors();
        libxml_use_internal_errors(true);

        /* Create aliases for the object properties. This is strictly so that we don't have to constantly
        type $this or self:: */
        $base = $this->base;
        $root = $this->root;
        $xpath = $this->xpath;
        $data = $this->data;
        $p1 = self::P1;
        $p2 = self::P2;
        $p3 = self::P3;

        //Insert DataVersionIdentifier node
        $root->appendChild($base->createElement('ABOUT_VERSIONS'))->
        appendChild($base->createElement('ABOUT_VERSION'))->
        appendChild($base->createElement('DataVersionIdentifier'))->
        appendChild($base->createTextNode($data->getDataVersion()));

        //Insert DEAL container
        $deal = $root->appendChild($base->createElement('DEAL_SETS'))->
            appendChild($base->createElement('DEAL_SET'))->
            appendChild($base->createElement('DEALS'))->
            appendChild($base->createElement('DEAL'));
        
        //Insert borrower's PARTY node
        $borr = $deal->appendChild($base->createElement('PARTIES'))->
            appendChild($base->createElement('PARTY'));
        $borr->setAttribute('SequenceNumber', '1');
        $borr->setAttributeNS($p2, 'label', 'Party1');
        //Insert borrower name
        if ($data->getName('b') === null) {
            throw new \Exception('Borrower name cannot be empty.');
        } else {
            $borr->appendChild($base->createElement('INDIVIDUAL'))->
                appendChild($data->getName('b')->getXML($base));
        }
        //Insert borrower role
        $borr->appendChild($base->createElement('ROLES'))->
            appendChild($base->createElement('ROLE'))->
            appendChild($base->createElement('ROLE_DETAIL'))->
            appendChild($base->createElement('PartyRoleType', 'Borrower'));
        //Insert social security number node
        if ($data->getSSN('b') === null || $data->getSSN('b') === '') {
            throw new \Exception('Borrower SSN cannot be emtpy.');
        } else {
            $borr->appendChild($base->createElement('TAXPAYER_IDENTIFIERS'))->
                appendChild($base->createElement('TAXPAYER_IDENTIFIER'))->
                appendChild($base->createElement('TaxpayerIdentifierType', 'SocialSecurityNumber'))->
                parentNode->appendChild($base->createElement('TaxpayerIdentifierValue'))->
                appendChild($base->createTextNode($data->getSSN('b')));
        }
        
        //If coborrower is present, insert their information
        if ($data->getName('c') !== null) {
            //Insert coborrower's PARTY container
            $coborr = $deal->getElementsByTagName('PARTIES')->item(0)->
                appendChild($base->createElement('PARTY'));
            $coborr->setAttribute('SequenceNumber', '2');
            $coborr->setAttributeNS($p2, 'label', 'Party2');
            //Insert coborrower name node
            $coborr->appendChild($base->createElement('INDIVIDUAL'))->
                appendChild($data->getName('c')->getXML($base));
            //Insert coborrower role type
            $coborr->appendChild($base->createElement('ROLES'))->
                appendChild($base->createElement('ROLE'))->
                appendChild($base->createElement('ROLE_DETAIL'))->
                appendChild($base->createElement('PartyRoleType', 'Borrower'));
            //Insert social security number node
            if ($data->getSSN('c') === null || $data->getSSN('c') === '') {
                throw new \Exception('Coborrower SSN cannot be emtpy.');
            } else {
                $coborr->appendChild($base->createElement('TAXPAYER_IDENTIFIERS'))->
                    appendChild($base->createElement('TAXPAYER_IDENTIFIER'))->
                    appendChild($base->createElement('TaxpayerIdentifierType', 'SocialSecurityNumber'))->
                    parentNode->appendChild($base->createElement('TaxpayerIdentifierValue'))->
                    appendChild($base->createTextNode($data->getSSN('c')));
            }
        }

        //RELATIONSHIPS container insertion
        $relationships = $deal->appendChild($base->createElement('RELATIONSHIPS'));
        //Insert borrower-service link
        $relationship1 = $relationships->appendChild($base->createElement('RELATIONSHIP'));
        $relationship1->setAttributeNS(
            $p2,
            'arcrole',
            'urn:fdc:Meridianlink.com:2017:mortgage/PARTY_IsVerifiedBy_SERVICE'
        );
        $relationship1->setAttributeNS($p2, 'from', 'Party1');
        $relationship1->setAttributeNS($p2, 'to', 'Service1');
        //If coborrower is present, insert their borrower-service link
        if ($data->getName('c') !== null) {
            $relationship2 = $relationships->appendChild($base->createElement('RELATIONSHIP'));
            $relationship2->setAttributeNS(
                $p2,
                'arcrole',
                'urn:fdc:Meridianlink.com:2017:mortgage/PARTY_IsVerifiedBy_SERVICE'
            );
            $relationship2->setAttributeNS($p2, 'from', 'Party2');
            $relationship2->setAttributeNS($p2, 'to', 'Service1');
        }
        //Insert SERVICE container
        $service = $deal->appendChild($base->createElement('SERVICES'))->
            appendChild($base->createElement('SERVICE'));
        $service->setAttributeNS($p2, 'label', 'Service1');
        //Insert credit options flags and request action type
        $service->appendChild($base->createElement('CREDIT'))->
            appendChild($base->createElement('CREDIT_REQUEST'))->
            appendChild($base->createElement('CREDIT_REQUEST_DATAS'))->
            appendChild($base->createElement('CREDIT_REQUEST_DATA'))->
            appendChild($base->createElement('CREDIT_REPOSITORY_INCLUDED'))->
            appendChild($base->createElement(
                'CreditRepositoryIncludedEquifaxIndicator',
                var_export($data->getEquifaxOptions()['credit'], true)
            ))->parentNode->
            appendChild($base->createElement(
                'CreditRepositoryIncludedExperianIndicator',
                var_export($data->getExperianOptions()['credit'], true)
            ))->parentNode->
            appendChild($base->createElement(
                'CreditRepositoryIncludedTransUnionIndicator',
                var_export($data->getTransUnionOptions()['credit'], true)
            ))->parentNode->parentNode->
            appendChild($base->createElement('CREDIT_REQUEST_DATA_DETAIL'))->
            appendChild($base->createElement('CreditReportRequestActionType'))->
            appendChild($base->createTextNode($data->getRequestType()));
        //Insert credit card payment info if present
        if ($data->getCreditCard() !== null) {
            $service->appendChild($base->createElement('SERVICE_PAYMENTS'))->
                appendChild($data->getCreditCard()->getXML($base));
        }
        //Insert service product type
        $service_detail = $service->appendChild($base->createElement('SERVICE_PRODUCT'))->
            appendChild($base->createElement('SERVICE_PRODUCT_REQUEST'))->
            appendChild($base->createElement('SERVICE_PRODUCT_DETAIL'));
        $service_detail->appendChild($base->createElement('ServiceProductDescription', 'CreditOrder'));
        //If any preferred formats are indicated, insert their nodes
        if ($data->getResponseFormats()->getCount() !== 0) {
            $pre_formats = $service_detail->appendChild($base->createElement('EXTENSION'))->
                appendChild($base->createElement('OTHER'));
            //Insert nodes for the formats being requested
            $pre_formats->appendChild($data->getResponseFormats()->getXML($base, $p3));
        }
        //Insert existing file number associated with the status query
        if ($data->getVendorOrderID() === null || $data->getVendorOrderID() === '') {
            throw new \Exception('VendorOrderIdentifier must be provided for ' .
                $data->getRequestType() . ' requests.');
        } else {
            $service->appendChild($base->createElement('SERVICE_PRODUCT_FULFILLMENT'))->
            appendChild($base->createElement('SERVICE_PRODUCT_FULFILLMENT_DETAIL'))->
            appendChild($base->createElement('VendorOrderIdentifier'))->
            appendChild($base->createTextNode($data->getVendorOrderID()));
        }
        return $base->saveXML();
    }

    /**
     * Generates an XML string for upgrading an existing file with an additional borrower and/or additional
     * bureaus
     *
     * @return string
     */
    private function outputXMLforUpgrade(): string
    {
        /* Disable XML errors. We turn this off because it will display even warnings, which tends to trigger
        because the 'inetapi/MISMO3_4_MCL_Extension.xsd' namespace isn't a fully qualified URI */
        libxml_clear_errors();
        libxml_use_internal_errors(true);

        /* Create aliases for the object properties. This is strictly so that we don't have to constantly
        type $this or self:: */
        $base = $this->base;
        $root = $this->root;
        $xpath = $this->xpath;
        $data = $this->data;
        $p1 = self::P1;
        $p2 = self::P2;
        $p3 = self::P3;

        //ABOUT_VERSIONS container insertion
        $root->appendChild($base->createElement('ABOUT_VERSIONS'))->
            appendChild($base->createElement('ABOUT_VERSION'))->
            appendChild($base->createElement('DataVersionIdentifier'))->
            appendChild($base->createTextNode($data->getDataVersion()));

        //DEAL container insertion
        $deal = $root->appendChild($base->createElement('DEAL_SETS'))->
            appendChild($base->createElement('DEAL_SET'))->
            appendChild($base->createElement('DEALS'))->
            appendChild($base->createElement('DEAL'));

        //Insert borrower's PARTY container
        $borr = $deal->appendChild($base->createElement('PARTIES'))->
            appendChild($base->createElement('PARTY'));
        $borr->setAttribute('SequenceNumber', '1');
        $borr->setAttributeNS($p2, 'label', 'Party1');
        
        //Insert borrower's name
        if ($data->getName('b') === null) {
            throw new \Exception('Borrower name cannot be empty.');
        } else {
            $borr->appendChild($base->createElement('INDIVIDUAL'))->
                appendChild($data->getName('b')->getXML($base));
        }

        //Insert borrower's role
        $borr->appendChild($base->createElement('ROLES'))->
            appendChild($base->createElement('ROLE'))->
            appendChild($base->createElement('ROLE_DETAIL'))->
            appendChild($base->createElement('PartyRoleType', 'Borrower'));

        //Insert borrower's SSN
        if ($data->getSSN('b') === null || $data->getSSN('b') === '') {
            throw new \Exception('Borrower SSN cannot be empty.');
        } else {
            $borr->appendChild($base->createElement('TAXPAYER_IDENTIFIERS'))->
                appendChild($base->createElement('TAXPAYER_IDENTIFIER'))->
                appendChild($base->createElement('TaxpayerIdentifierType', 'SocialSecurityNumber'))->
                parentNode->
                appendChild($base->createElement('TaxpayerIdentifierValue', $data->getSSN('b')));
        }

        //If coborrower is present, create their PARTY node
        if ($data->getName('c') !== null) {
            $coborr = $deal->getElementsByTagName('PARTIES')->item(0)->
                appendChild($base->createElement('PARTY'));
            $coborr->setAttribute('SequenceNumber', '2');
            $coborr->setAttributeNS($p2, 'label', 'Party2');

            //If phone or email is present, create CONTACT_POINTS node to insert them
            if (
                $data->getPhone('c') !== null ||
                $data->getEmail('c') !== null &&
                $data->getEmail('c') !== ''
            ) {
                $contact_points = $coborr->appendChild($base->createElement('INDIVIDUAL'))->
                    appendChild($base->createElement('CONTACT_POINTS'));
                //If phone is present, insert it
                if ($data->getPhone('c') !== null) {
                    $contact_points->appendChild($data->getPhone('c')->getXML($base));
                }
                //If email is present, insert it
                if ($data->getEmail('c') !== null && $data->getEmail('c') !== '') {
                    $contact_points->appendChild($base->createElement('CONTACT_POINT'))->
                        appendChild($base->createElement('CONTACT_POINT_EMAIL'))->
                        appendChild($base->createElement('ContactPointEmailValue'))->
                        appendChild($base->createTextNode($data->getEmail('c')));
                }
            }
            //Insert NAME group
            $coborr->getElementsByTagName('INDIVIDUAL')->item(0)->
                appendChild($data->getName('c')->getXML($base));
            
            //Insert ROLE group
            $coborr_role = $coborr->appendChild($base->createElement('ROLES'))->
                appendChild($base->createElement('ROLE'));
            //Insert DOB if present
            if ($data->getDOB('c') !== null && $data->getDOB('c') !== '') {
                $coborr_role->appendChild($base->createElement('BORROWER'))->
                    appendChild($base->createElement('BORROWER_DETAIL'))->
                    appendChild($base->createElement('BorrowerBirthDate'))->
                    appendChild($base->createTextNode($data->getDOB('c')));
            }
            //Insert coborrower role
            $coborr_role->appendChild($base->createElement('ROLE_DETAIL'))->
                appendChild($base->createElement('PartyRoleType', 'Borrower'));
            
            //Insert coborrower SSN
            if ($data->getSSN('c') === null || $data->getSSN('c') === '') {
                throw new \Exception('Coborrower SSN cannot be empty.');
            } else {
                $coborr->appendChild($base->createElement('TAXPAYER_IDENTIFIERS'))->
                    appendChild($base->createElement('TAXPAYER_IDENTIFIER'))->
                    appendChild($base->createElement('TaxpayerIdentifierType', 'SocialSecurityNumber'))->
                    parentNode->appendChild($base->createElement('TaxpayerIdentifierValue'))->
                    appendChild($base->createTextNode($data->getSSN('c')));
            }
        }

        //RELATIONSHIPS container insertion
        $relationships = $deal->appendChild($base->createElement('RELATIONSHIPS'));
        //Insert borrower-service xlink
        $relationship1 = $relationships->appendChild($base->createElement('RELATIONSHIP'));
        $relationship1->setAttributeNS(
            $p2,
            'arcrole',
            'urn:fdc:Meridianlink.com:2017:mortgage/PARTY_IsVerifiedBy_SERVICE'
        );
        $relationship1->setAttributeNS($p2, 'from', 'Party1');
        $relationship1->setAttributeNS($p2, 'to', 'Service1');
        //If coborrower exists, insert coborrower-service xlink
        if ($data->getName('c') !== null) {
            $relationship2 = $relationships->appendChild($base->createElement('RELATIONSHIP'));
            $relationship2->setAttributeNS(
                $p2,
                'arcrole',
                'urn:fdc:Meridianlink.com:2017:mortgage/PARTY_IsVerifiedBy_SERVICE'
            );
            $relationship2->setAttributeNS($p2, 'from', 'Party2');
            $relationship2->setAttributeNS($p2, 'to', 'Service1');
        }

        //Insert SERVICE container
        $service = $deal->appendChild($base->createElement('SERVICES'))->
            appendChild($base->createElement('SERVICE'));
        $service->setAttributeNS($p2, 'label', 'Service1');
        //Insert CREDIT_REQUEST_DATA container
        $credit_request_data = $service->appendChild($base->createElement('CREDIT'))->
            appendChild($base->createElement('CREDIT_REQUEST'))->
            appendChild($base->createElement('CREDIT_REQUEST_DATAS'))->
            appendChild($base->createElement('CREDIT_REQUEST_DATA'));
        //Insert credit option flags
        $credit_request_data->appendChild($base->createElement('CREDIT_REPOSITORY_INCLUDED'))->
            appendChild($base->createElement('CreditRepositoryIncludedEquifaxIndicator'))->
            appendChild($base->createTextNode(var_export($data->getEquifaxOptions()['credit'], true)))->
            parentNode->parentNode->
            appendChild($base->createElement('CreditRepositoryIncludedExperianIndicator'))->
            appendChild($base->createTextNode(var_export($data->getExperianOptions()['credit'], true)))->
            parentNode->parentNode->
            appendChild($base->createElement('CreditRepositoryIncludedTransUnionIndicator'))->
            appendChild($base->createTextNode(var_export($data->getTransUnionOptions()['credit'], true)))->
            parentNode->parentNode->
            appendChild($base->createElement('EXTENSION'))->
            appendChild($base->createElement('OTHER'))->
            appendChild($base->createElementNS($p3, 'RequestEquifaxScore'))->
            appendChild($base->createTextNode(var_export($data->getEquifaxOptions()['score'], true)))->
            parentNode->parentNode->
            appendChild($base->createElementNS($p3, 'RequestExperianFraud'))->
            appendChild($base->createTextNode(var_export($data->getExperianOptions()['fraud'], true)))->
            parentNode->parentNode->
            appendChild($base->createElementNS($p3, 'RequestExperianScore'))->
            appendChild($base->createTextNode(var_export($data->getExperianOptions()['score'], true)))->
            parentNode->parentNode->
            appendChild($base->createElementNS($p3, 'RequestTransUnionFraud'))->
            appendChild($base->createTextNode(var_export($data->getTransUnionOptions()['fraud'], true)))->
            parentNode->parentNode->
            appendChild($base->createElementNS($p3, 'RequestTransUnionScore'))->
            appendChild($base->createTextNode(var_export($data->getTransUnionOptions()['score'], true)));
        //Insert request action type
        $credit_request_data->appendChild($base->createElement('CREDIT_REQUEST_DATA_DETAIL'))->
            appendChild($base->createElement('CreditReportRequestActionType'))->
            appendChild($base->createTextNode('Upgrade'));
        //Insert credit card payment info if present
        if ($data->getCreditCard() !== null) {
            $service->appendChild($base->createElement('SERVICE_PAYMENTS'))->
                appendChild($data->getCreditCard()->getXML($base));
        }
        //Insert service product description
        $service_detail = $service->appendChild($base->createElement('SERVICE_PRODUCT'))->
            appendChild($base->createElement('SERVICE_PRODUCT_REQUEST'))->
            appendChild($base->createElement('SERVICE_PRODUCT_DETAIL'));
        $service_detail->appendChild($base->createElement('ServiceProductDescription'))->
            appendChild($base->createTextNode('CreditOrder'));
        //If any preferred response formats are set to true, insert them.
        if ($data->getResponseFormats()->getCount() !== 0) {
            $service_detail->appendChild($base->createElement('EXTENSION'))->
                appendChild($base->createElement('OTHER'))->
                appendChild($data->getResponseFormats()->getXML($base, $p3));
        }
        //Insert file number being upgraded
        if ($data->getVendorOrderID() === null || $data->getVendorOrderID() === '') {
            throw new \Exception('VendorOrderIdentifier required for ' .
                $data->getRequestType() . ' requests.');
        } else {
            $service->appendChild($base->createElement('SERVICE_PRODUCT_FULFILLMENT'))->
                appendChild($base->createElement('SERVICE_PRODUCT_FULFILLMENT_DETAIL'))->
                appendChild($base->createElement('VendorOrderIdentifier'))->
                appendChild($base->createTextNode($data->getVendorOrderID()));
        }
        return $base->saveXML();
    }

    /**
     * Generates XML string for a Refresh report order
     *
     * @return string
     */
    private function outputXMLforRefresh(): string
    {
        /* Disable XML errors. We turn this off because it will display even warnings, which tends to trigger
        because the 'inetapi/MISMO3_4_MCL_Extension.xsd' namespace isn't a fully qualified URI */
        libxml_clear_errors();
        libxml_use_internal_errors(true);

        /* Create aliases for the object properties. This is strictly so that we don't have to constantly
        type $this or self:: */
        $base = $this->base;
        $root = $this->root;
        $xpath = $this->xpath;
        $data = $this->data;
        $p1 = self::P1;
        $p2 = self::P2;
        $p3 = self::P3;

        //ABOUT_VERSIONS container insertion
        $root->appendChild($base->createElement('ABOUT_VERSIONS'))->
            appendChild($base->createElement('ABOUT_VERSION'))->
            appendChild($base->createElement('DataVersionIdentifier'))->
            appendChild($base->createTextNode($data->getDataVersion()));

        //DEAL container insertion
        $deal = $root->appendChild($base->createElement('DEAL_SETS'))->
            appendChild($base->createElement('DEAL_SET'))->
            appendChild($base->createElement('DEALS'))->
            appendChild($base->createElement('DEAL'));

        //COLLATERALS container insertion if subject property was provided
        if ($data->getSubjectPropAdd() !== null) {
            $subject_prop = $deal->appendChild($base->createElement('COLLATERALS'))->
                appendChild($base->createElement('COLLATERAL'))->
                appendChild($base->createElement('SUBJECT_PROPERTY'));
            $subject_prop->setAttributeNS($p2, 'label', 'Property1');
            $address = $subject_prop->appendChild($data->getSubjectPropAdd()->getXML($base));
        }

        //LOANS container insertion if loan identifier was provided
        if ($data->getLoanID() !== null && $data->getLoanID() !== '') {
            $deal->appendChild($base->createElement('LOANS'))->
                appendChild($base->createElement('LOAN'))->
                appendChild($base->createElement('LOAN_IDENTIFIERS'))->
                appendChild($base->createElement('LOAN_IDENTIFIER'))->
                appendChild($base->createElement('LoanIdentifier'))->
                appendChild($base->createTextNode($data->getLoanID()));
        }

        //PARTY container insertion for primary borrower
        $borr = $deal->appendChild($base->createElement('PARTIES'))->
            appendChild($base->createElement('PARTY'));
        $borr->setAttribute('SequenceNumber', '1');
        $borr->setAttributeNS($p2, 'label', 'Party1');
        $indiv = $borr->appendChild($base->createElement('INDIVIDUAL'));
        //If phone or email is present, create the CONTACT_POINTS container
        if ($data->getPhone('b') !== null || $data->getEmail('b') !== null && $data->getEmail('b') !== '') {
            $contact_points = $indiv->appendChild($base->createElement('CONTACT_POINTS'));
            //If phone is present, add it
            if ($data->getPhone('b') !== null) {
                $contact_points->appendChild($data->getPhone('b')->getXML($base));
            }
            //If email is present, add it
            if ($data->getEmail('b') !== null && $data->getEmail('b') !== '') {
                $contact_points->appendChild($base->createElement('CONTACT_POINT'))->
                    appendChild($base->createElement('CONTACT_POINT_EMAIL'))->
                    appendChild($base->createElement('ContactPointEmailValue'))->
                    appendChild($base->createTextNode($data->getEmail('b')));
            }
        }
        //NAME block insertion
        if ($data->getName('b') === null) {
            throw new \Exception('Borrower name is missing.');
        } else {
            $indiv->appendChild($data->getName('b')->getXML($base));
        }

        //Mailing address insertion if one is present
        if ($data->getAddress('b', 'Mailing') !== null) {
            //Insert address block and set temp pointer to ADDRESS element
            $temp = $borr->appendChild($base->createElement('ADDRESSES'))->
                appendChild($data->getAddress('b', 'Mailing')->getXML($base));
            /* Address object's getXML() doesn't insert an AddressType field, which is required to
            indicate this as a mailing address, so we target the CityName element and insert the needed node
            just before it */
            $ref_node = $temp->getElementsByTagName('CityName')->item(0);
            $temp->insertBefore($base->createElement('AddressType', 'Mailing'), $ref_node);
        }

        //Insert the [...]ROLES/ROLE/BORROWER node to hold more info for the borrower
        $borr_borr = $borr->appendChild($base->createElement('ROLES'))->
            appendChild($base->createElement('ROLE'))->
            appendChild($base->createElement('BORROWER'));
        if ($data->getDOB('b') !== null && $data->getDOB('b') !== '') {
            $borr_borr->appendChild($base->createElement('BORROWER_DETAIL'))->
                appendChild($base->createElement('BorrowerBirthDate'))->
                appendChild($base->createTextNode($data->getDOB('b')));
        }
        //RESIDENCES node insertion to hold residential addresses
        $residences = $borr_borr->appendChild($base->createElement('RESIDENCES'));
        //Insert Current residential address
        if ($data->getAddress('b', 'Current') === null) {
            throw new \Exception('Borrower\'s current address cannot be empty.');
        } else {
            $residences->appendChild($base->createElement('RESIDENCE'))->
                appendChild($data->getAddress('b', 'Current')->getXML($base))->
                parentNode->
                appendChild($base->createElement('RESIDENCE_DETAIL'))->
                appendChild($base->createElement('BorrowerResidencyType', 'Current'));
        }

        //Insert Prior residential address
        if ($data->getAddress('b', 'Prior') !== null) {
            $residences->appendChild($base->createElement('RESIDENCE'))->
                appendChild($data->getAddress('b', 'Prior')->getXML($base))->
                parentNode->
                appendChild($base->createElement('RESIDENCE_DETAIL'))->
                appendChild($base->createElement('BorrowerResidencyType', 'Prior'));
        }

        //Insert PartyRoleType for borrower
        $borr->getElementsByTagName('ROLES')->item(0)->getElementsByTagName('ROLE')->item(0)->
            appendChild($base->createElement('ROLE_DETAIL'))->
            appendChild($base->createElement('PartyRoleType', 'Borrower'));
        
        //Insert borrower SSN
        if ($data->getSSN('b') === null || $data->getSSN('b') === '') {
            throw new \Exception('Borrower SSN cannot be empty.');
        } else {
            $borr->appendChild($base->createElement('TAXPAYER_IDENTIFIERS'))->
                appendChild($base->createElement('TAXPAYER_IDENTIFIER'))->
                appendChild($base->createElement('TaxpayerIdentifierType', 'SocialSecurityNumber'))->
                parentNode->
                appendChild($base->createElement('TaxpayerIdentifierValue'))->
                appendChild($base->createTextNode($data->getSSN('b')));
        }

        //If coborrower name is present, then generate the PARTY node for the coborrower
        if ($data->getName('c') !== null) {
            $coborr = $deal->getElementsByTagName('PARTIES')->item(0)->
                appendChild($base->createElement('PARTY'));
            $coborr->setAttribute('SequenceNumber', '2');
            $coborr->setAttributeNS($p2, 'label', 'Party2');

            $coborr->appendChild($base->createElement('INDIVIDUAL'));
            //If coborrower has a phone or email present, then generate CONTACT_POINTS node
            if (
                $data->getPhone('c') !== null ||
                $data->getEmail('c') !== null &&
                $data->getEmail('c') !== ''
            ) {
                $contact_points = $coborr->getElementsByTagName('INDIVIDUAL')->item(0)->
                    appendChild($base->createElement('CONTACT_POINTS'));
                //If coborrower has a phone, then insert it
                if ($data->getPhone('c') !== null) {
                    $contact_points->appendChild($data->getPhone('c')->getXML($base));
                }
                //If coborrower has an email, then insert it
                if ($data->getEmail('c') !== null) {
                    $contact_points->appendChild($base->createElement('CONTACT_POINT'))->
                        appendChild($base->createElement('CONTACT_POINT_EMAIL'))->
                        appendChild($base->createElement('ContactPointEmailValue'))->
                        appendChild($base->createTextNode($data->getEmail('c')));
                }
            }

            //Insert coborrower NAME block
            $coborr->getElementsByTagName('INDIVIDUAL')->item(0)->
                appendChild($data->getName('c')->getXML($base));

            //Insert coborrower mailing address block
            if ($data->getAddress('c', 'Mailing') !== null) {
                $addresses = $coborr->appendChild($base->createElement('ADDRESSES'))->
                    appendChild($data->getAddress('c', 'Mailing')->getXML($base));
                $ref_node = $addresses->getElementsByTagName('CityName')->item(0);
                $addresses->insertBefore($base->createElement('AddressType', 'Mailing'), $ref_node);
            }

            //ROLE container insertion
            $role = $coborr->appendChild($base->createElement('ROLES'))->
                appendChild($base->createElement('ROLE'));

            //BORROWER container insertion
            $coborr_coborr = $role->appendChild($base->createElement('BORROWER'));

            //If coborrower DOB is present, insert it
            if ($data->getDOB('c') !== null && $data->getDOB('c') !== '') {
                $coborr_coborr->appendChild($base->createElement('BORROWER_DETAIL'))->
                    appendChild($base->createElement('BorrowerBirthDate'))->
                    appendChild($base->createTextNode($data->getDOB('c')));
            }

            //Insert current residencial address
            if ($data->getAddress('c', 'Current') === null) {
                throw new \Exception('Coborrower current address cannot be empty');
            } else {
                $coborr_coborr->appendChild($base->createElement('RESIDENCES'))->
                    appendChild($base->createElement('RESIDENCE'))->
                    appendChild($data->getAddress('c', 'Current')->getXML($base))->
                    parentNode->
                    appendChild($base->createElement('RESIDENCE_DETAIL'))->
                    appendChild($base->createElement('BorrowerResidencyType', 'Current'));
            }

            //Insert prior residential address, if provided
            if ($data->getAddress('c', 'Prior') !== null) {
                $coborr_coborr->getElementsByTagName('RESIDENCES')->item(0)->
                    appendChild($base->createElement('RESIDENCE'))->
                    appendChild($data->getAddress('c', 'Prior')->getXML($base))->
                    parentNode->
                    appendChild($base->createElement('RESIDENCE_DETAIL'))->
                    appendChild($base->createElement('BorrowerResidencyType', 'Prior'));
            }

            //Insert role type
            $role->appendChild($base->createElement('ROLE_DETAIL'))->
                appendChild($base->createElement('PartyRoleType', 'Borrower'));
            
            //Insert coborrower SSN
            if ($data->getSSN('c') === null || $data->getSSN('c') === '') {
                throw new \Exception('Coborrower SSN cannot be empty.');
            } else {
                $coborr->appendChild($base->createElement('TAXPAYER_IDENTIFIERS'))->
                    appendChild($base->createElement('TAXPAYER_IDENTIFIER'))->
                    appendChild($base->createElement('TaxpayerIdentifierType', 'SocialSecurityNumber'))->
                    parentNode->appendChild($base->createElement('TaxpayerIdentifierValue'))->
                    appendChild($base->createTextNode($data->getSSN('c')));
            }
        }

        //RELATIONSHIPS container insertion
        $relationships = $deal->appendChild($base->createElement('RELATIONSHIPS'));
        //Insert service xlink for borrower
        $relationship1 = $relationships->appendChild($base->createElement('RELATIONSHIP'));
        $relationship1->setAttributeNS(
            $p2,
            'arcrole',
            'urn:fdc:Meridianlink.com:2017:mortgage/PARTY_IsVerifiedBy_SERVICE'
        );
        $relationship1->setAttributeNS($p2, 'from', 'Party1');
        $relationship1->setAttributeNS($p2, 'to', 'Service1');
        //If coborrower exists, insert xlink for coborrower
        if ($data->getName('c') !== null) {
            $relationship2 = $relationships->appendChild($base->createElement('RELATIONSHIP'));
            $relationship2->setAttributeNS(
                $p2,
                'arcrole',
                'urn:fdc:Meridianlink.com:2017:mortgage/PARTY_IsVerifiedBy_SERVICE'
            );
            $relationship2->setAttributeNS($p2, 'from', 'Party2');
            $relationship2->setAttributeNS($p2, 'to', 'Service1');
        }
        //If subject property exists, insert xlink for this
        if ($data->getSubjectPropAdd() !== null) {
            $relationship3 = $relationships->appendChild($base->createElement('RELATIONSHIP'));
            $relationship3->setAttributeNS(
                $p2,
                'arcrole',
                'urn:fdc:Meridianlink.com:2017:mortgage/PROPERTY_IsVerifiedBy_SERVICE'
            );
            $relationship3->setAttributeNS($p2, 'from', 'Property1');
            $relationship3->setAttributeNS($p2, 'to', 'Service1');
        }

        //SERVICES container build-out
        $service = $deal->appendChild($base->createElement('SERVICES'))->
            appendChild($base->createElement('SERVICE'));
        $service->setAttributeNS($p2, 'label', 'Service1');
        $credit_request = $service->appendChild($base->createElement('CREDIT'))->
            appendChild($base->createElement('CREDIT_REQUEST'));
        
        //Insert credit bureau option flags
        $credit_req_data = $credit_request->appendChild($base->createElement('CREDIT_REQUEST_DATAS'))->
            appendChild($base->createElement('CREDIT_REQUEST_DATA'));
        $credit_req_data->appendChild($base->createElement('CREDIT_REPOSITORY_INCLUDED'))->
            appendChild($base->createElement(
                'CreditRepositoryIncludedEquifaxIndicator',
                var_export($data->getEquifaxOptions()['credit'], true)
            ))->parentNode->
            appendChild($base->createElement(
                'CreditRepositoryIncludedExperianIndicator',
                var_export($data->getExperianOptions()['credit'], true)
            ))->parentNode->
            appendChild($base->createElement(
                'CreditRepositoryIncludedTransUnionIndicator',
                var_export($data->getTransUnionOptions()['credit'], true)
            ))->parentNode->
            appendChild($base->createElement('EXTENSION'))->
            appendChild($base->createElement('OTHER'))->
            appendChild($base->createElementNS(
                $p3,
                'RequestEquifaxScore',
                var_export($data->getEquifaxOptions()['score'], true)
            ))->parentNode->
            appendChild($base->createElementNS(
                $p3,
                'RequestExperianFraud',
                var_export($data->getExperianOptions()['fraud'], true)
            ))->parentNode->
            appendChild($base->createElementNS(
                $p3,
                'RequestExperianScore',
                var_export($data->getExperianOptions()['score'], true)
            ))->parentNode->
            appendChild($base->createElementNS(
                $p3,
                'RequestTransUnionFraud',
                var_export($data->getTransUnionOptions()['fraud'], true)
            ))->parentNode->
            appendChild($base->createElementNS(
                $p3,
                'RequestTransUnionScore',
                var_export($data->getTransUnionOptions()['score'], true)
            ));

        //Insert request action type
        $credit_req_data->appendChild($base->createElement('CREDIT_REQUEST_DATA_DETAIL'))->
            appendChild($base->createElement('CreditReportRequestActionType', 'Other'))->parentNode->
            appendChild($base->createElement('CreditReportRequestActionTypeOtherDescription'))->
            appendChild($base->createTextNode($data->getRequestType()));
        
        //If credit card info is provided, insert it
        if ($data->getCreditCard() !== null) {
            $service->appendChild($base->createElement('SERVICE_PAYMENTS'))->
                appendChild($data->getCreditCard()->getXML($base));
        }

        //Insert service product description
        $service_detail = $service->appendChild($base->createElement('SERVICE_PRODUCT'))->
            appendChild($base->createElement('SERVICE_PRODUCT_REQUEST'))->
            appendChild($base->createElement('SERVICE_PRODUCT_DETAIL'));
        $service_detail->appendChild($base->createElement('ServiceProductDescription', 'CreditOrder'));
        //If any preferred response formats are requested, include the corresponding nodes
        if ($data->getResponseFormats()->getCount() !== 0) {
            $service_detail->appendChild($base->createElement('EXTENSION'))->
                appendChild($base->createElement('OTHER'))->
                appendChild($data->getResponseFormats()->getXML($base, $p3));
        }
        //Insert the file number the refresh report is being ordered off of
        if ($data->getVendorOrderID() === null || $data->getVendorOrderID() === '') {
            throw new \Exception('File number of original pull is required for Refresh report order');
        } else {
            $service->appendChild($base->createElement('SERVICE_PRODUCT_FULFILLMENT'))->
                appendChild($base->createElement('SERVICE_PRODUCT_FULFILLMENT_DETAIL'))->
                appendChild($base->createElement('VendorOrderIdentifier'))->
                appendChild($base->createTextNode($data->getVendorOrderID()));
        }
        return $base->saveXML();
    }

    /**
     * Generates an XML string for a permanent unmerge request
     *
     * @return string
     */
    private function outputXMLforPermUnmerge(): string
    {
        /* Disable XML errors. We turn this off because it will display even warnings, which tends to trigger
        because the 'inetapi/MISMO3_4_MCL_Extension.xsd' namespace isn't a fully qualified URI */
        libxml_clear_errors();
        libxml_use_internal_errors(true);

        /* Create aliases for the object properties. This is strictly so that we don't have to constantly
        type $this or self:: */
        $base = $this->base;
        $root = $this->root;
        $xpath = $this->xpath;
        $data = $this->data;
        $p1 = self::P1;
        $p2 = self::P2;
        $p3 = self::P3;

        //Insert DataVersionIdentifier node
        $root->appendChild($base->createElement('ABOUT_VERSIONS'))->
        appendChild($base->createElement('ABOUT_VERSION'))->
        appendChild($base->createElement('DataVersionIdentifier'))->
        appendChild($base->createTextNode($data->getDataVersion()));

        //Insert DEAL container
        $deal = $root->appendChild($base->createElement('DEAL_SETS'))->
            appendChild($base->createElement('DEAL_SET'))->
            appendChild($base->createElement('DEALS'))->
            appendChild($base->createElement('DEAL'));
        
        //Insert borrower's PARTY node
        $borr = $deal->appendChild($base->createElement('PARTIES'))->
            appendChild($base->createElement('PARTY'));
        $borr->setAttribute('SequenceNumber', '1');
        $borr->setAttributeNS($p2, 'label', 'Party1');
        //Insert borrower name
        if ($data->getName('b') === null) {
            throw new \Exception('Borrower name cannot be empty.');
        } else {
            $borr->appendChild($base->createElement('INDIVIDUAL'))->
                appendChild($data->getName('b')->getXML($base));
        }
        //Insert borrower role
        $borr->appendChild($base->createElement('ROLES'))->
            appendChild($base->createElement('ROLE'))->
            appendChild($base->createElement('ROLE_DETAIL'))->
            appendChild($base->createElement('PartyRoleType', 'Borrower'));
        //Insert social security number node
        if ($data->getSSN('b') === null || $data->getSSN('b') === '') {
            throw new \Exception('Borrower SSN cannot be emtpy.');
        } else {
            $borr->appendChild($base->createElement('TAXPAYER_IDENTIFIERS'))->
                appendChild($base->createElement('TAXPAYER_IDENTIFIER'))->
                appendChild($base->createElement('TaxpayerIdentifierType', 'SocialSecurityNumber'))->
                parentNode->appendChild($base->createElement('TaxpayerIdentifierValue'))->
                appendChild($base->createTextNode($data->getSSN('b')));
        }
        
        //If coborrower is present, insert their information
        if ($data->getName('c') !== null) {
            //Insert coborrower's PARTY container
            $coborr = $deal->getElementsByTagName('PARTIES')->item(0)->
                appendChild($base->createElement('PARTY'));
            $coborr->setAttribute('SequenceNumber', '2');
            $coborr->setAttributeNS($p2, 'label', 'Party2');
            //Insert coborrower name node
            $coborr->appendChild($base->createElement('INDIVIDUAL'))->
                appendChild($data->getName('c')->getXML($base));
            //Insert coborrower role type
            $coborr->appendChild($base->createElement('ROLES'))->
                appendChild($base->createElement('ROLE'))->
                appendChild($base->createElement('ROLE_DETAIL'))->
                appendChild($base->createElement('PartyRoleType', 'Borrower'));
            //Insert social security number node
            if ($data->getSSN('c') === null || $data->getSSN('c') === '') {
                throw new \Exception('Coborrower SSN cannot be emtpy.');
            } else {
                $coborr->appendChild($base->createElement('TAXPAYER_IDENTIFIERS'))->
                    appendChild($base->createElement('TAXPAYER_IDENTIFIER'))->
                    appendChild($base->createElement('TaxpayerIdentifierType', 'SocialSecurityNumber'))->
                    parentNode->appendChild($base->createElement('TaxpayerIdentifierValue'))->
                    appendChild($base->createTextNode($data->getSSN('c')));
            }
        }

        //RELATIONSHIPS container insertion
        $relationships = $deal->appendChild($base->createElement('RELATIONSHIPS'));
        //Insert borrower-service link
        $relationship1 = $relationships->appendChild($base->createElement('RELATIONSHIP'));
        $relationship1->setAttributeNS(
            $p2,
            'arcrole',
            'urn:fdc:Meridianlink.com:2017:mortgage/PARTY_IsVerifiedBy_SERVICE'
        );
        $relationship1->setAttributeNS($p2, 'from', 'Party1');
        $relationship1->setAttributeNS($p2, 'to', 'Service1');
        //If coborrower is present, insert their borrower-service link
        if ($data->getName('c') !== null) {
            $relationship2 = $relationships->appendChild($base->createElement('RELATIONSHIP'));
            $relationship2->setAttributeNS(
                $p2,
                'arcrole',
                'urn:fdc:Meridianlink.com:2017:mortgage/PARTY_IsVerifiedBy_SERVICE'
            );
            $relationship2->setAttributeNS($p2, 'from', 'Party2');
            $relationship2->setAttributeNS($p2, 'to', 'Service1');
        }
        //Insert SERVICE container
        $service = $deal->appendChild($base->createElement('SERVICES'))->
            appendChild($base->createElement('SERVICE'));
        $service->setAttributeNS($p2, 'label', 'Service1');
        //Insert credit options flags and request action type
        $service->appendChild($base->createElement('CREDIT'))->
            appendChild($base->createElement('CREDIT_REQUEST'))->
            appendChild($base->createElement('CREDIT_REQUEST_DATAS'))->
            appendChild($base->createElement('CREDIT_REQUEST_DATA'))->
            appendChild($base->createElement('CREDIT_REPOSITORY_INCLUDED'))->
            appendChild($base->createElement(
                'CreditRepositoryIncludedEquifaxIndicator',
                var_export($data->getEquifaxOptions()['credit'], true)
            ))->parentNode->
            appendChild($base->createElement(
                'CreditRepositoryIncludedExperianIndicator',
                var_export($data->getExperianOptions()['credit'], true)
            ))->parentNode->
            appendChild($base->createElement(
                'CreditRepositoryIncludedTransUnionIndicator',
                var_export($data->getTransUnionOptions()['credit'], true)
            ))->parentNode->parentNode->
            appendChild($base->createElement('CREDIT_REQUEST_DATA_DETAIL'))->
            appendChild($base->createElement('CreditReportRequestActionType'))->
            appendChild($base->createTextNode($data->getRequestType()));
        //Insert credit card payment info if present
        if ($data->getCreditCard() !== null) {
            $service->appendChild($base->createElement('SERVICE_PAYMENTS'))->
                appendChild($data->getCreditCard()->getXML($base));
        }
        //Insert service product type
        $service_detail = $service->appendChild($base->createElement('SERVICE_PRODUCT'))->
            appendChild($base->createElement('SERVICE_PRODUCT_REQUEST'))->
            appendChild($base->createElement('SERVICE_PRODUCT_DETAIL'));
        $service_detail->appendChild($base->createElement('ServiceProductDescription', 'CreditOrder'));
        //If any preferred formats are indicated, insert their nodes
        if ($data->getResponseFormats()->getCount() !== 0) {
            $pre_formats = $service_detail->appendChild($base->createElement('EXTENSION'))->
                appendChild($base->createElement('OTHER'));
            //Insert nodes for the formats being requested
            $pre_formats->appendChild($data->getResponseFormats()->getXML($base, $p3));
        }
        //Insert existing file number associated with the status query
        if ($data->getVendorOrderID() === null || $data->getVendorOrderID() === '') {
            throw new \Exception('VendorOrderIdentifier must be provided for ' .
                $data->getRequestType() . ' requests.');
        } else {
            $service->appendChild($base->createElement('SERVICE_PRODUCT_FULFILLMENT'))->
            appendChild($base->createElement('SERVICE_PRODUCT_FULFILLMENT_DETAIL'))->
            appendChild($base->createElement('VendorOrderIdentifier'))->
            appendChild($base->createTextNode($data->getVendorOrderID()));
        }
        return $base->saveXML();
    }
}
