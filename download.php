<?php
/** Authors: Jon Scherdin, Andrew Poppe */
# verify user access
if (!isset($_COOKIE['grant_repo'])) {
	header("Location: index.php");
}

require_once("base.php");

// grant record id for logging purposes
if (!isset($_GET['record'])) die('No Grant Identified');
$grant = $_GET['record'];

// log this download (accessing this page counts)
\REDCap::logEvent("Download uploaded document", NULL, NULL, $grant, NULL, $grantsProjectId);

// If ID is not in query_string, then return error
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) exit("{$lang['global_01']}!");

// need to set the project id since we are using a different variable name
if (!isset($_GET['p']) || !is_numeric($_GET['p'])) exit("{$lang['global_01']}!");
$project_id = $_GET['p'];
define("PROJECT_ID", $project_id);


//Download file from the "edocs" web server directory
$sql = "select * from redcap_edocs_metadata where project_id = $project_id and doc_id = ".$_GET['id'];
$q = db_query($sql);
if (!db_num_rows($q)) {
	die("<b>{$lang['global_01']}:</b> {$lang['file_download_03']}");
}
$this_file = db_fetch_array($q);

$basename = preg_replace("/\.[^\.]*$/", "", $this_file['stored_name']);
if (!preg_match("/\/$/", $basename)) {
	$basename.= "/";
}
$outDir = APP_PATH_TEMP.$basename;
mkdir($outDir);

$files = array();
if (preg_match("/\.zip$/i", $this_file['stored_name']) || ($this_file['mime_type'] == "application/x-zip-compressed")) {
	$zip = new ZipArchive;
	$res = $zip->open(EDOC_PATH.$this_file['stored_name']);
	if ($res) {
		$zip->extractTo($outDir);
		$zip->close();
		$files = inspectDir($outDir);
	}
} else {
	$fpIn = fopen(EDOC_PATH.$this_file['stored_name'], "r");
	$fpOut = fopen($outDir.$this_file['doc_name'], "w");
	while ($line = fgets($fpIn)) {
		fwrite($fpOut, $line);
	}
	fclose($fpIn);
	fclose($fpOut);
	$files = array($outDir.$this_file['doc_name']);
}

function truncateFile($filename) {
	return str_replace(APP_PATH_TEMP, "", $filename);
}

function inspectDir($dir) {
	$files = array();

	$allFiles = scandir($dir);
	$skip = array(".", "..");
	foreach ($allFiles as $filename) {
		if (!in_array($filename, $skip)) {
			if (is_dir($dir.$filename)) {
				$files = array_merge($files, inspectDir($dir.$filename."/"));
			} else {
				array_push($files, $dir.$filename);
			}
		}
	}
	return $files;
}

$role = updateRole($userid);
?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="css/basic.css">
	</head>
	<br/>
	<div id="container" style="padding-left:8%;  padding-right:10%; margin-left:auto; margin-right:auto; ">
		<div id="header">
		<?php createHeaderAndTaskBar($role);?>
		<h3>Download Grant Documents</h3>
		<i></i><hr/>
	</div>
	<div id="downloads">
	<?php
		if (!empty($files)) {
			echo "<h1>All Files (".count($files).")</h1>\n";
			foreach ($files as $filename) {
				$truncFilename = truncateFile($filename);
				echo "<p><a href='downloadFile.php?f=".urlencode($truncFilename)."'>".basename($filename)."</a></p>\n";
			}
			exit();
		} else {
			echo "<p>No files have been provided.</p>";
		}
	?>
</html>
