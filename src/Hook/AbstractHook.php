<?php

namespace Edbox\PSModule\EdboxModule\Hook;

use Edbox\PSModule\EdboxModule\EdboxModule;
use Context;
use Db;

abstract class AbstractHook
{
    const AVAILABLE_HOOKS = [];

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var EdboxModule
     */
    protected $module;

    /**
     * @var Db
     */
    protected $database;

    public function __construct(EdboxModule $module)
    {
        $this->module = $module;
        $this->context = $module->getContext();
        $this->database = $module->getDatabase();
    }

    /**
     * @return array
     */
    public function getAvailableHooks()
    {
        return static::AVAILABLE_HOOKS;
    }
}
