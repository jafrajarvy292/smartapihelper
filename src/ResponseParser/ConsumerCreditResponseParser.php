<?php

/**
 * This file is part of MeridianLink's SmartAPI Helper package.
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with
 * this source code.
 */

namespace jafrajarvy292\SmartAPIHelper\ResponseParser;

/**
 * This class handles the parsing of Consumer Credit response data. Native methods will allow users to quickly
 * grab the most commonly-used data points. Note that many of these methods require the presence of parsable
 * XML data. This means if your request did not include parsable XML as a preferred response format, these
 * methods will return exceptions, empty arrays, etc. Additionally, before attempting to extract data for
 * a specific applicant (i.e. borrower, coborrower), you should check to see if the person is present
 * in the data via the provided method for doing this.
 */
class ConsumerCreditResponseParser extends ResponseParser
{
    /** @var array Holds all the possible statuses that can be returned by the server for this product type.
     * See parent class for all possible statuses with SmartAPI and details on each enumeration
     */
    public const STATUS = [
        'REQUEST_ERROR' => 'REQUEST_ERROR',
        'SERVICE_ERROR' => 'SERVICE_ERROR',
        'NEW' => 'NEW',
        'PROCESSING' => 'PROCESSING',
        'COMPLETED' => 'COMPLETED',
        'ERROR' => 'ERROR'
    ];
    /**
     * @var array This table holds the mapping between RatingCode and RatingText
     *
     * - X or -: The creditor did not report any data for that month. Note that the response
     * XML may sometimes offer it's own corresponding rating text. In such cases, you may see a value
     * of "TooNew" for this code. Practically speaking, they can both be treated the same: nothing available.
     * - C: The account is current and paid on time.
     * - 1: The account is 30-59 days late on payment.
     * - 2: The account is 60-89 days late on payment.
     * - 3: The account is 90-119 days late on payment.
     * - 4 or 5 or 6: The account is 120 or more days late on payment.
     * - 7: The account has been included in a bankruptcy filing.
     * - 8: The secured debt is overdue and the creditor has moved to repossess collateral to settle.
     * - 9: The unsecured debt is overdue and the creditor has written it off. This may--or may already have
     * been--sold to a collection agency.
     */
    public const RATING_TEXT = [
        'X' => 'NoDataAvailable',
        '-' => 'NoDataAvailable',
        'C' => 'AsAgreed',
        '1' => 'Late30Days',
        '2' => 'Late60Days',
        '3' => 'Late90Days',
        '4' => 'LateOver120Days',
        '5' => 'LateOver120Days',
        '6' => 'LateOver120Days',
        '7' => 'BankruptcyOrWageEarnerPlan',
        '8' => 'ForeclosureOrRepossession',
        '9' => 'CollectionOrChargeOff'
    ];
    /** @var bool Holds flag that tells us if the response file contains a primary borrower */
    private $borr_present = false;
    /** @var string The label associated with the borrower, parsed from the XML response */
    private $borr_label = '';
    /** @var string To reference the borrower in the class's methods, use this */
    private $borr_id = 'b';

    /** @var string The label associated with the coborrower, parsed from the XML response */
    private $coborr_label = '';
    /** @var bool Holds flag that tells us if the response file contains a coborrower / secondary borrower */
    private $coborr_present = false;
    /** @var string To reference the coborrower in the class's methods, use this */
    private $coborr_id = 'c';

    /** @var array Holds an array of the labels identifying each of the borrower's CREDIT_FILE nodes */
    private $borr_credit_file_labels = [];
    /** @var array Holds an array of the labels identifying each of the coborrower's CREDIT_FILE nodes */
    private $coborr_credit_file_labels = [];

    /**
     * Load the XML response string to our parser. In doing so, the same parent method is called, which
     * extracts information that is universal to all products. This version of the method also extracts
     * information specific to the Consumer Credit product.
     *
     * @param string $xml_response The response string received from the server
     * @param string $xml_ver XML version
     * @param string $encoding XML encoding
     * @return void
     */
    public function loadXMLResponse(
        string $xml_response,
        string $xml_ver = '1.0',
        string $encoding = 'utf-8'
    ): void {
        //Run the parent class's constructor to populate all the items that are not product-specific
        parent::loadXMLResponse($xml_response, $xml_ver, $encoding);

        //Determine if the primary borrower is present in the response file.
        $borr_node = $this->xpath->evaluate(
            '/P1:MESSAGE/P1:DEAL_SETS/P1:DEAL_SET/P1:DEALS/P1:DEAL/P1:SERVICES/P1:SERVICE/P1:CREDIT/' .
            'P1:CREDIT_RESPONSE/P1:PARTIES/P1:PARTY/P1:ROLES/P1:ROLE[P1:BORROWER/P1:BORROWER_DETAIL/' .
            'P1:BorrowerClassificationType[text()="Primary"]]/@P2:label'
        )->item(0);
        //If borrower is present, store their label and set their flag to true
        if ($borr_node !== null) {
            $this->borr_label = $borr_node->textContent;
            $this->borr_present = true;
        }

        //Determine if the coborrower is present.
        $coborr_node = $this->xpath->evaluate(
            '/P1:MESSAGE/P1:DEAL_SETS/P1:DEAL_SET/P1:DEALS/P1:DEAL/P1:SERVICES/P1:SERVICE/P1:CREDIT/' .
            'P1:CREDIT_RESPONSE/P1:PARTIES/P1:PARTY/P1:ROLES/P1:ROLE[P1:BORROWER/P1:BORROWER_DETAIL/' .
            'P1:BorrowerClassificationType[text()="Secondary"]]/@P2:label'
        )->item(0);
        //If coborrower is present, store their label and set their flag to true
        if ($coborr_node !== null) {
            $this->coborr_label = $coborr_node->textContent;
            $this->coborr_present = true;
        }

        //If borrower is present, identify all CREDIT_FILE containers associated with them and save it.
        if ($this->borr_present === true) {
            $this->loadCreditFileLabels('b');
        }
        //If coborrower is present, identify all CREDIT_FILE containers associated with them and save it.
        if ($this->coborr_present === true) {
            $this->loadCreditFileLabels('c');
        }
    }

