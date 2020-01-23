<?php
/* This is a filter for PHP files that use phpdoc comment styling. Running through this filter
will ensure Doxygen can properly handle the file */

//Read in the full input file
$source = file_get_contents($argv[1]);

/* Look for all instances of "* @throws \Exception" and converts it to "@throws Exception".
"@throws \Exception" is a phpdoc comment to indicate a scenario where an exception will be thrown. phpdoc
requires use of a fully qualified namespace, which means including the initial backslash for global classes
if you're working within a separate namespace. Doxygen will trip on the backslash and not parse this
properly, so we need to remove it. */
$source = preg_replace('/\* @throws \\\(Exception)/', '* @throws Exception', $source);

/* Look for all instances of "* @var" and replaces it to just "* ". The @var tag is not recognized by
Doxygen and will cause the entire description line to not appear. This isn't a perfect solution, since
the property's variable type won't display in the header label, but it will at least appear in the
property's description area, along with the rest of our description. This filter could be improved, but
doing so also increases the chances of it not working for some comments. We'll play it safe. */
$source = preg_replace('/\* @var/', '* ', $source);

echo $source;
?>