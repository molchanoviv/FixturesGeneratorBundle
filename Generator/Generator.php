<?php
/**
 * This file is part of ONP.
 *
 * Copyright (c) 2014 Opensoft (http://opensoftdev.com)
 *
 * The unauthorized use of this code outside the boundaries of
 * Opensoft is prohibited.
 */

namespace Opensoft\FixturesGeneratorBundle\Generator;

use Doctrine\ORM\Mapping\ClassMetadata as DoctrineClassMetadata;
use Opensoft\FixturesGeneratorBundle\Metadata\MetadataDriverInterface;
use Opensoft\FixturesGeneratorBundle\Metadata\ClassMetadata;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Yaml\Yaml;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;

/**
 * Opensoft\FixturesGeneratorBundle\Generator\Generator
 *
 * @author Ivan Molchanov <ivan.molchanov@opensoftdev.ru>
 */
class Generator implements GeneratorInterface
{
    /**
     * @var MetadataDriverInterface
     */
    protected $metadataReader;

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var ClassMetadata[]
     */
    protected $metadata;

    /**
     * @var EntityManager;
     */
    protected $entityManager;

    /**
     * @var array
     */
    protected $data;

    /**
     * Constructor
     *
     * @param MetadataDriverInterface $metadataReader
     * @param ObjectManager $entityManager
     * @param string $directory
     */
    public function __construct(MetadataDriverInterface $metadataReader, ObjectManager $entityManager, $directory)
    {
        $this->metadataReader = $metadataReader;
        $this->directory = $directory;
        $this->entityManager = $entityManager;
    }

    /**
     * Generate Fixtures
     *
     * @return mixed
     */
    public function generate()
    {
        $this->metadata = $this->metadataReader->loadMetadata();
        $this->loadDataFromDatabase();
        $this->dump();
    }

    /**
     * @return void
     */
    protected function loadDataFromDatabase()
    {
        $data = [];
        foreach ($this->metadata as $metadataItem) {
            $queryBuilder = $this->entityManager->createQueryBuilder();
            $className = $metadataItem->getClassName();
            $tableAlias = $metadataItem->getAlias();
            $queryBuilder->select($tableAlias);
            $queryBuilder->from($className, $tableAlias);
            if (null !== $metadataItem->getWherePart()) {
                $queryBuilder->andWhere($metadataItem->getWherePart());
            }
            foreach ($metadataItem->getIdentifiers() as $identifier) {
                $queryBuilder->addOrderBy($tableAlias . '.' . $identifier);
            }
            $result = $queryBuilder->getQuery()->getResult();
            foreach ($result as $countNumber => $row) {
                $identifier = $countNumber + 1;
                $data[$className][$metadataItem->getPrefix() . $identifier] = $row;
            }
        }

        $this->updateReferences($data);
    }

    /**
     * @param $rawData
     */
    protected function updateReferences($rawData)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach ($rawData as $entityClass => $entities) {
            foreach ($entities as $entityId => $entity) {
                foreach ($this->metadata[$entityClass]->getFields() as $field) {
                    if ($this->metadata[$entityClass]->hasExcludedField($field)) {
                        continue;
                    }
                    $fieldValue = $propertyAccessor->getValue($entity, $field);
                    if (null !== $fieldValue) {
                        $this->data[$entityClass][$entityId][$field] = $fieldValue;
                    }
                }
                foreach ($this->metadata[$entityClass]->getReferences() as $reference) {
                    $propertyValue = $propertyAccessor->getValue($entity, $reference->getFieldName());
                    if (null === $propertyValue) {
                        continue;
                    }
                    $referenceType = $reference->getType();
                    if ($referenceType & DoctrineClassMetadata::TO_ONE) {
                        foreach (array_keys($this->metadata) as $referencedEntityClass) {
                            if ($referencedEntityClass === $reference->getClassName() ||
                                is_subclass_of($referencedEntityClass, $reference->getClassName())
                            ) {
                                foreach ($rawData[$referencedEntityClass] as $referencedEntityId => $referencedEntity) {
                                    if ($referencedEntity === $propertyValue) {
                                        $this->data[$entityClass][$entityId][$reference->getFieldName(
                                        )] = '@' . $referencedEntityId;
                                        continue 2;
                                    }
                                }
                            }
                        }
                    }
                    if ($referenceType & DoctrineClassMetadata::MANY_TO_MANY && !empty($reference->getJoinTable())) {
                        foreach (array_keys($this->metadata) as $referencedEntityClass) {
                            if ($referencedEntityClass === $reference->getClassName() ||
                                is_subclass_of($referencedEntityClass, $reference->getClassName())
                            ) {
                                foreach ($propertyValue as $referenceItem) {
                                    foreach ($rawData[$referencedEntityClass] as $referencedEntityId => $referencedEntity) {
                                        if ($referencedEntity === $referenceItem) {
                                            $this->data[$entityClass][$entityId][$reference->getFieldName()][] = '@' . $referencedEntityId;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Dump data to yaml files
     */
    protected function dump()
    {
        if (!file_exists($this->directory)) {
            mkdir($this->directory, 0777, true);
        }
        foreach ($this->data as $dataClassName => $dataItem) {
            foreach ($this->metadata as $metadataItem) {
                if ($dataClassName === $metadataItem->getClassName()) {
                    $tmpArray = [];
                    $fileName = $this->directory . DIRECTORY_SEPARATOR . $metadataItem->getFileName();
                    if (file_exists($fileName)) {
                        unlink($fileName);
                    }
                    $tmpArray[$dataClassName] = $dataItem;
                    $yaml = Yaml::dump($tmpArray, 3, 2, true, true);
                    file_put_contents($fileName, $yaml);
                }
            }
        }
    }
}