    /**
     * Check if a person is present in the parsable XML data. Pass in ID of the borrower or coborrower and
     * this will return a true or a false. This should be used prior to attempting run any methods to get that
     * person's data. Note if parsable XML was not requested as a preferred response format, then this will
     * always return false.
     *
     * @param string $id The ID of the person being checked.
     * @return boolean
     */
    public function isPersonPresent(string $id): bool
    {
        $this->checkPersonID($id);
        if ($id === $this->borr_id) {
            return $this->borr_present;
        } elseif ($id === $this->coborr_id) {
            return $this->coborr_present;
        }
    }

    /**
     * Return the status of each credit bureau ordered for the applicant as an array of associative arrays.
     * The associative array will have the following keys:
     * - BureauName: The name of the credit bureau (i.e. Equifax, Experian, TransUnion)
     * - Result: The bureau's response. Check MISMO 3.4 schema for 'CreditFileResultStatusBase' type for
     * list of enumerations. Anything other than 'FileReturned' value can be considered an unsuccessful order.
     * For example, if the borrower's file is frozen, that wuold return something other than FileReturned.
     * - ErrorDescription: If the result wasn't a success, the corresponding error message returned by the
     * bureau will be loaded here.
     *
     * Example below:
     * ```
     * ['BureauName'] => 'TransUnion'
     * ['Result'] => 'NoFileReturnedError'
     * ['ErrorDescription'] => 'FILE FROZEN BY CONSUMER'
     * ```
     *
     * @param string $id The ID of the person you wan to grab the data for.
     * @return array Returns an array of associative arrays
     * @throws \Exception If borrower data is requested, but is not present in the response data.
     * @throws \Exception If coborrower data is requested, is not present in the response data
     */
    public function getBureauResponses(string $id): array
    {
        $this->checkPersonID($id);
        //The final array of associative arrays that we return
        $return_data = [];
        /* Stores all the CREDIT_FILE labels we'll be looping through. The CREDIT_FILE container is where
        bureau response info is located */
        $labels = [];
        //If borrower info is being requested, then load the borrower's CREDIT_FILE labels to our variable
        if ($id === $this->borr_id) {
            if ($this->borr_present === false) {
                throw new \Exception('Borrower is not present in the response file.');
            } else {
                $labels = $this->borr_credit_file_labels;
            }
        //If coborrower info is being requested, then load the coborrower's CREDIT_FILE labels to our variable
        } elseif ($id === $this->coborr_id) {
            if ($this->coborr_present === false) {
                throw new \Exception('Coborrower is not present in the response file.');
            } else {
                $labels = $this->coborr_credit_file_labels;
            }
        }
        //Loop through each CREDIT_FILE container associated with the person
        for ($i = 0; $i < count($labels); $i++) {
            //Our associative array we'll be returning for each CREDIT_FILE container we loop through
            $data = [
                'BureauName' => '',
                'Result' => '',
                'ErrorDescription' => ''
            ];
            //Select the CREDIT_FILE node
            $node = $this->xpath->evaluate(
                '/P1:MESSAGE/P1:DEAL_SETS/P1:DEAL_SET/P1:DEALS/P1:DEAL/P1:SERVICES/P1:SERVICE/P1:CREDIT/' .
                'P1:CREDIT_RESPONSE/P1:CREDIT_FILES/P1:CREDIT_FILE[@P2:label="' . $labels[$i] . '"]'
            )->item(0);
            $bureau_node = $node->getElementsByTagName('CreditRepositorySourceType')->item(0);
            //If a bureau name is present, then store it
            if ($bureau_node !== null) {
                $data['BureauName'] = $bureau_node->textContent;
            }
            //If a result node is present, then store it
            $result_node = $node->getElementsByTagName('CreditFileResultStatusType')->item(0);
            if ($result_node !== null) {
                $data['Result'] = $result_node->textContent;
            }
            //If an error description node is present, then store it
            $description_node = $node->getElementsByTagName('CreditErrorMessageText')->item(0);
            if ($description_node !== null) {
                $data['ErrorDescription'] = $description_node->textContent;
            }
            //Push the associative array to our array of results
            $return_data[] = $data;
        }
        //Return the array of results, which will be an array of associative arrays
        return $return_data;
    }
    
