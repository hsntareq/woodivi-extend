<?php
/**
 * Hook Loader Class
 *
 * @package WooDiviExtended
 */

namespace WooDiviExtended;

/**
 * Hook Loader
 *
 * Manages registration of WordPress hooks (actions and filters)
 */
class Loader {

	/**
	 * Array of registered actions
	 *
	 * @var array
	 */
	private $actions = array();

	/**
	 * Array of registered filters
	 *
	 * @var array
	 */
	private $filters = array();

	/**
	 * Add a new action
	 *
	 * @param string $hook           The name of the action hook.
	 * @param object $component      A reference to the instance of the object on which the action is defined.
	 * @param string $callback       The name of the function definition on the $component.
	 * @param int    $priority       Optional. The priority at which the function should be fired. Default is 10.
	 * @param int    $accepted_args  Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 *
	 * @return void
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);
	}

	/**
	 * Add a new filter
	 *
	 * @param string $hook           The name of the filter hook.
	 * @param object $component      A reference to the instance of the object on which the filter is defined.
	 * @param string $callback       The name of the function definition on the $component.
	 * @param int    $priority       Optional. The priority at which the function should be fired. Default is 10.
	 * @param int    $accepted_args  Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 *
	 * @return void
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);
	}

	/**
	 * Run all registered hooks
	 *
	 * @return void
	 */
	public function run() {
		// Register all actions
		foreach ( $this->actions as $action ) {
			add_action(
				$action['hook'],
				array( $action['component'], $action['callback'] ),
				$action['priority'],
				$action['accepted_args']
			);
		}

		// Register all filters
		foreach ( $this->filters as $filter ) {
			add_filter(
				$filter['hook'],
				array( $filter['component'], $filter['callback'] ),
				$filter['priority'],
				$filter['accepted_args']
			);
		}
	}
}
