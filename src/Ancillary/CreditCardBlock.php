<?php

/**
 * This file is part of MeridianLink's SmartAPI Helper package.
 * 
 * For the full copyright and license information, please view the LICENSE file that was distributed with
 * this source code.
 */

namespace jafrajarvy292\SmartAPIHelper\Ancillary;

/**
 * Holds all info for a single credit card
 */
class CreditCardBlock
{
    /** @var PersonNameBlock Full name of the card holder */
    private $cardholder_name;
    /** @var AddressBlock Billing address associated with card */
    private $cardholder_address;
    /** @var string Credit card number */
    private $card_number = '';
    /** @var string Expiration month  */
    private $card_exp_month = '';
    /** @var string Expiration year */
    private $card_exp_year = '';
    /** @var string Card verification value / security code. Optional, but recommended */
    private $card_cvv = '';

    /**
     * Set the cardholder's name
     *
     * @param PersonNameBlock $name
     * @return void
     */
    public function setName(PersonNameBlock $name): void
    {
        $this->cardholder_name = $name;
    }

    /**
     * Set the card holder's billing address
     *
     * @param AddressBlock $address
     * @return void
     */
    public function setAddress(AddressBlock $address): void
    {
        $this->cardholder_address = $address;
    }

    /**
     * Set the credit card number
     *
     * @param string $number
     * @return void
     * @throws \Exception If credit card number is empty or fails luhn check
     */
    public function setCardNumber(string $number): void
    {
        $number = trim($number);
        if ($number === null || $number === '') {
            throw new \Exception('Card number cannot be empty.');
        } else {
            if (!self::validateCardNumber($number)) {
                throw new \Exception('Card number not valid, double-check for typos.');
            } else {
                $this->card_number = $number;
            }
        }
    }

    /**
     * Set the expiration month. Will accept 1 or 2 digit value. A few examples below:
     * ```
     * $object->setExpMonth('01');
     * $object->setExpMonth('1');
     * $object->setExpMonth(2);
     * ```
     *
     * @param string|int $month Expiration month as string or int.
     * @return void
     * @throws \Exception If month is not valid
     */
    public function setExpMonth(string $month): void
    {
        $month = trim($month);
        if ($month === null || $month === '') {
            throw new \Exception('Expiration month cannot be empty.');
        } else {
            //Cast month to integer so we can do easy check for value
            $integer = (int)$month;
            if ($integer < 1 || $integer > 12) {
                throw new \Exception('Expiration month is not valid.');
            //If month is a single digit, prefix with 0 and store that to adhere to XML schema
            } elseif ($integer >= 1 && $integer <= 9) {
                $this->card_exp_month = '0' . (string)$integer;
            //If month is already 2 digits, then just store it
            } elseif ($integer >= 10 && $integer <= 12) {
                $this->card_exp_month = (string)$integer;
            }
        }
    }

    /**
     * Set the expiration year. A few examples below:
     * ```
     * $object->setExpYear(2020);
     * $object->setExpYear('2020');
     * ```
     *
     * @param string|int $year Expiration year as string or int in YYYY format.
     * @return void
     * @throws \Exception If expiration year is not a valid string or integer
     */
    public function setExpYear(string $year): void
    {
        $year = trim($year);
        if ($year === null || $year === '') {
            throw new \Exception('Expiration year cannot be empty.');
        } else {
            if ((int)$year < 2000 || (int)$year > 3000) {
                throw new \Exception('Expiration year is not valid.');
            } else {
                $this->card_exp_year = (string)$year;
            }
        }
    }

    /**
     * Set the card verification value / security code
     *
     * @param string $cvv Should be a 3 or 4 digit code
     * @return void
     * @throws \Exception if CVV is not 3 or 4 digits
     */
    public function setCVV(string $cvv): void
    {
        $cvv = trim($cvv);
        if ($cvv === null || $cvv === '') {
            $cvv = '';
        } else {
            $is_valid = preg_match('/^\d{3,4}$/', $cvv);
            if ($is_valid === 0 || $is_valid === false) {
                throw new \Exception('CVV must be 3 or 4 digits.');
            } else {
                $this->card_cvv = $cvv;
            }
        }
    }

    /**
     * Return the cardholder name block
     *
     * @return PersonNameBlock|null
     */
    public function getCardholderName(): ?PersonNameBlock
    {
        if ($this->cardholder_name === null) {
            return null;
        } else {
            return $this->cardholder_name;
        }
    }

    /**
     * Return the cardholder billing address
     *
     * @return AddressBlock|null
     */
    public function getCardholderAddress(): ?AddressBlock
    {
        if ($this->cardholder_address === null) {
            return null;
        } else {
            return $this->cardholder_address;
        }
    }

    /**
     * Return the card number
     *
     * @return string
     */
    public function getCardNumber(): string
    {
        return $this->card_number;
    }

    /**
     * Return the card expiration month as a 2 digit string
     *
     * @return string
     */
    public function getExpMonth(): string
    {
        return $this->card_exp_month;
    }

    /**
     * Return the card expiration year as a 4 digit string
     *
     * @return string
     */
    public function getExpYear(): string
    {
        return $this->card_exp_year;
    }

    /**
     * Return the card CVV
     *
     * @return string
     */
    public function getCVV(): string
    {
        return $this->card_cvv;
    }

