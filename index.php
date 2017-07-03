<?php
include_once 'vkAPI.php';
include_once 'config.php';

$vkApi = new vkAPI();

$vkApi->getGroupMembers('kaspyinfo_astrakhan');