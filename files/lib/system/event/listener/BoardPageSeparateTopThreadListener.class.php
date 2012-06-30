<?php
// wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

/**
 * Separates top threads on BoardPage.
 *
 * @author	Stefan Hahn
 * @copyright	2010-2012 Stefan Hahn
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.leon.wbb.separateTopThreads
 * @subpackage	system.event.listener
 * @category	Burning Board
 */
class BoardPageSeparateTopThreadsListener implements EventListener {
	protected static $announcementsStatus = 1;
	protected static $stickiesStatus = 1;
	
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if ($eventName === 'readData') {
			// get status of the two lists
			if (WCF::getUser()->userID) {
				self::$announcementsStatus = intval(WCF::getUser()->announcementsStatus);
				self::$stickiesStatus = intval(WCF::getUser()->stickiesStatus);
			}
			else {
				if (WCF::getSession()->getVar('announcementsStatus') !== null) self::$announcementsStatus = WCF::getSession()->getVar('announcementsStatus');
				if (WCF::getSession()->getVar('stickiesStatus') !== null) self::$stickiesStatus = WCF::getSession()->getVar('stickiesStatus');
			}
		}
		else if ($eventName === 'assignVariables') {
			// variables
			$announcements = array();
			$newAnnouncements = 0;
			$stickies = array();
			$newStickies = 0;
			
			// get top threads
			$topThreads = ($eventObj->threadList != null) ? $eventObj->threadList->topThreads : null;
			
			// categorize top threads
			if ($topThreads != null) {
				foreach ($topThreads as $topThread) {
					if ($topThread->isAnnouncement) {
						$announcements[] = $topThread;
						if ($topThread->isNew()) $newAnnouncements++;
					}
					
					if ($topThread->isSticky) {
						$stickies[] = $topThread;
						if ($topThread->isNew()) $newStickies++;
					}
				}
			}
			
			// assign announcements and stickies to the template engine
			WCF::getTPL()->assign(array(
				'announcements' => $announcements,
				'newAnnouncements' => $newAnnouncements,
				'announcementsStatus' => self::$announcementsStatus,
				'stickies' => $stickies,
				'newStickies' => $newStickies,
				'stickiesStatus' => self::$stickiesStatus
			));
		}
	}
}
