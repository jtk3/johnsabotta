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
// Copyright (c) 2004,2005 Christian Machmeier
// ---------------------------------------------------------------------
//
// $Id: SimpleViewer.php,v 1.2 2004/06/06 20:52:35 grisu Exp $
//

// Load the SimpleViewer configuration.
require_once 'SimpleViewerConfig.php';

// Load the SimpleViewer functions library.
require_once 'SimpleViewerFunctions.php';

// An album name was specified.
if (!empty($_GET['album']) && is_dir($_GET['album']) && isPublished($_GET['album'])) {

	$dirList = getPublishedAlbums();
	natsort($dirList['List']);
	
	foreach ($dirList['List'] as $k => $v) {
		if ($v == $_GET['album']) {
			// Get the previous album.
			if (array_key_exists($k-1, $dirList['List'])) {
				$previous 	   = $dirList['List'][$k-1];
				$previousAlbum = getConfig('./' . $previous . '/imageData.xml');
			}
			
			// Get the current album.
			$currentAlbum 	   = getConfig('./' . $v . '/imageData.xml');
			
			// Get the next album.
			if (array_key_exists($k+1, $dirList['List'])) {
				$next 	   	   = $dirList['List'][$k+1];
				$nextAlbum 	   = getConfig('./' . $next . '/imageData.xml');
			}
			break;
		}
	}

?>
	<script language="javascript" type="text/javascript" src="../SimpleViewerAdmin/flash_detect.js">
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
	<table cellpadding="0" cellspacing="0" align="center" width="100%" id="viewer" style="background-color: <?php echo str_replace('0x', '#', $currentAlbum['backgroundColor']); ?>;">
		<tr>
			<td width="33%" align="left">
				&nbsp;<?php if (!empty($previous)) { echo '<a href="./?album=' . $previous . '" style="font-size: 80%;">&larr; ' . ((isPhpOfVersion(5) == -1) ? utf8_encode($previousAlbum['title']) : $previousAlbum['title']) . '</a>' ; } ?>
			</td>
			<td width="33%" align="center">
				<a href="../SimpleViewerAdmin/." style="font-size: 80%;">Back to "<?php echo (!empty($simpleViewer['title']) ? $simpleViewer['title'] : $simpleViewer['defaultTitle']); ?>"</a>
			</td>
			<td width="33%" align="right">
				<?php if (!empty($next)) { echo '<a href="./?album=' . $next . '" style="font-size: 80%;">' . ((isPhpOfVersion(5) == -1) ? utf8_encode($nextAlbum['title']) : $nextAlbum['title']) . ' &rarr;</a>' ; } ?>&nbsp;
			</td>
		</tr>
		<tr>
		    <td align="center" colspan="3" width="100%" height="95%"> 
		    	<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="<?php echo $simpleViewer['galleryWidth']; ?>" height="<?php echo $simpleViewer['galleryHeight']; ?>">
				    <param name="movie" value="../SimpleViewerAdmin/viewer.swf" />
				    <param name="quality" value="high" />
					<param name="scale" value="noscale" />
					<param name="bgcolor" value="<?php echo str_replace('0x', '#', $currentAlbum['backgroundColor']); ?>" />
					<param name="FlashVars" value="xmlDataPath=SimpleViewerImageData.php?album=<?php echo $_GET['album'];?>" />
				    <embed src="../SimpleViewerAdmin/viewer.swf" quality="high" scale="noscale" bgcolor="<?php echo str_replace('0x', '#', $currentAlbum['backgroundColor']); ?>" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" FlashVars="xmlDataPath=SimpleViewerImageData.php?album=<?php echo $_GET['album'];?>" width="<?php echo $simpleViewer['galleryWidth']; ?>" height="<?php echo $simpleViewer['galleryHeight']; ?>"></embed>
				</object>
    		</td>
	    </tr>
		<tr>
			<td colspan="3" style="text-align: right; height: 20px;">
				<a href="../SimpleViewerAdmin/SimpleViewerAdmin.php" style="font-size: 80%;">Login</a>
				&bull;
				<a href="http://www.redsplash.de/projects/simplevieweradmin/" style="font-size: 80%; margin-right: 5px;">SimpleViewerAdmin <?php echo $simpleViewer['version']; ?></a>
			</td>
		</tr>
	</table>
<?php
// No album name was specified
} else {
?>
	<table cellpadding="0" cellspacing="0" align="center" width="<?php echo $simpleViewer['galleryWidth']; ?>" border="0" id="viewer">
		<tr>
			<td align="center" valign="middle">
				<div id="overview">
					<?php
					// Get the current number of albums.
					$dirList = getPublishedAlbums();
					natcasesort($dirList['List']);
					
					// SimpleViewer is not installed yet.
					if ($dirList['Number'] == 0) {
						echo '
							<h2>SimpleViewer is not set up!</h2>
							<p><a href="SimpleViewerAdmin.php">Please log in!</a></p>
						';
					// SimpleViewer is installed, show the list of albums.
					} else {
						// My SimpleViewerAdmin Gallery
						echo '<h2>' . (!empty($simpleViewer['title']) ? $simpleViewer['title'] : $simpleViewer['defaultTitle']) . '</h2>
							<p>' . (!empty($simpleViewer['welcomeText']) ? $simpleViewer['welcomeText'] : 'Please choose one of my albums:') . '</p>
						';
						
						// Extende display of the list as a table featuring 
						// random thumbnails.
						if ($simpleViewer['showThumbnailsOnIndex'] == 'true') {
						
							echo "<ul id=\"extended\">\n";
							foreach ($dirList['List'] as $k => $v) {
								// Get the real name of the current album.
								$xmlData 	   = getConfig('./' . $v . '/imageData.xml');
								$albumRealName = ((isPhpOfVersion(5) == -1) ? utf8_encode($xmlData['title']) : $xmlData['title']);
								
								// Get the list of thumbnail files in the current album.
								$fileList = getDirList('./' . $v . '/thumbs/', '', false, 'files');
								srand(makeSeed());
								
								echo "<li>\n";
								if ($fileList['Number'] == 0) {
									echo '<div>';
								} else {
									echo '<a href="?album=' . $v . '" title="' . $albumRealName . '" style="background-image: url(' . $v . '/thumbs/' . $fileList['List'][rand(0, $fileList['Number']-1)] . '); background-position: 3px 50%; background-repeat: no-repeat;">';
								}
								echo $albumRealName . '<br /><span>(' . $fileList['Number'] . ' images)</span>';
								if ($fileList['Number'] == 0) {
									echo '</div>';
								} else {
									echo '</a>';
								}
								echo "\n</li>\n";
							}
							echo "</ul>\n";
							
						// Display the albums as a list.
						} else {						
							echo "<ul>\n";
							foreach ($dirList['List'] as $k => $v) {
								// Get the real name of the current album.
								$xmlData 	   = getConfig('./' . $v . '/imageData.xml');
								$albumRealName = ((isPhpOfVersion(5) == -1) ? utf8_encode($xmlData['title']) : $xmlData['title']);
								
								// Get the list of thumbnail files in the current album.
								$fileList = getDirList('./' . $v . '/thumbs/', '', false, 'files');
								
								if ($fileList['Number'] == 0) {
									echo '<li>' . $albumRealName . " <span>(" . $fileList['Number'] . " images)</span></li>\n";
								} else {
									echo '<li><a href="?album=' . $v . '" title="' . $albumRealName . '">' . $albumRealName . " <span>(" . $fileList['Number'] . " images)</span></a></li>\n";
								}
							}
							echo "</ul>\n";
						}
						
						echo '<p style="clear: both; position: relative; top: 1em;">' . (!empty($simpleViewer['extraText']) ? $simpleViewer['extraText'] : '') . '</p>';
					}
					?>
				</div>
			</td>
		</tr>
		<tr>
			<td style="text-align: right; height: 20px;">
				<a href="../SimpleViewerAdmin/SimpleViewerAdmin.php" style="font-size: 80%;">Login</a>
				&bull;
				<a href="http://www.redsplash.de/projects/simplevieweradmin/" style="font-size: 80%; margin-right: 5px;">SimpleViewerAdmin <?php echo $simpleViewer['version']; ?></a>
			</td>
		</tr>
	</table>
<?php
}
?>
