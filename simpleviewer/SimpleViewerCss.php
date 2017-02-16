<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
//
// SimpleViewer - CSS
// ---------------------------------------------------------------------
// The decision to store CSS formatting details in a includable PHP file
// was driven by the considerations to provide simple configuration 
// options for the user. Make changes in the main configuration file.
// 
// Note:
// This file is the main stylesheet.
//
// ---------------------------------------------------------------------
// Copyright (c) 2004,2005 Christian Machmeier
// ---------------------------------------------------------------------
//
// $Id: SimpleViewerCss.php,v 1.2 2004/06/02 07:09:21 grisu Exp $
//

// Load the SimpleViewer configuration.
require_once 'SimpleViewerConfig.php';

// Load the SimpleViewer functions library.
require_once 'SimpleViewerFunctions.php';

// Output the CSS formatting definitions.
header('Content-type: text/css');

echo '
	A						{
		color: ' . str_replace('0x', '#', $simpleViewer['foregroundColor']) . ';
		text-decoration: none;
	}
	
	A:hover					{
		color: ' . str_replace('0x', '#', $simpleViewer['foregroundColor']) . ';
		text-decoration: underline;
	}
	
	H1, TABLE, TR, TD		{
		padding: 0;
		border: 0;
		margin: 0;
	}	
	
	H1, H2					{
		font-size: large;
		text-align: center;
	}
	
	HTML, BODY 				{
		font-family: sans-serif;
		font-size: 10pt;
		color: ' . str_replace('0x', '#', $simpleViewer['foregroundColor']) . ';
		background-color: ' . str_replace('0x', '#', $simpleViewer['backgroundColor']) . ';
		width: ' . $simpleViewer['galleryWidth'] . ';
		height: ' . $simpleViewer['galleryHeight'] . ';
		padding: 0;
		border: 0;
		margin: 0;
	}
	
	EMBED, OBJECT, TABLE#viewer {
		background-color: ' . str_replace('0x', '#', $simpleViewer['backgroundColor']) . ';
		width: ' . $simpleViewer['galleryWidth'] . ';
		height: ' . $simpleViewer['galleryHeight'] . ';
	}
	
	UL 						{
		padding: 10px 0 10px 20px;
		margin: 0;
	}

	UL LI					{
		list-style-type: square;
	}

	DIV#overview 			{
		color: ' . str_replace('0x', '#', $simpleViewer['foregroundColor']) . ';
		text-align: left;
		padding: 10px;
		margin: 0;
		width: 390px;
	}
	
	DIV#overview UL#extended {
		padding: 0;
		margin: 0;
	}
	
	DIV#overview UL#extended LI {
		font-weight: bold;
		list-style-type: none;
		float: left;
	}
	
	DIV#overview UL#extended LI A,
	DIV#overview UL#extended LI DIV {
		width: 139px;
		height: 51px;
		color: ' . str_replace('0x', '#', $simpleViewer['foregroundColor']) . ';
		background-color: #'. makeColorVariant($simpleViewer['backgroundColor'], 30) . ';
		overflow: hidden;
		padding: 0 0 0 51px;
		border: 0;
		margin: 1px;
		display: block;
	}
	
	DIV#overview UL#extended LI A:hover {
		text-decoration: none;
		color: ' . str_replace('0x', '#', $simpleViewer['backgroundColor']) . ';
		background-color: ' . str_replace('0x', '#', $simpleViewer['foregroundColor']) . ';
	}
	
	DIV#overview UL#extended LI SPAN {
		font-size: 75%;
		font-weight: normal;
		font-style: italic;
	}
';

// The following stylesheets are only used within the AdminInterface.
echo '
	BODY.admin A 			{
		color: #06c;
		text-decoration: none;
	}
	
	BODY.admin A:hover		{
		color: #06c;
		text-decoration: underline;
	}
	
	HTML, BODY.admin		{
		font-family: sans-serif;
		font-size: 10pt;
		color: #333;
		background: #eee repeat 0 0;
		width: 100%;
		height: 100%;
		padding: 0;
		border: 0;
		margin: 0;
	}
	
	BODY.admin FORM 		{
		padding: 0;
		margin: 0;
	}

	BODY.admin H1			{
		font-size: large;
		color: #ccc;
		margin-right: 10px;
		display: inline;
	}
	
	BODY.admin H3,
	BODY.admin H4			{
		font-size: small;
	}
	
	BODY.admin H3			{
		font-style: italic;
		color: #06c;
	}
	
	BODY.admin H4.label 	{
		margin: 0 0 10px;
	}
	
	BODY.admin H5			{
		color: #999;
		padding: 0;
		margin: 0;
	}
	
	BODY.admin IMG			{
		background-color: #fff;
		padding: 2px;
		border: 1px solid #000;
		margin: 0;
	}
	
	BODY.admin LABEL		{
		font-size: x-small;
		font-weight: bold;
	}
	
	BODY.admin UL 			{
		padding: 0 0 10px 16px;
		margin: 0;
	}

	BODY.admin DIV#header 	{
		color: #fff;
		background-color: #666;
		padding: 20px;
		border-bottom: 5px solid #ccc;
		margin: 0;
		position: absolute;
		top: 0;
		left: 0;
		width: 665px;
		height: 20px;
	}
	
	BODY.admin DIV#menue   	{
		color: #333;
		text-align: left;
		background-color: #fff;
		padding: 10px;
		border-bottom: 5px solid #666;
		width: 210px;
		position: absolute;
		top: 70px;
		left: 0;
	}
		
	BODY.admin DIV#content	{
		color: #333;
		background-color: #fff;
		width: 450px;
		padding: 10px;
		border-bottom: 5px solid #666;
		margin: 0;
		position: absolute;
		top: 70px;
		left: 235px;
	}
	
	BODY.admin .label		{
		font-weight: bold;
		color: #666;
		padding: 0;
		border-bottom: 1px solid #999;
		margin: 0;
	}
	
	BODY.admin #login 		{
		padding: 10px;
		background-color: #fff;
		border-bottom: 5px solid #666;
		width: 685px;
		position: absolute;
		top: 70px;
		left: 0;
	}
	
	BODY.admin #login P		{
		padding: 0;
		margin: 0;
	}
	
	BODY.admin TABLE#summary {
		padding: 0;
		margin: 0 0 10px;
	}
	
	BODY.admin TABLE#summary TD.thumb {
		font-size: x-small;
		text-align: center;
		vertical-align: middle;
		background-color: #eee;
		width: 65px;
		padding: 10px;
		border: 1px solid #999;
		margin: 0;
	}
	
	BODY.admin TABLE#summary TD.thumb INPUT {
		margin: 0;
	}
	
	BODY.admin TABLE#summary TD.thumb IMG {
		margin: 4px 0;
	}
	
';

?>
