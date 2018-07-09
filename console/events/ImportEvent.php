<?php


namespace solbianca\fias\console\events;


use yii\base\Event;
use yii\db\Connection;

class ImportEvent extends Event
{

    /**
     * @var Connection
     */
    private $db;

    /**
     * ImportEvent constructor.
     *
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
        parent::__construct();
    }


    public function connection()
    {
        return $this->db;
    }

}