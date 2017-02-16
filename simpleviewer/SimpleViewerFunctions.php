<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
//
// SimpleViewer - Functions
// ---------------------------------------------------------------------
// This file contains a huge collection of functions that are used 
// within your SimpleViewer gallery and the AdminInterface.
//
// 
// Note:
// This file is the main function library.
//
// ---------------------------------------------------------------------
// Copyright (c) 2004,2005 Christian Machmeier
// ---------------------------------------------------------------------
//
// $Id: SimpleViewerFunctions.php,v 1.9 2004/06/19 10:05:46 grisu Exp $
//

// {{{

/**
* Test the major version number of the PHP installation in use.
* 
* @return	int
* @return	int		-1, 0, 1		lesser, equal to, greater
* @access	public
* @author	Christian Machmeier
*/
function isPhpOfVersion($version = 5)
{
	$curVersion = explode('.', phpversion());
	$curVersion = $curVersion[0];
	if ($curVersion < $version) {
		return -1;
	} else if ($curVersion == $version) {
		return 0;
	} else {
		return 1;
	}
}

// }}}
// {{{ getPublishedAlbums()

/**
* Retrieve a list all published albums.
* 
* @return	mixed
* @access	public
* @author	Roy Cornelissen
*/
function getPublishedAlbums()
{
	$dirList = getDirList('./', '', false, 'dirs', true);
	return $dirList;
}

// }}}
// {{{ isPublished()

/**
* Indicates whether or not an album is published.
* 
* @param	string		$albumName		name of the album to check
* @return	boolean
* @access	public
* @author	Roy Cornelissen
*/
function isPublished($albumName)
{
	if (empty($albumName)) {
		die('Give me an album name to check.');
	}
	
	$config = getConfig($albumName . '/imageData.xml');
	return $config["isPublished"] == 'true';
}

// }}}
// {{{ getDirList()

/**
* Retrieve a list of directories in a recursive manner.
* 
* @param	string		$dir		name of directory to parse
* @param	int			$pos		position of file cursor
* @param	boolean		$recursive	specify whether you want to parse 
*									subdirectories or not
* @param	string		$mode		'all', 'dirs', 'files'.
* @return	mixed
* @access	public
* @author	Christian Machmeier
*/
function getDirList($dir, $pos = 2, $recursive = true, $mode = 'all', $checkPublished = false)
{
	$dirCounter = 0;
	$dirList	= array();
	$tmpArray	= array();
    $dirHandle  = @opendir($dir);
	
    while ($file = @readdir($dirHandle)) {
        if (eregi("^\.{1,2}$", $file)) {
            continue;
        }
		
        if (is_dir($dir.$file) && $file != 'images' && $file != 'thumbs' && $file != 'CVS' && $file != 'img') {
			if ($mode == 'all' || $mode == 'dirs') {
				if ($checkPublished) {
					if (isPublished($file)) {
						$dirCounter++;
						$dirList[] = $file;
					}
				} else {
					$dirCounter++;
					$dirList[] = $file;
					if ($recursive) {
						getDirList($dir . $file . '/', $pos + 3);
					}				
				}
			}
        } else {
			if ($mode == 'all' || $mode == 'files') {
				if ($file != 'CVS') {
					$dirCounter++;
					$dirList[] = $file;
				}
			}
		}
    }
    @closedir($dirHandle);

	// This is nasty, but PHP4 required it.. bah!
	natcasesort($dirList);
	foreach ($dirList as $k => $v) {
		$tmpArray[] = $v;
	}

	return array('Number' => $dirCounter, 'List' => $tmpArray);
}

// }}}
// {{{ printSummary()

/**
* Print the summary table that displays all images of an album.
* 
* @param	string		$album		Name of the album tp print 
*									the summary from.
* @access	public
* @author	Christian Machmeier
*/
function printSummary($album)
{
	global $simpleViewer;
	
	if (empty($album)) {
		return false;
	}
	
	$i			= 0;
	$xmlData	= array();
	$sourceData = parseXML($_SESSION['currentDir'] . 'imageData.xml');
	
	foreach ($sourceData as $k => $v) {
		if ($v['tag'] == 'NAME') {
			$xmlData[$i]['name'] = $v['value'];
		}
		if ($v['tag'] == 'CAPTION') {
			$xmlData[$i]['caption'] = (!empty($v['value']) ? $v['value'] : '');
			$i++;
		}
	}
	
	$numberOfImages = count($xmlData);
	
	echo "\n<table cellpadding=\"0\" cellspacing=\"2\" border=\"0\" id=\"summary\">\n<tr>\n";
	
	if ($numberOfImages == 0) {
		echo '<td><em>This album contains no images.</em></td>';
	} else {
		$cellCounter = 1;
		
		foreach ($xmlData as $k => $v) {
			echo "\n<td class=\"thumb\">\n<div>\n";
			// Display the "backward"  button for every image execpt the first.
			if ($k != 0) {
				echo "
					<form name=\"\" method=\"post\" action=\"?action=edit&amp;file=album&amp;name=" . $album . "\" style=\"display: inline;\">
						<input type=\"hidden\" name=\"imagekey\" value=\"" . $k . "\" />
						<input type=\"image\" name=\"imagebackward\" src=\"./img/ArrowLeft.gif\" title=\"Move image backward\" />
					</form>
				";
			}
			
			// Display the "forward"  button for every image execpt the last.
			if ($k != $numberOfImages-1) {
				echo "
					<form name=\"\" method=\"post\" action=\"?action=edit&amp;file=album&amp;name=" . $album . "\" style=\"display: inline;\">
						<input type=\"hidden\" name=\"imagekey\" value=\"" . $k . "\" />
						<input type=\"image\" name=\"imageforward\" src=\"./img/ArrowRight.gif\" title=\"Move image forward\" />
					</form>
				";
			}
			
			echo "
					</div>
					<img src=\"" . $_SESSION['currentDir'] . "thumbs/" . $v['name'] . "\" alt=\"\" />
					<div>
					<a href=\"#\" onclick=\"editImage('" . $album . "', '" . $v['name'] . "'); return false;\" title=\"Edit caption\"><img src=\"./img/Edit.gif\" alt=\"Edit caption\" border=\"0\" style=\"background: none; padding: 0; border: 0; margin: 0;\" /></a>
					<form method=\"post\" name=\"delimageform" . ++$k . "\" action=\"?action=delete&amp;file=image&amp;name=" . $v['name'] . "&amp;redirect\" style=\"display: inline;\">
						<input type=\"hidden\" name=\"delete\" />
						<input type=\"image\" name=\"delete\" src=\"./img/Delete.gif\" title=\"Delete image!\" onclick=\"return confirm('Are you sure you want to delete this image?');\" />
					</form>
					</div>
				</td>
			";
			
			if (($cellCounter % 5) == 0) {
				echo "</tr><tr>\n";
				$cellCounter = 0;
			}
			$cellCounter++;
			
		}
		
		if ($cellCounter != 1) {
			for ($i = 0; $i <= 5 - $cellCounter; $i++) {
				echo "<td class=\"thumb\">&nbsp;</td>\n";
			}
		}		
	}
	echo "</tr>\n</table>\n";
}

