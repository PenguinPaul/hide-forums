<?php
// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Nope.");
}

if(!defined("PLUGINLIBRARY"))
{
    define("PLUGINLIBRARY", MYBB_ROOT."inc/plugins/pluginlibrary.php");
}

$plugins->add_hook("forumbit","hideforums");

function hideforums_info()
{
	return array(
		"name"			=> "Hide Certain Forums",
		"description"	=> "Hide certain forums from being displayed, while still being accessible by a direct link.",
		"website"		=> "https://github.com/PenguinPaul/hide-forums",
		"author"		=> "Paul Hedman",
		"authorsite"	=> "http://www.paulhedman.com",
		"version"		=> "1.0",
		"guid" 			=> "",
		"compatibility" => "*"
	);
}

function hideforums_activate()
{
	global $PL;
	
	if(!file_exists(PLUGINLIBRARY))
	{
		flash_message("PluginLibrary is missing.  Get it at http://mods.mybb.com/view/pluginlibrary", "error");
		admin_redirect("index.php?module=config-plugins");
	}
	
	$PL or require_once PLUGINLIBRARY;
	
	$edits = array(
		'search' => '$forums = $subforums = $sub_forums = \'\';',
		'before' => "if(in_array(\$forum['fid'],explode(',',\$mybb->settings['hideforums_forums'])))\n{\nbreak;\n}"
	);
	
	$PL->edit_core("hideforums","inc/functions_forumlist.php",&$edits,true);

	if($edits == false)
	{
		flash_message("Edits could not be applied.", "error");
		admin_redirect("index.php?module=config-plugins");	
	}
	
	$PL->settings('hideforums',
              'Hide Forums Settings',
              'The forums to hide from displaying are set in here.',
              array(
                  'forums' => array(
                      'title' => 'Forums to hide',
                      'description' => 'A CSV list of forums to hide from being displayed.',
                      'optionscode' => 'text',
                      ),
                  )
    );
}

function hideforums_deactivate()
{
	global $PL;
	
	if(!file_exists(PLUGINLIBRARY))
	{
		flash_message("PluginLibrary is missing.  Get it at http://mods.mybb.com/view/pluginlibrary", "error");
		admin_redirect("index.php?module=config-plugins");
	}
	
	$PL or require_once PLUGINLIBRARY;
	
	$edits = $PL->edit_core("hideforums","inc/functions_forumlist.php",null,true);
	
	$PL->settings_delete('hideforums');
}
?>
