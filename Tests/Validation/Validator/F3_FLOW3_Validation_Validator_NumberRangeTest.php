<?php
declare(ENCODING = 'utf-8');
namespace F3::FLOW3::Validation::Validator;

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
 * @subpackage Tests
 * @version $Id: F3::FLOW3::Validation::Validator::NumberRangeTest.php 688 2008-04-03 09:35:36Z andi $
 */

/**
 * Testcase for the number range validator
 *
 * @package FLOW3
 * @subpackage Tests
 * @version $Id: F3::FLOW3::Validation::Validator::NumberRangeTest.php 688 2008-04-03 09:35:36Z andi $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class NumberRangeTest extends F3::Testing::BaseTestCase {

	/**
	 * @test
	 * @author Andreas Förthner <andreas.foerthner@netlogix.de>
	 */
	public function numberRangeValidatorReturnsTrueForASimpleIntegerInRange() {
		$numberRangeValidator = new F3::FLOW3::Validation::Validator::NumberRange(0, 1000);
		$validationErrors = new F3::FLOW3::Validation::Errors();

		$this->assertTrue($numberRangeValidator->isValidProperty(10.5, $validationErrors));
	}

	/**
	 * @test
	 * @author Andreas Förthner <andreas.foerthner@netlogix.de>
	 */
	public function numberRangeValidatorReturnsFalseForANumberOutOfRange() {
		$numberRangeValidator = new F3::FLOW3::Validation::Validator::NumberRange(0, 1000);
		$validationErrors = new F3::FLOW3::Validation::Errors();

		$this->assertFalse($numberRangeValidator->isValidProperty(1000.1, $validationErrors));
	}

	/**
	 * @test
	 * @author Andreas Förthner <andreas.foerthner@netlogix.de>
	 */
	public function numberRangeValidatorReturnsTrueForANumberInReversedRange() {
		$numberRangeValidator = new F3::FLOW3::Validation::Validator::NumberRange(1000, 0);
		$validationErrors = new F3::FLOW3::Validation::Errors();

		$this->assertTrue($numberRangeValidator->isValidProperty(100, $validationErrors));
	}

	/**
	 * @test
	 * @author Andreas Förthner <andreas.foerthner@netlogix.de>
	 */
	public function numberRangeValidatorReturnsFalseForAString() {
		$numberRangeValidator = new F3::FLOW3::Validation::Validator::NumberRange(0, 1000);
		$validationErrors = new F3::FLOW3::Validation::Errors();

		$this->assertFalse($numberRangeValidator->isValidProperty('not a number', $validationErrors));
	}
}

?>