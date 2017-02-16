<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
//
// SimpleViewer - Upgrade
// ---------------------------------------------------------------------
// This file upgrades the SimpleViewerAdmin configuration files.
//
//
// ---------------------------------------------------------------------
// Copyright (c) 2004,2005 Christian Machmeier
// ---------------------------------------------------------------------
//
// $Id: upgrade.php,v 1.2 2005/07/15 10:05:46 cm Exp $
//

// Load the SimpleViewer configuration.
require_once 'SimpleViewerConfig.php';

// Load the SimpleViewer functions library.
require_once 'SimpleViewerFunctions.php';

// The backed up version of SimpleViewerConfig.xml.
$backedupGalleryConfigFile = 'SimpleViewerConfig.xml.bak';
$galleryConfigFile 		   = 'SimpleViewerConfig.xml';

$galleryUpgradeError	   = false;
$galleryUpgradeStatus      = array();
$albumUpgradeError	   	   = false;
$albumUpgradeStatus    	   = array();

?>
<!DOCTYPE html 
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>SimpleViewerAdmin :: Upgrade</title>
		<link rel="stylesheet" type="text/css" href="SimpleViewerCss.php" />
		<style type="text/css">
		<!--
		HTML, BODY { font-size: 87.5%; text-align: center; background-color: #fff; }
		HTML, BODY, FORM { padding: 0; border: 0; margin: 0; }
		DIV#container, DIV#success, DIV#error { text-align: left; width: 420px; padding: 1em; margin: 2em auto; }
		DIV#container { font-size: medium; color: #000; background-color: #eee; border: 1px solid #666; }
		DIV#success { color: #060; background-color: #efe; border: 1px solid #060;}
		DIV#error { color: #600; background-color: #fee; border: 1px solid #600;}
		DIV#success A, DIV#error A { color: inherit; text-decoration: underline; }
		DIV#success H1, DIV#error H1 { color: inherit; }
		H1, INPUT { font-family: Georgia, Times, serif; font-size: 1.2em; color: #666; }
		FORM { text-align: center; }
		INPUT { font-size: 1em; width: 60%; padding: 1em; }
		-->
		</style>
	</head>
	<body>
		<div id="container">
			<h1>SimpleViewerAdmin :: Upgrade</h1>
<?php
if (empty($_POST['startUpgrade'])) {
?>
			<form action="upgrade.php" method="post">
				<p><input type="submit" name="startUpgrade" value="Start It!" /></p>
			</form>

<?php
} else {
	
	// Upgrade the gallery configuration.
	if (!file_exists($backedupGalleryConfigFile)) {
	
		$galleryUpgradeError	= true;
		$galleryUpgradeStatus[] = '
			Could not find ' . $backedupGalleryConfigFile . '.<br />
			Check that the directory properties are set properly, try 
			again, and, if it fails again, proceed with 
			<a href="http://www.redsplash.de/projects/simplevieweradmin/#Upgrade">Step (2) of the upgrade instructions</a>.
		';
		
	} else {
		
		if (!is_readable($backedupGalleryConfigFile)) {
		
			$galleryUpgradeError	= true;
			$galleryUpgradeStatus[] = '
				Could not read ' . $backedupGalleryConfigFile . '.<br />
				Check that the directory properties are set properly, try 
				again, and, if it fails again, proceed with 
				<a href="http://www.redsplash.de/projects/simplevieweradmin/#Upgrade">Step (2) of the upgrade instructions</a>.
			';
			
		} else {
			
			if (!file_exists($galleryConfigFile)) {
		
				$galleryUpgradeError	= true;
				$galleryUpgradeStatus[] = '
					Could not find ' . $galleryConfigFile . '.<br />
					Check that the directory properties are set properly, try 
					again, and, if it fails again, proceed with 
					<a href="http://www.redsplash.de/projects/simplevieweradmin/#Upgrade">Step (2) of the upgrade instructions</a>.
				';
		
			} else {
		
				if (!is_readable($galleryConfigFile)) {
		
					$galleryUpgradeError	= true;
					$galleryUpgradeStatus[] = '
						Could not read ' . $galleryConfigFile . '.<br />
						Check that the directory properties are set properly, try 
						again, and, if it fails again, proceed with 
						<a href="http://www.redsplash.de/projects/simplevieweradmin/#Upgrade">Step (2) of the upgrade instructions</a>.
					';
			
				} else {
				
					// Get the attributes and its values, loop through attributes and 
					// compare them with each other respectively. Afterwards write 
					// contents back to file.
					$defaultGalleryConfig = getConfig($galleryConfigFile);
					$oldGalleryConfig 	  = getConfig($backedupGalleryConfigFile);
					$newGalleryConfig 	  = array();
					foreach ($defaultGalleryConfig as $k => $v) {
						if (array_key_exists($k, $oldGalleryConfig)) {
							if ($oldGalleryConfig[$k] == $v || $k == 'version') {
								$newGalleryConfig[$k] = $v;
							} else {
								$newGalleryConfig[$k] = $oldGalleryConfig[$k];
							}
						} else {
							$newGalleryConfig[$k] = $v;
						}
					}
					
					$simpleViewerConfig  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<SIMPLEVIEWER_DATA";
					foreach ($newGalleryConfig as $k => $v) {
						$simpleViewerConfig .= "\n " . $k . '="' . $v . '"';
					}
					$simpleViewerConfig .= ">\n</SIMPLEVIEWER_DATA>";
					
					if (!$fileHandle = @fopen($galleryConfigFile, 'w')) {
						$galleryUpgradeError	= true;
						$galleryUpgradeStatus[] = '
							Cannot open ' . $galleryConfigFile . ' for writing.<br />
							Check that the directory properties are set properly, try 
							again, and, if it fails again, proceed with 
							<a href="http://www.redsplash.de/projects/simplevieweradmin/#Upgrade">Step (2) of the upgrade instructions</a>.
						';
					} else {
						if (@fwrite($fileHandle, $simpleViewerConfig) === false) {
							$galleryUpgradeError	  	 = true;
							$galleryUpgradeStatus[] = '
								Cannot save ' . $galleryConfigFile . '.<br />
								Check that the directory properties are set properly, 
								try again, and, if it fails again, proceed with 
								<a href="http://www.redsplash.de/projects/simplevieweradmin/#Upgrade">Step (3) of the upgrade instructions</a>.

							';
						} else {
							$galleryUpgradeStatus[] = 'Gallery configuration successfully upgraded.';
						}
						@fclose($fileHandle);					
					}
					
				}
				
			}
			
		}
		
	}
	
	// Display status messages.
	if (!empty($galleryUpgradeStatus)) {
	
		echo '
		</div>
		<div id="' . (($galleryUpgradeError) ? 'error' : 'success') . '">
			<h1>Gallery Configuration Upgrade Status: ' . (($galleryUpgradeError) ? 'Error' : 'Success') . '</h1>
			<ul>
		';
		foreach ($galleryUpgradeStatus as $statusKey => $statusValue) {
			echo '<li>' . $statusValue . '</li>';
		}
		echo '</ul>';
		
	}
	
	// Upgrade the albums.
	$dirList = getDirList('./', '', false, 'dirs');
	if ($dirList['Number'] == 0) {
		$albumUpgradeStatus[] = 'No albums found. Nothing to upgrade here.';
	} else {
		
		foreach ($dirList['List'] as $k => $v) {
	
			if (!file_exists('./' . $v . '/imageData.xml')) {
		
				$albumUpgradeError	  = true;
				$albumUpgradeStatus[] = '
					Could not find imageData.xml of in folder "' . $v . '".<br />
					Check that the directory properties are set properly, try 
					again, and, if it fails again, proceed with 
					<a href="http://www.redsplash.de/projects/simplevieweradmin/#Upgrade">Step (3) of the upgrade instructions</a>.
				';
			
			} else {
		
				if (!is_readable('./' . $v . '/imageData.xml')) {
		
					$albumUpgradeError	  = true;
					$albumUpgradeStatus[] = '
						Could not read imageData.xml of in folder "' . $v . '".<br />
						Check that the directory properties are set properly, try 
						again, and, if it fails again, proceed with 
						<a href="http://www.redsplash.de/projects/simplevieweradmin/#Upgrade">Step (3) of the upgrade instructions</a>.
					';
			
				} else {
			
					$albumConfigFile = './' . $v . '/imageData.xml';
					$albumConfig 	 = getConfig($albumConfigFile);
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
		
					$imageData = '';
					foreach ($xmlData as $xmlKey => $xmlValue) {
						$imageData .= "\n\t<IMAGE>\n\t\t<NAME>" . $xmlValue['name'] . "</NAME>\n\t\t<CAPTION><![CDATA[" . $xmlValue['caption'] . "]]></CAPTION>\n\t</IMAGE>\n";
					}
					$simpleViewerConfig  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<SIMPLEVIEWER_DATA";
					foreach ($albumConfig as $albumKey => $albumValue) {
						if ($k == 'title') {
							$simpleViewerConfig .= ' ' . $albumKey . '="' . ((isPhpOfVersion(5) == -1) ? convertStr($albumValue, true, false) : convertStr($albumValue, true, false)) . '"';
						} else {
							$simpleViewerConfig .= ' ' . $albumKey . '="' . $albumValue . '"';
						}
					}
				
					if (!array_key_exists('isPublished', $albumConfig)) {
						$simpleViewerConfig .= ' isPublished="true"';
					}
				
					$simpleViewerConfig .= '>' . $imageData . "</SIMPLEVIEWER_DATA>";
					if (!$fileHandle = @fopen($albumConfigFile, 'w')) {
						$albumUpgradeError	  = true;
						$albumUpgradeStatus[] = '
							Cannot open "' . $albumConfigFile . '" for writing.<br />
							Check that the directory properties are set properly,
							try again, and, if it fails again, proceed with 
							<a href="http://www.redsplash.de/projects/simplevieweradmin/#Upgrade">Step (6) of the upgrade instructions</a>.
						';
					} else {
						if (@fwrite($fileHandle, $simpleViewerConfig) === false) {
							$albumUpgradeError	  = true;
							$albumUpgradeStatus[] = 'Cannot save ' . $albumConfigFile . '.';
						} else {
							$albumUpgradeStatus[] = '"' . $albumConfigFile . '" successfully upgraded and saved.';
						}
						@fclose($fileHandle);					
					}
				
				}
			
			}
		
		}
		
	}
	
	// Display status messages.
	if (!empty($albumUpgradeStatus)) {
	
		echo '
		</div>
		<div id="' . (($albumUpgradeError) ? 'error' : 'success') . '">
			<h1>Album Configuration Upgrade Status: ' . (($albumUpgradeError) ? 'Error' : 'Success') . '</h1>
			<ul>
		';
		foreach ($albumUpgradeStatus as $statusKey => $statusValue) {
			echo '<li>' . $statusValue . '</li>';
		}
		echo '</ul>' . ((!$albumUpgradeError) ? '<p>Album configurations successfully upgraded.</p>' : '');
		
	}
	
	if (!$galleryUpgradeError && !$albumUpgradeError) {
		echo '<p>Now proceed with <a href="http://www.redsplash.de/projects/simplevieweradmin/#Upgrade">Step (8) of the upgrade instructions</a>.</p>';
	}
	
}

?>
		</div>
	</body>
</html>
