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
// This file contains the administration interface.
//
// ---------------------------------------------------------------------
// Copyright (c) 2004,2005 Christian Machmeier
// ---------------------------------------------------------------------
//
// $Id: SimpleViewerAdmin.php,v 1.8 2004/06/19 08:53:46 grisu Exp $
//

// Load the SimpleViewer configuration.
require_once 'SimpleViewerConfig.php';

$simpleViewer['error']	  = false;
$simpleViewer['errorMsg'] = '';

// Load the SimpleViewer functions library.
require_once 'SimpleViewerFunctions.php';

// {{{ authentication
@session_start();
$authed = false;

// The user is already authenticated.
if (isset($_SESSION['is_authed']) && $_SESSION['is_authed'] == 1) {
	$authed 			   = true;
	$_SESSION['is_authed'] = true;
	
	// The user is logged in and tries to logout.
	if (!empty($_GET['logout'])) {
		$_SESSION['is_authed'] = false;
		@session_destroy();
		header('Location: SimpleViewerAdmin.php');
		exit;
	}

// The user isn't authenticated yet.
} else {
	$_SESSION['is_authed'] = false;

	// The user is not logged in and tries to.
	if (isset($_POST['login']) && isset($_POST['username']) && isset($_POST['password']) && $_POST['username'] == $simpleViewer['adminUsername'] && md5($_POST['password']) == $simpleViewer['adminPassword']) {
		$authed 			   = true;
		$_SESSION['is_authed'] = true;
	}
}

