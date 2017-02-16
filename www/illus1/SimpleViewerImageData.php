<?php


/* vim: set expandtab tabstop=4 shiftwidth=4: */
//
// SimpleViewer - ImageData
// ---------------------------------------------------------------------
// The requested XML file is read and global configuration variables are
// passed to the introductory XML string.
// 
// 
// Note:
// This file reads the requested XML file.
//
// ---------------------------------------------------------------------
// Copyright (c) 2004 Christian Machmeier
// ---------------------------------------------------------------------
//
// $Id: SimpleViewerImageData.php,v 1.1.1.1 2004/05/27 22:20:25 grisu Exp $
//

// An album argument was provided properly.
if (!empty($_GET['album']) && is_dir($_GET['album'])) {
	
	// Load the SimpleViewer configuration.
	require_once 'SimpleViewerConfig.php';

	// XML container.
	$xmlData = '';

	// Set the XML file to read.
	$xmlFile = './' . $_GET['album'] . '/imageData.xml';
	
	// Check if the XML file is readable.
	if (is_file($xmlFile) && is_readable($xmlFile)) {
		
		// Open the XML file.
		$fileHandle = @fopen($xmlFile, 'r');
		
		// Read the XML file contents.
		$xmlData .= @fread($fileHandle, @filesize($xmlFile)) or die('Could not read file!');
		
		// Close the file.
		@fclose($fileHandle);
		
	// Exit the application on error.
	} else {
		die('Could not open file!');
	}
	
	// Send the correct HTTP header.
	header('Content-Type: text/xml');
	
	// Print the XML file contents.
	echo $xmlData;
	
// No album argument was provided. Exit the application.
} else {
	die('You should give me an existing album name, so I can parse it!');
}


?>