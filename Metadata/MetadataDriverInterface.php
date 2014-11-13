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

/**
 * Opensoft\FixturesGeneratorBundle\Metadata\MetadataDriverInterface
 *
 * @author Ivan Molchanov <ivan.molchanov@opensoftdev.ru>
 */
interface MetadataDriverInterface
{
    /**
     * @return ClassMetadata[]
     */
    public function loadMetadata();
}
