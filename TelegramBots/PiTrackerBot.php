<?php
require_once("BotMessage.php");
require_once("/var/www/gpiosrv/phpgpio.php");

$strBotUrl 	= "https://api.telegram.org/bot283089360:AAFEWJ_PkXuPoWeqVZkiqr-z53T77x-_ygk/";
$strMethod	= "getUpdates";
$strIniFile = "/var/www/TelegramBots/PiTrackerBot.ini";


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
		case "on"		:
			echo "\nOut";
			$objGPIO = new phpGPIO(17,"out");
			$objGPIO->write(1);
			break;
		case "off"	:
			echo "\nOut";
			$objGPIO = new phpGPIO(17,"out");
			$objGPIO->write(0);
			break;
		default:
			$strMessage = "NichtUnterstuetzt";
			$objMessage->sendMessage($strMessage);
			break;
	}
}


?>