<?php
/**
 * Initialize all the classes for tabs and sub-tabs.
 *
 * @package ultimate-auto-updates
 */

namespace SCEOptions\Includes\Admin_Tabs;

/**
 * Init all the classes and sub-tabs.
 */
class Init_Tabs {
	/**
	 * Class runner.
	 */
	public function run() {
		new Main();
		new Appearance();
		new Translations();
		new License();
		new Support();
	}
}
