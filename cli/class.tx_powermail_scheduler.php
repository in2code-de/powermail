<?php
class tx_powermail_scheduler extends tx_scheduler_Task {
	
	/**
	* Function executed from the Scheduler.
	*
	* @return    bool
	*/
	public function execute() {
	
		
	
	
		mail('ake@conject.com', 'scheduler', 'scheduler');
		return 'fehler';
	}
}

?>