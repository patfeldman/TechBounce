<?php 
class comms{
	public static function ListUpdatedSendMessages(){
		$tweetprivate = new tweet();
		$tweetprivate->sendWatchlistUpdate();
		
		$updateEmail = new email(email::ADDRESSES_ALL_CHOSEN);
		$updateEmail->sendWatchlistUpdate();		
	}
	
}