// }}}
// {{{ convertStr()

/**
* Convert a string to a non-ambigious string.
* 
* @param	string		$str		String to convert
* @return	string		$str		Converted string
* @access	public
* @author	Christian Machmeier
*/
function convertStr($str = null, $markup = false, $albumCreation = false)
{
	if (empty($str)) {
		return $str;
	}
	
	$str = utf8_decode($str);
	
    // The "$markup" parameter is set, when a caption is to be stored.
	if ($markup) {
		$str = strip_tags($str, '<a>,<br>,<b>,<u>,<i>,<font>');
	// So this parameter isn't set, when an album is created, therefore
	// the album name may neither contain any special characters, nor any
	// markup.
	} else {	
		$str = strip_tags($str);
	}
	
    // Array to cover all special letters from ISO-8859-1, used for captions and album titles.
    $replaceChars = array(
    	'&' => '&amp;',
        '¡' => '&#161;', '¢' => '&#162;', '£' => '&#163;', '¤' => '&#164;',
        '¥' => '&#165;', '¦' => '&#166;', '§' => '&#167;', '¨' => '&#168;',
        '©' => '&#169;', 'ª' => '&#170;', '«' => '&#171;', '¬' => '&#172;',
        '­' => '&#173;', '®' => '&#174;', '¯' => '&#175;', '°' => '&#176;',
        '±' => '&#177;', '²' => '&#178;', '³' => '&#179;', '´' => '&#180;',
        'µ' => '&#181;', '¶' => '&#182;', '·' => '&#183;', '¸' => '&#184;',
        '¹' => '&#185;', 'º' => '&#186;', '»' => '&#187;', '¼' => '&#188;',
        '½' => '&#189;', '¾' => '&#190;', '¿' => '&#191;', 'À' => '&#192;',
        'Á' => '&#193;', 'Â' => '&#194;', 'Ã' => '&#195;', 'Ä' => '&#196;',
        'Å' => '&#197;', 'Æ' => '&#198;', 'Ç' => '&#199;', 'È' => '&#200;',
        'É' => '&#201;', 'Ê' => '&#202;', 'Ë' => '&#203;', 'Ì' => '&#204;',
        'Í' => '&#205;', 'Î' => '&#206;', 'Ï' => '&#207;', 'Ð' => '&#208;',
        'Ñ' => '&#209;', 'Ò' => '&#210;', 'Ó' => '&#211;', 'Ô' => '&#212;',
        'Õ' => '&#213;', 'Ö' => '&#214;', '×' => '&#215;', 'Ø' => '&#216;',
        'Ù' => '&#217;', 'Ú' => '&#218;', 'Û' => '&#219;', 'Ü' => '&#220;',
        'Ý' => '&#221;', 'Þ' => '&#222;', 'ß' => '&#223;', 'à' => '&#224;',
        'á' => '&#225;', 'â' => '&#226;', 'ã' => '&#227;', 'ä' => '&#228;',
        'å' => '&#229;', 'æ' => '&#230;', 'ç' => '&#231;', 'è' => '&#232;',
        'é' => '&#233;', 'ê' => '&#234;', 'ë' => '&#235;', 'ì' => '&#236;',
        'í' => '&#237;', 'î' => '&#238;', 'ï' => '&#239;', 'ð' => '&#240;',
        'ñ' => '&#241;', 'ò' => '&#242;', 'ó' => '&#243;', 'ô' => '&#244;',
        'õ' => '&#245;', 'ö' => '&#246;', '÷' => '&#247;', 'ø' => '&#248;',
        'ù' => '&#249;', 'ú' => '&#250;', 'û' => '&#251;', 'ü' => '&#252;',
        'ý' => '&#253;', 'þ' => '&#254;', 'ÿ' => '&#255;'
    );
    
	if ($albumCreation) {
		// Array mapping all invalid characters for the directory name.
		$replaceChars = array (
    	    ' ' => '_',  '!' => '',   '"' => '',   '$' => '',   '%'  => '',
			'&' => '',   '/' => '',   '(' => '',   ')' => '',   '='  => '',
			'?' => '',   '#' => '',   '{' => '',   '[' => '',   ']'  => '',
			'}' => '',   '+' => '',   '*' => '',   '-' => '',   '~'  => '',
			':' => '',   ',' => '',   ';' => '',   '<'  => '',
			'>' => '',   '|' => '',   '@' => '',   '€' => '',   '\\' => '',
			'^' => '',   '°' => '',   '¡' => '',   '¢' => '',   '£'  => '',
			'¤' => '',   '¥' => '',   '¦' => '',   '§' => '',   '¨'  => '',
        	'©' => '',   'ª' => '',   '«' => '',   '¬' => '',   '­'  => '',
        	'®' => '',   '¯' => '',   '°' => '',   '±' => '',   '²'  => '', 
        	'³' => '',   '´' => '',   'µ' => '',   '¶' => '',   '·'  => '',
        	'¸' => '',   '¹' => '',   'º' => '',   '»' => '',   '¼'  => '',
    	    '½' => '',   '¾' => '',   '¿' => '',   'À' => 'A',  'Á'  => 'A', 
    	    'Â' => 'A',  'Ã' => 'A',  'Ä' => 'Ae', 'Å' => 'A',  'Æ'  => 'Ae',
    	    'Ç' => 'C',  'È' => 'E',  'É' => 'E',  'Ê' => 'E',  'Ë'  => 'E',
    	    'Ì' => 'i',  'Í' => 'i',  'Î' => 'i',  'Ï' => 'i',  'Ð'  => '',
	        'Ñ' => 'N',  'Ò' => 'O',  'Ó' => 'O',  'Ô' => 'O',  'Õ'  => 'O',
	        'Ö' => 'Oe', '×' => '',   'Ø' => 'O',  'Ù' => 'U',  'Ú'  => 'U',
	        'Û' => 'U',  'Ü' => 'Ue', 'Ý' => 'Y',  'Þ' => '',   'ß'  => 'ss',
	        'à' => 'a',  'á' => 'a',  'â' => 'a',  'ã' => 'a',  'ä'  => 'ae',
        	'å' => 'a',  'æ' => 'ae', 'ç' => 'c',  'è' => 'e',  'é'  => 'e',
        	'ê' => 'e',  'ë' => 'e',  'ì' => 'i',  'í' => 'i',  'î'  => 'i',
        	'ï' => 'i',  'ð' => '',   'ñ' => 'n',  'ò' => 'o',  'ó'  => 'o', 
        	'ô' => 'o',  'õ' => 'o',  'ö' => 'oe', '÷' => '',   'ø'  => 'o',
    	    'ù' => 'u',  'ú' => 'u',  'û' => 'u',  'ü' => 'ue', 'ý'  => 'y',
    	    'þ' => '',   'ÿ' => 'y',  "'" => ''
        );
    }
    
	// Either which one of the above arrays is specified to be processed, the
	// given string of characters will now be converted and returned afterwards.
	foreach ($replaceChars as $k => $v) {
		$str = str_replace($k, $v, $str);
	}
	
	if ($markup) {
		utf8_encode($str);
	}
	
	return $str;
}

