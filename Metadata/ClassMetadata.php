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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\ClassMetadata as DoctrineClassMetadata;
use Doctrine\Common\Persistence\Mapping\ClassMetadata as DoctrineClassMetadataInterface;
use Doctrine\Common\Inflector\Inflector;

/**
 * Opensoft\FixturesGeneratorBundle\Metadata\ClassMetadata
 *
 * @author Ivan Molchanov <ivan.molchanov@opensoftdev.ru>
 */
class ClassMetadata
{
    /**
     * @var DoctrineClassMetadata
     */
    protected $doctrineClassMetadata;

    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var ArrayCollection|ReferenceMetadata[]
     */
    protected $references;

    /**
     * @var string
     */
    protected $wherePart;

    /**
     * @var ArrayCollection
     */
    protected $excludedFields;

    /**
     * @param DoctrineClassMetadataInterface $classMetadata
     */
    public function __construct(DoctrineClassMetadataInterface $classMetadata)
    {
        $this->doctrineClassMetadata = $classMetadata;
        $this->references = new ArrayCollection();
        $this->excludedFields = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     * @return ClassMetadata
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * @return DoctrineClassMetadata
     */
    public function getDoctrineClassMetadata()
    {
        return $this->doctrineClassMetadata;
    }

    /**
     * @param DoctrineClassMetadata $doctrineClassMetadata
     * @return ClassMetadata
     */
    public function setDoctrineClassMetadata($doctrineClassMetadata)
    {
        $this->doctrineClassMetadata = $doctrineClassMetadata;

        return $this;
    }

    /**
     * @return ArrayCollection|ReferenceMetadata[]
     */
    public function getReferences()
    {
        return $this->references;
    }

    /**
     * @param ArrayCollection|ReferenceMetadata[] $references
     * @return ClassMetadata
     */
    public function setReferences($references)
    {
        $this->references = $references;

        return $this;
    }

    /**
     * @param ReferenceMetadata $reference
     * @return ClassMetadata
     */
    public function addReference(ReferenceMetadata $reference)
    {
        $this->references->set($reference->getFieldName(), $reference);

        return $this;
    }

    /**
     * @param ReferenceMetadata $reference
     * @return ClassMetadata
     */
    public function removeReference(ReferenceMetadata $reference)
    {
        $this->references->removeElement($reference);

        return $this;
    }

    /**
     * @param string $fieldName
     * @return ClassMetadata
     */
    public function removeReferenceByFieldName($fieldName)
    {
        $this->references->remove($fieldName);

        return $this;
    }

    /**
     * @param ReferenceMetadata $reference
     * @return bool
     */
    public function hasReference(ReferenceMetadata $reference)
    {
        return $this->references->contains($reference);
    }

    /**
     * @param string $propertyName
     * @return bool
     */
    public function hasReferenceWithPropertyName($propertyName)
    {
        return $this->references->containsKey($propertyName);
    }

    /**
     * @return string
     */
    public function getWherePart()
    {
        return $this->wherePart;
    }

    /**
     * @param string $wherePart
     * @return ClassMetadata
     */
    public function setWherePart($wherePart)
    {
        $this->wherePart = $wherePart;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getExcludedFields()
    {
        return $this->excludedFields;
    }

    /**
     * @param ArrayCollection $excludedFields
     * @return ClassMetadata
     */
    public function setExcludedFields($excludedFields)
    {
        $this->excludedFields = $excludedFields;

        return $this;
    }

    /**
     * @param string $excludedField
     * @return ClassMetadata
     */
    public function addExcludedField($excludedField)
    {
        $this->excludedFields->add($excludedField);

        return $this;
    }

    /**
     * @param string $excludedField
     * @return ClassMetadata
     */
    public function removeExcludedField($excludedField)
    {
        $this->excludedFields->removeElement($excludedField);

        return $this;
    }

    /**
     * @param string $excludedField
     * @return bool
     */
    public function hasExcludedField($excludedField)
    {
        return $this->excludedFields->contains($excludedField);
    }

    /**
     * @return array
     */
    public function getIdentifiers()
    {
        return $this->doctrineClassMetadata->getIdentifier();
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->doctrineClassMetadata->getName();
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return Inflector::tableize(substr(strrchr($this->doctrineClassMetadata->getName(), "\\"), 1) . '_');
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->doctrineClassMetadata->getFieldNames();
    }

    /**
     * @param $field
     * @return mixed
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    public function getFieldColumn($field)
    {
        return $this->doctrineClassMetadata->getFieldMapping($field)['columnName'];
    }

    /**
     * @param $field
     * @return mixed
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    public function getFieldType($field)
    {
        return $this->doctrineClassMetadata->getFieldMapping($field)['type'];
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->doctrineClassMetadata->getTableName();
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return Inflector::tableize(substr(strrchr($this->doctrineClassMetadata->getName(), "\\"), 1)) . '_alias';
    }

    /**
     * @return array
     */
    public function getDiscriminatorMap()
    {
        return $this->doctrineClassMetadata->discriminatorMap;
    }

    /**
     * @return array
     */
    public function getDiscriminatorColumn()
    {
        return $this->doctrineClassMetadata->discriminatorColumn;
    }

    /**
     * @return string
     */
    public function getDiscriminatorColumnName()
    {
        return $this->doctrineClassMetadata->discriminatorColumn['name'];
    }

    /**
     * @return string
     */
    public function getDiscriminatorValue()
    {
        return $this->doctrineClassMetadata->discriminatorValue;
    }
}
