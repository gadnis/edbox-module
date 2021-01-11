<?php

namespace Edbox\PSModule\EdboxModule\Concerns;

use PrestaShop\PrestaShop\Adapter\ContainerFinder as PrestaContainerFinder;
use PrestaShop\PrestaShop\Adapter\ContainerBuilder;
use Exception;
use Context;
use AppKernel;

/**
 * Provides symfony container and container cache clear methods
 */
trait HasContainer
{
    public function getContainer($enveronment = 'prod')
    {
        // Lets build
        $kernel = new AppKernel($enveronment, _PS_MODE_DEV_);
        $kernel->boot();
        $container = $kernel->getContainer();

        return $container;
    }

    public function findContainer()
    {
        $context = Context::getContext();
        $containerFinder = new PrestaContainerFinder($context);
        $container = null;

        try {
            $container = $containerFinder->getContainer();

        } catch (Exception $e) {
            // Kernel Container is not available
            // Lets build it
            $container = $this->getContainer();
        }

        return $container;
    }

    /**
     * Clear Kernel cache
     *
     * @return nothing
     */
    public function clearCache($env = 'prod')
    {
        $kernel = new AppKernel($env, true);
        $kernel->boot();

        // $kernel = $this->get('kernel'); // or like this when in controller
        $application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
        $application->setAutoExit(false);

        // Try to increase the maximum execution time
        set_time_limit(300); // 5 minutes
        // ini_set('max_execution_time', '300');

        $application->run(new \Symfony\Component\Console\Input\ArrayInput(
            ['command' => 'cache:clear']
        ));
    }
}
