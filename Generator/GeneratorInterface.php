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

/**
 * Opensoft\FixturesGeneratorBundle\Generator\GeneratorInterface
 *
 * @author Ivan Molchanov <ivan.molchanov@opensoftdev.ru>
 */
interface GeneratorInterface
{
    /**
     * Generate Fixtures
     *
     * @return mixed
     */
    public function generate();
}
