<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
//
// SimpleViewerAdmin
// ---------------------------------------------------------------------
// Administer your SimpleViewer gallery in a nice and comfortable way. 
// This UI provides everything you need to upload or delete your images.
// You can also create, rename or delete albums.
// 
// Note:
// This file contains the "edit image"-form, which is part of the 
// administration interface.
//
// ---------------------------------------------------------------------
// Copyright (c) 2004,2005 Christian Machmeier
// ---------------------------------------------------------------------
//
// $Id: SimpleViewerEditImage.php,v 1.1 2005/07/13 08:53:46 cm Exp $
//

// Load the SimpleViewer configuration.
require_once 'SimpleViewerConfig.php';

// Load the SimpleViewer functions library.
require_once 'SimpleViewerFunctions.php';

if (empty($_GET['albumName']) || empty($_GET['imageName'])) {
	die('This script can only work with all parameters specified.');
} else {
	$albumName	  = $_GET['albumName'];
	$imageName	  = $_GET['imageName'];
	$imageCaption = '';

	$albumConfigFile = './' . $albumName . '/imageData.xml';
	$sourceData	 	 = parseXML($albumConfigFile);
	$xmlData    	 = array();
	$i 				 = 0;
	
	foreach ($sourceData as $sourceKey => $sourceValue) {
		if ($sourceValue['tag'] == 'NAME') {
			$xmlData[$i]['name'] = $sourceValue['value'];
		}
		if ($sourceValue['tag'] == 'CAPTION') {
			$xmlData[$i]['caption'] = (!empty($sourceValue['value']) ? $sourceValue['value'] : '');
			$i++;
		}
	}
	
	foreach ($xmlData as $xmlKey => $xmlValue) {
		if ($xmlValue['name'] == $imageName) {
			$imageCaption = $xmlValue['caption'];
			break;
		}
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>Edit Caption</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="SimpleViewerCss.php" />
	</head>
	<body class="admin" style="background-color: #eee;">
		<form action="SimpleViewerAdmin.php?action=edit&amp;file=image&amp;name="<?php echo $imageName; ?>"&amp;redirect" target="mainWindow" method="post">
			<input type="hidden" name="imageName" value="<?php echo $imageName; ?>" />
			<table cellpadding="5" cellspacing="2" border="0" width="99%">
				<tr>
					<td colspan="2"><h4 class="label">Edit caption</h4></td>
				</tr>
				<tr>
					<td width="15%">&nbsp;<image src="./<?php echo $albumName; ?>/thumbs/<?php echo $imageName; ?>" border="1" /></td>
					<td width="75%"><label for="autoCaption">Autolink image</label> <input type="checkbox" name="autoCaption" id="autoCaption" value="On" onclick="document.getElementById('imageCaption').value='';" /></td>
				</tr>
				<tr>
					<td valign="top"><label for="imageCaption">Caption</label></td>
					<td><textarea name="imageCaption" id="imageCaption" rows="4" cols="50"><?php echo stripslashes($imageCaption); ?></textarea></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<input type="submit" name="saveImageData" value="Save Changes" />
						&nbsp;
						<input type="button" name="abort" value="Abort" onclick="window.close();" />
					</td>
				</tr>
			</table>
		</form>
	</body>
</html>
<?php
}
?>
