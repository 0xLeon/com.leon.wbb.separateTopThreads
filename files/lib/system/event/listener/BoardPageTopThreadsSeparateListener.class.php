<?php
// wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

/**
 * Separates top threads on BoardPage
 *
 * @author      Stefan Hahn
 * @copyright   2010 Stefan Hahn
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package     net.leon.wbb.separateTopThreads
 * @subpackage  system.event.listener
 * @category    Community Framework 
 */
class BoardPageTopThreadsSeparateListener implements EventListener {
	protected $topThreads;
	
	protected $announcements = array();
	protected $newAnnouncements = 0;
	protected $stickies = array();
	protected $newStickies = 0;
	
	protected static $announcementsStatus = 1;
	protected static $stickiesStatus = 1;
	
	
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if ($eventName == 'assignVariables') {
			// get top threads
			$this->topThreads = ($eventObj->threadList != null) ? $eventObj->threadList->topThreads : null;
			
			// categorize top threads
			if ($this->topThreads != null) {
				foreach ($this->topThreads as $topThread) {
					if ($topThread->isAnnouncement) {
						$this->announcements[] = $topThread;
						if ($topThread->isNew()) $this->newAnnouncements++;
					}
					
					if ($topThread->isSticky) {
						$this->stickies[] = $topThread;
						if ($topThread->isNew()) $this->newStickies++;
					}
				}
			}
			
			// assign announcements and stickies to the template engine
			WCF::getTPL()->assign(array(
				'announcements' => (($this->topThreads != null) ? $this->announcements : null),
				'newAnnouncements' => (($this->topThreads != null) ? $this->newAnnouncements : 0),
				'announcementsStatus' => self::$announcementsStatus,
				'stickies' => (($this->topThreads != null) ? $this->stickies : null),
				'newStickies' => (($this->topThreads != null) ? $this->newStickies : 0),
				'stickiesStatus' => self::$stickiesStatus
			));
		}
		
		if ($eventName == 'readData') {
			// get status of the two new lists and safe it
			if (WCF::getUser()->userID) {
				self::$announcementsStatus = intval(WCF::getUser()->announcementsStatus);
				self::$stickiesStatus = intval(WCF::getUser()->stickiesStatus);
			}
			else {
				if (WCF::getSession()->getVar('announcementsStatus') !== null) self::$announcementsStatus = WCF::getSession()->getVar('announcementsStatus');
				if (WCF::getSession()->getVar('stickiesStatus') !== null) self::$stickiesStatus = WCF::getSession()->getVar('stickiesStatus');
			}
		}
	}
}
?>