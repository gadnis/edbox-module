<?php

namespace Edbox\PSModule\EdboxModule\Hook;

use Edbox\PSModule\EdboxModule\EdboxModule;

/**
 * Class works with Hook\AbstractHook instances in order to reduce EdboxModule.php size.
 *
 * The dispatch method is called from the __call method in the module class.
 */
class HookDispatcher
{

    /**
     * List of available hooks
     *
     * @var string[]
     */
    protected $availableHooks = [];

    /**
     * Hook classes
     *
     * @var Hook\AbstractHook[]
     */
    protected $hooks = [];

    /**
     * Module
     *
     * @var EdboxPriceMonitor
     */
    protected $module;

    /**
     * Init hooks
     *
     * @param EdboxPriceMonitor $module
     */
    public function __construct(EdboxModule $module)
    {
        $this->module = $module;

        $classes = $module->getHookClasses();

        foreach ($classes as $hookClass) {
            $hook = new $hookClass($this->module);
            $this->availableHooks = array_merge($this->availableHooks, $hook->getAvailableHooks());
            $this->hooks[] = $hook;
        }
    }

    /**
     * Get available hooks
     *
     * @return string[]
     */
    public function getAvailableHooks()
    {
        return $this->availableHooks;
    }

    /**
     * Find hook and dispatch it
     *
     * @param string $hookName
     * @param array $params
     *
     * @return mixed
     */
    public function dispatch($hookName, array $params = [])
    {

        $hookName = preg_replace('~^hook~', '', $hookName);

        foreach ($this->hooks as $hook) {
            if (method_exists($hook, $hookName)) {
                return call_user_func([$hook, $hookName], $params);
            }
        }

    }
}