    /**
     * Returns a multidimensional array containing the credit scores reported by the bureaus and all related
     * information. An array of the below associative arrays will be returned. Descriptions in parenthesis
     * - BureauName: The credit bureau that reported the score (i.e. Equifax, TransUnion, Experian)
     * - DateGenerated: The date the credit bureau generated the score, this will coincide with
     * the date of the credit pull
     * - MaximumValue: The ceiling for this score model
     * - MinimumValue: The floor for this score model
     * - ModelName: The score model name
     * - PercentileRank: The person's percentile rank
     * - ScoreFactors: An array of score factors--code and corresponding text
     * - ScoreValue: The person's credit score
     *
     * Example below:
     * ```
     * ['BureauName'] => 'TransUnion'
     * ['DateGenerated'] => '2019-12-29'
     * ['MaximumValue'] => '843'
     * ['MinimumValue'] => '336'
     * ['ModelName'] => 'FICORiskScoreClassic98'
     * ['PercentileRank'] => '32'
     * ['ScoreFactors'] =>
     *  [0]
     *   ['Code'] => '040'
     *   ['Text'] => 'Too many inquiries'
     *  [1]
     *   ['Code'] => '041'
     *   ['Text'] => 'Too many lates'
     * ['ScoreValue'] => '667'
     * ```
     *
     * @param string $id The ID of the applicant you want to obtain this info for
     * @return array
     */
    public function getCreditScores(string $id): array
    {
        $this->checkPersonID($id);
        $label = '';
        $score_labels = [];
        $credit_file_labels = $this->getCreditFileLabels($id);
        $return_array = [];

        if ($id === $this->borr_id) {
            $label = $this->borr_label;
        } elseif ($id === $this->coborr_id) {
            $label = $this->coborr_label;
        }

        /* Loop through each RELATIONSHIP element of type CREDIT_SCORE_IsAssociatedWith_ROLE and extract
        all the score labels associated with the person. One score label is one credit score */
        $node = $this->xpath->evaluate(
            '//P1:SERVICES/P1:SERVICE/P1:RELATIONSHIPS/' .
            'P1:RELATIONSHIP[@P2:arcrole="urn:fdc:Meridianlink.com:2017:mortgage' .
            '/CREDIT_SCORE_IsAssociatedWith_ROLE"][@P2:to="' . $label . '"]/@P2:from'
        );
        for ($i = 0; $i < $node->length; $i++) {
            $score_labels[] = $node->item($i)->textContent;
        }
        //Loop through each score label and grab all the related data for our data array
        for ($i = 0; $i < count($score_labels); $i++) {
            /* This is the associatve array we store the score info into. Where more than one score is found,
            we will return an array of these */
            $data = [
                'BureauName' => '',
                'DateGenerated' => '',
                'MaximumValue' => '',
                'MinimumValue' => '',
                'ModelName' => '',
                'PercentileRank' => '',
                'ScoreFactors' => [],
                'ScoreValue' => ''
            ];
            /* Find the score's corresponding CREDIT_FILE label. We need this so we can find out what bureau
            reported the score */
            $bureau_label = $this->xpath->evaluate(
                '/P1:MESSAGE/P1:DEAL_SETS/P1:DEAL_SET/P1:DEALS/P1:DEAL/P1:SERVICES/P1:SERVICE/' .
                'P1:RELATIONSHIPS/P1:RELATIONSHIP[@P2:arcrole="urn:fdc:Meridianlink.com:2017:mortgage/' .
                'CREDIT_FILE_IsAssociatedWith_CREDIT_SCORE"][@P2:from="' . $score_labels[$i] . '"]/@P2:to'
            )->item(0)->textContent;
            //Use the CREDIT_FILE label to get the bureau name
            $bureau_name = $this->xpath->evaluate(
                '/P1:MESSAGE/P1:DEAL_SETS/P1:DEAL_SET/P1:DEALS/P1:DEAL/P1:SERVICES/P1:SERVICE/P1:CREDIT/' .
                'P1:CREDIT_RESPONSE/P1:CREDIT_FILES/P1:CREDIT_FILE[@P2:label="' . $bureau_label . '"]/' .
                'P1:CREDIT_FILE_DETAIL/P1:CreditRepositorySourceType'
            )->item(0)->textContent;
            //Store the bureau name to the data array
            $data['BureauName'] = $bureau_name;

            //Bookmark the CREDIT_SCORE node, since we'll need to pull a handful of items from it
            $score_node = $this->xpath->evaluate(
                '/P1:MESSAGE/P1:DEAL_SETS/P1:DEAL_SET/P1:DEALS/P1:DEAL/P1:SERVICES/P1:SERVICE/P1:CREDIT/' .
                'P1:CREDIT_RESPONSE/P1:CREDIT_SCORES/P1:CREDIT_SCORE[@P2:label="' . $score_labels[$i] . '"]'
            )->item(0);
            //Get the date that the score was generated
            $data['DateGenerated'] = $score_node->getElementsByTagName('CreditScoreDate')->item(0)->
                textContent;
            
            /* Get the score model name. Use CreditScoreModelNameType value, but if CreditScoreModelNameType
            is of value 'Other', then use CreditScoreModelNameTypeOtherDescription */
            if (
                $score_node->getElementsByTagName('CreditScoreModelNameType')
                    ->item(0)->textContent === 'Other'
            ) {
                $data['ModelName'] = $score_node->
                    getElementsByTagName('CreditScoreModelNameTypeOtherDescription')->item(0)->textContent;
            } else {
                $data['ModelName'] = $score_node->getElementsByTagName('CreditScoreModelNameType')->
                    item(0)->textContent;
            }

            /* Get the percentile rank, if available */
            $percentile_rank = $score_node->getElementsByTagName('CreditScoreRankPercentileValue')->item(0);
            if ($percentile_rank !== null) {
                $data['PercentileRank'] = $percentile_rank->textContent;
            }

            //Get the score value, if available */
            $score_value = $score_node->getElementsByTagName('CreditScoreValue')->item(0);
            if ($score_node !== null) {
                $data['ScoreValue'] = $score_value->textContent;
            }

            //Get the score factors
            $score_factors = $this->xpath->evaluate(
                'P1:CREDIT_SCORE_FACTORS/P1:CREDIT_SCORE_FACTOR',
                $score_node
            );
            //Loop through each CREDIT_SCORE_FACTOR node and extract the data
            for ($k = 0; $k < $score_factors->length; $k++) {
                /* We create one associative array for each score factor, containing the code and its
                corresponding text description */
                $factors = [
                    'Code' => '',
                    'Text' => ''
                ];
                //If a CreditScoreFactorCode exists, load it, else leave it as empty string
                if (
                    $score_factors->item($k)->getElementsByTagName('CreditScoreFactorCode')->item(0) !==
                    null
                ) {
                    $factors['Code'] = $score_factors->item($k)->
                    getElementsByTagName('CreditScoreFactorCode')->item(0)->textContent;
                }
                //If a CreditScoreFactorText exists, load it, else leave it as empty string
                if (
                    $score_factors->item($k)->getElementsByTagName('CreditScoreFactorText')->item(0) !==
                    null
                ) {
                    $factors['Text'] = $score_factors->item($k)->
                    getElementsByTagName('CreditScoreFactorText')->item(0)->textContent;
                }
                //Push the factor array to the ScoreFactors array
                $data['ScoreFactors'][] = $factors;
            }

            /* Get Maximum and Minimum score value range. This one tricky to obtain because the
            CREDIT_SCORE_MODEL node doesn't include the bureau name. Instead, we use the CREDIT_FILE label of
            the CREDIT_FILE that reported this score and look for all CREDIT_SCORE_MODELs that were reported
            by that CREDIT_FILE. Once obtain these, we loop through them to look for the one that reported
            this specific score model name. */
            /* Get the score model name we determind earlier. We'll look for the CREDIT_SCORE_MODEL that
            matches this */
            $model_name = $data['ModelName'];
            /* Look for all RELATIONSHIP elements where the CREDIT_FILE that reported the score also reported
            CREDIT_SCORE_MODELs. */
            $pointer = $this->xpath->evaluate(
                '//P1:SERVICE/P1:RELATIONSHIPS/' .
                'P1:RELATIONSHIP[@P2:arcrole="urn:fdc:Meridianlink.com:2017:mortgage/' .
                'CREDIT_FILE_IsAssociatedWith_CREDIT_SCORE_MODEL"][@P2:to="' . $bureau_label .
                '"]//@P2:from'
            );
            //Loop through all the maching RELATIONSHIP nodes and extract the CREDIT_SCORE_MODEL labels.
            $score_model_labels = [];
            for ($k = 0; $k < $pointer->length; $k++) {
                $score_model_labels[] = $pointer->item($k)->textContent;
            }
            /* Loop through each of the CREDIT_SCORE_MODEL nodes that we identified earlier and look for the
            one that is reports either a CreditScoreModelNameType or CreditScoreModelNameTypeOtherDescription
            value that matches the model name */
            for ($k = 0; $k < count($score_model_labels); $k++) {
                $model_node = $this->xpath->evaluate(
                    '//P1:SERVICE/P1:CREDIT/P1:CREDIT_RESPONSE/P1:CREDIT_SCORE_MODELS/' .
                    'P1:CREDIT_SCORE_MODEL[@P2:label="' . $score_model_labels[$k] . '"]/' .
                    'P1:CREDIT_SCORE_MODEL_DETAIL[P1:CreditScoreModelNameType[text()="' . $model_name .
                    '"]|P1:CreditScoreModelNameTypeOtherDescription[text()="' . $model_name . '"]]'
                )->item(0);
                //Once we find our node with the match (ie. !== null)
                if ($model_node !== null) {
                    //If the max score value node exists, then store it, else leave it as empty string
                    if ($model_node->getElementsByTagName('CreditScoreMaximumValue')->item(0) !== null) {
                        $data['MaximumValue'] = $model_node->getElementsByTagName('CreditScoreMaximumValue')->
                            item(0)->textContent;
                    }
                    //If the min score value node exists, then store it, else leave it as empty string
                    if ($model_node->getElementsByTagName('CreditScoreMinimumValue')->item(0) !== null) {
                        $data['MinimumValue'] = $model_node->getElementsByTagName('CreditScoreMinimumValue')->
                            item(0)->textContent;
                    }
                }
            }
            //Push the completed data array to the final array we'll be returning
            $return_array[] = $data;
        }
        return $return_array;
    }

