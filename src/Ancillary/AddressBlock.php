<?php

/**
 * @author David Tran <hunterr83@gmail.com>
 */

namespace jafrajarvy292\SmartAPIHelper\Ancillary;

/**
 * Holds a standard address block
 */
class AddressBlock
{
    /** @var array Array of valid state abbreviations that are allowed */
    public const VALID_STATES = [
        'AA' => 'ARMED FORCES',
        'AB' => 'ALBERTA',
        'AE' => 'ARMED FORCES',
        'AK' => 'ALASKA',
        'AL' => 'ALABAMA',
        'AP' => 'ARMED FORCES',
        'AR' => 'ARKANSAS',
        'AS' => 'AMERICAN SAMOA',
        'AZ' => 'ARIZONA',
        'BC' => 'BRITISH COLUMBIA',
        'CA' => 'CALIFORNIA',
        'CO' => 'COLORADO',
        'CT' => 'CONNECTICUT',
        'DC' => 'DISTRICT OF COLUMBIA',
        'DE' => 'DELEWARE',
        'FL' => 'FLORIDA',
        'FM' => 'FED STATES MICRONESIA',
        'GA' => 'GEORGIA',
        'GU' => 'GUAM',
        'HI' => 'HAWAII',
        'IA' => 'IOWA',
        'ID' => 'IDAHO',
        'IL' => 'ILLINOIS',
        'IN' => 'INDIANA',
        'KS' => 'KANSAS',
        'KY' => 'KENTUCKY',
        'LA' => 'LOUISIANA',
        'MA' => 'MASSACHUSETTS',
        'MB' => 'MANITOBA',
        'MD' => 'MARYLAND',
        'ME' => 'MAINE',
        'MH' => 'MARSHALL ISLANDS',
        'MI' => 'MICHIGAN',
        'MN' => 'MINNESOTA',
        'MO' => 'MISSOURI',
        'MP' => 'NOR MARIANA ISLANDS',
        'MS' => 'MISSISSIPPI',
        'MT' => 'MONTANA',
        'NB' => 'NEW BRUNSWICK',
        'NC' => 'NORTH CAROLINA',
        'ND' => 'NORTH DAKOTA',
        'NE' => 'NEBRASKA',
        'NH' => 'NEW HAMPSHIRE',
        'NJ' => 'NEW JERSEY',
        'NL' => 'NEWFOUNDLAND AND LABRADOR',
        'NM' => 'NEW MEXICO',
        'NS' => 'NOVA SCOTIA',
        'NV' => 'NEVADA',
        'NY' => 'NEW YORK',
        'OH' => 'OHIO',
        'OK' => 'OKLAHOMA',
        'ON' => 'ONTARIO',
        'OR' => 'OREGON',
        'PA' => 'PENNSYLVANIA',
        'PE' => 'PRINCE EDWARD ISLAND',
        'PR' => 'PUERTO RICO',
        'PW' => 'PALAU',
        'QC' => 'QUEBEC',
        'RI' => 'RHODE ISLAND',
        'SC' => 'SOUTH CAROLINA',
        'SD' => 'SOUTH DAKOTA',
        'SK' => 'SASKATCHEWAN',
        'TN' => 'TENNESSEE',
        'TX' => 'TEXAS',
        'UT' => 'UTAH',
        'VA' => 'VIRGINIA',
        'VI' => 'VIRGIN ISLANDS',
        'VT' => 'VERMONT',
        'WA' => 'WASHINGTON',
        'WI' => 'WISCONSIN',
        'WV' => 'WEST VIRGINIA',
        'WY' => 'WYOMING'];
    /** @var array Array of valid country abbreviations */
    public const VALID_COUNTRIES = ['US' => 'UNITED STATES', 'CA' => 'CANADA'];
    /** @var string Regex of a standard 5 digit US zip code */
    public const US_ZIP_REGEX = '/^\d{5}$/';
    /** @var string Regex of a standard 5 digit US zip+4 code with hyphen separator */
    public const US_ZIP4_REGEX = '/^\d{5}-\d{4}$/';
    /** @var string Regex of a standard 6 character Canadian postal code with optional space separator */
    public const CAN_ZIP_REGEX = '/^[a-zA-z]\d[a-zA-Z] ?\d[a-zA-Z]\d$/';
    /** @var string First line of the address: house number, street, etc. */
    private $street;
    /** @var string City name*/
    private $city;
    /** @var string State abbreviation */
    private $state;
    /** @var string US zip code or Canadian postal code*/
    private $zip;
    /** @var string Country abbreviation */
    private $country;

