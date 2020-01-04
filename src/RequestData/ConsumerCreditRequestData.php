<?php

/**
 * @package SmartAPI Helper
 * @author David Tran <hunterr83@gmail.com>
 */

namespace jafrajarvy292\SmartAPIHelper\RequestData;

use jafrajarvy292\SmartAPIHelper\Ancillary\AddressBlock;
use jafrajarvy292\SmartAPIHelper\Ancillary\PersonNameBlock;
use jafrajarvy292\SmartAPIHelper\Ancillary\PhoneNumberBlock;
use jafrajarvy292\SmartAPIHelper\Ancillary\CreditCardBlock;
use jafrajarvy292\SmartAPIHelper\Ancillary\ResponseFormats;
use jafrajarvy292\SmartAPIHelper\RequestGenerator\ConsumerCreditRequestGenerator;

/**
 * Stores all the information needed to submit various type of requests for consumer credit and also generate
 * the corresponding XML request for them.
 */
class ConsumerCreditRequestData
{
    /** Borrower's info */
    /** @var string Value used to reference the borrower when setting object properties */
    private $borr_id = 'b';
    /** @var PersonNameBlock|null Full name of the borrower */
    private $borr_name;
    /** @var string Social security number in ######### format */
    private $borr_ssn = '';
    /** @var AddressBlock|null Borrower's current residential address */
    private $borr_curr_address;
    /** @var string Borrower's DOB in YYYY-MM-DD format */
    private $borr_dob = '';
    /** @var AddressBlock|null Borrower's prior residential address */
    private $borr_prev_address;
    /** @var AddressBlock|null Borrower's mailing address, if different from current residence */
    private $borr_mail_address;
    /** @var PhoneNumberBlock|null Borrower's phone number in ########## format */
    private $borr_phone;
    /** @var string Borrower's email address */
    private $borr_email = '';
    
    /** Coborrower's info */
    /** @var string Value used to reference the coborrower when setting object properties */
    private $coborr_id = 'c';
    /** @var PersonNameBlock|null Full name of the coborrower */
    private $coborr_name;
    /** @var string Social security number in ######### format */
    private $coborr_ssn = '';
    /** @var AddressBlock|null Coborrower's current residential address */
    private $coborr_curr_address;
    /** @var string Coborrower's DOB in YYYY-MM-DD format */
    private $coborr_dob = '';
    /** @var AddressBlock|null Coborrower's prior residential address */
    private $coborr_prev_address;
    /** @var AddressBlock|null Coborrower's mailing address, if different from current residence */
    private $coborr_mail_address;
    /** @var PhoneNumberBlock|null Coborrower's phone number in ########## format */
    private $coborr_phone;
    /** @var string Coborrower's email address */
    private $coborr_email = '';
    
    /** Miscellaneous loan info */
    /** @var AddressBlock|null Address of the property being sold. */
    private $subject_property;
    /** @var string The loan reference ID, typically assigned by the broker */
    private $loan_identifier = '';
    /** @var string The type of loan for which the credit inquiry is being made */
    private $loan_type = '';

    /** Options for credit report order */
    /** @var CreditCardBlock|null Credit card info to be used if opting to pay at time of new order */
    private $credit_card;
    /** @var array Equifax ordering options
     * - credit: Set true to order credit data
     * - score: Set true to order score add-on. Only ordered if corresponding credit data is also ordered.
     */
    private $equifax_options = [
        'credit' => true,
        'score' => true];
    /** @var array Experian ordering options
     * - credit: Set true to order credit data
     * - score: Set true to order score add-on. Only ordered if corresponding credit data is also ordered.
     * - fraud: Set true to order fraud add-on. Only ordered if corresponding credit data is also ordered.
     */
    private $experian_options = [
        'credit' => true,
        'score' => true,
        'fraud' => true];
    /** @var array TransUnion ordering options
     * - credit: Set true to order credit data
     * - score: Set true to order score add-on. Only ordered if corresponding credit data is also ordered.
     * - fraud: Set true to order fraud add-on. Only ordered if corresponding credit data is also ordered.
     */
    private $transunion_options = [
        'credit' => true,
        'score' => true,
        'fraud' => true];
    /** @var ResponseFormats Holds the preferred response formats indicated by user */
    private $response_formats;
    /** @var string Indicate action being requested */
    private $request_type = '';
    /** @var string If querying an existing report, indicate the vendor-assigned ID here */
    private $vendor_order_id = '';
    /** @var array The types of requests that can be submitted. This will determine the final XML document
     * that is generated.
     * - Submit: Order a new credit report.
     * - StatusQuery: Retrieve an existing credit report or query for its status.
     * - Upgrade: Upgrade an existing credit report to add a spouse or additional credit bureaus.
     * - Refresh: Order a "refresh" of an existing credit report.
     * - PermUnmerge: Unmerge a borrower and/or bureau(s) from an existing file. The borrower and/or bureau(s)
     * that the user wants to keep should be included in the XML request. This unmerged data will be stored to
     * a new file with its own VendorOrderIdentifier. The original file is not modified in any way.
     */
    public const REQUEST_TYPES = ['Submit', 'StatusQuery', 'Upgrade', 'Refresh', 'PermUnmerge'];
    
