<?php

/*
* The MIT License (MIT)
*
* Copyright (c) 2014 J. Jégou <jejeavo@gmail.com>
*
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included in all
* copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
* SOFTWARE.
*/

namespace Avoo\SerializerTranslation\Configuration\Metadata;

use Metadata\MergeableClassMetadata;
use Metadata\MergeableInterface;
use Metadata\PropertyMetadata;

/**
 * @author Jérémy Jégou <jejeavo@gmail.com>
 */
class ClassMetadata extends MergeableClassMetadata implements ClassMetadataInterface
{
    /**
     * @var array
     */
    protected $propertiesToTranslate = array();

    /**
     * Add property
     *
     * @param PropertyMetadata $propertyMetadata
     *
     * @return $this
     */
    public function addPropertyToTranslate(PropertyMetadata $propertyMetadata)
    {
        $this->propertiesToTranslate[] = $propertyMetadata;

        return $this;
    }

    /**
     * Get properties to translate
     *
     * @return array
     */
    public function getPropertiesToTranslate()
    {
        return $this->propertiesToTranslate;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function merge(MergeableInterface $object)
    {
        if (!$object instanceof self) {
            throw new \InvalidArgumentException(sprintf('Object must be an instance of %s.', __CLASS__));
        }

        if ($object instanceof ClassMetadata)
        {
            foreach ($object->getPropertiesToTranslate() as $prop) {
                $this->addPropertyToTranslate($prop);
            }

        }

        parent::merge($object);
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize(array(
            parent::serialize(),
            $this->propertiesToTranslate
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($str)
    {

        list(
            $parentStr,
            $this->propertiesToTranslate
        ) = unserialize($str);

        parent::unserialize($parentStr);
    }
}
