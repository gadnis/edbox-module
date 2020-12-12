<?php

namespace Edbox\PSModule\EvaApskaitaGate\Hook;

/**
 * Example hookClass to understand how to use it
 * Such class must be placed inside your module.php
 * Example:
 *     protected $hookClasses = [
 *         \Edbox\PSModule\EvaApskaitaGate\Hook::class,
 *     ];
 */
class ExampleHook extends AbstractHook
{
    const AVAILABLE_HOOKS = [
        'actionAdminControllerSetMedia',
    ];

    /**
     * Set media, js, css for Admin pages
     *
     * @return mixed
     */
    public function actionAdminControllerSetMedia()
    {
        $this->context->controller->addCSS($this->module->getPathUri() . 'views/css/example.css');
    }

}