    /** Administrative Variables */
    /** @var string Signals to the vendor the API version under which this request should be processed. */
    private $data_version = '201703';

    /**
     * Constructor function will initalize any necessary properties
     */
    public function __construct()
    {
        /* Initalize the response formats property. While the formats themselves can all set to false to
        indicate no formats are wanted, the property itself must not be null. As we build out the XML
        request file, we determine if any formats are wanted by by seeing the count of those set to true.
        If this property is null, it will break our 'if' statements */
        $this->response_formats = new ResponseFormats();
    }

    /**
     * Used to verify that the person ID passed in corresponds to either of the borrowers
     *
     * @param string $id This should match $this->borr_id or $this->coborr_id
     * @return void
     */
    private function checkPersonID(string $id): void
    {
        if ($id !== $this->borr_id && $id !== $this->coborr_id) {
            throw new \Exception("Person ID must be of the following: $this->borr_id,$this->coborr_id");
        }
    }

    /**
     * Set the full name for a person
     *
     * @param string $person ID used to reference which person this is for
     * @param PersonNameBlock|null $name The person's full name
     * @return void
     */
    public function setName(string $person_id, PersonNameBlock $name = null): void
    {
        $this->checkPersonID($person_id);
        if ($person_id === $this->borr_id) {
            $this->borr_name = $name;
        } elseif ($person_id === $this->coborr_id) {
            $this->coborr_name = $name;
        }
    }

    /**
     * Set the SSN value for a person.
     *
     * @param string $person_id ID used to reference which person this is for.
     * @param string $ssn SSN for the person, should be a string of 9 digits, no special chars
     * @return void
     */
    public function setSSN(string $person_id, string $ssn): void
    {
        //Check that person ID is valid
        $this->checkPersonID($person_id);
        $ssn = trim($ssn);
        if ($ssn === null || $ssn === '') {
            $ssn = '';
        //If SSN is not blank, then validate it
        } else {
            $is_valid = preg_match('/^\d{9}$/', $ssn);
            if ($is_valid !== 1) {
                throw new \Exception('SSN invalid, must be of format #########.');
            }
        }
        //If we get this far, then SSN wasn't blank and is valid, save it
        if ($person_id === $this->borr_id) {
            $this->borr_ssn = $ssn;
        } elseif ($person_id === $this->coborr_id) {
            $this->coborr_ssn = $ssn;
        }
    }

    /**
     * Provide the full address for a specific address entry
     *
     * @param string $person_id ID used to reference which person this is for.
     * @param AddressBlock|null $address A full address
     * @param string $type The address type: Current, Prior, or Mailing
     * @return void
     */
    public function setAddress(string $person_id, AddressBlock $address = null, string $type = 'Current'): void
    {
        //Check that person ID is valid
        $this->checkPersonID($person_id);
        //Prep the address type provided for comparison
        $type = trim($type);
        $type = ucfirst(strtolower($type));
        //Save address based on type indicated
        switch ($type) {
            case 'Current':
                if ($person_id === $this->borr_id) {
                    $this->borr_curr_address = $address;
                } elseif ($person_id === $this->coborr_id) {
                    $this->coborr_curr_address = $address;
                }
                break;
            case 'Prior':
                if ($person_id === $this->borr_id) {
                    $this->borr_prev_address = $address;
                } elseif ($person_id === $this->coborr_id) {
                    $this->coborr_prev_address = $address;
                }
                break;
            case 'Mailing':
                if ($person_id === $this->borr_id) {
                    $this->borr_mail_address = $address;
                } elseif ($person_id === $this->coborr_id) {
                    $this->coborr_mail_address = $address;
                }
                break;
            default:
                throw new \Exception('Address type must be of following: Current,Prior,Mailing');
        }
    }