// }}}
// {{{ deMarkup()

/**
* Takes a string, converts its markup.
*
* @param	string		$str		String to convert
* @return	string		$str		converted string
* @access	public
* @author	Christian Machmeier
*/
function deMarkup($str=null)
{
	$str = str_replace('<', '&lt;',	  $str);
	$str = str_replace('>', '&gt;',   $str);
	$str = str_replace('"', '&quot;', $str);
	return $str;
}

// }}}
// {{{ setCurrentDir()

/**
* Sets the current directory after approval as working directory.
* 
* @param	string		$name		Name of the directory to set.
* @access	public
* @author	Christian Machmeier
*/
function setCurrentDir($name)
{
	$_SESSION['currentDir'] = './';
	
	if (!empty($name)) {
		
		if (!preg_match('/\.{1,2}/', $name)) {
			
			if (!stristr($_SESSION['currentDir'], $name)) {
				$_SESSION['currentDir'] = './' . $name . '/';
			}
			
		}
		
	}
}

// }}}
// {{{ addItem()

/**
* Display the form to store and handle the logic of the storage of an 
* item (album or image) in the file system.
* 
* @param	string		$mode		Item is an image or an album.
* @param	string		$name		New/existing image/album.
* @access	public
* @author	Christian Machmeier
*/
function addItem($mode = 'album', $name = null)
{
	global $simpleViewer;
	$gdInfo = checkGD();
	
	// The form was submitted
	if (isset($_POST['save'])) {
		// Convert the album name and create the corresponding directories.
		if ($mode == 'album') {
		    
			$old_umask 	   = @umask(0);
			$albumDirName  = convertStr($_POST['name'], false, true);
			$albumRealName = convertStr($_POST['name'], true, false);
			
			if (@mkdir($_SESSION['currentDir'] . $albumDirName, 0777)) {
				
				@mkdir($_SESSION['currentDir'] . $albumDirName . '/images', 0777);
				@mkdir($_SESSION['currentDir'] . $albumDirName . '/thumbs', 0777);
				$_SESSION['statusMsg'][] = 'The album ' . $albumRealName . ' was created.';
				
				// Attempt to create the XML file.
				if (!$fileHandle = @fopen($_SESSION['currentDir'] . $albumDirName . '/imageData.xml', 'w')) {
		        	$_SESSION['statusMsg'][] = 'Cannot open XML file.';
   				}
				
				// Replace the two empty attributes (imagePath and thumbPath) with their valued twins.
				$xmlTemplate = $simpleViewer['basicXMLTemplate'];
				$xmlTemplate = str_replace('imagePath=""', 'imagePath="' . $_SESSION['currentDir'] . $albumDirName . '/images/"', $xmlTemplate);
				$xmlTemplate = str_replace('thumbPath=""', 'thumbPath="' . $_SESSION['currentDir'] . $albumDirName . '/thumbs/"', $xmlTemplate);
				$xmlTemplate = str_replace('title=""', 'title="' . $albumRealName . '"', $xmlTemplate);
				
				// Write the basic template to the opened file.
				if (@fwrite($fileHandle, $xmlTemplate) === false) {
		        	$_SESSION['statusMsg'][] = 'Cannot write to XML file.';
				}
				
				// Write the file to disk.
				@fclose($fileHandle);
				
			} else {
				$_SESSION['statusMsg'][] = 'Could not create the requested directory structure.';
			}
            @umask($old_umask);
			
		// Save the image into the corresponding album directory.
		} else {
			
			// The submitted file could be found in memory.
			if (!empty($_FILES['file']['name'])) {
				
				$imageName = convertStr($_FILES['file']['name'], false, true);
				
				// Check for GD's availability.
				if ($gdInfo == false) {
					
					$_SESSION['statusMsg'][] = '
						Necessary GD image functions are not available.<br />
						You will waste harddisk capacity due to non thumbnail creation. Your original image is instead simply 
						copied as preview image. But beware: using this inadvisable approach causes	that your image preview 
						within your albums will be very slow.
					';
					
					// Copy image to its destination
					if (@copy($_FILES['file']['tmp_name'], $_SESSION['currentDir'] . 'images/' . $imageName)) {
						$_SESSION['statusMsg'][] = 'The image was saved.';
					} else {
						$_SESSION['statusMsg'][] = 'Could not save the image.';
					}
					
					// Copy the original image instead as thumbnail.
					if (@copy($_FILES['file']['tmp_name'], $_SESSION['currentDir'] . 'thumbs/' . $imageName)) {
						$_SESSION['statusMsg'][] = 'The preview image was saved.';
						
						$captionText = $_POST['name'];
						if (!empty($_POST['autoCaption'])) {
							$captionText = '<a href="' . $destFile . '" target="_blank"><u>Open image in new window</u></a>';
						}
									
						// Update the XML file.
						saveImageToXML($_SESSION['currentDir'] . 'images/' . $imageName, $captionText);
					} else {
						$_SESSION['statusMsg'][] = 'Could not save the preview image.';
					}
					
				// The necessary GD functions are available.
				} else {
					
					// Check the uploaded image's file type.
					if (stristr($_FILES['file']['type'], 'image')) {
						
						$format 	= '';
						$errorImage = false;
						
						if (stristr($_FILES['file']['type'], 'gif') && $gdInfo['formats']['gif'] == 1) {
							$format   = 'gif';
							$destFile = $_SESSION['currentDir'] . 'images/' . str_replace($format , 'jpg', $imageName);
						} else if (stristr($_FILES['file']['type'], 'jpeg') && $gdInfo['formats']['jpg'] == 1) {
							$format = 'jpg';
							$destFile = $_SESSION['currentDir'] . 'images/' . str_replace($format , 'jpg', $imageName);
						} else if (stristr($_FILES['file']['type'], 'png') && $gdInfo['formats']['png'] == 1) {
							$format = 'png';
							$destFile = $_SESSION['currentDir'] . 'images/' . str_replace($format , 'jpg', $imageName);
						} else {
							$errorImage = true;
						}
						
						// No error occured no upload.
						if (!$errorImage) {
							
							// Create the image.
							if (createImage($_FILES['file']['tmp_name'], $format, 'images', $destFile)) {
								
								$_SESSION['statusMsg'][] = 'The image was saved.';
								
								// Create the thumbnail.
								if (createImage($_FILES['file']['tmp_name'], $format, 'thumbs', str_replace('images', 'thumbs', $destFile))) {
									
									$_SESSION['statusMsg'][] = 'The preview thumbnail was created.';
									
									$captionText = $_POST['name'];
									if (!empty($_POST['autoCaption'])) {
										$captionText = '<a href="' . $destFile . '" target="_blank"><u>Open image in new window</u></a>';
									}
									
									// Update the XML file.
									saveImageToXML($_SESSION['currentDir'] . 'images/' . $imageName, $captionText);
									
								} else {
									$_SESSION['statusMsg'][] = 'The preview thumbnail could not be created.';
								}
								
							// The image could be created.
							} else {
								$_SESSION['statusMsg'][] = 'Could not save the image.';
							}
							
						// The uploaded image was of the wrong file type.
						} else {
							$_SESSION['statusMsg'][] = 'Images of type <i>' . $_FILES['file']['type'] . '</i> cannot be handled.';
						}
						
					// The uploaded file was of the wrong file type.
					} else {
						$_SESSION['statusMsg'][] = ' Files of type "<i>' . (!empty($_FILES['file']['type']) ? $_FILES['file']['type'] : 'unknown') . '</i>" cannot be processed.';
					}
					
				}
				
			// The submitted file could not be found in memory.
			} else {
				$_SESSION['statusMsg'][] = 'Give me an image you want to upload.';
			}
			
		}
		
	}
	
	// Display the form.
	echo '
		<form method="post" action="?action=add&amp;file=' . $mode . '&amp;name=' . $name . '" ' . ($mode == 'image' ? ' enctype="multipart/form-data"' : null) . '>
		<table cellpadding="1" cellspacing="1" border="0" width="100%">
			<tr>
				<td colspan="2"><h4 class="label">Add ' . ($mode == 'image' ? $mode :  'an ' . $mode) . '</h4></td>
			</tr>
			<tr>
				<td valign="top">Name</td>
				<td>
	';
	if ($mode == 'album') {
		echo '
					<input type="text" name="name" id="name" maxlength="255" value="" />
					<p>"<em>Please click the submit button only once.</em>"</p>
		';
	} else {
		echo '
					<input type="file" name="file" maxlength="255" />
				</td>
			</tr>
			<tr>
				<td>Caption</td>
				<td><input type="text" name="name" id="name" maxlength="255" value="" /></td>
			</tr>
			<tr>
				<td>Autolink image</td>
				<td><input type="checkbox" name="autoCaption" id="autoCaption" value="On" onclick="document.getElementById(\'name\').value=\'\';" />
		';
	}
	echo '
				</td>
			</tr>
			<tr>
				<td width="30%">&nbsp;</td>
				<td width="70%"><input type="submit" name="save" value="Save" /></td>
			</tr>
	';
	if ($mode == 'image') {
		echo '
				<tr>
					<td colspan="2">Note: 
		';
		if ($gdInfo == false) {
			echo 'You can only upload non-progressive JPEG images.';
		} else {
			echo 'You can upload the following file formats: ';
			$i = 0;
			foreach ($gdInfo['formats'] as $k => $v) {
				if ($v == 1) {
					if ($i != 0) { echo ', '; }
					echo '.' . $k;
					$i++;
				}
			}
		}
		echo '
					</td>
				</tr>
		';
	}
	echo '
		</table>
		</form>
	';
}

