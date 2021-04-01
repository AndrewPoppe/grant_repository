<?php

require_once("base.php");

$numRecordsInTest = 50;

$data = array(
	'token' => $apiToken,
	'content' => 'record',
	'format' => 'json',
	'type' => 'flat',
	'csvDelimiter' => '',
	'fields' => array('record_id'),
	'rawOrLabel' => 'raw',
	'rawOrLabelHeaders' => 'raw',
	'exportCheckboxLabel' => 'false',
	'exportSurveyFields' => 'false',
	'exportDataAccessGroups' => 'false',
	'returnFormat' => 'json'
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_VERBOSE, 0);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_AUTOREFERER, true);
curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
$output = curl_exec($ch);
curl_close($ch);

$redcapData = json_decode($output, TRUE);
$records = array();
foreach ($redcapData as $row) {
	array_push($records, $row['record_id']);
} 

if (count($records) > 0) {
	if (count($records) > $numRecordsInTest) {
		$records = array_splice($records, 0, $numRecordsInTest);
	}

	$data = array(
		'token' => $apiToken,
		'action' => 'delete',
		'content' => 'record',
		'records' => $records
	);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $apiUrl);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_VERBOSE, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
	$output = curl_exec($ch);
	echo "Deleted: ".$output."<br>";
	curl_close($ch);
}
