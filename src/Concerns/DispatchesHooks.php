<?php

namespace Edbox\PSModule\EdboxModule\Concerns;

use Edbox\PSModule\EvaApskaitaGate\Hook\HookDispatcher;

trait DispatchesHooks
{
    /**
     * @var array
     */
    protected $hookClasses = [];

    /**
     * @var HookDispatcher
     */
    protected $hookDispatcher;

    /**
     * Dispatch hooks
     *
     * @param string $methodName
     * @param array $arguments
     */
    public function __call($methodName, array $arguments)
    {
        return $this->getHookDispatcher()->dispatch(
            $methodName,
            !empty($arguments[0]) ? $arguments[0] : []
        );
    }


    /**
     * @return HookDispatcher
     */
    public function getHookDispatcher()
    {
        return $this->hookDispatcher;
    }

    /**
     * Get hook classes for the module
     *
     * @return array
     */
    public function getHookClasses()
    {
        return is_array($this->hookClasses) ? $this->hookClasses : [];
    }
}
