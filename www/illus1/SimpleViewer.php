<?php

/* vim: set expandtab tabstop=4 shiftwidth=4: */
//
// SimpleViewer
// ---------------------------------------------------------------------
// Access your SimpleViewer gallery right through this page by getting
// a summary of your currently installed albums first. After your choice
// the requested album will be loaded from the according directory.
// 
// Note:
// This file contains the summary, the albums and the installation link.
// This file is embedded in the index.php.
// ---------------------------------------------------------------------
// Copyright (c) 2004 Christian Machmeier
// ---------------------------------------------------------------------
//
// $Id: SimpleViewer.php,v 1.2 2004/06/06 20:52:35 grisu Exp $
//

// Load the SimpleViewer configuration.
require_once 'SimpleViewerConfig.php';

// Load the SimpleViewer functions library.
require_once 'SimpleViewerFunctions.php';

// An album name was specified.
if (!empty($_GET['album']) && is_dir($_GET['album'])) {

	$albumConfig = getConfig('./' . $_GET['album'] . '/imageData.xml');

?>
	<script language="javascript" type="text/javascript" src="flash_detect.js">
	<!--
		function getFlashVersion() { return null; };
	//-->
	</script>
	<script language="javascript" type="text/javascript">
	<!--
		var flashVersion = getFlashVersion();
		if (flashVersion < 6) {		
			window.location.replace("upgrade.html");
		}
	//-->
	</script>
	<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="<?php echo $simpleViewer['galleryWidth']; ?>" height="<?php echo $simpleViewer['galleryHeight']; ?>" id="viewer" align="top">
		<param name="movie"   	value="viewer.swf" />
		<param name="quality" 	value="high" />
		<param name="scale"   	value="noscale" />
		<param name="bgcolor"   value="<?php echo str_replace('0x', '#', $albumConfig['backgroundColor']); ?>" />
		<param name="FlashVars" value="xmlDataPath=SimpleViewerImageData.php?album=<?php echo $_GET['album'];?>" />
		<embed src="viewer.swf" width="<?php echo $simpleViewer['galleryWidth']; ?>" height="<?php echo $simpleViewer['galleryHeight']; ?>" align="top" quality="high" scale="noscale" bgcolor="<?php echo str_replace('0x', '#', $albumConfig['backgroundColor']); ?>" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" FlashVars="xmlDataPath=SimpleViewerImageData.php?album=<?php echo $_GET['album'];?>" />
    </object>
<?php
// No album name was specified
} else {
?>
	<table cellpadding="0" cellspacing="0" width="<?php echo $simpleViewer['galleryWidth']; ?>" height="<?php echo $simpleViewer['galleryHeight']; ?>" border="0">
		<tr>
			<td align="center" valign="middle">
				<div id="overview">
					<?php
					// Get the current number of albums.
					$dirList = getDirList('./', '', false, 'dirs');
					
					// SimpleViewer is not installed yet.
					if ($dirList['Number'] == 0) {
						echo '
							<h2>SimpleViewer is not installed!</h2>
							Please log in: <a href="SimpleViewerAdmin.php" class="overview">' . $simpleViewer['adminTitle'] . '</a>
						';
					// SimpleViewer is installed, show the list of albums.
					} else {
						echo '
							<h2>' . $simpleViewer['title'] . '</h2>
							Please choose one of my albums:
							<ul>
						';
						
						// sort and output the album list.
						natsort($dirList['List']);
						foreach ($dirList['List'] as $k => $v) {
							$xmlData = getConfig('./' . $v . '/imageData.xml');
							echo '<li><a href="?album=' . $v . '" class="overview">' . $xmlData['title'] . "</a></li>\n";
						}
						echo '
							</ul>
							Manage your gallery? <a href="SimpleViewerAdmin.php" class="overview">Login!</a>
						';
					}
					?>
				</div>
			</td>
		</tr>
		<tr>
			<td style="text-align: right; height: 20px;">
				<a href="http://www.redsplash.de/projects/simplevieweradmin/" class="overview" style="margin-right: 5px;" target="_blank">SimpleViewerAdmin <?php echo $simpleViewer['version']; ?></a>
			</td>
		</tr>
	</table>
<?php
}
?>