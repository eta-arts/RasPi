<?php
require_once("BotMessage.php");
require_once("/var/www/gpiosrv/phpgpio.php");

$strBotUrl 	= "https://api.telegram.org/bot234523482:AAHoZjlYrgzjmaCVp0zlo5RWvcot5jg1KwY/";
$strMethod	= "getUpdates";
$strIniFile = "/var/www/TelegramBots/MotorradBot.ini";

global $arrQuizList; 
$arrQuizList = array();

$intLastUpdateId = ((int)file_get_contents($strIniFile) + 1);
$strOffset	= "?offset=" . $intLastUpdateId;

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
			switch($objMessage->strType)
			{
				case "command" :
					runCommand($objMessage);
					break;
				case "callback":
					echo "button pressed" . $objMessage->data;
					checkCallback($objMessage);
					break;
				default:
					break;
			}
		}
		else
		{
		echo "."; //"nichts neues\n";
		}
	} 
	$strOffset = "?offest=" . $intLastUpdateId;
	sleep(1);
}

function runCommand($objMessage){
	$objMessage->strCommand = substr($objMessage->strMessage,1);
	echo $objMessage->strCommand;
	switch($objMessage->strCommand)
	{
		case "help" :
			$strHelp		= "Das ist der *MotorradBot*, der wird euch viel Spaß machen";
			$objMessage->sendMessage($strHelp);
			break;
		case "getpic":
			$strImgageUrl = "http://www.motorradankauf.de/_shared/TelegramBot/Motorrad/Images/HarleyDavidsonXL1200CSportster.png";
			//$strImgageUrl .="Harley Davidson XL 1200 C Sportster.png";
			$objMessage->sendImage($strImgageUrl);
			break;
		case "play":
			playGame($objMessage);
			break;
		case "buttons":
			$arrButtonList = array();
			array_push($arrButtonList,array('text' => 'BMW','callback_data' => 'button1pushed'));
			array_push($arrButtonList,array('text' => 'Harley Davidson XL 1200 C Sportster','callback_data' => 'button2pushed'));
			array_push($arrButtonList,array('text' => 'Yamaha XV 750','callback_data' => 'button3pushed'));
			array_push($arrButtonList,array('text' => 'KTM','callback_data' => 'button4pushed'));
			echo count($arrButtonList);
			$objMessage->setButton($arrButtonList);
			break;
		default:
			$strMessage = "NichtUnterstuetzt";
			$objMessage->sendMessage($strMessage);
			break;
	}
}

function playGame($objMessage){
	$strImgageUrl = "http://www.motorradankauf.de/_shared/TelegramBot/Motorrad/Images/HarleyDavidsonXL1200CSportster.png";
	
	//array_push($arrQuizList,array('chat_id' => $objMessage->chat_id);	
	$GLOBALS["arrQuizList"][$objMessage->intChatId]["answer"] = "button2pushed";
	$objMessage->sendImage($strImgageUrl);
	$arrButtonList = array();
	array_push($arrButtonList,array('text' => 'BMW','callback_data' => 'button1pushed'));
	array_push($arrButtonList,array('text' => 'Harley Davidson XL 1200 C Sportster','callback_data' => 'button2pushed'));
	array_push($arrButtonList,array('text' => 'Yamaha XV 750','callback_data' => 'button3pushed'));
	array_push($arrButtonList,array('text' => 'KTM','callback_data' => 'button4pushed'));

	//echo count($arrButtonList);
	$objMessage->setButton($arrButtonList,"Welches Motorrad wird auf dem Bild gezeigt?");
	// var_dump($GLOBALS["arrQuizList"]);
}

function checkCallback($objMessage){
	
	if($GLOBALS["arrQuizList"][$objMessage->intChatId]["answer"] == $objMessage->data)
	{
		echo "richtig";
		$objMessage->sendMessage("Richtig");
	}
	else
	{
		echo "falsch";
		$objMessage->sendMessage("Falsch");
	}
	
	
}

?>