// }}}
// {{{ deleteItem()

/**
* Display the form to delete and handle the logic of the deletion of an 
* item (album or image) in the file system.
* 
* @param	string		$mode		Item is an image or an album.
* @param	string		$name		New/existing image/album.
* @return	boolean
* @access	public
* @author	Christian Machmeier
*/
function deleteItem($mode = 'album', $name = null)
{
	$error = false;
	
	// The form was submitted
	if (isset($_POST['delete'])) {
		
		// Delete the album directory recursively.
		if ($mode == 'album') {
			
			if (@deleteDir($_SESSION['currentDir'])) {
				$_SESSION['statusMsg'][] = "The album " . $_GET['name'] . " was deleted successfully.";
			} else {
				$_SESSION['statusMsg'][] = "Couldn't delete the album " . $_GET['name'];
			}
			
		// Delete the image from the corresponding album directory.
		} else {
			
			if (@unlink($_SESSION['currentDir'] . 'images/' . $name) && @unlink($_SESSION['currentDir'] . 'thumbs/' . $name)) {
				$_SESSION['statusMsg'][] = "The image " . $name . " was deleted successfully.";
				
				// Update the XML file.
				saveImageToXML($_SESSION['currentDir'] . 'images/' . $name, '');
			} else {
				$_SESSION['statusMsg'][] = "Couldn't delete the image " . $name;
			}
			
		}
		
	// Display the form.
	} else {
		echo '
			<form method="post" action="?action=delete&amp;file=' . $mode . '&amp;name=' . $name . '&amp;redirect">
			<table cellpadding="1" cellspacing="1" border="0" width="100%">
				<tr>
					<td colspan="2"><h4 class="label">Delete ' . $mode  . '</h4></td>
				</tr>
				<tr>
					<td width="30%">&nbsp;</td>
					<td width="70%"><input type="submit" name="delete" onclick="return confirm(\'Are you sure you want to delete this album?\');" value="Delete" /></td>
				</tr>
			</table>
			</form>
			<br /><br />
		';
	}
}

// }}}
// {{{ deleteDir()

/**
* Delete a directory and the files it's containing recursively.
* 
* @param	string		$dir		Name of directory to delete.
* @return	boolean
* @access	public
* @author	Christian Machmeier
*/
function deleteDir($dir)
{
	if (empty($dir)) {
		return false;
	}
	
	$handle = @opendir($dir);
	
	while (false !== ($FolderOrFile = @readdir($handle))) {
		
		if($FolderOrFile != '.' && $FolderOrFile != '..') {
			
			if(is_dir($dir . '/' . $FolderOrFile)) { 
				@deleteDir($dir . '/' . $FolderOrFile);
			} else {
				@unlink($dir . '/' . $FolderOrFile); 
			}
			
		}
		
	}
	
	@closedir($handle);
	
	if(@rmdir($dir)) {
		return true; 
	}
}

// }}}
// {{{ renameItem()

/**
* Display the form to rename/move and handle the logic of the renaming of
* an item (album or image) in the file system.
* 
* @param	string		$mode		Item is an image or an album.
* @param	string		$name		New/existing image/album.
* @return	boolean
* @access	public
* @author	Christian Machmeier
*/
function renameItem($mode = 'album', $name)
{
	$error = false;
	
	// Rename the album directory.
	if ($mode == 'album') {
		
		if (!empty($name)) {
			if (!empty($_GET['name']) && (convertStr($name, false, true) == $_GET['name'])) {
				return true;
			}
			
			if (!is_dir(convertStr($name, false, true))) {
				if (rename(substr($_SESSION['currentDir'], 0, -1), './' . convertStr($name, false, true))) {
					setCurrentDir(convertStr($name, false, true));
					return true;
				} else {
					$_SESSION['statusMsg'][] = "Couldn't rename the album " . $_GET['name'];
					return false;
				}
				
			} else {
				return false;
			}
		} else {
			$_SESSION['statusMsg'][] = "You should give me a new name to rename the album " . $_GET['name'] . ".";
			return false;
		}
		
	// Rename the image in the corresponding album directory.
	} else {
		/*
		* @todo	insert useful code here. ;)
		*/
		return false;
	}
}

