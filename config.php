<?php
/** Author: Andrew Poppe */

// PIDs of REDCap Projects
$grantsProjectId = 15;
$userProjectId = 16;

// grab api token (make sure token.txt stays in .gitignore)
$apiToken = file_get_contents("token.txt");

// API URL
$apiUrl = "http://localhost:8080/api/";

// Aesthetics
$logoImage = "./img/yu.png";
$topBarColor = "#00356b";