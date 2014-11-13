<?php
/**
 * This file is part of ONP.
 *
 * Copyright (c) 2014 Opensoft (http://opensoftdev.com)
 *
 * The unauthorized use of this code outside the boundaries of
 * Opensoft is prohibited.
 */

namespace Opensoft\FixturesGeneratorBundle\Annotation;

/**
 * Opensoft\FixturesGeneratorBundle\Annotation\Expose
 *
 * @Annotation
 *
 * @author Ivan Molchanov <ivan.molchanov@opensoftdev.ru>
 */
class ExposeToFixturesGeneration
{
    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var string
     */
    protected $where;

    /**
     * @param array $options
     */
    public function __construct($options)
    {
        if (isset($options['value'])) {
            $options['fileName'] = $options['value'];
            unset($options['value']);
        }

        foreach ($options as $key => $value) {
            if (!property_exists($this, $key)) {
                throw new \InvalidArgumentException(sprintf('Property "%s" does not exist', $key));
            }

            $this->$key = $value;
        }
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @return string
     */
    public function getWhere()
    {
        return $this->where;
    }
}
