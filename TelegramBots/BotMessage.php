<?php
class BotMessage
{
	public $strBotUrl;
	public $objMessage;
	
	public function __construct($Message,$BotUrl)
	{
		$this->objMessage			= $Message;
		$this->strBotUrl			= $BotUrl;
		$this->intUpdateId		= $this->objMessage->update_id;

		if(isset($this->objMessage->message))
		{
			$this->strMessage		= $this->objMessage->message->text;
			if(substr($this->strMessage,0,1) == "/")
			{
				$this->strType		= "command";
				$this->strCommand = substr($this->strMessage,1);
			}
			else
			{
				$this->strType		= "message";
			}
			$this->intMessageId = $this->objMessage->message->message_id;
			$this->strUserName	= $this->objMessage->message->chat->username;
			$this->intChatId		= $this->objMessage->message->chat->id;
		}
		elseif(isset($this->objMessage->callback_query))
		{
			echo "callback";
			$this->strType			= "callback";
			$this->data					= $this->objMessage->callback_query->data;
			$this->intChatId		= $this->objMessage->callback_query->message->chat->id;
			// var_dump($this->objMessage);
		}
		else
		{
			//echo "kein typ";
		}
	}		
	
	public function sendMessage($strMessage)
	{
		$strMethod = "sendMessage";
		$strOffset = "?chat_id=" . $this->intChatId;
		$strOffset .= "&parse_mode=Markdown";
		$strOffset .= "&text=" . rawurlencode($strMessage);
		$strResponse = file_get_contents($this->strBotUrl . $strMethod . $strOffset);	
	}
	
	public function sendImage($photoUrl)
	{
		$strMethod = "sendPhoto";
		$strOffset = "?chat_id=" . $this->intChatId;
		$strOffset .= "&photo=" . $photoUrl;
		$strResponse = file_get_contents($this->strBotUrl . $strMethod . $strOffset);	
	}
	
	public function setButton($arrButtonList,$strText="Bitte waehlen:")
	{
		switch(count($arrButtonList))
		{
			case 1:
				$arrKeyboard = [
								'inline_keyboard' => [
								[['text' =>  $arrButtonList[0]['text'], 'callback_data' => $arrButtonList[0]['callback_data']]],
								]										
						 ];
				break;
			case 2:
				$arrKeyboard = [
								'inline_keyboard' => [
								[['text' =>  $arrButtonList[0]['text'], 'callback_data' => $arrButtonList[0]['callback_data']]],
								[['text' =>  $arrButtonList[1]['text'], 'callback_data' => $arrButtonList[1]['callback_data']]],
								]										
						 ];
				break;
			case 3:
				$arrKeyboard = [
								'inline_keyboard' => [
								[['text' =>  $arrButtonList[0]['text'], 'callback_data' => $arrButtonList[0]['callback_data']]],
								[['text' =>  $arrButtonList[1]['text'], 'callback_data' => $arrButtonList[1]['callback_data']]],
								[['text' =>  $arrButtonList[2]['text'], 'callback_data' => $arrButtonList[2]['callback_data']]],
								]										
						 ];
				break;
			case 4:
				$arrKeyboard = [
								'inline_keyboard' => [
								[['text' =>  $arrButtonList[0]['text'], 'callback_data' => $arrButtonList[0]['callback_data']]],
								[['text' =>  $arrButtonList[1]['text'], 'callback_data' => $arrButtonList[1]['callback_data']]],
								[['text' =>  $arrButtonList[2]['text'], 'callback_data' => $arrButtonList[2]['callback_data']]],
								[['text' =>  $arrButtonList[3]['text'], 'callback_data' => $arrButtonList[3]['callback_data']]],
								]										
						 ];
				break;
			default:
				break;
		}
		
		if(isset($arrKeyboard))
		{
			$reply = json_encode($arrKeyboard);
			$strMethod = "sendMessage";
			$strOffset = "?chat_id=" . $this->intChatId;
			$strOffset .= "&text=" . rawurlencode($strText) . "&reply_markup="; //.$reply;
			$strOffset .= json_encode($arrKeyboard);
			$strResponse = file_get_contents($this->strBotUrl . $strMethod . $strOffset);	
			// var_dump($strResponse);
		}
	}
	
}