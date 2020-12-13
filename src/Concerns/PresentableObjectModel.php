<?php

namespace Edbox\PSModule\EdboxModule\Concerns;

use Doctrine\Common\Collections\ArrayCollection;
use PrestaShop\PrestaShop\Adapter\Presenter\Object\ObjectPresenter;

trait PresentableObjectModel
{
    /**
     * Get presented object as array
     *
     * @return array
     *
     * @throws Exception
     */
    public function toArray()
    {
        return (new ObjectPresenter)->present($this);
    }

    /**
     * Get presented object as symfony array collection
     *
     * @return ArrayCollection
     *
     * @throws Exception
     */
    public function toCollection()
    {
        return new ArrayCollection($this->toArray());
    }
}