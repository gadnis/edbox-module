<?php

namespace Edbox\PSModule\EdboxModule;

use Edbox\PSModule\EdboxModule\Concerns\FillableObjectModel;
use Edbox\PSModule\EdboxModule\Concerns\PresentableObjectModel;

abstract class EdboxModel extends \ObjectModel
{
    use FillableObjectModel, PresentableObjectModel;
}