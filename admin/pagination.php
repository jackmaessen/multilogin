<?php
// calculation for Pagination
$totalUsers = count($allFiles);				
$records = 25; // number of records per page
$startRecord = 0;
$endRecord = $startRecord + $records;
$lastPage = ceil($totalUsers/$records);
$totalPages = $lastPage;
$pageRange = 5;
$pagination = true;

if($pageRange > $totalPages) {
	$pagination = false;
}

$jumpRecords = 3;

// disable/enable prev/next button according to pagenumber
if($pageNumber > 1) {
	$result['prev'] = 'enabled'; // enable prev button
}
else {
	$result['prev'] = 'disabled'; // disable prev button 
}
if($pageNumber < $lastPage && $lastPage != 1) {
	$result['next'] = 'enabled'; // enable next button
}
else {
	$result['next'] = 'disabled'; // disable next button
}

// create pagination range 
if($pageNumber <= $pageRange) {
	$newStart = 2;
	$newEnd = $newStart + $pageRange -1;
}
elseif($pageNumber >= ($lastPage - $pageRange)) {
	$newEnd = $lastPage - 1;
	$newStart = $lastPage - $pageRange;
}
else {
	$newStart = $pageNumber - floor($pageRange/2);				
	$newEnd = $pageNumber + floor($pageRange/2);	
}
if($newEnd >= $lastPage) {
	$newEnd = $lastPage - 1;
}

// hide triple dots 
if(!$pagination || $pageNumber < 4) {
	$result['hideprev'] = '1';
}
if(!$pagination || $pageNumber > ($lastPage - 3)) {
	$result['hidenext'] = '1';
}


	
?>