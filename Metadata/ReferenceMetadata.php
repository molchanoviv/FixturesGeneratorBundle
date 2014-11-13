<?php
/**
 * This file is part of ONP.
 *
 * Copyright (c) 2014 Opensoft (http://opensoftdev.com)
 *
 * The unauthorized use of this code outside the boundaries of
 * Opensoft is prohibited.
 */
 
namespace Opensoft\FixturesGeneratorBundle\Metadata;

use Doctrine\Common\Inflector\Inflector;

/**
 * Opensoft\FixturesGeneratorBundle\Metadata\ReferenceMetadata
 *
 * @author Ivan Molchanov <ivan.molchanov@opensoftdev.ru>
 */
class ReferenceMetadata
{
    /**
     * @var array
     */
    protected $associationMapping;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * @param array $associationMapping
     */
    public function __construct(array $associationMapping)
    {
        $this->associationMapping = $associationMapping;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->associationMapping['targetEntity'];
    }

     /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->associationMapping['fieldName'];
    }

    /**
     * @return array|\string[]
     */
    public function getJoinColumns()
    {
        return isset($this->associationMapping['joinColumns']) ? $this->associationMapping['joinColumns'] : [];
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->associationMapping['type'];
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return Inflector::tableize(substr(strrchr($this->associationMapping['targetEntity'], "\\"), 1) . '_');
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return Inflector::tableize(substr(strrchr($this->associationMapping['targetEntity'], "\\"), 1)) . '_alias';
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param string $tableName
     * @return ReferenceMetadata
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;

        return $this;
    }

    /**
     * @return array
     */
    public function getJoinTable()
    {
        return isset($this->associationMapping['joinTable']) ? $this->associationMapping['joinTable'] : [];
    }
}
