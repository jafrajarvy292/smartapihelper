<?php

/**
 * This file is part of MeridianLink's SmartAPI Helper package.
 * 
 * For the full copyright and license information, please view the LICENSE file that was distributed with
 * this source code.
 */

namespace jafrajarvy292\SmartAPIHelper\Ancillary;

/**
 * This holds a standard phone number
 */
class PhoneNumberBlock
{
    /** @var array Valid values for phone type, as defined by XML schema */
    public const VALID_TYPES = ['Home', 'Mobile', 'Work', 'Other'];
    /** @var string The 10 digit phone number in format ########## */
    private $number = '';
    /** @var string An extension, if applicable */
    private $extension = '';
    /** @var string The phone number type (e.g. Home, Work, etc) */
    private $type = 'Home';
    /** @var string Description of the number if type is Other */
    private $description = '';
    /** @var string The regular expression for what is considered a valid 10 digit phone number. Will accept
     * the following or similar common formats:
     * - (888) 444-5555
     * - 4445556666
     * - 222 444 5555
     * - 222-444-5555
     */
    public const NUMBER_REGEX = '/^[^\d]?\d{3}[^\d]{0,2}\d{3}[^\d]?\d{4}$/';

    /**
     * PhoneNumberBlock values are passed at time of instantiation. Examples below:
     * ```
     * $phone = new PhoneNumberBlock('7143334444')
     * $phone = new PhoneNumberBlock('7143334444','3432,'Other','Front Desk')
     * ```
     *
     * @param string $number 10 digit phone number
     * @param string $extension Phone extension, if applicable
     * @param string $type Phone type, should coincide with enumerated list
     * @param string $description If phone type is Other, then briefly describe it
     * @throws \Exception If phone number is emtpy or of an invalid format
     * @throws \Exception If phone extension contains anything other than digits
     * @throws \Exception If phone type is not a valid enumeration
     */
    public function __construct(
        string $number,
        string $extension = '',
        string $type = 'Home',
        string $description = ''
    ) {
        //Validate phone number before saving
        $number = trim($number);
        if ($number === '') {
            throw new \Exception('Phone number cannot be empty.');
        } else {
            if (!self::validateNumber($number)) {
                throw new \Exception('Phone number must be exactly 10 digit number of the following formats: ' .
                    '##########,###-###-####,(###) ###-####, or similar.');
            } else {
                //If number is valid, remove anything that isn't a digit, then save it
                $number = preg_replace('/[^\d]/', '', $number);
                $this->number = $number;
            }
        }

        //Validate extension before saving
        $extension = trim($extension);
        if ($extension === null || $extension === '') {
            $this->extension = '';
        } else {
            if (!self::validateExt($extension)) {
                throw new \Exception('Extension must contain only numbers.');
            } else {
                $this->extension = $extension;
            }
        }
        
        //If type is valid, save it
        $type = trim($type);
        $type = ucfirst(strtolower($type));
        if (!self::validateType($type)) {
            throw new \Exception('Phone type must be of the following: ' .
                implode(',', self::VALID_TYPES));
        } else {
            $this->type = $type;
        }

        //Light cleanup work on the description field.
        $description = trim($description);
        if ($description === null || $description === '') {
            $this->description = '';
        } else {
            $this->description = $description;
        }
    }

    /**
     * Get the object's phone number
     *
     * @return string Returns just the phone number
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * Get the object's phone extension
     *
     * @return string Returns just the extension
     */
    public function getExt(): string
    {
        return $this->extension;
    }

    /**
     * Get the object's phone type value
     *
     * @return string Returns the phone number type
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Return the phone's type description
     *
     * @return string Returns the description
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Generates a person's phone block and returns it as a node
     *
     * This generates a phone block, sampled below, and returns it as a node, which can be appended under
     * another element using appendChild(). Example of usage and returned node below:
     * ```
     * $element->appendChild($phone->getXML($base));
     *
     * <CONTACT_POINT>
     *  <CONTACT_POINT_TELEPHONE>
     *   <ContactPointTelephoneExtensionValue>234</ContactPointTelephoneExtensionValue>
     *   <ContactPointTelephoneValue>7143337777</ContactPointTelephoneValue>
     *  </CONTACT_POINT_TELEPHONE>
     *  <CONTACT_POINT_DETAIL>
     *   <ContactPointRoleType>Other</ContactPointRoleType>
     *   <ContactPointRoleTypeOtherDescription>Moms Phone</ContactPointRoleTypeOtherDescription>
     *  </CONTACT_POINT_DETAIL>
     * </CONTACT_POINT>
     * ```
     *
     * @param \DOMDocument $base The DOM document we're adding this to
     * @param string|null $namespace The namespace this should be associated with. Null refers to default
     * namespace
     * @return \DOMNode
     */
    public function getXML(\DOMDocument $base, string $namespace = null): \DOMNode
    {
        //If namespace value is not passed, then use the document's default namespace
        if ($namespace === null) {
            $namespace = $base->lookupNamespaceUri(null);
        }
        //Create the NAME container and populate it
        $parent = $base->createElementNS($namespace, 'CONTACT_POINT');
        $contact_telephone = $parent->appendChild($base->createElementNS(
            $namespace,
            'CONTACT_POINT_TELEPHONE'
        ));
        if ($this->extension !== null && $this->extension !== '') {
            $contact_telephone->appendChild($base->createElementNS(
                $namespace,
                'ContactPointTelephoneExtensionValue'
            ))->appendChild($base->createTextNode($this->extension));
        }
        $contact_telephone->appendChild($base->createElementNS($namespace, 'ContactPointTelephoneValue'))->
                appendChild($base->createTextNode($this->number));
        $contact_detail = $parent->appendChild($base->createElementNS($namespace, 'CONTACT_POINT_DETAIL'));
        $contact_detail->appendChild($base->createElementNS($namespace, 'ContactPointRoleType'))->
            appendChild($base->createTextNode($this->type));
        if ($this->description !== null && $this->description !== '') {
            $contact_detail->appendChild($base->createElementNS(
                $namespace,
                'ContactPointRoleTypeOtherDescription'
            ))->appendChild($base->createTextNode($this->description));
        }
        return $parent;
    }

    /**
     * Validate the phone type against pre-defined enumeration
     *
     * @param string $type The user-provided phone type
     * @return boolean Returns true if phone type provided is valid, else false
     */
    public static function validateType(string $type): bool
    {
        $type = ucfirst(strtolower($type));
        $is_valid = array_search($type, self::VALID_TYPES);
        if ($is_valid === false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Validate the phone number against our pre-defined regex.
     *
     * @param string $number The user-provided number
     * @return boolean Returns true if phone number is valid, else false
     */
    public static function validateNumber(string $number): bool
    {
        //Regex returns 1 if string is exactly 10 digits.
        $is_valid = preg_match(self::NUMBER_REGEX, $number);
        if ($is_valid === 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Validate a phone's extension (i.e. makes sure it's just numbers)
     *
     * @param string $extension Extension value
     * @return boolean Returns true if ext contains only numbers, else false
     */
    public static function validateExt(string $extension): bool
    {
        $is_valid = preg_match('/^\d+$/', $extension);
        if ($is_valid === 1) {
            return true;
        } else {
            return false;
        }
    }
}
