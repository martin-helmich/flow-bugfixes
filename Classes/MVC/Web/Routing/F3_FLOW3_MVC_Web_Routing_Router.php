<?php
declare(ENCODING = 'utf-8');
namespace F3::FLOW3::MVC::Web::Routing;

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
 * @version $Id$
 */

/**
 * The default web router
 *
 * @package FLOW3
 * @subpackage MVC
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class Router implements F3::FLOW3::MVC::Web::Routing::RouterInterface {

	/**
	 * @var F3::FLOW3::Component::ManagerInterface $componentManager: A reference to the Component Manager
	 */
	protected $componentManager;

	/**
	 * @var F3::FLOW3::Component::FactoryInterface $componentFactory
	 */
	protected $componentFactory;

	/**
	 * @var F3::FLOW3::Utility::Environment
	 */
	protected $utilityEnvironment;

	/**
	 * @var F3::FLOW3::Configuration::Container The FLOW3 configuration
	 */
	protected $configuration;

	/**
	 * Array of routes to match against
	 * @var array
	 */
	protected $routes = array();

	/**
	 * Constructor
	 *
	 * @param F3::FLOW3::Component::ManagerInterface $componentManager A reference to the component manager
	 * @param F3::FLOW3::Utility::Environment $utilityEnvironment A reference to the environment
	 * @param F3::FLOW3::Configuration::Manager $configurationManager A reference to the configuration manager
	 * @return void
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function __construct(F3::FLOW3::Component::ManagerInterface $componentManager, F3::FLOW3::Component::FactoryInterface $componentFactory, F3::FLOW3::Utility::Environment $utilityEnvironment) {
		$this->componentManager = $componentManager;
		$this->componentFactory = $componentFactory;
		$this->utilityEnvironment = $utilityEnvironment;
	}

	/**
	 * Sets the routes configuration.
	 *
	 * @param F3::FLOW3::Configuration::Container $configuration The routes configuration
	 * @return void
	 * @author Bastian Waidelich <bastian@typo3.org>
	 */
	public function setRoutesConfiguration(F3::FLOW3::Configuration::Container $routesConfiguration) {
		foreach ($routesConfiguration as $routeName => $routeConfiguration) {
			$route = $this->componentFactory->getComponent('F3::FLOW3::MVC::Web::Routing::Route');
			$route->setUrlPattern($routeConfiguration->urlPattern);
			$route->setDefaults($routeConfiguration->defaults);
			if (isset($routeConfiguration->controllerComponentNamePattern)) $route->setControllerComponentNamePattern($routeConfiguration->controllerComponentNamePattern);
			if (isset($routeConfiguration->viewComponentNamePattern)) $route->setViewComponentNamePattern($routeConfiguration->viewComponentNamePattern);
			$this->routes[$routeName] = $route;
		}
	}

	/**
	 * Routes the specified web request by setting the controller name, action and possible
	 * parameters. If the request could not be routed, it will be left untouched.
	 *
	 * @param F3::FLOW3::MVC::Web::Request $request The web request to be analyzed. Will be modified by the router.
	 * @return void
	 * @author Robert Lemke <robert@typo3.org>
	 * @author Bastian Waidelich <bastian@typo3.org>
	 */
	public function route(F3::FLOW3::MVC::Web::Request $request) {
		$requestURI = $request->getRequestURI();
		$requestPath = F3::PHP6::Functions::substr($requestURI->getPath(), F3::PHP6::Functions::strlen((string)$request->getBaseURI()->getPath()));
		if (F3::PHP6::Functions::substr($requestPath, 0, 9) == 'index.php' || F3::PHP6::Functions::substr($requestPath, 0, 13) == 'index_dev.php') {
			$requestPath = strstr($requestPath, '/');
		}

		foreach (array_reverse($this->routes) as $route) {
			if ($route->matches($requestPath)) {
				$matchResults = $route->getMatchResults();
				foreach ($matchResults as $argumentName => $argumentValue) {
					if ($argumentName{0} == '@') {
						switch ($argumentName) {
							case '@package' :
								$request->setControllerPackageKey($argumentValue);
							break;
							case '@controller' :
								$request->setControllerName($argumentValue);
							break;
							case '@action' :
								$request->setControllerActionName($argumentValue);
							break;
							case '@format' :
								$request->setFormat($argumentValue);
							break;
						}
					} else {
						$request->setArgument($argumentName, $argumentValue);
					}
				}
				$pattern = $route->getControllerComponentNamePattern();
				if ($pattern !== NULL) $request->setControllerComponentNamePattern($pattern);

				$pattern = $route->getViewComponentNamePattern();
				if ($pattern !== NULL) $request->setViewComponentNamePattern($pattern);
				break;
			}
		}

		foreach ($this->utilityEnvironment->getPOSTArguments() as $argumentName => $argumentValue) {
			$request->setArgument($argumentName, $argumentValue);
		}
		foreach ($requestURI->getArguments() as $argumentName => $argumentValue) {
			$request->setArgument($argumentName, $argumentValue);
		}
	}

	/**
	 * Builds the corresponding url (excluding protocol and host) by iterating through all configured routes
	 * and calling their respective resolves()-method.
	 * If no matching route is found, an empty string is returned.
	 *
	 * @param array $routeValues Key/value pairs to be resolved. E.g. array('@package' => 'MyPackage', '@controller' => 'MyController');
	 * @return string
	 * @author Bastian Waidelich <bastian@typo3.org>
	 */
	public function resolve(array $routeValues) {
		foreach (array_reverse($this->routes) as $route) {
			if ($route->resolves($routeValues)) {
				return $route->getMatchingURL();
			}
		}
		return '';
	}
}
?>