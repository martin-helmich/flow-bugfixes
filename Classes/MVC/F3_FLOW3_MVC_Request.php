<?php
declare(ENCODING = 'utf-8');
namespace F3::FLOW3::MVC;

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
 * @subpackage MVC
 * @version $Id:F3::FLOW3::MVC::Request.php 467 2008-02-06 19:34:56Z robert $
 */

/**
 * Represents a generic request.
 *
 * @package FLOW3
 * @subpackage MVC
 * @version $Id:F3::FLOW3::MVC::Request.php 467 2008-02-06 19:34:56Z robert $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 * @scope prototype
 */
class Request {

	const PATTERN_MATCH_FORMAT = '/^[a-z0-9]{1,5}$/';

	/**
	 * @var F3::FLOW3::Component::ManagerInterface
	 */
	protected $componentManager;

	/**
	 * @var F3::FLOW3::Package::ManagerInterface
	 */
	protected $packageManager;

	/**
	 * Pattern after which the controller component name is built
	 *
	 * @var string
	 */
	protected $controllerComponentNamePattern = 'F3::@package::Controller::@controllerController';

	/**
	 * Pattern after which the view component name is built
	 *
	 * @var string
	 */
	protected $viewComponentNamePattern = 'F3::@package::View::@controller@action@format';

	/**
	 * Package key of the controller which is supposed to handle this request.
	 *
	 * @var string
	 */
	protected $controllerPackageKey = 'FLOW3';

	/**
	 * @var string Component name of the controller which is supposed to handle this request.
	 */
	protected $controllerName = 'Default';

	/**
	 * @var string Name of the action the controller is supposed to take.
	 */
	protected $controllerActionName = 'index';

	/**
	 * @var ArrayObject The arguments for this request
	 */
	protected $arguments;

	/**
	 * @var string The requested representation format
	 */
	protected $format = 'txt';

	/**
	 * @var boolean If this request has been changed and needs to be dispatched again
	 */
	protected $dispatched = FALSE;

	/**
	 * Constructs this request
	 *
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function __construct() {
		$this->arguments = new ::ArrayObject;
	}

	/**
	 * Injects the component manager
	 *
	 * @param F3::FLOW3::Component::ManagerInterface $componentManager A reference to the component manager
	 * @return void
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function injectComponentManager(F3::FLOW3::Component::ManagerInterface $componentManager) {
		$this->componentManager = $componentManager;
	}

	/**
	 * Injects the package
	 *
	 * @param F3::FLOW3::Package::ManagerInterface $packageManager A reference to the package manager
	 * @return void
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function injectPackageManager(F3::FLOW3::Package::ManagerInterface $packageManager) {
		$this->packageManager = $packageManager;
	}

	/**
	 * Sets the dispatched flag
	 *
	 * @param boolean $flag If this request has been dispatched
	 * @return void
	 */
	public function setDispatched($flag) {
		$this->dispatched = $flag ? TRUE : FALSE;
	}

