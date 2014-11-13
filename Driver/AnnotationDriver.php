<?php
/**
 * This file is part of ONP.
 *
 * Copyright (c) 2014 Opensoft (http://opensoftdev.com)
 *
 * The unauthorized use of this code outside the boundaries of
 * Opensoft is prohibited.
 */

namespace Opensoft\FixturesGeneratorBundle\Driver;

use Opensoft\FixturesGeneratorBundle\Metadata\ClassMetadata;
use Opensoft\FixturesGeneratorBundle\Metadata\MetadataDriverInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Opensoft\FixturesGeneratorBundle\Metadata\ReferenceMetadata;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\ClassMetadata as DoctrineClassMetadata;
use Opensoft\FixturesGeneratorBundle\Annotation\ExposeToFixturesGeneration;

/**
 * Opensoft\FixturesGeneratorBundle\Driver\AnnotationDriver
 *
 * @author Ivan Molchanov <ivan.molchanov@opensoftdev.ru>
 */
class AnnotationDriver implements MetadataDriverInterface
{

    /**
     * @var Registry
     */
    protected $doctrine;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * Constructor
     *
     * @param RegistryInterface $doctrine
     * @param Reader $reader
     */
    public function __construct(RegistryInterface $doctrine, Reader $reader)
    {
        $this->doctrine = $doctrine;
        $this->reader = $reader;
    }

    /**
     * @return ClassMetadata[]
     */
    public function loadMetadata()
    {
        $result = [];
        foreach ($this->doctrine->getManagers() as $entityManager) {
            /** @var DoctrineClassMetadata[] $doctrineMetadataClasses */
            $doctrineMetadataClasses = $entityManager->getMetadataFactory()->getAllMetadata();
            foreach ($doctrineMetadataClasses as $doctrineMetadataClass) {
                $className = $doctrineMetadataClass->getName();
                $reflectionClass = new \ReflectionClass($className);
                /** @var ExposeToFixturesGeneration $annotation */
                $annotation = $this->reader->getClassAnnotation(
                    $reflectionClass,
                    'Opensoft\FixturesGeneratorBundle\Annotation\ExposeToFixturesGeneration'
                );
                if (null !== $annotation) {
                    $metadata = new ClassMetadata($doctrineMetadataClass);
                    if (null !== $annotation->getFileName()) {
                        $metadata->setFileName($annotation->getFileName());
                    } else {
                        $metadata->setFileName(substr(strrchr($className, "\\"), 1) . '.yml');
                    }
                    $metadata->setWherePart($annotation->getWhere());
                    foreach ($doctrineMetadataClass->getAssociationMappings() as $associationMapping) {
                        $referenceMetadata = new ReferenceMetadata($associationMapping);
                        $referenceMetadata->setTableName($entityManager->getClassMetadata($associationMapping['targetEntity'])->getTableName());
                        $metadata->addReference($referenceMetadata);
                    }
                    foreach($reflectionClass->getProperties() as $reflectionProperty){
                        $excludeFieldAnnotation = $this->reader->getPropertyAnnotation(
                            $reflectionProperty,
                            'Opensoft\FixturesGeneratorBundle\Annotation\ExcludeField'
                        );
                        if(null !== $excludeFieldAnnotation){
                            $metadata->addExcludedField($reflectionProperty->getName());
                        }
                    }
                    foreach($doctrineMetadataClass->getIdentifier() as $identifier){
                        $metadata->addExcludedField($identifier);
                    }

                    $result[$className] = $metadata;
                }
            }
        }
        /** @var ClassMetadata $metadata */
        foreach ($result as $metadata) {
            /** @var ReferenceMetadata $reference */
            foreach ($metadata->getReferences() as $reference) {
                foreach (array_keys($result) as $resultClass) {
                    if ($resultClass == $reference->getClassName() ||
                        is_subclass_of($resultClass, $reference->getClassName())
                    ) {
                        continue 2;
                    }
                }
                $metadata->removeReference($reference);
            }
        }

        return $result;
    }
}
