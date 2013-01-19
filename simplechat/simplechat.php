<?php

/**
* Name: simplechat
* Description: includes a simple node.js based chat
* Version: 0.0.1
* Author: Max Weller
*
*/

function simplechat_install() {
register_hook('page_end', 'addon/simplechat/simplechat.php', 'simplechat_script');

$chat_server = get_config("simplechat", "chat_server");
if ($chat_server==="") set_config("simplechat", "chat_server", "https://secure.teamwiki.de:8003/");

$sec_token = get_config("simplechat", "sec_token");
if ($sec_token==="") set_config("simplechat", "sec_token", sha1(time().rand(1,9999999)));

// set addon version so that safe updates are possible later
$addon_version = get_config("simplechat", "version");
if ($addon_version==="") set_config("simplechat", "version", "1");
}


function simplechat_uninstall() {
unregister_hook('page_end', 'addon/simplechat/simplechat.php', 'simplechat_script');
}


function simplechat_module() {}
function simplechat_init(&$a) {
echo "Test";
}


function simplechat_script(&$a,&$s) {
    if(! local_user()) return;
	
	$chat_server = get_config("simplechat", "chat_server");
	
    // generate access token
    $uid = local_user();
    $sec_token = get_config("simplechat", "sec_token");
	$accessToken = $uid."&".sha1($sec_token.$uid);
	
    // add javascript to start simpleChat
    $a->page['htmlhead'] .= <<<HEREDOC
    <script type="text/javascript">
        jQuery(document).ready(function() {
        	var ifr = jQuery("<div  style=' position: fixed; display: none; right: 15px; top: 40px; bottom: 40px; width: 250px; border: 1px solid #ddd;'><iframe src='$chat_server#$accessToken' style='width:100%;height:100%;border:0;'></iframe></div>");
           	var lasche = jQuery("<div style='position: fixed; right: 0; top: 40px; bottom: 40px; width: 15px; text-align:center; background: #bbb url(/addon/simplechat/chat-arrow.png) no-repeat center center;'></div>");
           	jQuery("body").append(ifr).append(lasche);
           	lasche.click(function() { ifr.toggle("fast"); });
        });
    </script>
HEREDOC;

    return;
}
function simplechat_plugin_admin(&$a, &$o) {
	// chat server
	$chat_server = get_config("simplechat", "chat_server");
	$o .= '<label for="simplechat-chatserver">Chat server:</label>';
	$o .= ' <input id="simplechat-chatserver" type="text" name="simplechat-chatserver" value="'.$chat_server.'" /><br />';

	// submit button
	$o .= '<input type="submit" name="simplechat-admin-settings" value="OK" />';
}

function simplechat_plugin_admin_post(&$a) {
	// set chat server
	$submit = $_REQUEST['simplechat-admin-settings'];
	if ($submit) {
		$chat_server = $_REQUEST['simplechat-chatserver'];
		set_config("simplechat", "chat_server", $chat_server);
	}
}
