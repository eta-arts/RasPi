<?php
require_once("BotMessage.php");
require_once("/var/www/gpiosrv/phpgpio.php");

$strBotUrl 	= "https://api.telegram.org/bot259779632:AAES2fhpbDgGefPZQ_6bWCMtgumh7_oBxQc/";
$strMethod	= "getUpdates";
$strIniFile = "/var/www/TelegramBots/MotorradAnkaufBot.ini";


$intLastUpdateId = ((int)file_get_contents($strIniFile) + 1);
$strOffset	= "?offset=" . $intLastUpdateId;

//$i=100;
while(true) 
{
	$strResponse = file_get_contents($strBotUrl . $strMethod . $strOffset);	
	
	$objJson=json_decode($strResponse);
	foreach ($objJson->result as $objCurrentMessage) {
		if($objCurrentMessage->update_id > $intLastUpdateId)
		{
			$objMessage = new BotMessage($objCurrentMessage,$strBotUrl);
			$intLastUpdateId = $objMessage->intUpdateId;
			$strOffset	= file_put_contents($strIniFile, $intLastUpdateId);
			$objMessage->checkMessage();
			if($objMessage->strMessageType == "command")
			{
				runCommand($objMessage);
			}
		}
		else
		{
		echo "nichts neues\n";
		}
	} 
	$strOffset = "?offest=" . $intLastUpdateId;
	sleep(1);
}


function runCommand($objMessage){
	//commando auswerten
	$objMessage->strCommand = substr($objMessage->strMessage,1);
	echo $objMessage->strCommand;
	switch($objMessage->strCommand)
	{
		case "help" :
			$strHelp		= "Das ist der *PiTrackerBot*, der wird euch viel Spaß machen";
			$objMessage->sendMessage($strHelp);
			break;
		default:
			$strMessage = "NichtUnterstuetzt";
			$objMessage->sendMessage($strMessage);
			break;
	}
}


?>