// }}}
// {{{ gd_info()

/**
* The function "gd_info()" is available as of PHP version 4.3.0.
* The redefinition here does essentially the same as the native function.
* 
* @return	mixed		array
* @access	public
* @author	Christian Machmeier
*/
if (!function_exists('gd_info')) {

	function gd_info()
	{
		$array = array (
			'GD Version' 		 => '',
			'FreeType Support'	 => 0,
			'FreeType Support'	 => 0,
			'FreeType Linkage'	 => '',
			'T1Lib Support'		 => 0,
			'GIF Read Support'	 => 0,
			'GIF Create Support' => 0,
			'JPG Support'		 => 0,
			'PNG Support'		 => 0,
			'WBMP Support'		 => 0,
			'XBM Support'		 => 0
		);
		
		$gif_support = 0;
		
		ob_start();
		phpinfo(8);
		$info = ob_get_contents();
		ob_end_clean();
		
		foreach (explode("\n", $info) as $line) {
			
			if (strpos($line, 'GD Version') 		!== false) {
				$array['GD Version']		 = trim(str_replace('GD Version', '', strip_tags($line)));
			}
			
			if (strpos($line, 'FreeType Support')   !== false) {
				$array['FreeType Support']	 = trim(str_replace('FreeType Support', '', strip_tags($line)));
			}
			
			if (strpos($line, 'FreeType Linkage')   !== false) {
				$array['FreeType Linkage']	 = trim(str_replace('FreeType Linkage', '', strip_tags($line)));
			}
			
			if( strpos($line, 'T1Lib Support') 	   !== false) {
				$array['T1Lib Support']		 = trim(str_replace('T1Lib Support', '', strip_tags($line)));
			}
			
			if (strpos($line, 'GIF Read Support')   !== false) {
				$array['GIF Read Support']   = trim(str_replace('GIF Read Support', '', strip_tags($line)));
			}
			
			if (strpos($line, 'GIF Create Support') !== false) {
				$array['GIF Create Support'] = trim(str_replace('GIF Create Support', '', strip_tags($line)));
			}
			
			if (strpos($line, 'GIF Support') 	   !== false) {
				$gif_support				 = trim(str_replace('GIF Support', '', strip_tags($line)));
			}
			
			if (strpos($line, 'JPG Support') 	   !== false) {
				$array['JPG Support']		 = trim(str_replace('JPG Support', '', strip_tags($line)));
			}
			
			if (strpos($line, 'PNG Support') 	   !== false) {
				$array['PNG Support']		 = trim(str_replace('PNG Support', '', strip_tags($line)));
			}
			
			if (strpos($line, 'WBMP Support') 	   !== false) {
				$array['WBMP Support']		 = trim(str_replace('WBMP Support', '', strip_tags($line)));
			}
			
			if (strpos($line, 'XBM Support') 	   !== false) {
				$array['XBM Support']		 = trim(str_replace('XBM Support', '', strip_tags($line)));
			}
			
		}
		
		if ($gif_support				 === 'enabled') {
			$array['GIF Read Support']   = 1;
			$array['GIF Create Support'] = 1;
		}
		
		if ($array['FreeType Support']	 === 'enabled') {
			$array['FreeType Support'] 	 = 1;
		}
		
		if ($array['T1Lib Support']		 === 'enabled') {
			$array['T1Lib Support']		 = 1;   
		}
		
		if ($array['GIF Read Support']	 === 'enabled') {
			$array['GIF Read Support']	 = 1;
		}
		
		if ($array['GIF Create Support'] === 'enabled') {
			$array['GIF Create Support'] = 1;   
		}
		
		if ($array['JPG Support']		 === 'enabled') {
			$array['JPG Support']		 = 1;
		}
		
		if ($array['PNG Support']		 === 'enabled') {
			$array['PNG Support']		 = 1;
		}
		
		if ($array['WBMP Support']		 === 'enabled') {
			$array['WBMP Support']		 = 1;
		}
		
		if ($array['XBM Support']		 === 'enabled') {
			$array['XBM Support']		 = 1;
		}
		
		return $array;
	}
	
}

// }}}
// {{{ checkGD()

/**
* Checks, if the required GD library is available. Returns false on error,
* else returns the value of the installed GD version.
* 
* @return	mixed
* @see		gd_info()
* @see		createImage()
* @access	public
* @author	Christian Machmeier
*/
function checkGD()
{
	$error 				  = false;
	$gd    				  = array();
	$gd['version'] 		  = 0;
	$gd['formats']['gif'] = 0;
	$gd['formats']['jpg'] = 0;
	$gd['formats']['png'] = 0;
	
	if (!extension_loaded('gd')) {
		if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
			if (!dl('php_gd.dll')) {
				$error = true;
			}
		} else {
			if (!dl('gd.so')) {
				$error = true;
			}
		}
	}
	
	if (!$error) {
		$gdInfo		 		  = gd_info();
		$gd['version'] 		  = stristr($gdInfo['GD Version'], '2.') ? 2 : 1;
		$gd['formats']['gif'] = $gdInfo['GIF Read Support'];
		$gd['formats']['jpg'] = $gdInfo['JPG Support'];
		$gd['formats']['png'] = $gdInfo['PNG Support'];		
	}
	
	return $gd;
}

// }}}
// {{{ createImage()

