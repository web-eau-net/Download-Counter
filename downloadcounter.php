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


require_once dirname(__FILE__).'/helper.php'; 
$lang = JFactory::getLanguage();
$lang->load('plg_content_downloadcounter',JPATH_ADMINISTRATOR);
class plgContentDownloadCounter extends JPlugin
{ 
	public function onContentPrepare($context, &$article, &$params, $limitstart) 
	{ 
	$user = JFactory::getUser();
$groups = $user->groups;
$accessgroups = $this->params->get('download_access_group');
foreach ($accessgroups as $accessgroup)
{
if (in_array($accessgroup, $groups)):
$level = $accessgroup;
endif;
}
	$cache = JCache::getInstance('callback');
	$cache->clean(null, 'notgroup');
	$matches = array(); // array for matches
	  if (preg_match_all('/\(downloadCounter.+\)/', $article->text, $matches)) {  // if there is nothing with the tag (sdc xxxxxxx) then we do not sweat further
	  $sdc = array();
	  $downfolder = $this->params->get('downfolder').'/';
	  foreach($matches as $brand => $massiv)
		{
			foreach($massiv  as  $inner_key => $value)
		{   
		    $old = $value;
			$value=trim($value, '()'); // remove brackets
		    $value = mb_substr($value, mb_strpos($value, ' ')); //remove the sdc tag
			$value = str_replace(" ","",$value); // remove the space
			$sdc[$old] = $value;
		}
		}
		foreach ($sdc as $tag => $name) {
		$url = JURI::base().$downfolder.$name; // External file path
		$loc = $downfolder.$name; // Local file path
		If (JFile::exists($loc)) {
		SdcHelper::check($name);
		$downloadtext = $name;
		if ($this->params->def('show_download_text', 1))
		{
		$downloadtext = JText::_('PLG_CONTENT_SDC_SITE_DOWNLOAD_TEXT');
		}
		if ($this->params->def('show_file_name', 1))
		{
		$downloadtext = $downloadtext."".$name;
		}
		if ($this->params->def('show_filesize', 1))
		{
		$loc = filesize($loc);
		$downloadtext = $downloadtext." (".SdcHelper::fsize($loc).")";
		}
		if ($this->params->def('show_file_md5hash', 0))
		{
		$downloadtext = $downloadtext." (MD5 Hash: ".md5_file($url).")";
		}
		if ($this->params->def('force_download_link_target', 0))
		{
		$target = $this->params->get('force_download_link_target');
		}
		if ($this->params->def('show_clicks', 1))
		{
		$clicks = SdcHelper::showclick($name); // Get the number of clicks
		$show_clicks_text = JText::_('PLG_CONTENT_DOWNLOAD_COUNTER_SITE_CLICKS_TEXT');
		$show_clicks_text_after = JText::_(SdcHelper::plural($clicks[0]->clicks));
		$clickshtml = "</br>".$show_clicks_text."".$clicks[0]->clicks."".$show_clicks_text_after;
		}
		if($_GET['download'] == $name){
		SdcHelper::click($name); // Write 1 click to the database
		SdcHelper::downloadfile($url); // Download the file
		}
		$linkcss = $this->params->get('link_css');
		if (isset($linkcss)) { 
		$linkcss = "class='".$linkcss."'";
		}
		$down_off_text = JText::_('PLG_CONTENT_DOWNLOAD_COUNTER_SITE_FILE_DOWN_OFF');
		if (preg_match("/downloadCounter-off/i", $tag)) {$htmlcode = "<button ".$linkcss." disabled>".$downloadtext."</button>".$clickshtml.". ".$down_off_text; // output code to instead of (sdc-off ......) 
		}
		else		
		{ $htmlcode = "<a ".$target.$linkcss." href='?download=$name'>".$downloadtext."</a>".$clickshtml; } // output code instead of (sdc......) 
		//}
		$user = JFactory::getUser();
		$groups = $user->groups;
		if (!in_array($level, $groups)):
		$htmlcode = JText::sprintf( 'PLG_CONTENT_DOWNLOAD_COUNTER_SITE_FILE_NO_RIGHTS', $name );
		endif;
		$article->text = str_ireplace($tag, $htmlcode, $article->text); // Substitution of article text
		}
		else {
		$htmlcode = JText::sprintf( 'PLG_CONTENT_DOWNLOAD_COUNTER_SITE_FILE_NOT_FOUND', $name ); // File missing message
		$article->text = str_ireplace($tag, $htmlcode, $article->text); // Substitution of a tag for a message about the absence of a file
		}
		}

	  }
}
}
?>