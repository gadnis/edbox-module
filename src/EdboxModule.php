<?php

namespace Edbox\PSModule\EdboxModule;

use Edbox\PSModule\EdboxModule\Concerns\DispatchesHooks;
use Edbox\PSModule\EdboxModule\Concerns\HasDatabase;
use Edbox\PSModule\EdboxModule\Hook\HookDispatcher;
use Edbox\PSModule\EdboxModule\Concerns\HasTabs;
use Context;
use Module;
use Tools;

abstract class EdboxModule extends Module
{
    use HasDatabase, DispatchesHooks, HasTabs;

    /** @var string */
    protected $translationDomain = '';

    /**
     * Constructor.
     *
     * @param string $name Module unique name
     * @param Context $context
     */
    public function __construct($name = null, Context $context = null)
    {
        $this->hookDispatcher = new HookDispatcher($this);

        parent::__construct($name, $context);
    }

    /**
     * Return current context
     *
     * @return Context
     */
    public function getContext()
    {
        return !empty($this->context) ? $this->context : Context::getContext();
    }

    /**
     * Insert module into datable
     * and all the hooks if any is available from hook dispatcher
     */
    public function install()
    {
        $installed = parent::install()
            && $this->registerHook($this->getHookDispatcher()->getAvailableHooks());

        // Installation failed (or hook registration) => uninstall the module
        if (!$installed) {
            $this->uninstall();

            return false;
        }

        if (!$this->installTabs()) {
            $this->_errors[] = $this->context->getTranslator()->trans('The module tabs is not installed.', [], $this->getTranslationDomain());

            return false;
        }

        return true;
    }

    /**
     * Delete module from datable
     * and remove tabs
     *
     * @return bool result
     */
    public function uninstall()
    {
        if (!$this->removeTabs()) {

            $this->_errors[] = $this->context->getTranslator()->trans('The module tabs is not removed.', [], $this->getTranslationDomain());

            return false;
        }

        return parent::uninstall();
    }


    /**
     * @inherit
     */
    public function enable($force_all = false)
    {
        if (! $this->installTabs()) {
            $this->_errors[] = $this->context->getTranslator()->trans('The module tabs is not installed when enabling module.', [], $this->getTranslationDomain());

            return false;
        }

        return parent::enable($force_all);
    }

    /**
     * Get Translation domain for the module
     *
     *  @return string
     */
    public function getTranslationDomain($end = 'Admin')
    {
        if (!$this->translationDomain) {
            $this->translationDomain = 'Modules.' . Tools::ucwords($this->name) . '.' . $end;
        }
        return $this->translationDomain;
    }
}