// }}}
?>
<!DOCTYPE html 
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
<?php
// Emergency-Mode
if ($simpleViewer['emergency'] == 'true') {
?>
		<title>SimpleViewerAdmin :: Emergency mode</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<?php $temp = getConfig('SimpleViewerConfig.xml'); ?>
		<meta name="Powered-By" content="SimpleViewerAdmin <?php echo $temp['version']; ?>" />
		<link rel="stylesheet" type="text/css" href="../SimpleViewerAdmin/SimpleViewerCss.php" />
		<style type="text/css">
		<!--
		BODY.admin DIV#content {
			width: 685px;
			position: absolute;
			left: 0;
		}
		
		BODY.admin DIV#content TABLE TR TH { font-weight: normal; font-style: italic; color: #666; text-align: left; vertical-align: top; }
		BODY.admin DIV#content TABLE TR TD UL { padding: 0 0 0 1.2em; margin: 0; }
		BODY.admin FORM {
			padding: 10px;
			background-color: #fff;
			border-top: 5px solid #eee;
			width: 685px;
			position: absolute;
			left: 0;
		}
		BODY.admin FIELDSET { border: 0; margin: 0; }
		BODY.admin LEGEND { font-weight: bold; color: #666; }
		-->
		</style>
	</head>
	<body class="admin" id="mainWindow">
		<div id="header" onclick="window.location.replace('./SimpleViewerAdmin.php');">
			<h1>SimpleViewerAdmin :: Emergency mode</h1>
		</div>
		<div id="content">
			<table>
				<tr>
					<th width="35%">Installed PHP-Version (>= 4.1.x):</th>
					<td width="65%"><?php echo phpversion(); ?></td>
				</tr>
				<tr>
					<th>Server API:</th>
					<td><?php echo ucfirst(php_sapi_name()); ?></td>
				</tr>
				<tr>
					<th>Server Version:</th>
					<td><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
				</tr>
				<tr>
					<th>php.ini Variables:</th>
					<td>
						<ul>
							<li>enable_dl="<?php echo ini_get('enable_dl'); ?>" (<em>Recommended: 1</em>)</li>
							<li>file_uploads="<?php echo ini_get('file_uploads'); ?>" (<em>Recommended: 1</em>)</li>
							<li>max_execution_time="<?php echo ini_get('max_execution_time'); ?>" (<em>Recommended: 30</em>)</li>
							<li>post_max_size="<?php echo ini_get('post_max_size'); ?>" (<em>Recommended: 8M</em>)</li>
							<li>register_globals="<?php echo ((ini_get('register_globals') == 0) ? 'Off' : 'On'); ?>" (<em>Recommended: Off</em>)</li>
							<li>safe_mode="<?php echo ((ini_get('safe_mode') == 0) ? 'Off' : 'On'); ?>" (<em>Mandatory: Off</em>)</li>
							<li>short_open_tag="<?php echo ((ini_get('short_open_tag') == 0) ? 'Off' : 'On'); ?>" (<em>Recommended: Off</em>)</li>
							<li>session.gc_maxlifetime="<?php echo ini_get('session.gc_maxlifetime'); ?>" (<em>Default: 1800</em>)</li>
							<li>session.use_cookies="<?php echo ini_get('session.use_cookies'); ?>" (<em>Recommended: 1</em>)</li>
							<li>session.use_trans_sid="<?php echo ini_get('session.use_trans_sid'); ?>" (<em>Recommended: 1</em>)</li>
							<li>upload_max_filesize="<?php echo ini_get('upload_max_filesize'); ?>" (<em>Recommended: 2M</em>)</li>
							<li>variables_order="<?php echo ini_get('variables_order'); ?>" (<em>Recommended: GPCS</em>)</li>
						</ul>
					</td>
				</tr>
				<tr>
					<th>Loaded PHP-Extensions:</th>
					<td>
					<?php
						$extArr = get_loaded_extensions();
						sort($extArr);
						foreach ($extArr as $k => $v) {
							echo $v . (($k != count($extArr) - 1) ? ', ' : '');
						}
					?>
						<br />(<em>PHP-exttensions that have to be available at least: GD and XML.</em>)
					</td>
				</tr>
				<tr>
					<th>GD-Extension details:</th>
					<td>
					<?php
						$gdInfo = checkGD();
						echo '<ul>';
						echo '<li>Version ' . (($gdInfo['version'] != 0) ? $gdInfo['version'] : 'Not installed') . ' (<em>Recommended: Version 2</em>)</li>';
						foreach ($gdInfo['formats'] as $k => $v) {
							echo '<li>' . strtoupper($k) . ' Support: ' . (($v == 1) ? 'Enabled' : 'Not available')  . ' (<em>Recommended: Enabled</em>)</li>';
						}
						echo '</ul>';
					?>
					</td>
				</tr>
				<tr>
					<th>SimpleViewerAdmin-Functions:</th>
					<td>
					<?php
						$funcArr = get_defined_functions();
						echo '<ul>';
						foreach ($funcArr['user'] as $k => $v) {
							echo '<li>' . $v . '()</li>';
						}
						echo '</ul>';
					?>
					</td>
				</tr>
			</table>
			<?php
			$dirList = getDirList('./', 2, false, 'dirs');
			if (count($dirList['List']) != 0) {
			?>
			<form action="../SimpleViewerAdmin/SimpleViewerAdmin.php" name="emDelForm" method="get">
				<fieldset>
					<legend>Emergency album deletion</legend>
					<p>
						In case you borked an album, here's the opportunity to 
						delete it and its contents by hand.
					</p>
					<p>
						The dropdown contains the directory names of the albums 
						created. Choose the one, you wish to get rid of, and hit 
						the button (only once please).
					</p>
					<?php
						$statusStr 	 = 'Nothing deleted yet.';
						$statusColor = 'silver';
						if (!empty($_GET['emDelBtn']) && is_dir($_GET['albumDropdown'])) {
							if (deleteDir($_GET['albumDropdown'])) {
								$statusStr	 = 'The directory "<em>' . $_GET['albumDropdown'] . '</em>" was deleted.';
								$statusColor = 'green';
							} else {
								$statusStr 	 = 'The directory "<em>' . $_GET['albumDropdown'] . '</em>" cannot be deleted.';
								$statusColor = 'red';
							}
						}
					?>
					<p>Status: <span style="color: <?php echo $statusColor; ?>;"><?php echo $statusStr; ?></span></p>
					<p>
						<select name="albumDropdown" size="1">
						<?php
							$dirList = getDirList('./', 2, false, 'dirs');
							foreach ($dirList['List'] as $k => $v) {
								echo '<option value="' . $v . '">' . $v . '</option>';
							}
						?>
						</select>
						<input type="submit" name="emDelBtn" value="Delete it!" />
					</p>
				</fieldset>
			</form>
			<?php
			}
			?>
		</div>
<?php
} else {
?>
		<title><?php echo $simpleViewer['adminTitle']; ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<?php $temp = getConfig('SimpleViewerConfig.xml'); ?>
		<meta name="Powered-By" content="SimpleViewerAdmin <?php echo $temp['version']; ?>" />
		<link rel="stylesheet" type="text/css" href="../SimpleViewerAdmin/SimpleViewerCss.php" />
		<script language="javascript" type="text/javascript">
		<!--
			// {{{ checkConfigForm()
			
			/**
			* The Configuration form must never be submitted with text fields left empty.
			* 
			* @return	boolean
			* @access	public
			* @author	Christian Machmeier
			*/
			function checkConfigForm()
			{
				var error = false;
				
				for (i = 0; i < document.getElementById('configForm').length; i++) {
					if (document.getElementById('configForm').elements[i].value == '') {
						error = true;
						if ((document.getElementById('configForm').elements[i].name == 'configData[extraText]')
							|| (document.getElementById('configForm').elements[i].name == 'configData[welcomeText]')
							|| (document.getElementById('configForm').elements[i].name == 'configData[title]')) {
							error = false;
						}
					}
					
				}
				
				if (error) {
					alert('Error!\nThe configuration can only be saved with all variables filled.');
					return false;
				}
				
				return true;
			}
			
			// }}}
			// {{{ editImage()
			
			/**
			* Create and open the editImageWindow.
			* 
			* @param	albumName
			* @param	imageName
			* @access	public
			* @author	Christian Machmeier
			*/
			function editImage(albumName, imageName)
			{
				// Calculate the window size.
				windowWidth  = 525;
				windowHeight = 195;
				
				// Calculate the window position.
				x = (screen.width - windowWidth - 15);
				y = 45;
				
				if (typeof editImageWindow != 'undefined') {
					if (!editImageWindow.closed) {
						editImageWindow.close();
					}
				}
								
				// Init the editImageWindow.
				editImageWindow = window.open("SimpleViewerEditImage.php?albumName=" + albumName + "&imageName=" + imageName + "","imageWindow","width=" + windowWidth + ",height=" + windowHeight + ",left=" + x + ",top=" + y + ",menubar=no,resizable=no,scrollbars=no,status=no,toolbar=no");
				editImageWindow.focus();
				
			}
			
			// }}}
			
			// Set the name of the main window. (used as target in editImageWindow)
            window.name = 'mainWindow';
        //-->
        </script>
	</head>
	<body class="admin" id="mainWindow">
		<div id="header" onclick="window.location.replace('./SimpleViewerAdmin.php');">
			<h1><?php echo $simpleViewer['adminTitle']; ?></h1>
		</div>
<?php
	// The user is authenticated.
	if ($authed) {
?>
		<div id="content">
			<?php
			// Check whether there are status messages to be displayed.
			if (!empty($_SESSION['statusMsg'])) {
				
				// Display them.
				echo '
					<h4 class="label" style="font-style: italic; color: #06c;">Notice(s)</h4>
					<div style=" margin-bottom: 20px;">
						<ul>
				';
				foreach ($_SESSION['statusMsg'] as $k => $v) {
					echo '<li>' . $v . '</li>';
				}
				echo '
						</ul>
					</div>
				';
				
				// Reset the container for the next round.
				if (!isset($_GET['redirect'])) {
					$_SESSION['statusMsg'] = array();
				}
			}
			
			// Start processing of page content.
			if (!empty($_GET['file'])) {
				
				// Handle albums.
				if ($_GET['file'] == 'album') {
					
					// Set the current working directory.
					setCurrentDir(!empty($_GET['name']) ? $_GET['name'] : '');
					
					// Switch the requested action.
					if (!empty($_GET['action'])) {
						switch ($_GET['action']) {
						
						// Add an album.
						case 'add':
							addItem('album', $name = (!empty($_GET['name']) ? $_GET['name'] : ''));
							if (!empty($_POST['name'])) {
								echo '
									<script language="javascript" type="text/javascript">
									<!--
									window.location.replace("?action=edit&file=album&name=' . convertStr($_POST['name'], false, true) . '");
									//-->
									</script>
								';
							}
							break;
							
						// Edit an album.
						case 'edit':
							if (!empty($_GET['name'])) {
								
								$name 		   = $_GET['name'];
								$renameSuccess = true;
								
								if (isset($_POST['saveConfiguration'])) {
									// Rename the album in the filesystem after 
									// the form was submitted.
									$renameSuccess = renameItem('album', $_POST['configData']['title']);
									
									if ($renameSuccess) {
										$name = convertStr($_POST['configData']['title'], false, true);
									}
									echo '
										<script language="javascript" type="text/javascript">
										<!--
										window.location.replace("?action=edit&file=album&name=' . $name . '");
										//-->
										</script>
									';
								}
								
								// Get the config data for the current album.
								$albumConfig = getConfig($_SESSION['currentDir'] . 'imageData.xml');
								echo '<h4 class="label">Album <em>' . ((isPhpOfVersion(5) == -1) ? utf8_encode($albumConfig['title']) : $albumConfig['title']) . '</em></h4>';
								
								// Move an image as requested.
								moveImage();
								
								// Print the summary of all images within the 
								// current album.
								printSummary($name);
								addItem('image', $name);
								
								// Display the options.
								echo '<h3>Options</h3>';
								configure('album', $renameSuccess);
								deleteItem('album', $name);
								
							} else {
								$simpleViewer['error'] = true;
								$simpleViewer['errorMsg'] .= "No album name specified.\n";
							}
							break;
							
						// Delete an album.
						case 'delete':
							if (!empty($_GET['name'])) {
								deleteItem('album', $_GET['name']);
								if (isset($_POST['delete'])) {
									echo '
										<script language="javascript" type="text/javascript">
										<!--
										window.location.replace("SimpleViewerAdmin.php");
										//-->
										</script>
									';
								}
							} else {
								$simpleViewer['error'] = true;
								$simpleViewer['errorMsg'] .= "No album name specified.\n";
							}
							break;
							
						}
						
					// No action action argument provided.
					} else {
						$simpleViewer['error'] = true;
					}
				}
				
				// Handle images
				if ($_GET['file'] == 'image') {
					
					// Switch the requested action.
					if (!empty($_GET['action'])) {
						switch ($_GET['action']) {
							
						// Add an image.
						case 'add':
							addItem('image', $name = (!empty($_GET['name']) ? $_GET['name'] : ''));
							echo '
								<script language="javascript" type="text/javascript">
								<!--
								window.location.replace("?action=edit&file=album&name=' . substr($_SESSION['currentDir'], 2, -1) . '");
								//-->
								</script>
							';
							break;
							
						// Edit an image.
						case 'edit':
							if (isset($_POST['saveImageData'])) {
								$captionText = stripslashes($_POST['imageCaption']);
								if (!empty($_POST['autoCaption'])) {
									$tmpArr   	 = explode('.', $_POST['imageName']);
									$imageName	 = $tmpArr[0] . '.jpg';
									$captionText = '<a href="' . $_SESSION['currentDir'] . 'images/' . $imageName . '" target="_blank"><u>Open image in new window</u></a>';
								}
								saveImageToXML($_POST['imageName'], $captionText);
							}
							echo '
								<script language="javascript" type="text/javascript">
								<!--
								if (fref = open("", "imageWindow")) {
									fref.close();
								}
								window.location.replace("?action=edit&file=album&name=' . substr($_SESSION['currentDir'], 2, -1) . '");
								//-->
								</script>
							';
							break;
							
						// Delete an image.
						case 'delete':
							if (!empty($_GET['name'])) {
								deleteItem('image', $_GET['name']);
								echo '
									<script language="javascript" type="text/javascript">
									<!--
									window.location.replace("?action=edit&file=album&name=' . substr($_SESSION['currentDir'], 2, -1) . '");
									//-->
									</script>
								';
							} else {
								$simpleViewer['error'] = true;
								$simpleViewer['errorMsg'] .= "No image name specified.\n";
							}
							break;
						}
						
					// No action action argument provided.
					} else {
						$simpleViewer['error'] = true;
					}
				}
			}
			
			// Handle the gallery configuration.
			if (!empty($_GET['configureGallery'])) {
				echo '<h4 class="label">Edit gallery configuration</h4>';
				configure('gallery');
				if (isset($_POST['saveConfiguration'])) {
					echo '
						<script language="javascript" type="text/javascript">
						<!--
						window.location.replace("?configureGallery=1");
						//-->
						</script>
					';
				}
			}
			
			// An error occured somewhere above. Print a notice and show an error description
			if ($simpleViewer['error']) {
				echo '<b>Something bad happened!</b>
					<br />One or more errors occured.<br />
					<br /><i>Notice(s)</i>
					<br />' . nl2br($simpleViewer['errorMsg']);
				
			// This is the default case.
			// After the user is logged in, I show him the main page containing some introductory text.
			} else {
				if (empty($_SERVER['QUERY_STRING'])) {
					echo '
						<h4 class="label">' . $simpleViewer['adminTitle'] . '</h4>
						<em>Documentation</em>
						<ul>
							<li>Read the <a href="http://www.redsplash.de/projects/simplevieweradmin/#Documentation" target="_blank">Documentation &uarr;</a>, if you have not already done so.</li>
						</ul>
						<em>Credits</em>
						<ul>
							<li>SimpleViewer by <a href="http://www.airtightinteractive.com/simpleviewer/" target="_blank">Airtight &uarr;</a></li>
							<li>Fonts by <a href="http://www.miniml.com/" target="_blank">Miniml &uarr;</a></li>
							<li>Preloader class by <a href="http://www.helpqlodhelp.com/blog/" target="_blank">Bokel &uarr;</a></li>
							<li>Flash detection script by <a href="http://www.dithered.com/" target="_blank">Dithered &uarr;</a></li>
							<li>SimpleViewerAdmin by <a href="http://www.redsplash.de/" target="_blank">Christian Machmeier &uarr;</a></li>
						</ul>
						<em>Legal</em>
						<ul>
							<li>This work is licensed under a <a href="http://creativecommons.org/licenses/by-nc-sa/1.0/" target="_blank">Creative Commons License &uarr;</a>.</li>
						</ul>
						<em>Version</em>
						<ul>
							<li>
								The currently installed version of <a href="http://www.redsplash.de/projects/simplevieweradmin/" target="_blank">SimpleViewerAdmin &uarr;</a> 
								is ' . $simpleViewer['version'] . '. Please visit <a href="http://www.redsplash.de/projects/simplevieweradmin/" target="_blank">the project\'s website &uarr;</a>
								to make sure you have installed the latest version.
							</li>
						</ul>
					';
				}
			}
			?>
		</div>
		<div id="menue">
			<h4 class="label">Gallery</h4>
			<ul>
				<li><a href="../SimpleViewerAdmin/?configureGallery=1">Configuration</a></li>
			</ul>
			<h4 class="label">Albums <a href="../SimpleViewerAdmin/?action=add&amp;file=album" title="Add an album!">(+)</a></h4>
			<?php
				// Get all albums.
				$dirList 		   = getDirList('./', '', false, 'dirs');

				// Get all published albums.
				$publishedAlbums   = getPublishedAlbums();
				$publishedAlbums   = $publishedAlbums['List'];
				
				// Compute the unpublished albums.
				$unpublishedAlbums = array_diff($dirList['List'], $publishedAlbums);
				natcasesort($unpublishedAlbums);
				
				// Show something?
				$showPublishedAlbums   = ((count($publishedAlbums) != 0)   ? true : false);
				$showUnpublishedAlbums = ((count($unpublishedAlbums) != 0) ? true : false);
				
				// Display both lists appropriately.
				if ($showPublishedAlbums) {
			?>
					<h5>Published</h5>
					<ul>
					<?php
					foreach ($publishedAlbums as $k => $v) {
						$xmlData = getConfig('./' . $v . '/imageData.xml');
						echo '<li><a href="?action=edit&amp;file=album&amp;name=' . $v . '">' . ((isPhpOfVersion(5) == -1) ? utf8_encode($xmlData['title']) : $xmlData['title']) . "</a></li>\n";
					}
					?>
					</ul>
			<?php
				}
				
				if ($showUnpublishedAlbums) {
			?>
					<h5>Unpublished</h5>
					<ul>
					<?php
					foreach ($unpublishedAlbums as $k => $v) {
						$xmlData = getConfig('./' . $v . '/imageData.xml');
						echo '<li><a href="?action=edit&amp;file=album&amp;name=' . $v . '">' . ((isPhpOfVersion(5) == -1) ? utf8_encode($xmlData['title']) : $xmlData['title']) . "</a></li>\n";
					}
					?>
					</ul>
			<?php
				}
				
				if (!$showPublishedAlbums && !$showUnpublishedAlbums) {
					echo '<ul><li>None</li></ul>';
				}
			?>
			<h4 class="label">Miscellaneous</h4>
			<ul>
				<li><a href="http://www.redsplash.de/projects/simplevieweradmin/#Documentation" target="_blank">Documentation &uarr;</a></li>
				<li><a href="../SimpleViewerAdmin/index.php">Back to Gallery</a></li>
				<li><a href="../SimpleViewerAdmin/?logout=1">Logout</a></li>
			</ul>
		</div>
<?php
	// The user is not authenticated -> Display the login form.
	} else {
?>
		<div id="login">
			<form action="../SimpleViewerAdmin/SimpleViewerAdmin.php" method="post">
				<h4 class="label" style="margin-bottom: 5px;">Login</h4>
				<p><input type="text" name="username" value="username" maxlength="8" style="margin-bottom: 5px;" onfocus="this.value='';" /></p>
				<p><input type="password" name="password" value="password" maxlength="8" style="margin-bottom: 5px;" onfocus="this.value='';" /></p>
				<p><input type="submit" name="login" value="Login" /></p>
			</form>
		</div>
<?php
	}
}
?>
	</body>
</html>
