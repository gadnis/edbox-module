<?php

namespace Edbox\PSModule\EdboxModule\Concerns;

use Db;

trait HasDatabase
{
    /**
     * @var Db
     */
    protected $database;

    /**
     * Return the current database instance
     *
     * @return Db
     */
    public function getDatabase()
    {
        if ($this->database === null) {
            $this->database = Db::getInstance();
        }
        return $this->database;
    }
}