    /**
     * Generates a credit card payment block for XML insertion
     *
     * This function will generate a full credit card payment data block and return it as a DOM node,
     * which can be appended to an element using appendChild(). While we don't make a lot of the fields here
     * mandatory, the only field that is typically optional is the CVV. The rest of the credit card data
     * fields should be provided to ensure a successful transaction by the payment gateway.
     * A sample of the usage and returning card block is below.
     * ```
     * $element->appendChild($credit_card->getXML($base));
     *
     * <SERVICE_PAYMENT>
     *  <ADDRESS>
     *   <AddressLineText>123 Main St</AddressLineText>
     *   <CityName>Garden Grove</CityName>
     *   <CountryCode>US</CountryCode>
     *   <PostalCode>58394</PostalCode>
     *   <StateCode>CA</StateCode>
     *  </ADDRESS>
     *  <NAME>
     *   <FirstName>David</FirstName>
     *   <LastName>Testcase</LastName>
     *   <MiddleName>R</MiddleName>
     *   <SuffixName>JR</SuffixName>
     *  </NAME>
     *  <SERVICE_PAYMENT_DETAIL>
     *  <ServicePaymentAccountIdentifier>4111111111111111</ServicePaymentAccountIdentifier>
     *   <ServicePaymentCreditAccountExpirationDate>2021-02</ServicePaymentCreditAccountExpirationDate>
     *   <ServicePaymentSecondaryCreditAccountIdentifier>334</ServicePaymentSecondaryCreditAccountIdentifier>
     *  </SERVICE_PAYMENT_DETAIL>
     * </SERVICE_PAYMENT>
     * ```
     *
     * @param \DOMDocument $base The DOMDocument to which we'll be adding this address block
     * @param string|null $namespace The namespace associated with thse elements. Null refers to default
     * namespace
     * @return \DOMNode
     */
    public function getXML(\DOMDocument $base, string $namespace = null): \DOMNode
    {
        //If namespace value is not passed, then use the document's default namespace
        if ($namespace === null) {
            $namespace = $base->lookupNamespaceUri(null);
        }
        //If cardholder address is provided, generate XML for it
        $parent = $base->createElementNS($namespace, 'SERVICE_PAYMENT');
        if ($this->cardholder_address !== null) {
            $parent->appendChild($this->getCardholderAddress()->getXML($base, $namespace));
        }
        //If cardholder name is provided, generate XML for it
        if ($this->cardholder_name !== null) {
            $parent->appendChild($this->getCardholderName()->getXML($base, $namespace));
        }
        $payment_detail = $parent->appendChild($base->createElementNS($namespace, 'SERVICE_PAYMENT_DETAIL'));
        //If credit card number is provided, generate XML for it
        if ($this->card_number !== null && $this->card_number !== '') {
            $payment_detail->appendChild($base->createElementNS(
                $namespace,
                'ServicePaymentAccountIdentifier',
                $this->card_number
            ));
        }
        //If expiration month and year are both provided, generate XML for this
        if (
            $this->card_exp_month !== null &&
            $this->card_exp_month !== '' &&
            $this->card_exp_year !== null &&
            $this->card_exp_year !== ''
        ) {
            $payment_detail->appendChild($base->createElementNS(
                $namespace,
                'ServicePaymentCreditAccountExpirationDate',
                $this->card_exp_year . '-' . $this->card_exp_month
            ));
        }
        //If CVV was provided, insert it
        if ($this->getCVV() !== null && $this->getCVV() !== '') {
            $payment_detail->appendChild($base->createElementNS(
                $namespace,
                'ServicePaymentSecondaryCreditAccountIdentifier',
                $this->card_cvv
            ));
        }
        return $parent;
    }

    /**
     * Validates the card number against Luhn algorithm, helpful in flagging typos. This does not verify
     * that the card was actually issued by a bank, has sufficient funds, etc.
     *
     * @param string $number Full card number, without hyphen or spaces (e.g. separators)
     * @return boolean Will return true if card passes validity check, else false
     */
    public static function validateCardNumber(string $number): bool
    {
        //If number passed is empty string or null, return false
        if ($number === null || $number === '') {
            return false;
        /* Check to see if the card number contains anything other than digits or if the regex attempt
        fails. If so, return false */
        } elseif (
            preg_match('/[^0-9]/', $number) === 1 ||
            preg_match('/[^0-9]/', $number) === false
        ) {
            return false;
        }
        //If number is all digits, run Luhn algorithm check
        //Convert input string to array, one array slot for each digit
        $array = str_split($number);
        //Cast each array entry from string to int
        for ($i = 0; $i < count($array); $i++) {
            $array[$i] = (int)$array[$i];
        }
        //Apply the "doubling" portion of the Luhn algorithm to applicable items
        for ($i = count($array) - 2; $i >= 0; $i -= 2) {
            $doubled = (int)$number[$i] * 2;
            if ($doubled > 9) {
                $split = str_split($doubled);
                $doubled = array_sum($split);
            }
            $array[$i] = $doubled;
        }
        //Sum the resulting array
        $result = array_sum($array);
        if ($result % 10 === 0) {
            return true;
        } else {
            return false;
        }
    }
}
