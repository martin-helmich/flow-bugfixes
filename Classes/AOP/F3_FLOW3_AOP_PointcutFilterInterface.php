<?php
declare(ENCODING = 'utf-8');
namespace F3::FLOW3::AOP;

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
 * @subpackage AOP
 * @version $Id$
 */

/**
 * The contract for an AOP Pointcut Filter class
 *
 * @package FLOW3
 * @subpackage AOP
 * @version $Id:F3::FLOW3::AOP::PointcutFilterInterface.php 201 2007-03-30 11:18:30Z robert $
 * @author Robert Lemke <robert@typo3.org>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
interface PointcutFilterInterface {

	/**
	 * Checks if the specified class and method matches against the filter
	 *
	 * @param F3::FLOW3::Reflection::ReflectionClass $class: The class to check the name of
	 * @param F3::FLOW3::Reflection::Method $method: The method to check the name of
	 * @param mixed $pointcutQueryIdentifier: Some identifier for this query - must at least differ from a previous identifier. Used for circular reference detection.
	 * @return boolean TRUE if the class / method match, otherwise FALSE
	 */
	public function matches(F3::FLOW3::Reflection::ReflectionClass $class, F3::FLOW3::Reflection::Method $method, $pointcutQueryIdentifier);
}

?>