    /**
     * Returns an array of all the CREDIT_FILE labels associated with the person. This method is made public
     * for users who want to do more with the data in these containers than offered by the class' native
     * methods
     *
     * @param string $id The ID of the person for which the labels are sought.
     * @return array
     * @throws \Exception If borrower data is being requested, but they are not present in the response file.
     * @throws \Exception If coborrower data is being requested, but they are not present in the response.
     */
    public function getCreditFileLabels(string $id): array
    {
        $this->checkPersonID($id);
        if ($id === $this->borr_id) {
            if ($this->borr_present === false) {
                throw new \Exception('Borrower not present on file. No CREDIT_FILE labels to provide.');
            } else {
                return $this->borr_credit_file_labels;
            }
        } elseif ($id === $this->coborr_id) {
            if ($this->coborr_present === false) {
                throw new \Exception('Coborrower not present on file. No CREDIT_FILE labels to provide.');
            }
            return $this->coborr_credit_file_labels;
        }
    }

    /**
     * This returns an array of associative arrays. Each associative array contains all details of a single
     * CREDIT_LIABILITY node. Below is an example of the associative array. Most of these are
     * self-explanatory. Refer to the MISMO 3.4 XSD for details on any of these or speak with your technical
     * rep. The default value for each array key is an empty string. This is overwritten if the provider
     * returns a value for that data point.
     * ```
     * FullName
     * CreditLiabilityAccountIdentifier
     * CreditLiabilityAccountType
     * CreditLiabilityUnpaidBalanceAmount
     * CreditLiabilityCurrentRatingCode
     * CreditLiabilityCurrentRatingType
     * CreditLiabilityMonthlyPaymentAmount
     * CreditLiabilityAccountOwnershipType
     * AccountRemarks
     * CreditBusinessType
     * DetailCreditBusinessType
     * CreditLoanType
     * CreditLiability30DaysLateCount
     * CreditLiability60DaysLateCount
     * CreditLiability90DaysLateCount
     * CreditLiabilityAccountOpenedDate
     * CreditLiabilityAccountClosedDate
     * CreditLiabilityAccountPaidDate
     * CreditLiabilityAccountReportedDate
     * CreditLiabilityAccountStatusType
     * CreditLiabilityCreditLimitAmount
     * CreditLiabilityHighBalanceAmount
     * CreditLiabilityHighestAdverseRatingDate
     * CreditLiabilityHighestAdverseRatingCode
     * CreditLiabilityHighestAdverseRatingType
     * CreditLiabilityLastActivityDate
     * CreditLiabilityMonthsRemainingCount
     * CreditLiabilityMonthsReviewedCount
     * CreditLiabilityPastDueAmount
     * CreditLiabilityPaymentPatternDataText
     * CreditLiabilityPaymentPatternStartDate
     * CreditLiabilityTermsDescription
     * CreditLiabilityTermsMonthsCount
     * CreditLiabilityTermsSourceType
     * CreditorAddressStreet
     * CreditorAddressCity
     * CreditorAddressState
     * CreditorAddressZip
     * ContactPointTelephoneValue
     * ```
     *
     * @param string $id On joint credit files, if only liabilities associated with the borrower or
     * coborrower should be returned, indicate their ID here to apply the filter. Otherwise, all liabilities
     * will be returned
     * @return array An array of associative arrays
     * @throws \Exception If borrower data is being requested, but they are not present in the response file.
     * @throws \Exception If coborrower data is being requested, but they are not present in the response.
     */
    public function getLiabilities(string $id = ''): array
    {
        $person_label = '';
        $return_array = [];
        /* If person ID was provided, then we will grab their label to return only liabilities associated
        with them */
        if ($id !== '') {
            //Ensure a valid person ID is passed
            $this->checkPersonID($id);
            //If borrower ID is passed, check that they're present in our response data
            if ($id === 'b') {
                if ($this->borr_present === false) {
                    throw new \Exception(
                        'Borrower not present in data. Unable to filter their liabilities.'
                    );
                } else {
                    $person_label = $this->borr_label;
                }
            //If coborrower ID is passed, check that they're present in our response data
            } elseif ($id === 'c') {
                if ($this->coborr_present === false) {
                    throw new \Exception(
                        'Coborrower not present in data. Unable to filter their liabilities.'
                    );
                } else {
                    $person_label = $this->coborr_label;
                }
            }
            //If we get this far, person ID is valid and present, obtain liability data
            $liability_labels = [];
            /* Grab all RELATIONSHIP nodes where CREDIT_LIABILITY_IsAssociatedWith_ROLE shows association with
            the person label */
            $relationship_nodes = $this->xpath->evaluate(
                '//P1:SERVICE/P1:RELATIONSHIPS/' .
                'P1:RELATIONSHIP[@P2:arcrole="urn:fdc:Meridianlink.com:2017:mortgage/' .
                'CREDIT_LIABILITY_IsAssociatedWith_ROLE"][@P2:to="' . $person_label . '"]/@P2:from'
            );
            /* Loop through all the RELATIONSHIP we identified earlier and extract the CREDIT_LIABILITY
            label and store them to array */
            for ($i = 0; $i < $relationship_nodes->length; $i++) {
                $liability_labels[] = $relationship_nodes->item($i)->textContent;
            }
            /* Find the CREDIT_LIABILITY node associated with each label in the array and run it
            through our parser */
            for ($i = 0; $i < count($liability_labels); $i++) {
                $liability_node = $this->xpath->evaluate(
                    '//P1:SERVICE/P1:CREDIT/P1:CREDIT_RESPONSE/P1:CREDIT_LIABILITIES/' .
                    'P1:CREDIT_LIABILITY[@P2:label="' . $liability_labels[$i] . '"]'
                )->item(0);
                //Add the associative array our parser returns to our array we'll be returning
                $return_array[] = $this->parseLiability($liability_node);
            }
        // if no person ID was provided, then we return all liabilities on file
        } else {
            //Select every CREDIT_LIABILTY node
            $liability_nodes = $this->xpath->evaluate(
                '//P1:SERVICE/P1:CREDIT/P1:CREDIT_RESPONSE/P1:CREDIT_LIABILITIES/P1:CREDIT_LIABILITY'
            );
            //Loop through each one and run it through our parser and add results to our array
            for ($i = 0; $i < $liability_nodes->length; $i++) {
                $return_array[] = $this->parseLiability($liability_nodes->item($i));
            }
        }
        return $return_array;
    }