    /**
     * Constructor function. Requires full address to be passed in upon instantiation. Example below.
     * ```
     * $object = new AddressBlock('123 Main St.','Santa Ana','CA','92626','US');
     * ```
     * @param string $street Full street address (e.g. 123 N Main St #389);
     * @param string $city
     * @param string $state 2-character abbreviation
     * @param string $zip
     * @param string $country 2-character abbreviation
     * @throws \Exception If street is empty or has invalid characters
     * @throws \Exception If city is empty
     * @throws \Exception If state abbreivation is empty or invalid
     * @throws \Exception If zip code is not of a valid format or empty
     * @throws \Exception If country code is empty or invalid
     */
    public function __construct(
        string $street,
        string $city,
        string $state,
        string $zip,
        string $country = 'US'
    ) {
        //Validate street prior to saving
        $street = trim($street);
        if ($street === null || $street === '') {
            throw new \Exception('Street cannot be empty');
        } else {
            if (!self::validateStreet($street)) {
                throw new \Exception('Street address not valid. Please remove any unusual characters.');
            } else {
                $this->street = $street;
            }
        }

        //Light validation on city prior to saving
        $city = trim($city);
        if ($city === null || $city === '') {
            throw new \Exception('City cannot be empty.');
        } else {
            $this->city = $city;
        }

        //Validate state prior to saving
        $state = trim($state);
        $state = strtoupper($state);
        if ($state === null || $state === '') {
            throw new \Exception('State abbreviation cannot be empty.');
        } else {
            if (!self::validateState($state)) {
                throw new \Exception('State not valid. Must be of the following: ' .
                    implode(',', array_keys(self::VALID_STATES)));
            } else {
                $this->state = $state;
            }
        }

        //Validate and sanitize zip code
        $zip = trim($zip);
        if ($zip === null || $zip === '') {
            throw new \Exception('Zip code cannot be empty.');
        } else {
            switch ($zip) {
                //If zip code is just 5 digits, then store that.
                case (preg_match(self::US_ZIP_REGEX, $zip) === 1):
                    $this->zip = $zip;
                    break;
                //If zip code includes the +4, save only the 5 digit portion
                case (preg_match(self::US_ZIP4_REGEX, $zip) === 1):
                    $match = [];
                    preg_match('/\d{5}/', $zip, $match);
                    $this->zip = $match[0];
                    break;
                //If zip code is Canadian, remove any space separator and save
                case (preg_match(self::CAN_ZIP_REGEX, $zip) === 1):
                    $this->zip = strtoupper(preg_replace('/ /', '', $zip));
                    break;
                default:
                    throw new \Exception('Zip code is not of a valid format.');
            }
        }

        //Validate country
        $country = trim($country);
        $country = strtoupper($country);
        if ($country === null || $country === '') {
            throw new \Exception('Country code cannot be empty.');
        } else {
            if (!self::validateCountry($country)) {
                throw new \Exception('Country code not valid. Must be of the following: ' .
                    implode(',', array_keys(self::VALID_COUNTRIES)));
            } else {
                $this->country = $country;
            }
        }
    }

    /**
     * Return the street portion of the address
     *
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * Return city portion of the address
     *
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * Return state abbreviation of the address
     *
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * Return zip code portion of the address
     *
     * @return string
     */
    public function getZip(): string
    {
        return $this->zip;
    }