/**
* Create a squared thumbnail from an existing image.
* 
* @param	string		$file		Location and name where the file is stored .
* @return	boolean
* @access	public
* @author	Christian Machmeier
*/
function createImage($file, $format, $mode, $fileDest)
{
	// Get information about the installed GD.
	$gdInfo    = checkGD();
	$gdVersion = $gdInfo['version'];
	
	if ($gdInfo == false) {
		return false;
	}
	
	// Ensure the given format is supported.
	if ($gdInfo['formats'][$format] != 1) {
		return false;
	}
	
	// Get the image dimensions.
	$dimensions = @getimagesize($file);
	$width		= $dimensions[0];
	$height		= $dimensions[1];
	
	// images mode.
	if ($mode == 'images') {
		$deltaX   = 0;
		$deltaY   = 0;
		$portionX = $outputX = $width;
		$portionY = $outputY = $height;
		$quality  = 100;
	}
	
	// thumbs mode.
	if ($mode == 'thumbs') {
		$outputX  = 45;
		$outputY  = 45;
		$quality  = 85;
		
		// The image is of vertical shape.
		if ($width < $height) {
			$deltaX   = 0;
			$deltaY   = ($height - $width) / 2;
			$portionX = $width;
			$portionY = $width;
			
		// The image is of horizontal shape.
		} else if ($width > $height) {
			$deltaX   = ($width - $height) / 2;
			$deltaY   = 0;
			$portionX = $height;
			$portionY = $height;
			
		// The image is of squared shape.
		} else {
			$deltaX   = 0;
			$deltaY   = 0;
			$portionX = $width;
			$portionY = $height;
		}
	}

	// Get the source image in gif format.
	if ($format == 'gif') {
		$imageSrc  = @imagecreatefromgif($file);
	}
	
	// Get the source image in jpg format.
	if ($format == 'jpg') {
		$imageSrc  = @imagecreatefromjpeg($file);
	}
	
	// Get the source image in png format.
	if ($format == 'png') {
		$imageSrc  = @imagecreatefrompng($file);
	}
	
	// The thumbnail creation with GD1.x functions does the job.
	if ($gdVersion == 1) {
		
		// Create an empty thumbnail image.
		$imageDest = @imagecreate($outputX, $outputY);
		
		// Try to create the thumbnail from the source image.
		if (@imagecopyresized($imageDest, $imageSrc, 0, 0, $deltaX, $deltaY, $outputX, $outputY, $portionX, $portionY)) {
			
			// save the thumbnail image into a file.
			@imagejpeg($imageDest, $fileDest, $quality);
			
			// Delete both image resources.
			@imagedestroy($imageSrc);
			@imagedestroy($imageDest);
			
			return true;
			
		}
		
	}
	
	// The recommended approach is the usage of the GD2.x functions.
	if ($gdVersion == 2) {
		
		// Create an empty thumbnail image.
		$imageDest = @imagecreatetruecolor($outputX, $outputY);
		
		// Try to create the thumbnail from the source image.
		if (@imagecopyresampled($imageDest, $imageSrc, 0, 0, $deltaX, $deltaY, $outputX, $outputY, $portionX, $portionY)) {
			
			// save the thumbnail image into a file.
			@imagejpeg($imageDest, $fileDest, $quality);
			
			// Delete both image resources.
			@imagedestroy($imageSrc);
			@imagedestroy($imageDest);
			
			return true;
			
		}
		
	}
	
	return false;
}

// }}}
// {{{ parseXML()

