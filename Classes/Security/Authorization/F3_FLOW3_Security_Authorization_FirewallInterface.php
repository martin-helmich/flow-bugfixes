<?php
declare(ENCODING = 'utf-8');
namespace F3::FLOW3::Security::Authorization;

/*                                                                        *
 * This script is part of the TYPO3 project - inspiring people to share!  *
 *                                                                        *
 * TYPO3 is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License version 2 as published by  *
 * the Free Software Foundation.                                          *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        */

/**
 * @package FLOW3
 * @subpackage Security
 * @version $Id:$
 */

/**
 * Contract for firewall
 *
 * @package FLOW3
 * @subpackage Security
 * @version $Id:$
 * @author Andreas Förthner <andreas.foerthner@netlogix.de>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
interface FirewallInterface {

	/**
	 * Analyzes a request against the configured firewall rules and blocks
	 * any illegal request.
	 *
	 * @param F3::FLOW3::MVC::Request $request The request to be analyzed
	 * @return void
	 */
	public function blockIllegalRequests(F3::FLOW3::MVC::Request $request);
}

?>