    /**
     * Return country abbreviation of the address
     *
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }
    
    /**
     * Generates an address block and returns it as a node.
     *
     * This function will generate a full address block and return it as a DOM node, which can be
     * appended to an element using appendChild(). A sample of usage and the address block is below:
     * ```
     * $element->appendChild($address->getXML($base));
     *
     * <ADDRESS>
     *  <AddressLineText>123 Main St #2</AddressLineText>
     *  <CityName>Irvine</CityName>
     *  <CountryCode>US</CountryCode>
     *  <PostalCode>93842</PostalCode>
     *  <StateCode>CA</StateCode>
     * </ADDRESS>
     * ```
     *
     * @param \DOMDocument $base The DOMDocument to which we'll be adding this address block
     * @param string|null $namespace The namespace associated with the elements. Null refers to default
     * namespace
     * @return \DOMNode
     */
    public function getXML(\DOMDocument $base, string $namespace = null): \DOMNode
    {
        //If namespace value is not passed, then use the document's default namespace
        if ($namespace === null) {
            $namespace = $base->lookupNamespaceUri(null);
        }
        $parent = $base->createElementNS($namespace, 'ADDRESS');
        $parent->appendChild($base->createElementNS($namespace, 'AddressLineText'))->
            appendChild($base->createTextNode($this->street));
        $parent->appendChild($base->createElementNS($namespace, 'CityName'))->
            appendChild($base->createTextNode($this->city));
        $parent->appendChild($base->createElementNS($namespace, 'CountryCode'))->
            appendChild($base->createTextNode($this->country));
        $parent->appendChild($base->createElementNS($namespace, 'PostalCode'))->
            appendChild($base->createTextNode($this->zip));
        $parent->appendChild($base->createElementNS($namespace, 'StateCode'))->
            appendChild($base->createTextNode($this->state));
        return $parent;
    }

    /**
     * Validate the street address against characters that aren't typically present in an address
     *
     * @param string $street The full street address (i.e. 123 Main St #334)
     * @return bool Returns true if the street passes validation
     */
    public static function validateStreet(string $street): bool
    {
        //This regex returns 1 if any unacceptable chars are located in the street
        $is_valid = preg_match('/[^a-zA-Z0-9 \-\'\.#&\/]/', $street);
        //If regex returns a 1 or false, it means unacceptable chars were found or the search errored.
        if ($is_valid === 1 || $is_valid === false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Validate the state abbreviation against list of US states, territories, military bases,
     * and Canadian provinces.
     *
     * @param string $state 2-character state abbreviation
     * @return bool Returns true if the state abbreviation is found in our list
     */
    public static function validateState(string $state): bool
    {
        $is_valid = array_key_exists(strtoupper($state), self::VALID_STATES);
        if ($is_valid === false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Validates the zip code using the various regexes defined
     *
     * @param string $zip User-provided US zip or CA postal code
     * @return boolean Returns true if a valid US/CA code, else false
     */
    public static function validateZip(string $zip): bool
    {
        switch ($zip) {
            case (preg_match(self::US_ZIP_REGEX, $zip) === 1):
                return true;
                break;
            case (preg_match(self::US_ZIP4_REGEX, $zip) === 1):
                return true;
                break;
            case (preg_match(self::CAN_ZIP_REGEX, $zip) === 1):
                return true;
                break;
            default:
                return false;
        }
    }

    /**
     * Validate country abbreviation against list of acceptable countries
     *
     * @param string $country 2-char country abbreviation
     * @return bool Returns true if abbreviation is valid, else returns false
     */
    public static function validateCountry(string $country): bool
    {
        $is_valid = array_key_exists(strtoupper($country), self::VALID_COUNTRIES);
        if ($is_valid === false) {
            return false;
        } else {
            return true;
        }
    }
}
