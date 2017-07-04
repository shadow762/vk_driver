<?php
include_once 'vkAPI.php';
include_once 'config.php';

$vkApi = new vkAPI();

//$vkApi->getGroupMembers('kaspyinfo_astrakhan');
//$vkApi->get_countries();
//print_r($vkApi->get_regions(1));
echo '<pre>';
print_r($vkApi->get_users_data([3354442]));
echo '</pre>';