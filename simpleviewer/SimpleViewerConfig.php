<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
//
// SimpleViewer - Config
// ---------------------------------------------------------------------
// Adjust the settings for your SimpleViewer gallery and the 
// AdminInterface right in this file. You will not have to make changes
// in any other file. The main configuration information is stored here.
// 
// Note:
// This is the main configuration file.
//
// ---------------------------------------------------------------------
// Copyright (c) 2004,2005 Christian Machmeier
// ---------------------------------------------------------------------
//
// $Id: SimpleViewerConfig.php,v 1.4 2004/06/06 20:52:36 grisu Exp $
//

// {{{ Environment

error_reporting(0);

// Send a proper/appropriate Content-Type.
header('Content-Type: text/html;charset=utf-8');

// Set PHP ini-file directives.
ini_set('session.gc_maxlifetime', 1800);
(empty($_SESSION) ? ini_set('session.use_trans_sid', 1) : null);

// }}}
// {{{ Server

// Start a new session, if it doesn't already exist.
@session_start();

// Check, if the requirements can be fitted.
if (empty($_SESSION['reqFit'])) {
    $_SESSION['reqFit'] = true;
}

if ($_SESSION['reqFit'] != true) {

	$_SESSION['reqFit'] = true;
	
	// Check the requested PHP version.
	$version = explode('.', phpversion());
	if ($version[0] < 4) {
		$_SESSION['reqFit'] = false;
		die('<h1>Error</h1><p>You cannot run SimpleViewerAdmin, because you should have installed at least PHP 4.1 or above.</p>');
	}
	
	// Check for XML support.
	$xmlError = true;
	foreach (get_loaded_extensions() as $k => $v) {
		if ($v == 'xml') {
			$xmlError = false;
		}
	}
	
	if ($xmlError) {
		$_SESSION['reqFit'] = false;
		die('<h1>Error</h1><p>You cannot run SimpleViewerAdmin, because you need to have PHP compiled with native XML support.</p>');
	}
	
}

// }}}
// {{{ Configuration

// Open the XML file for reading.
if (!($fileHandle = @fopen('SimpleViewerConfig.xml', 'r'))) {
	die("Can't read file: " . 'SimpleViewerConfig.xml');
}
	
// Read the XML file.
$data = '';
while ($chunk = @fread($fileHandle, 4096)) {
	$data .= $chunk;
}

// Initialize the SAX parser.
$xmlParser = @xml_parser_create();

// Control the parser behaviour.
@xml_parser_set_option($xmlParser, XML_OPTION_CASE_FOLDING, false);
@xml_parser_set_option($xmlParser, XML_OPTION_SKIP_WHITE, 	true);

// Container arrays.
$elementArray 	= array();
$frequencyArray = array();

// Parse the XML file.
if (!xml_parse_into_struct($xmlParser, $data, $elementArray, $frequencyArray)) {
	die('XML Parser error: ' . xml_error_string(xml_get_error_code($xmlParser)));
}

// All done, clean up!
@xml_parser_free($xmlParser);

// Global configuration container.
$simpleViewer = array();

// Parse the configuration attributes.
foreach ($elementArray[0]['attributes'] as $k => $v) {
	$simpleViewer[$k] = $v;
}

// After parsing the configuration data, init a basic XML template.
$simpleViewer['basicXMLTemplate'] = '<?xml version="1.0" encoding="UTF-8"?>
<SIMPLEVIEWER_DATA maxImageDimension="' . $simpleViewer['maxImageDimension'] . 
'" textColor="' . $simpleViewer['textColor'] . '" frameColor="' . $simpleViewer['frameColor'] . 
'" backgroundColor="' . $simpleViewer['backgroundColor'] . '" frameWidth="' . $simpleViewer['frameWidth'] . 
'" stagePadding="' . $simpleViewer['stagePadding'] . '" thumbnailColumns="' . $simpleViewer['thumbnailColumns'] . 
'" thumbnailRows="' . $simpleViewer['thumbnailRows'] . '" navPosition="' . $simpleViewer['navPosition'] .
'" navDirection="' . $simpleViewer['navDirection'] . '" isPublished="false" title="" imagePath="" thumbPath="">
</SIMPLEVIEWER_DATA>';

// Set a default title for the gallery.
$simpleViewer['defaultTitle'] = 'My SimpleViewerAdmin Gallery';

// Set error reporting mode.
if ($simpleViewer['emergency'] == 1) {
	error_reporting(E_ALL);
} else {
	error_reporting(0);
}

// }}}

?>
