<?php
	if (ereg('demo', $_SERVER['SERVER_NAME']))
		require_once("config-demo.php");
	else
		require_once("config-live.php");
	
	require_once("functions.php");
	require_once("class_zabbix.php");
	require_once("cookies.php");
	
	$arrSettings["zabbixApiUrl"] = str_replace("api_jsonrpc.php", "", $zabbixApi);
	$zabbix = new Zabbix($arrSettings);
	
	// Populate our class
	$zabbix->setUsername($zabbixUser);
	$zabbix->setPassword($zabbixPass);
	$zabbix->setZabbixApiUrl($zabbixApi);
	
	// Login
	if (isset($zabbixAuthHash) && strlen($zabbixAuthHash) > 0) { 
		// Try it with the authentication hash we have
		$zabbix->setAuthToken($zabbixAuthHash);
	} elseif (strlen($zabbix->getUsername()) > 0 && strlen($zabbix->getPassword()) > 0 && strlen($zabbix->getZabbixApiUrl()) > 0) {
		$zabbix->login();
	} 

	if (!$zabbix->isLoggedIn()) {
		header("Location: index.php");
		exit();
	}
	
	require_once("template/header.php");
	
	$zabbixGraphId = (int) $_GET['graphid'];
	if ($zabbixGraphId > 0) {
		$fileid		= rand(0, 999999);
		$graphImage = $zabbix->getGraphImageById($zabbixGraphId, $fileid);	// This generates our graph file
		$graph 		= $zabbix->getGraphById($zabbixGraphId);		
?>
	<div id="graphs_general<?=$zabbixGraphId?>" class="current">
		<div class="toolbar">
			<h1><?=$graph->name?></h1>
			<a class="back" href="#">Back</a>
		</div>

		<img src="graph_img.php?fileid=<?=$fileid?>" width="200" />
	</div>
<?php
	} else {
		echo "Invalid graph.";
	}
?>