    /**
     * This method converts rating codes to their text equivalent by searching the corresponding array
     *
     * @param string $code The rating code. Should be a single character.
     * @param boolean $suppress_invalid False by default, but if set to true, submitting an invalid rating
     * code will return an empty string instead of throwing an exception.
     * @return string
     * @throws \Exception If rating code provided is not a valid enumeration
     */
    public static function getRatingText(string $code, bool $suppress_invalid = false): string
    {
        $code = strtoupper(trim($code));
        
        //Loop through rating text array to find a match and return it
        foreach (self::RATING_TEXT as $key => $value) {
            if ($code === (string)$key) {
                return $value;
            }
        }

        /* If code makes it this far, then rating code provided isn't in our mapping table. Return exception
        if the suppress invalid flag isn't set to true */
        if ($suppress_invalid === true) {
            return '';
        } else {
            throw new \Exception('Rating Code provided not a valid enumeration.');
        }
    }

    /**
     * This helper function converts a CREDIT_LIABILITY DOMElement to an associative array. Depending on the
     * type of liability, some of the data points will return an empty string to indicate nothing was provided
     * for it. This can often occur if the datapoint is not applicable for the liability. For example, an auto
     * loan would not have a CreditLiabilityCreditLimitAmount data point, so it will return an empty string.
     *
     * @param \DOMElement $node The CREDIT_LIABILITY node to be converted
     * @return array An associative array containing all the data points.
     */
    private function parseLiability(\DOMElement $liability): array
    {
        $data = [
            'FullName' => '',
            'CreditLiabilityAccountIdentifier' => '',
            'CreditLiabilityAccountType' => '',
            'CreditLiabilityUnpaidBalanceAmount' => '',
            'CreditLiabilityCurrentRatingCode' => '',
            'CreditLiabilityCurrentRatingType' => '',
            'CreditLiabilityMonthlyPaymentAmount' => '',
            'CreditLiabilityAccountOwnershipType' => '',
            'AccountRemarks' => '',
            'CreditBusinessType' => '',
            'DetailCreditBusinessType' => '',
            'CreditLoanType' => '',
            'CreditLiability30DaysLateCount' => '',
            'CreditLiability60DaysLateCount' => '',
            'CreditLiability90DaysLateCount' => '',
            'CreditLiabilityAccountOpenedDate' => '',
            'CreditLiabilityAccountClosedDate' => '',
            'CreditLiabilityAccountPaidDate' => '',
            'CreditLiabilityAccountReportedDate' => '',
            'CreditLiabilityAccountStatusType' => '',
            'CreditLiabilityCreditLimitAmount' => '',
            'CreditLiabilityHighBalanceAmount' => '',
            'CreditLiabilityHighestAdverseRatingDate' => '',
            'CreditLiabilityHighestAdverseRatingCode' => '',
            'CreditLiabilityHighestAdverseRatingType' => '',
            'CreditLiabilityLastActivityDate' => '',
            'CreditLiabilityMonthsRemainingCount' => '',
            'CreditLiabilityMonthsReviewedCount' => '',
            'CreditLiabilityPastDueAmount' => '',
            'CreditLiabilityPaymentPatternDataText' => '',
            'CreditLiabilityPaymentPatternStartDate' => '',
            'CreditLiabilityTermsDescription' => '',
            'CreditLiabilityTermsMonthsCount' => '',
            'CreditLiabilityTermsSourceType' => '',
            'CreditorAddressStreet' => '',
            'CreditorAddressCity' => '',
            'CreditorAddressState' => '',
            'CreditorAddressZip' => '',
            'ContactPointTelephoneValue' => ''
        ];

        $node = $liability->getElementsByTagName('CreditBusinessType')->item(0);
        if ($node !== null) {
            $data['CreditBusinessType'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('CreditLiability30DaysLateCount')->item(0);
        if ($node !== null) {
            $data['CreditLiability30DaysLateCount'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('CreditLiability60DaysLateCount')->item(0);
        if ($node !== null) {
            $data['CreditLiability60DaysLateCount'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('CreditLiability90DaysLateCount')->item(0);
        if ($node !== null) {
            $data['CreditLiability90DaysLateCount'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('CreditLiabilityAccountClosedDate')->item(0);
        if ($node !== null) {
            $data['CreditLiabilityAccountClosedDate'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('CreditLiabilityAccountIdentifier')->item(0);
        if ($node !== null) {
            $data['CreditLiabilityAccountIdentifier'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('CreditLiabilityAccountOpenedDate')->item(0);
        if ($node !== null) {
            $data['CreditLiabilityAccountOpenedDate'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('CreditLiabilityAccountOwnershipType')->item(0);
        if ($node !== null) {
            $data['CreditLiabilityAccountOwnershipType'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('CreditLiabilityAccountPaidDate')->item(0);
        if ($node !== null) {
            $data['CreditLiabilityAccountPaidDate'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('CreditLiabilityAccountReportedDate')->item(0);
        if ($node !== null) {
            $data['CreditLiabilityAccountReportedDate'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('CreditLiabilityAccountStatusType')->item(0);
        if ($node !== null) {
            $data['CreditLiabilityAccountStatusType'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('CreditLiabilityAccountType')->item(0);
        if ($node !== null) {
            $data['CreditLiabilityAccountType'] = $node->textContent;
        }

        /* The credit comment with source of CreditBureau is the one that combines all the comments that
        each individual bureau may have returned, so we want to use this one */
        $node = $this->xpath->evaluate(
            'P1:CREDIT_COMMENTS/P1:CREDIT_COMMENT[P1:CreditCommentSourceType[text()="CreditBureau"]]/' .
            'P1:CreditCommentText',
            $liability
        )->item(0);
        if ($node !== null) {
            $data['AccountRemarks'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('CreditLiabilityCreditLimitAmount')->item(0);
        if ($node !== null) {
            $data['CreditLiabilityCreditLimitAmount'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('CreditLiabilityCurrentRatingCode')->item(0);
        if ($node !== null) {
            $data['CreditLiabilityCurrentRatingCode'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('CreditLiabilityCurrentRatingType')->item(0);
        if ($node !== null) {
            $data['CreditLiabilityCurrentRatingType'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('CreditLiabilityHighBalanceAmount')->item(0);
        if ($node !== null) {
            $data['CreditLiabilityHighBalanceAmount'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('CreditLiabilityHighestAdverseRatingCode')->item(0);
        if ($node !== null) {
            $data['CreditLiabilityHighestAdverseRatingCode'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('CreditLiabilityHighestAdverseRatingDate')->item(0);
        if ($node !== null) {
            $data['CreditLiabilityHighestAdverseRatingDate'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('CreditLiabilityHighestAdverseRatingType')->item(0);
        if ($node !== null) {
            $data['CreditLiabilityHighestAdverseRatingType'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('CreditLiabilityLastActivityDate')->item(0);
        if ($node !== null) {
            $data['CreditLiabilityLastActivityDate'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('CreditLiabilityMonthlyPaymentAmount')->item(0);
        if ($node !== null) {
            $data['CreditLiabilityMonthlyPaymentAmount'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('CreditLiabilityMonthsRemainingCount')->item(0);
        if ($node !== null) {
            $data['CreditLiabilityMonthsRemainingCount'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('CreditLiabilityMonthsReviewedCount')->item(0);
        if ($node !== null) {
            $data['CreditLiabilityMonthsReviewedCount'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('CreditLiabilityPastDueAmount')->item(0);
        if ($node !== null) {
            $data['CreditLiabilityPastDueAmount'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('CreditLiabilityPaymentPatternDataText')->item(0);
        if ($node !== null) {
            $data['CreditLiabilityPaymentPatternDataText'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('CreditLiabilityPaymentPatternStartDate')->item(0);
        if ($node !== null) {
            $data['CreditLiabilityPaymentPatternStartDate'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('CreditLiabilityTermsDescription')->item(0);
        if ($node !== null) {
            $data['CreditLiabilityTermsDescription'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('CreditLiabilityTermsMonthsCount')->item(0);
        if ($node !== null) {
            $data['CreditLiabilityTermsMonthsCount'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('CreditLiabilityTermsSourceType')->item(0);
        if ($node !== null) {
            $data['CreditLiabilityTermsSourceType'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('CreditLiabilityUnpaidBalanceAmount')->item(0);
        if ($node !== null) {
            $data['CreditLiabilityUnpaidBalanceAmount'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('CreditLoanType')->item(0);
        if ($node !== null) {
            $data['CreditLoanType'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('CityName')->item(0);
        if ($node !== null) {
            $data['CreditorAddressCity'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('StateCode')->item(0);
        if ($node !== null) {
            $data['CreditorAddressState'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('AddressLineText')->item(0);
        if ($node !== null) {
            $data['CreditorAddressStreet'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('PostalCode')->item(0);
        if ($node !== null) {
            $data['CreditorAddressZip'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('FullName')->item(0);
        if ($node !== null) {
            $data['FullName'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('ContactPointTelephoneValue')->item(0);
        if ($node !== null) {
            $data['ContactPointTelephoneValue'] = $node->textContent;
        }

        $node = $liability->getElementsByTagName('DetailCreditBusinessType')->item(0);
        if ($node !== null) {
            $data['DetailCreditBusinessType'] = $node->textContent;
        }
        return $data;
    }

    /**
     * This will extract all the CREDIT_FILE labels for the corresponding person and load
     * it to the corresponding pre-defined array properties. This essentially bookmarks these nodes for quick
     * access for use with other functions that pull data from these nodes. SmartAPI will also return an
     * infile for manually edited data, this is almost never used, so we'll ignore it, since its presence
     * will likely cause confusion for users.
     *
     * @param string $id The ID of the person we want to load the labels for
     * @return void
     * @throws \Exception If borrower data is being requested, but they are not present in the response file.
     * @throws \Exception If coborrower data is being requested, but they are not present in the response.
     */
    private function loadCreditFileLabels(string $id): void
    {
        $this->checkPersonID($id);
        $person_label = '';
        $temp_file_labels = [];
        $credit_file_labels = [];
        //If the ID provided belongs to the borrower, then grab their label to use with the search
        if ($id === $this->borr_id) {
            if ($this->borr_present === false) {
                throw new \Exception('Borrower is not present on file. Unable to parse corresponding ' .
                'CREDIT_FILE labels');
            } else {
                $person_label = $this->borr_label;
            }
        //If the ID provided belongs to the coborrower, then grab their label to use with the search
        } elseif ($id === $this->coborr_id) {
            if ($this->coborr_present === false) {
                throw new \Exception('Borrower is not present on file. Unable to parse corresponding ' .
                'CREDIT_FILE labels');
            } else {
                $person_label = $this->coborr_label;
            }
        }
        /* We search for all RELATIONSHIP elements of CREDIT_FILE_IsAssociatedWith_ROLE attribute linked to
        the label we've identified */
        $node = $this->xpath->evaluate(
            '/P1:MESSAGE/P1:DEAL_SETS/P1:DEAL_SET/P1:DEALS/P1:DEAL/P1:SERVICES/P1:SERVICE/P1:RELATIONSHIPS/' .
            'P1:RELATIONSHIP[@P2:arcrole="urn:fdc:Meridianlink.com:2017:mortgage/' .
            'CREDIT_FILE_IsAssociatedWith_ROLE"][@P2:to="' . $person_label . '"]/@P2:from'
        );
        /* We loop through each RELATIONSHIP element and extract the CREDIT_FILE label. This essentially
        identifies all CREDIT_FILE containers that are associated with the person */
        for ($i = 0; $i < $node->length; $i++) {
            $temp_file_labels[] = $node->item($i)->textContent;
        }
        /* We do one final loop of the CREDIT_FILE labels array and only keep the ones that were reported
        by the bureaus. As mentioned in the method description, we don't want to keep manual ones */
        for ($i = 0; $i < count($temp_file_labels); $i++) {
            $text = $this->xpath->evaluate(
                '/P1:MESSAGE/P1:DEAL_SETS/P1:DEAL_SET/P1:DEALS/P1:DEAL/P1:SERVICES/P1:SERVICE/P1:CREDIT/' .
                'P1:CREDIT_RESPONSE/P1:CREDIT_FILES/P1:CREDIT_FILE[@P2:label="' . $temp_file_labels[$i] .
                '"]/P1:CREDIT_FILE_DETAIL/P1:CreditRepositorySourceType'
            )->item(0)->textContent;
            if (
                $text === 'Equifax' ||
                $text === 'Experian' ||
                $text === 'TransUnion'
            ) {
                $credit_file_labels[] = $temp_file_labels[$i];
            }
        }
        //Save the final array to the borrower or coborrower's pre-defined property
        if ($id === $this->borr_id) {
            $this->borr_credit_file_labels = $credit_file_labels;
        } elseif ($id === $this->coborr_id) {
            $this->coborr_credit_file_labels = $credit_file_labels;
        }
    }

    /**
     * For functions where a person ID can be passed in to filter returned data, this checks to see
     * if that value is valid
     *
     * @param string $id The ID of the person for which data is sought
     * @return void
     * @throws \Exception If the person ID passed in doesn't match any applicant
     */
    private function checkPersonID(string $id): void
    {
        if ($id !== $this->borr_id && $id !== $this->coborr_id) {
            throw new \Exception('Person ID is invalid, must be any of the following ' .
                $this->borr_id . ',' . $this->coborr_id);
        }
    }
}