	/**
	 * If this request has been dispatched and addressed by the responsible
	 * controller and the response is ready to be sent.
	 *
	 * The dispatcher will try to dispatch the request again if it has not been
	 * addressed yet.
	 *
	 * @return boolean TRUE if this request has been disptached sucessfully
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function isDispatched() {
		return $this->dispatched;
	}

	/**
	 * Returns the component name of the controller defined by the package key and
	 * controller name
	 *
	 * @return string The controller's Component Name
	 * @throws F3::FLOW3::MVC:Exception::NoSuchController if the controller does not exist
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function getControllerComponentName() {
		$lowercaseComponentName = str_replace('@package', $this->controllerPackageKey, $this->controllerComponentNamePattern);
		$lowercaseComponentName = strtolower(str_replace('@controller', $this->controllerName, $lowercaseComponentName));
		$componentName = $this->componentManager->getCaseSensitiveComponentName($lowercaseComponentName);
		if ($componentName === FALSE) throw new F3::FLOW3::MVC::Exception::NoSuchController('The controller component "' . $lowercaseComponentName . '" does not exist.', 1220884009);

		return $componentName;
	}

	/**
	 * Sets the pattern for building the controller component name.
	 *
	 * The pattern may contain the placeholders "@package" and "@controller" which will be substituted
	 * by the real package key and controller name.
	 *
	 * @param string $pattern The pattern
	 * @return void
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function setControllerComponentNamePattern($pattern) {
		$this->controllerComponentNamePattern = $pattern;
	}

	/**
	 * Returns the pattern for building the controller component name.
	 *
	 * @return string $pattern The pattern
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function getControllerComponentNamePattern() {
		return $this->controllerComponentNamePattern;
	}

	/**
	 * Sets the pattern for building the view component name
	 *
	 * @param string $pattern The view component name pattern, eg. F3::@package::View::@controller@action
	 * @return void
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function setViewComponentNamePattern($pattern) {
		if (!is_string($pattern)) throw new ::InvalidArgumentException('The view component name pattern must be a valid string, ' . gettype($pattern) . ' given.', 1221563219);
		$this->viewComponentNamePattern = $pattern;
	}

	/**
	 * Returns the View Component Name Pattern
	 *
	 * @return string The pattern
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function getViewComponentNamePattern() {
		return $this->viewComponentNamePattern;
	}

	/**
	 * Returns the view's (possible) component name according to the defined view component
	 * name pattern and the specified values for package, controller, action and format.
	 *
	 * If no valid view component name could be resolved, FALSE is returned
	 *
	 * @return mixed Either the view component name or FALSE
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function getViewComponentName() {
		$possibleViewName = $this->viewComponentNamePattern;
		$possibleViewName = str_replace('@package', $this->controllerPackageKey, $possibleViewName);
		$possibleViewName = str_replace('@controller', $this->controllerName, $possibleViewName);
		$possibleViewName = str_replace('@action', $this->controllerActionName, $possibleViewName);

		$viewComponentName = $this->componentManager->getCaseSensitiveComponentName(str_replace('@format', $this->format, $possibleViewName));
		if ($viewComponentName === FALSE) {
			$viewComponentName = $this->componentManager->getCaseSensitiveComponentName(str_replace('@format', '', $possibleViewName));
		}
		return $viewComponentName;
	}

	/**
	 * Sets the package key of the controller.
	 *
	 * @param string $packageKey The package key.
	 * @return void
	 * @throws F3::FLOW3::MVC::Exception::InvalidPackageKey if the package key is not valid
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function setControllerPackageKey($packageKey) {
		$upperCamelCasedPackageKey = $this->packageManager->getCaseSensitivePackageKey($packageKey);
		if ($upperCamelCasedPackageKey === FALSE) throw new F3::FLOW3::MVC::Exception::InvalidPackageKey('"' . $packageKey . '" is not a valid package key.', 1217961104);
		$this->controllerPackageKey = $upperCamelCasedPackageKey;
	}

	/**
	 * Returns the package key of the specified controller.
	 *
	 * @return string The package key
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function getControllerPackageKey() {
		return $this->controllerPackageKey;
	}

	/**
	 * Sets the name of the controller which is supposed to handle the request.
	 * Note: This is not the component name of the controller!
	 *
	 * @param string $controllerName Name of the controller
	 * @return void
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function setControllerName($controllerName) {
		if (!is_string($controllerName)) throw new F3::FLOW3::MVC::Exception::InvalidControllerName('The controller name must be a valid string, ' . gettype($controllerName) . ' given.', 1187176358);
		if (strpos($controllerName, '_') !== FALSE) throw new F3::FLOW3::MVC::Exception::InvalidControllerName('The controller name must not contain underscores.', 1217846412);
		$this->controllerName = $controllerName;
	}

	/**
	 * Returns the component name of the controller supposed to handle this request, if one
	 * was set already (if not, the name of the default controller is returned)
	 *
	 * @return string Component name of the controller
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function getControllerName() {
		return $this->controllerName;
	}

	/**
	 * Sets the name of the action contained in this request.
	 *
	 * Note that the action name must start with a lower case letter.
	 *
	 * @param string $actionName: Name of the action to execute by the controller
	 * @return void
	 * @throws F3::FLOW3::MVC::Exception::InvalidActionName if the action name is not valid
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function setControllerActionName($actionName) {
		if (!is_string($actionName)) throw new F3::FLOW3::MVC::Exception::InvalidActionName('The action name must be a valid string, ' . gettype($actionName) . ' given (' . $actionName . ').', 1187176358);
		if ($actionName{0} !== F3::PHP6::Functions::strtolower($actionName{0})) throw new F3::FLOW3::MVC::Exception::InvalidActionName('The action name must start with a lower case letter, "' . $actionName . '" does not match this criteria.', 1218473352);
		$this->controllerActionName = $actionName;
	}

	/**
	 * Returns the name of the action the controller is supposed to execute.
	 *
	 * @return string Action name
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function getControllerActionName() {
		return $this->controllerActionName;
	}

	/**
	 * Sets the value of the specified argument
	 *
	 * @param string $argumentName Name of the argument to set
	 * @param mixed $value The new value
	 * @return void
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function setArgument($argumentName, $value) {
		if (!is_string($argumentName) || F3::PHP6::Functions::strlen($argumentName) == 0) throw new F3::FLOW3::MVC::Exception::InvalidArgumentName('Invalid argument name.', 1210858767);
		$this->arguments[$argumentName] = $value;
	}

	/**
	 * Sets the whole arguments ArrayObject and therefore replaces any arguments
	 * which existed before.
	 *
	 * @param ::ArrayObject $arguments An ArrayObject of argument names and their values
	 * @return void
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function setArguments(::ArrayObject $arguments) {
		$this->arguments = $arguments;
	}

	/**
	 * Returns an ArrayObject of arguments and their values
	 *
	 * @return ::ArrayObject ArrayObject of arguments and their values (which may be arguments and values as well)
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function getArguments() {
		return $this->arguments;
	}

	/**
	 * Sets the requested representation format
	 *
	 * @param string $format The desired format, something like "html", "xml", "png", "json" or the like.
	 * @return void
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function setFormat($format) {
		if (!preg_match(self::PATTERN_MATCH_FORMAT, $format)) throw new F3::FLOW3::MVC::Exception::InvalidFormat('An invalid request format (' . $format . ') was given.', 1218015038);
		$this->format = $format;
	}

	/**
	 * Returns the requested representation format
	 *
	 * @return string The desired format, something like "html", "xml", "png", "json" or the like.
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function getFormat() {
		return $this->format;
	}

	/**
	 * Returns the value of the specified argument
	 *
	 * @param string $argumentName Name of the argument
	 * @return string Value of the argument
	 * @author Robert Lemke <robert@typo3.org>
	 * @throws F3::FLOW3::MVC::Exception::NoSuchArgument if such an argument does not exist
	 */
	public function getArgument($argumentName) {
		if (!isset($this->arguments[$argumentName])) throw new F3::FLOW3::MVC::Exception::NoSuchArgument('An argument "' . $argumentName . '" does not exist for this request.', 1176558158);
		return $this->arguments[$argumentName];
	}

	/**
	 * Checks if an argument of the given name exists (is set)
	 *
	 * @param string $argumentName Name of the argument to check
	 * @return boolean TRUE if the argument is set, otherwise FALSE
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function hasArgument($argumentName) {
		return isset($this->arguments[$argumentName]);
	}
}
?>