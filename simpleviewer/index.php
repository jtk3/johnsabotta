<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
//
// SimpleViewer
// ---------------------------------------------------------------------
// Access your SimpleViewer gallery right through this page. For 
// embedding purposes it only includes a the SimpleViewer.php 
// 
// 
// Note:
// This index.php just includes the summary page.
//
// ---------------------------------------------------------------------
// Copyright (c) 2004,2005 Christian Machmeier
// ---------------------------------------------------------------------
//
// $Id: index.php,v 1.1.1.1 2004/05/27 22:20:25 grisu Exp $
//

// {{{ Cache control headers

header("Expires: Mon, 31 Dec 2003 12:34:56 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") ." GMT");
header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Cache-Control: post-check=0, pre-check=0", false);

// }}}

// Load the SimpleViewer configuration.
require_once 'SimpleViewerConfig.php';

// Load the SimpleViewer functions library.
require_once 'SimpleViewerFunctions.php';

// Get the current number of albums.
$dirList = getDirList('./', '', false, 'dirs');

// SimpleViewer is not installed yet.
if ($dirList['Number'] == 0) {
	header('Location:SimpleViewerAdmin.php');
	exit;
}

?>
<!DOCTYPE html 
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title><?php echo (!empty($simpleViewer['title']) ? $simpleViewer['title'] : $simpleViewer['defaultTitle']); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="../SimpleViewerAdmin/SimpleViewerCss.php" />
	</head>
	<body>
		<?php require_once 'SimpleViewer.php'; ?>
	</body>
</html>