/**
* Function to actually perform parsing
* 
* @param	string		$file		name of XML file to be parsed.
* @access	public
* @author	Christian Machmeier
*/
function parseXML($xmlFile)
{
	// Of course the XMl file is mandatory.
	if (empty($xmlFile)) {
		die('Give me an XML file to read.');
	}
	
	// Open the XML file for reading.
	if (!($fileHandle = @fopen($xmlFile, 'r'))) {
		die("Can't read file: " . $xmlFile);
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
	
	// Parse the XML file.
	$elementArray 	= array();
	$frequencyArray = array();
	
	if (!xml_parse_into_struct($xmlParser, $data, $elementArray, $frequencyArray)) {
		die('XML Parser error: ' . xml_error_string(xml_get_error_code($xmlParser)));
	}
	
	// All done, clean up!
	@xml_parser_free($xmlParser);
	
	return $elementArray;
}

// }}}
// {{{ saveImageToXML()

/**
* Save the image name and its caption to the XML file in 
* the according album directory.
* 
* @param	string		$image		Path to and name of the current image.
* @param	string		$caption	Caption of the image to save.
* @access	public
* @author	Christian Machmeier
*/
function saveImageToXML($image, $caption = '')
{
	// Get the image data from the XML file.
	$imageData = array();
	$i 		   = 0;
	foreach (parseXML($_SESSION['currentDir'] . 'imageData.xml') as $k => $v) {
		if ($v['tag'] == 'NAME') {
			$imageData[$i]['name'] = $v['value'];
		}
		if ($v['tag'] == 'CAPTION') {
			$imageData[$i]['caption'] = (!empty($v['value']) ? $v['value'] : '');
			$i++;
		}
	}
	
	// Parse/Create the opening XML tag.
	$xmlData = '<SIMPLEVIEWER_DATA';
	foreach (getConfig($_SESSION['currentDir'] . 'imageData.xml') as $k => $v) {
		$xmlData .= ' ' . $k  . '="' . (($k == 'title') ? ((isPhpOfVersion(5) == -1) ? convertStr($v, true, false) : convertStr($v, true, false)) : convertStr($v, true, false)) . '"';
	}
	$xmlData .= '>';
	
	// Get the list of image file from the current album directory.
	$fileList = getDirList($_SESSION['currentDir'] . 'images/', '', false, 'files');
	
	// Synchronize the filelist with the imageData.
	$tmpArray 	 = array();
	$tmpFileList = $fileList['List'];
	foreach ($imageData as $xmlKey => $xmlValue) {
		foreach ($tmpFileList as $key => $imageName) {
			if ($xmlValue['name'] == $imageName) {
				$tmpArray[] = $imageName;
				unset($tmpFileList[$key]);
			}
		}
	}
	
	// There might be some files in the directory that weren't added to the 
	// imageData file yet.
	foreach ($tmpFileList as $tmpKey => $tmpValue) {
		$tmpArray[] = $tmpValue;
	}
	
	// Get a copy of the synchronized file list.
	$fileList['List'] = $tmpArray;
	
	stripslashes($caption);
	
	// Process the file list.
	foreach ($fileList['List'] as $k => $v) {
		
		$noCaption = true;
		
		// Write the opening IMAGE tag and store the name of the Image file.
		$xmlData .= "\n\t<IMAGE>\n\t\t<NAME>" . $v . "</NAME>\n";
		
		// Check the caption for each image name from the directory and compare 
		// it with the image name found in the XML file.
		foreach ($imageData as $xmlKey => $xmlValue) {
			// The image name from the XML file is the same as the one from the 
			// current directory listing.
			if ($xmlValue['name'] == $v) {
				$xmlData .= "\t\t<CAPTION><![CDATA[";
				// The current (uploaded) image already is in the XML file..
				if ($xmlValue['name'] == $image) {
					// ..and the caption hasn't changed.
					if ($xmlValue['caption'] == $caption) {
						$xmlData .= $xmlValue['caption'];
					// The caption has changed.
					} else {
						$xmlData .= convertStr($caption, true);
					}
				// The uploaded image is new to the XML file.
				} else {
					$xmlData .= $xmlValue['caption'];
				}
				$xmlData .= "]]></CAPTION>";
				$noCaption = false;
			}
		}
		
		// Write the submitted caption for each new image that was just uploaded.
		if ($noCaption) {
			$xmlData .= "\t\t<CAPTION><![CDATA[" . convertStr($caption, true) . "]]></CAPTION>";
		}
		
		$xmlData .= "\n\t</IMAGE>\n";
	}
	
	// Write the closing XML root tag.
	$xmlData .= '</SIMPLEVIEWER_DATA>';
	
	// Attempt to create/write the XML file.
	if (!$fileHandle = @fopen($_SESSION['currentDir'] . 'imageData.xml', 'w')) {
		$_SESSION['statusMsg'][] = 'Cannot open XML file.';
	}
	
	// Write the basic XML template to the opened file.
	if (@fwrite($fileHandle, $xmlData) === false) {
		$_SESSION['statusMsg'][] = 'Cannot write to XML file.';
	}
	
	// Write the XML file to disk.
	@fclose($fileHandle);
}

// }}}
// {{{ getConfig()

/**
* Load the configuration part of an XML file.
* 
* @param	string		$xmlFile	Name of the configuration file to parse.
* @return	array
* @see		parseXML()
* @access	public
* @author	Christian Machmeier
*/
function getConfig($xmlFile)
{
	// Of course the XMl file is mandatory.
	if (empty($xmlFile)) {
		die('Give me an XML file to read.');
	}
	
	// Read and parse the config file.
	$configData = parseXML($xmlFile);
	
	// Return the configuration part of the parsed XML struct.
	return $configData[0]['attributes'];
}

// }}}
// {{{ configure()

/**
* Displays a configuration interface.
* 
* @param	string		$mode		gallery or album?
* @see		getConfig()
* @access	public
* @author	Christian Machmeier
*/
function configure($mode, $renameSuccess = true)
{
	$imageData = '';
	
	if ($mode == 'gallery') {
		
		$filestr = 'SimpleViewerConfig.xml';
		
	} else {
		
		$filestr 	= $_SESSION['currentDir'] . 'imageData.xml';
		$sourceData = parseXML($filestr);
		$xmlData    = array();
		$i 			= 0;
		
		foreach ($sourceData as $k => $v) {
			if ($v['tag'] == 'NAME') {
				$xmlData[$i]['name'] = $v['value'];
			}
			if ($v['tag'] == 'CAPTION') {
				$xmlData[$i]['caption'] = (!empty($v['value']) ? $v['value'] : '');
				$i++;
			}
		}
		
		foreach ($xmlData as $k => $v) {
			$imageData .= "\n\t<IMAGE>\n\t\t<NAME>" . $v['name'] . "</NAME>\n\t\t<CAPTION><![CDATA[" . $v['caption'] . "]]></CAPTION>\n\t</IMAGE>\n";
		}
		
	}

	// The form was submitted.
	if (isset($_POST['saveConfiguration'])) {
		
		$error = false;
		
		// Check the configData's validity.
		foreach ($_POST['configData'] as $k => $v) {
			if (!isset($v)) {
				$error = true;
			}
		}
		
		if ($error) {
			$_SESSION['statusMsg'][] = 'Cannot write configuration file.<br />Some configuration values have been left empty.';
		} else {
			
			if ($renameSuccess) {
				
				// Read the configuration data and generate the XML file.
				$simpleViewerConfig  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<SIMPLEVIEWER_DATA";
				foreach ($_POST['configData'] as $k => $v) {
					if ($k == 'imagePath') {
						$simpleViewerConfig .= ' ' . $k . '="' . $_SESSION['currentDir'] . 'images/"';
					} else if ($k == 'thumbPath') {
						$simpleViewerConfig .= ' ' . $k . '="' . $_SESSION['currentDir'] . 'thumbs/"';
					} else if ($k == 'title') {
						$simpleViewerConfig .= ' ' . $k . '="' . ((isPhpOfVersion(5) == -1) ? convertStr($v, true, false) : convertStr($v, true, false)) . '"';
					} else {
						$simpleViewerConfig .= ' ' . $k . '="' . ( ($k == 'adminPassword') ? strstr($v[2], $v[1]) ? $v[2] : md5($v[1]) : $v) . '"';
					}
				}
				$simpleViewerConfig .= ">";
				$simpleViewerConfig .= $imageData . "</SIMPLEVIEWER_DATA>";
				
				// Attempt to create the XML file.
				if (!$fileHandle = @fopen($filestr, 'w')) {
					$_SESSION['statusMsg'][] = 'Cannot open configuration file.';
				} else {
					
					// Write the basic template to the opened file.
					if (@fwrite($fileHandle, $simpleViewerConfig) === false) {
						$_SESSION['statusMsg'][] = 'Cannot save the configuration.';
					}
					
					$_SESSION['statusMsg'][] = 'Configuration saved.';
					
					// Write the file to disk.
					@fclose($fileHandle);
					
				}
				
			} else {
				$_SESSION['statusMsg'][] = 'Cannot write configuration file.<br />Your suggested album name already exists.';
			}
			
		}
		
	}
	
	// Display the gallery configuration form.
	echo '
		<form method="post" id="configForm" onsubmit="return checkConfigForm();" action="?' . ($mode == 'gallery' ? 'configureGallery=1' : 'action=edit&amp;file=album&amp;name=' . ((isset($_POST['saveConfiguration']) && $renameSuccess) ? convertStr($_POST['configData']['title']) : $_GET['name'])) . '">
	';
	foreach (getConfig($filestr) as $k => $v) {
		if ($k == 'adminTitle' || $k == 'version' || $k == 'imagePath' ||  $k == 'thumbPath' || $k == 'emergency') {
			echo '<input type="hidden" name="configData[' . $k . ']" value="' . $v . '" />' . " \n";
		} else if ($k == 'maxImageDimension' || $k == 'textColor' || $k == 'frameColor' || $k == 'frameWidth' || 
			$k == 'stagePadding' || $k == 'thumbnailColumns' || $k == 'thumbnailRows' || $k == 'navPosition' || 
			$k == 'navDirection' || $k == 'galleryWidth' || $k == 'galleryHeight') {
			if ($mode == 'gallery') {
				echo '<input type="hidden" name="configData[' . $k . ']" value="' . $v . '" />' . " \n";
			}
		}
	}
	echo '
		<table cellpadding="1" cellspacing="1" border="0" width="100%">
		' . (($mode == 'album') ? '<tr><td colspan="2"><h4 class="label">Edit album configuration</h4></td></tr>' : '');
	
	// Process the configuration options.
	foreach (getConfig($filestr) as $k => $v) {
		
		if ($k == 'adminTitle' || $k == 'version' || $k == 'imagePath' ||  $k == 'thumbPath' || $k == 'emergency') {
			echo '';
		} else if ($k == 'adminUsername') {
			echo '
				<tr>
					<td>' . $k . '</td>
					<td><input type="text" name="configData[' . $k . ']" value="' . $v . '" maxlength="8" /></td>
				</tr>
			';
		} else if ($k == 'adminPassword') {
			echo '
				<tr>
					<td>' . $k . '</td>
					<td>
						<input type="password" name="configData[' . $k . '][1]" value="' . $v . '" maxlength="8" />
						<input type="hidden" name="configData[' . $k . '][2]" value="' . $v . '" />
					</td>
				</tr>
			';
		} else if ($k == 'maxImageDimension' || $k == 'textColor' || $k == 'frameColor' || $k == 'frameWidth' || 
			$k == 'stagePadding' || $k == 'thumbnailColumns' || $k == 'thumbnailRows' || $k == 'navPosition' || 
			$k == 'navDirection' || $k == 'galleryWidth' || $k == 'galleryHeight') {
			if ($mode == 'gallery') {
				echo '';
			} else {
				echo '
					<tr>
						<td>' . $k . '</td>
						<td><input type="text" name="configData[' . $k . ']" value="' . $v . '" maxlength="255" /></td>
					</tr>
				';
			}
		} else {
			echo '
				<tr>
					<td>' . $k . '</td>
					<td><input type="text" name="configData[' . $k . ']" value="' . (($k == 'title') ? ((isPhpOfVersion(5) == -1) ? utf8_encode($v) : $v) : $v) . '" maxlength="255" /></td>
				</tr>
			';
		}
		
	}
	
	// Display the rest of the configuration form.
	echo '
			<tr>
				<td width="30%">&nbsp;</td>
				<td width="70%"><input type="submit" name="saveConfiguration" value="Save Configuration" /></td>
			</tr>
		</table>
		</form>
		<br /><br />
	';
}

// }}}
// {{{ moveImage()

/**
* Read the contents of the album's XML file and move the requested image 
* forwards or backwards.
*
* @see		printSummary()
* @access	public
* @author	Christian Machmeier
*/
function moveImage()
{
	if (isset($_POST['imageforward_x']) || isset($_POST['imagebackward_x'])) {
		
		if (isset($_POST['imageforward_x'])) {
			$currentKey = $_POST['imagekey'];
			$newKey 	= $currentKey + 1;
		}
		
		if (isset($_POST['imagebackward_x'])) {
			$currentKey = $_POST['imagekey'];
			$newKey 	= $currentKey - 1;
		}
				
		$i			 = 0;
		$xmlData	 = array();
		$baseArray	 = array();
		$insertArray = array();
		$sourceData  = parseXML($_SESSION['currentDir'] . 'imageData.xml');
		
		foreach ($sourceData as $k => $v) {
			if ($v['tag'] == 'NAME') {
				$xmlData[$i]['name'] = $v['value'];
			}
			if ($v['tag'] == 'CAPTION') {
				$xmlData[$i]['caption'] = (!empty($v['value']) ? $v['value'] : '');
				$i++;
			}
		}
		
		$insertArray[$newKey] = $xmlData[$currentKey];
		unset($xmlData[$currentKey]);
		
		foreach ($xmlData as $k => $v) {
			if ($k == $newKey) {
				$baseArray[$currentKey] = $v;
			} else {
				$baseArray[$k] = $v;
			}
		}
		
		foreach ($insertArray as $k => $v) {
			$baseArray[$k] = $v;
		}
		
		ksort($baseArray);
		
		// Parse/Create the opening XML tag.
		$xmlData = '<SIMPLEVIEWER_DATA';
		foreach (getConfig($_SESSION['currentDir'] . 'imageData.xml') as $k => $v) {
			$xmlData .= ' ' . $k  . '="' . (($k == 'title') ? convertStr($v, true, false) : convertStr($v, true, false)) . '"';
		}
		$xmlData .= '>';
		
		// Process the file list.
		foreach ($baseArray as $k => $v) {
			
			// Write the opening IMAGE tag and store the name of the Image file.
			$xmlData .= "\n\t<IMAGE>\n\t\t<NAME>" . $v['name'] . "</NAME>\n";
			$xmlData .= "\t\t<CAPTION><![CDATA[" . $v['caption'] . "]]></CAPTION>";
			$xmlData .= "\n\t</IMAGE>";
			
		}
		
		// Write the closing XML root tag.
		$xmlData .= "\n</SIMPLEVIEWER_DATA>";
		
		// Attempt to create/write the XML file.
		if (!$fileHandle = @fopen($_SESSION['currentDir'] . 'imageData.xml', 'w')) {
			$_SESSION['statusMsg'][] = 'Cannot open XML file.';
		}
		
		// Write the basic XML template to the opened file.
		if (@fwrite($fileHandle, $xmlData) === false) {
			$_SESSION['statusMsg'][] = 'Cannot write to XML file.';
		}
		
		// Write the XML file to disk.
		@fclose($fileHandle);
		
	}
}

// }}}
// {{{ makeSeed()

/**
* Generate the seed for PHP's built-in function "rand()".
*
* @access	public
* @author	Christian Machmeier
*/
function makeSeed()
{
	list($usec, $sec) = explode(' ', microtime());
	return (float) $sec + ((float) $usec * 100000);
}

// }}}
// {{{ makeColorVariant()

/**
* Convert a hexadecimal value, increment oder decrement it, and reconvert it.
*
* @param	int		$hex		Hexadecimal value.
* @param	int		$step		Decimal value.
* @return	int					The modified color.
* @access	public
* @author	Christian Machmeier
*/
function makeColorVariant($hex, $step)
{
	// If the step is zero, there's nothing to do, so exit here.
	if ($step == 0) {
		return $hex;
	}
	
	// Set the boundaries for the incrementation/decrementation.
	$boundary = 255;
	if ($step < 0) {
		$boundary = 0;
	}
	
	// Remove the hex-prefix, if any.
	$hex = (stristr($hex, '0x') ? str_replace('0x', '', $hex) : $hex);

	// Get the 'red' value, convert it to its decimal counterpart, and add the 
	// '$step' value.
	$r = hexdec(substr($hex, 0, 2)) + $step;
	// Convert the decimal value back to its hexadecimal representation, after 
	// checking the value obeys the given boundaries.
	$r = dechex((($boundary == 0) 
				  ? (($r < $boundary) ? $boundary : $r) 
				  : (($r > $boundary) ? $boundary : $r)));
	$r = (('0x' . $r < 0x10) ? '0' . $r : $r);

	// Same as above, just for the 'green' value.
	$g = hexdec(substr($hex, 2, 2)) + $step;
	$g = dechex((($boundary == 0) 
				  ? (($g < $boundary) ? $boundary : $g) 
				  : (($g > $boundary) ? $boundary : $g)));
	$g = (('0x' . $g < 0x10) ? '0' . $g : $g);

	// Same as above, just for the 'blue' value.
	$b = hexdec(substr($hex, 4, 2)) + $step;
	$b = dechex((($boundary == 0) 
				  ? (($b < $boundary) ? $boundary : $b) 
				  : (($b > $boundary) ? $boundary : $b)));
	$b = (('0x' . $b < 0x10) ? '0' . $b : $b);
	
	return $r . $g . $b;
}

// }}}

?>
