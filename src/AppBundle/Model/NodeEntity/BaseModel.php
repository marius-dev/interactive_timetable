<?php

namespace AppBundle\Model\NodeEntity;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * Class BaseModel
 * @package AppBundle\Model
 *
 * @OGM\Node(label="BaseNode")
 */
abstract class BaseModel
{
    /**
     * @OGM\GraphId()
     * @var int
     */
    protected $id;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}