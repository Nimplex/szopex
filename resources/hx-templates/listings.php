<?php

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
$listing = (new App\Builder\ListingBuilder())->make();

$page = $_GET['page'] ?: 1;
var_dump($listing->listAll($page));
