<?php

namespace Edbox\PSModule\EdboxModule\Concerns;

use PrestaShop\PrestaShop\Core\Foundation\IoC\Container\ServiceLocator;

trait HasLogger
{
    /**
     * @var LegacyLogger
     */
    protected $logger;

    /**
     * Return the current logger instance
     *
     * @return Db
     */
    public function getLogger()
    {
        if ($this->logger === null) {
            $this->logger = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Adapter\\LegacyLogger');
        }
        return $this->logger;
    }
}