    /**
     * Set the date of birth for a person
     *
     * @param string $person_id ID used to reference which person this is for.
     * @param string $dob DOB of the person in YYYY-MM-DD or MM-DD-YYYY format. Will ultimately be stored in
     * YYYY-MM-DD format.
     * @return void
     */
    public function setDOB(string $person_id, string $dob): void
    {
        //Check that person ID is valid
        $this->checkPersonID($person_id);
        $dob = trim($dob);
        //Array to handle one of the regex searches
        $matches = [];
        //Check that DOB is in a valid format
        switch ($dob) {
            case (null || ''):
                $dob = '';
                break;
            //If format is MM-DD-YYYY, then convert it to YYYY-MM-DD
            case (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $dob, $matches) === 1):
                $dob = "{$matches[3]}-{$matches[1]}-{$matches[2]}";
                break;
            //If format is YYYY-MM-DD, then do nothing, as it's already in the preferred format
            case (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dob) === 1):
                break;
            //If format doesn't match any valid format, throw exception
            default:
                throw new \Exception('DOB must be of the following formats: YYYY-MM-DD,MM-DD-YYYY');
        }
        //Save the DOB
        if ($person_id === $this->borr_id) {
            $this->borr_dob = $dob;
        } elseif ($person_id === $this->coborr_id) {
            $this->coborr_dob = $dob;
        }
    }

    /**
     * Set Phone Number
     *
     * @param string $person_id ID used to reference which person this is for
     * @param PhoneNumberBlock|null $phone Phone number object
     * @return void
     */
    public function setPhone(string $person_id, PhoneNumberBlock $phone = null): void
    {
        //Check that person ID is valid
        $this->checkPersonID($person_id);
        if ($person_id === $this->borr_id) {
            $this->borr_phone = $phone;
        } elseif ($person_id === $this->coborr_id) {
            $this->coborr_phone = $phone;
        }
    }

    /**
     * Set email address
     *
     * @param string $person_id ID used to reference which person this is for
     * @param string $email A single email address associated with the person.
     * @return void
     */
    public function setEmail(string $person_id, string $email): void
    {
        //Check that person ID is valid
        $this->checkPersonID($person_id);
        $email = trim($email);
        if ($email === null || $email === '') {
            $email = '';
        //We apply a very basic regex check to prevent invalid email or more than one email.
        } elseif (preg_match('/(@.*){2}|[; ,]/', $email) === 1) {
            throw new \Exception('Email is invalid or more than one email was provided.');
        }
        //If we make it this far, then email is valid, save it
        if ($person_id === $this->borr_id) {
            $this->borr_email = $email;
        } elseif ($person_id === $this->coborr_id) {
            $this->coborr_email = $email;
        }
    }

    /**
     * Set the subject property address; the property involved in the transaction.
     *
     * @param AddressBlock|null $address The address block of the property
     * @return void
     */
    public function setSubjectPropAdd(AddressBlock $address = null): void
    {
        $this->subject_property = $address;
    }

    /**
     * Set the loan identifier value--usually assigned by the broker to help track the loan.
     *
     * @param string $id The loan ID
     * @return void
     */
    public function setLoanID(string $id): void
    {
        $id = trim($id);
        if ($id === null || $id === '') {
            $this->loan_identifier = '';
        } else {
            $this->loan_identifier = $id;
        }
    }

    /**
     * Set the loan type. This is a pre-defined enumeration configured by the service provider.
     *
     * @param string $type The loan type
     * @return void
     */
    public function setLoanType(string $type): void
    {
        $type = trim($type);
        if ($type === null || $type === '') {
            $this->loan_type = '';
        } else {
            $this->loan_type = $type;
        }
    }

    /**
     * Set credit card to be charged if user wants to (or needs to) pay at the time of ordering.
     * To clear this valuee, simply call the method, but pass in nothing.
     *
     * @param CreditCardBlock|null $info Full credit card details.
     * @return void
     */
    public function setCreditCard(CreditCardBlock $info = null): void
    {
        $this->credit_card = $info;
    }

    /**
     * Set Equifax ordering options
     *
     * @param boolean $credit Indicate whether credit data will be ordered.
     * @param boolean $score If credit data is ordered, indicate whether to include score add-on
     * @return void
     */
    public function setEquifaxOptions(bool $credit, bool $score = true): void
    {
        $this->equifax_options['credit'] = $credit;
        $this->equifax_options['score'] = $score;
    }

    /**
     * Set Experian ordering options
     *
     * @param boolean $credit Indicate whether credit data will be ordered.
     * @param boolean $score If credit data is ordered, indicate whether to include score add-on
     * @param boolean $fraud If credit data is ordered, indicate whether to include fraud add-on
     * @return void
     */
    public function setExperianOptions(bool $credit, bool $score = true, bool $fraud = true): void
    {
        $this->experian_options['credit'] = $credit;
        $this->experian_options['score'] = $score;
        $this->experian_options['fraud'] = $fraud;
    }

    /**
     * Set TransUnion ordering options
     *
     * @param boolean $credit Indicate whether credit data will be ordered.
     * @param boolean $score If credit data is ordered, indicate whether to include score add-on
     * @param boolean $fraud If credit data is ordered, indicate whether to include fraud add-on
     * @return void
     */
    public function setTransUnionOptions(bool $credit, bool $score = true, bool $fraud = true): void
    {
        $this->transunion_options['credit'] = $credit;
        $this->transunion_options['score'] = $score;
        $this->transunion_options['fraud'] = $fraud;
    }

    /**
     * Set the request type
     *
     * @param string $request This should match a pre-defined enumeration
     * @return void
     */
    public function setRequestType(string $request): void
    {
        /* Check to see if $request being set matches something from our enumeration. Initial comparison
        compares in all lower case, but if match, then we set with the proper, case-sensitive value */
        $request = trim($request);
        for ($i = 0; $i < count(self::REQUEST_TYPES); $i++) {
            if (strtolower($request) === strtolower(self::REQUEST_TYPES[$i])) {
                $this->request_type = self::REQUEST_TYPES[$i];
                return;
            }
        }
        //If logic makes it this far, request type being set isn't a valid one.
        throw new \Exception('Request type must be of the following: ' .
            implode(',', self::REQUEST_TYPES));
    }

    /**
     * Set the preferred response formats
     *
     * @param ResponseFormats $formats The preferred formats object
     * @return void
     */
    public function setResponseFormats(ResponseFormats $formats): void
    {
        $this->response_formats = $formats;
    }

    /**
     * On requests involving an existing file, indicate that file number here
     *
     * @param string $id The file number.
     * @return void
     */
    public function setVendorOrderID(string $id): void
    {
        $id = trim($id);
        if ($id === null || $id === '') {
            $this->vendor_order_id = '';
        } else {
            $this->vendor_order_id = $id;
        }
    }

    /**
     * Set the API version under which this request should be processed
     *
     * @param string $data_version The API version
     * @return void
     */
    public function setDataVersion(string $data_version = '201703'): void
    {
        if ($data_version === null || $data_version === '') {
            throw new \Exception('Data Version ID cannot be blank.');
        } else {
            $this->data_version = $data_version;
        }
    }

    /**
     * Get the ID assigned to the borrower
     *
     * @return string
     */
    public function getBorrowerID(): string
    {
        return $this->borr_id;
    }

    /**
     * Get the ID assigned to the coborrower
     *
     * @return string
     */
    public function getCoborrowerID(): string
    {
        return $this->coborr_id;
    }

    /**
     * Return the data version ID
     *
     * @return string
     */
    public function getDataVersion(): string
    {
        return $this->data_version;
    }

    /**
     * Returns the subject property address block
     *
     * @return AddressBlock|null
     */
    public function getSubjectPropAdd(): ?AddressBlock
    {
        return $this->subject_property;
    }

    /**
     * Return the person's name block
     *
     * @param string $person_id ID for the person
     * @return PersonNameBlock|null
     */
    public function getName(string $person_id): ?PersonNameBlock
    {
        $this->checkPersonID($person_id);
        if ($person_id === $this->borr_id) {
            return $this->borr_name;
        } elseif ($person_id === $this->coborr_id) {
            return $this->coborr_name;
        }
    }

    /**
     * Return the person's SSN
     *
     * @param string $person_id ID for the person
     * @return string
     */
    public function getSSN(string $person_id): string
    {
        $this->checkPersonID($person_id);
        if ($person_id === $this->borr_id) {
            return $this->borr_ssn;
        } elseif ($person_id === $this->coborr_id) {
            return $this->coborr_ssn;
        }
    }

    /**
     * Return a person's address block
     *
     * @param string $person_id ID for the person
     * @param string $type The address type
     * @return AddressBlock|null
     */
    public function getAddress(string $person_id, string $type = 'Current'): ?AddressBlock
    {
        $this->checkPersonID($person_id);
        $type = ucfirst(strtolower($type));
        switch ($type) {
            case 'Current':
                if ($person_id === $this->borr_id) {
                    return $this->borr_curr_address;
                } elseif ($person_id === $this->coborr_id) {
                    return $this->coborr_curr_address;
                }
                break;
            case 'Prior':
                if ($person_id === $this->borr_id) {
                    return $this->borr_prev_address;
                } elseif ($person_id === $this->coborr_id) {
                    return $this->coborr_prev_address;
                }
                break;
            case 'Mailing':
                if ($person_id === $this->borr_id) {
                    return $this->borr_mail_address;
                } elseif ($person_id === $this->coborr_id) {
                    return $this->coborr_mail_address;
                }
                break;
            default:
                throw new \Exception('Address type must be of following: Current,Prior,Mailing');
        }
    }

    /**
     * Return the DOB of the person in YYYY-MM-DD format
     *
     * @param string $person_id ID of the person
     * @return string The DOB string
     */
    public function getDOB(string $person_id): string
    {
        $this->checkPersonID($person_id);
        if ($person_id === $this->borr_id) {
            return $this->borr_dob;
        } elseif ($person_id === $this->coborr_id) {
            return $this->coborr_dob;
        }
    }

    /**
     * Return the person's phone number block
     *
     * @param string $person_id ID of the person
     * @return PhoneNumberBlock|null Phone number block for the corresponding person
     */
    public function getPhone(string $person_id): ?PhoneNumberBlock
    {
        $this->checkPersonID($person_id);
        if ($person_id === $this->borr_id) {
            return $this->borr_phone;
        } elseif ($person_id === $this->coborr_id) {
            return $this->coborr_phone;
        }
    }

    /**
     * Return the person's email address
     *
     * @param string $person_id ID of the person
     * @return string
     */
    public function getEmail(string $person_id): string
    {
        $this->checkPersonID($person_id);
        if ($person_id === $this->borr_id) {
            return $this->borr_email;
        } elseif ($person_id === $this->coborr_id) {
            return $this->coborr_email;
        }
    }

    /**
     * Return the loan identifier value
     *
     * @return string
     */
    public function getLoanID(): string
    {
        return $this->loan_identifier;
    }

    /**
     * Return the loan type
     *
     * @return string
     */
    public function getLoanType(): string
    {
        return $this->loan_type;
    }

    /**
     * Return the credit card data block
     *
     * @return CreditCardBlock|null
     */
    public function getCreditCard(): ?CreditCardBlock
    {
        return $this->credit_card;
    }

    /**
     * Return Equifax ordering options
     *
     * @return array Associative array of options and corresponding bool flags
     */
    public function getEquifaxOptions(): array
    {
        return $this->equifax_options;
    }

    /**
     * Return Experian ordering options
     *
     * @return array Associative array of options and corresponding bool flags
     */
    public function getExperianOptions(): array
    {
        return $this->experian_options;
    }

    /**
     * Return TransUnion ordering options
     *
     * @return array Associative array of options and corresponding bool flags
     */
    public function getTransUnionOptions(): array
    {
        return $this->transunion_options;
    }

    /**
     * Return the configured request type
     *
     * @return string
     */
    public function getRequestType(): string
    {
        return $this->request_type;
    }

    /**
     * Return the indicated preferred response formats
     *
     * @return ResponseFormats Object containing the preferred response formats indicated
     */
    public function getResponseFormats(): ResponseFormats
    {
        return $this->response_formats;
    }

    /**
     * Return the vendor order identifier involved with the transaction
     *
     * @return string
     */
    public function getVendorOrderID(): string
    {
        return $this->vendor_order_id;
    }

    /**
     * Generates the XML request string that will be submitted to the service API
     *
     * @return string
     */
    public function getXMLString(): string
    {
        $xml_request = new ConsumerCreditRequestGenerator($this);
        $xml_request_string = $xml_request->outputXMLString();
        return $xml_request_string;
    }
}
