<?php
/*
# Plugin Download Counter by Web-eau.net  - Forked of 'Simple Download Counter' from Yagnenok
# ------------------------------------------------------------------------
# Author    web-eau.net
# Copyright (C) 2022 - web-eau-net - All Rights Reserved.
# License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: https://web-eau.net
*/

// no direct access
defined( '_JEXEC' ) or die;

abstract class DownloadCounterHelper
{
	public function getnames($text) {
		$matches = array(); // Array for matches
		if (preg_match_all('/\(downloadCounter.+\)/', $text, $matches)) {  // If there is nothing with the tag (sdc xxxxxxx) then we do not sweat further
		$sdc = array();
		foreach($matches as $brand => $massiv)
		{
			foreach($massiv  as  $inner_key => $value)
		{   
		    $old = $value;
			$value=trim($value, '()'); // Remove brackets
		    $value = mb_substr($value, mb_strpos($value, ' ')); // Remove the sdc tag
			$value = str_replace(" ","",$value); // Remove the space
			$sdc[$old] = $value;
		}
		}
		}
		return $sdc;
		}
		public static function check($name) {
        $db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from($db->quoteName('#__download_counter'));
		$query->where($db->quoteName('name')." = ".$db->quote($name));
		$db->setQuery($query);
		$result = $db->loadObjectList();
		if (!count($result) > 0)
        {
		$query = $db->getQuery(true);
		$columns = array('name', 'clicks');
		$values = array($db->quote($name), 0);
		$query
		->insert($db->quoteName('#__download_counter'))
		->columns($db->quoteName($columns))
		->values(implode(',', $values));
		$db->setQuery($query);
		$db->execute();
		 }
		}
		
		public static function fsize($bytes, $decimals = 0) {
		$sz = array('b','kb','mb','gb');
		$factor = floor((strlen($bytes) - 1) / 3);
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
		}
		
    	public static function click($name) {
        $db =& JFactory::getDBO();
		$query = $db->getQuery(true)
			->update('#__download_counter')
			->set('clicks = (clicks + 1)')
			->where("name = '$name'");
		$db->setQuery($query);
		$db->query(); }	
		
		public static function showclick($name) {
        $db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('clicks');
		$query->from($db->quoteName('#__download_counter'));
		$query->where($db->quoteName('name')." = ".$db->quote($name));
		$db->setQuery($query);
		$result = $db->loadObjectList();
		return $result;
		}
		
		public static function plural($number) {
		$suffix = array("PLG_CONTENT_DOWNLOAD_COUNTER_SITE_CLICKS_COUNTER","PLG_CONTENT_DOWNLOAD_COUNTER_SITE_CLICKS_COUNTER2","PLG_CONTENT_SDC_SITE_CLICKS_COUNTER3");
		$keys = array(2, 0, 1, 1, 1, 2);
		$mod = $number % 100;
		$suffix_key = ($mod > 7 && $mod < 20) ? 2: $keys[min($mod % 10, 5)];
		return $suffix[$suffix_key];	
		}
		
		public static function downloadfile($url) {
		header("Location: $url ");
		die; 
		}		
}
?>
