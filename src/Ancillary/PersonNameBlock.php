<?php

/**
 * @author David Tran <hunterr83@gmail.com>
 */

namespace jafrajarvy292\SmartAPIHelper\Ancillary;

/**
 * This holds a person's full name
 */
class PersonNameBlock
{
    /** @var string $first First name */
    private $first;
    /** @var string Last name */
    private $last;
    /** @var string Middle name */
    private $middle;
    /** @var string Suffix from pre-defined enumeration */
    private $suffix;
    /** @var array List of valid suffixes */
    public const VALID_SUFFIXES = ['SR', 'JR', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX'];

    /**
     * A person's full name is required at time of instantiation. A few examples below:
     * ```
     * $person1_name = new PersonNameBlock('John','Jameson');
     * $person1_name = new PersonNameBlock('Jack','Robinson','R');
     * $person1_name = new PersonNameBlock('Jack','Robinson','','SR');
     * ```
     *
     * @param string $first
     * @param string $last
     * @param string $middle
     * @param string $suffix
     * @throws \Exception If first name is empty or invalid
     * @throws \Exception If last name is empty or invalid
     * @throws \Exception If middle name is not valid
     * @throws \Exception If suffix is not a valid enumeration
     */
    public function __construct(
        string $first,
        string $last,
        string $middle = '',
        string $suffix = ''
    ) {
        //Validate first name prior to saving
        $first = trim($first);
        if ($first === '') {
            throw new \Exception('First name cannot be empty.');
        } elseif (!self::validateName($first)) {
            throw new \Exception('First name not valid. Please remove any unusual characters.');
        } else {
            $this->first = $first;
        }

        //Validate last name prior to saving
        $last = trim($last);
        if ($last === '') {
            throw new \Exception('Last name cannot be empty.');
        } elseif (!self::validateName($last)) {
            throw new \Exception('Last name not valid. Please remove any unusual characters.');
        } else {
            $this->last = $last;
        }
        
        //Validate middle name prior to saving
        $middle = trim($middle);
        if ($middle === null || $middle === '') {
            $this->middle = '';
        } else {
            if (!self::validateName($middle)) {
                throw new \Exception('Middle name not valid. Please remove any unusual characters.');
            } else {
                $this->middle = $middle;
            }
        }

        //Validate suffix prior to saving
        $suffix = trim($suffix);
        if ($suffix === null || $suffix === '') {
            $this->suffix = '';
        } else {
            if (!self::validateSuffix($suffix)) {
                throw new \Exception('Suffix not valid. Must be of the following: ' .
                    implode(',', self::VALID_SUFFIXES));
            } else {
                $this->suffix = strtoupper($suffix);
            }
        }
    }

    /**
     * Return first name
     *
     * @return string
     */
    public function getFirst(): string
    {
        return $this->first;
    }

    /**
     * Return middle name
     *
     * @return string
     */
    public function getMiddle(): string
    {
        return $this->middle;
    }

    /**
     * Return last name
     *
     * @return string
     */
    public function getLast(): string
    {
        return $this->last;
    }

    /**
     * Return suffix
     *
     * @return string
     */
    public function getSuffix(): string
    {
        return $this->suffix;
    }

    /**
     * Generates a person's name block and returns it as a node
     *
     * This generates a name block, sampled below, and returns it as a node, which can be appended under
     * another element using appendChild(). An example of usage and the returning node is below:
     * ```
     * $element->appendChild($name->getXML($base));
     *
     * <NAME>
     *  <FirstName>Davie</FirstName>
     *  <LastName>Testcase</LastName>
     *  <MiddleName>R</MiddleName>
     *  <SuffixName>JR</SuffixName>
     * </NAME>
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
        $parent = $base->createElementNS($namespace, 'NAME');
        $parent->appendChild($base->createElementNS($namespace, 'FirstName'))->
            appendChild($base->createTextNode($this->first));
        $parent->appendChild($base->createElementNS($namespace, 'LastName'))->
            appendChild($base->createTextNode($this->last));
        if ($this->middle !== null && $this->middle !== '') {
            $parent->appendChild($base->createElementNS($namespace, 'MiddleName'))->
                appendChild($base->createTextNode($this->middle));
        }
        if ($this->suffix !== null && $this->suffix !== '') {
            $parent->appendChild($base->createElementNS($namespace, 'SuffixName'))->
                appendChild($base->createTextNode($this->suffix));
        }
        return $parent;
    }

    /**
     * Validate a person's name by searching for any unusual characters
     * This validates a single name field, not the entire name.
     *
     * @param string $name Name to be validated
     * @return bool Returns true if name is valid, else false.
     */
    public static function validateName(string $name): bool
    {
        //Regex returns 1 if it finds any unacceptable characters
        $is_valid = preg_match('/[^a-zA-z \-\'\.]/', $name);
        //If regex returns 1 or false, it means unacceptable character was found or search failed.
        if ($is_valid === 1 || $is_valid === false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Validate a person's name suffix against pre-defined enumeration
     *
     * @param string $suffix
     * @return bool Returns true if suffix is valid, else false.
     */
    public static function validateSuffix(string $suffix): bool
    {
        $is_valid = array_search(strtoupper($suffix), self::VALID_SUFFIXES);
        if ($is_valid === false) {
            return false;
        } else {
            return true;
        }
    }
}
