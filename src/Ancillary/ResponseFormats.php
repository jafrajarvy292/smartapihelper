<?php

/**
 * @author David Tran <hunterr83@gmail.com>
 */

namespace jafrajarvy292\SmartAPIHelper\Ancillary;

/**
 * Holds the indicated preferred response formats. Preferred response formats tell the system, when returning
 * a completed order, what formats you want the report in.
 */
class ResponseFormats
{
    /** @var array The pre-defined enumeration of the types of response formats that can be requested.
     * - Xml: Will return the completed reports in a parsable XML format. Good format if you need to parse
     * specific data points and do things with them
     * - Html: Returns the full HTML report wrapped in a CDATA segment
     * - Pdf: Returns the full PDF report encoded in base64.
     */
    public const DEFINED_TYPES = ['Xml', 'Html', 'Pdf'];
    /** @var array Holds the flags for the 3 primary formats offered by SmartAPI */
    private $formats = [];
    
    /**
     * Constructor allows you to set the flags for each of the 3 primary formats at instantiation.
     *
     * @param boolean $xml Indicate whether you want parsable XML format returned.
     * @param boolean $html Indicate whether you want an HTML version of the report.
     * @param boolean $pdf Indicate whether you want a PDF version of the report.
     */
    public function __construct(bool $xml = true, bool $html = true, bool $pdf = true)
    {
        $this->formats['Xml'] = $xml;
        $this->formats['Html'] = $html;
        $this->formats['Pdf'] = $pdf;
    }

    /**
     * In the event that SmartAPI is enhanced to support more formats than the pre-defined ones,
     * you can add them here. Example below:
     *
     * @param string $format The format being requested. Note this field is typically case-sensitive.
     * @param boolean $flag Set to true or false
     * @return void
     * @throws \Exception If format provided is an existing pre-defined enumeration. This method should not
     * be used to set pre-defined formats.
     */
    public function setCustomFormat(string $format, bool $flag): void
    {
        $format = trim($format);

        //If user's custom format is actually a pre-defined type, throw error, else save it
        if (array_search(strtolower($format), array_map('strtolower', self::DEFINED_TYPES)) !== false) {
            throw new \Exception("Custom format {$format} is a pre-defined type. Use corresponding " .
                'setter function to adjust pre-defined types.');
        } else {
            $this->formats[$format] = $flag;
        }
    }

    /**
     * Individually set the flag for parsable XML response
     *
     * @param boolean $flag
     * @return void
     */
    public function setXMLFormat(bool $flag): void
    {
        $this->formats['Xml'] = $flag;
    }

    /**
     * Individually set the flag for HTML response
     *
     * @param boolean $flag
     * @return void
     */
    public function setHTMLFormat(bool $flag): void
    {
        $this->formats['Html'] = $flag;
    }

    /**
     * Individually set the flag for PDF response
     *
     * @param boolean $flag
     * @return void
     */
    public function setPDFFormat(bool $flag): void
    {
        $this->formats['Pdf'] = $flag;
    }

    /**
     * Returns an array of all formats that are set to true Example below:
     * ```
     * ['Xml', 'Pdf']
     * ```
     *
     * @return array
     */
    public function getFormats(): array
    {
        $temp = [];
        //Loop through the object's array and pushes only those set to true to our temp array and returns it
        foreach ($this->formats as $key => $value) {
            if ($value === true) {
                $temp[] =  $key;
            }
        }
        return $temp;
    }

    /**
     * Returns an associative array of all formats, regardless of their flag. Example below:
     * ```
     * ['Xml'] => true
     * ['Html'] => true
     * ['Pdf'] => false
     * ```
     *
     * @return array
     */
    public function getAllFormats(): array
    {
        return $this->formats;
    }

    /**
     * Returns a count of the number of formats set to true. Ideally, we want to check to ensure at least
     * one format is set to true before we go generating the XML container for this, otherwise we get a
     * container that has no child elements. This won't cause any errors, though it's sloppy.
     *
     * @return int
     */
    public function getCount(): int
    {
        $count = 0;
        foreach ($this->formats as $key => $value) {
            if ($value === true) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Generates the XML container used to indicate the preferred response formats
     *
     * This function will generate a full SERVICE_PREFERRED_RESPONSE_FORMATS block and return it as
     * an XML node, which can be appended to an element using appendChild(). A sample of the block is below.
     * getCount() method should be called first to check if at least one preferred method is indicated before
     * inserting the XML block, else you'll end up with a container with nothing in it. Below is an example
     * of usage and corresponding node returned:
     * ```
     * $parent->appendChild($data->getResponseFormats()->getXML($base));
     *
     * <SERVICE_PREFERRED_RESPONSE_FORMATS>
     *  <SERVICE_PREFERRED_RESPONSE_FORMAT>
     *   <SERVICE_PREFERRED_RESPONSE_FORMAT_DETAIL>
     *    <PreferredResponseFormatType>Xml</PreferredResponseFormatType>
     *   </SERVICE_PREFERRED_RESPONSE_FORMAT_DETAIL>
     *  </SERVICE_PREFERRED_RESPONSE_FORMAT>
     *  <SERVICE_PREFERRED_RESPONSE_FORMAT>
     *   <SERVICE_PREFERRED_RESPONSE_FORMAT_DETAIL>
     *    <PreferredResponseFormatType>Html</PreferredResponseFormatType>
     *   </SERVICE_PREFERRED_RESPONSE_FORMAT_DETAIL>
     *  </SERVICE_PREFERRED_RESPONSE_FORMAT>
     *  <SERVICE_PREFERRED_RESPONSE_FORMAT>
     *   <SERVICE_PREFERRED_RESPONSE_FORMAT_DETAIL>
     *    <PreferredResponseFormatType>Pdf</PreferredResponseFormatType>
     *   </SERVICE_PREFERRED_RESPONSE_FORMAT_DETAIL>
     *  </SERVICE_PREFERRED_RESPONSE_FORMAT>
     * </SERVICE_PREFERRED_RESPONSE_FORMATS>
     * ```
     *
     * @param \DOMDocument $base The XML document to which we'll be adding the nodes
     * @param string|null $namespace The namespace URI under which this container should belong. Null refers
     * to default namespace
     * @return \DOMNode Returns a single node that can be appended to another node via appendChild()
     */
    public function getXML(\DOMDocument $base, string $namespace = null): \DOMNode
    {
        //If namespace value is not passed, then use the document's default namespace
        if ($namespace === null) {
            $namespace = $base->lookupNamespaceUri(null);
        }
        //Create the main container element
        $preferred_formats = $base->createElementNS($namespace, 'SERVICE_PREFERRED_RESPONSE_FORMATS');
        //Loop through the preferred responses array and generate only nodes for those formats set to true
        foreach ($this->formats as $key => $value) {
            if ($value === true) {
                $preferred_formats->appendChild($base->createElementNS(
                    $namespace,
                    'SERVICE_PREFERRED_RESPONSE_FORMAT'
                ))->
                appendChild($base->createElementNS(
                    $namespace,
                    'SERVICE_PREFERRED_RESPONSE_FORMAT_DETAIL'
                ))->appendChild($base->createElementNS(
                    $namespace,
                    'PreferredResponseFormatType'
                ))->appendChild($base->createTextNode($key));
            }
        }
        return $preferred_formats;
    }
}
