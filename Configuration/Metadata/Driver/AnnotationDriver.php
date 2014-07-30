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

namespace Avoo\SerializerTranslation\Configuration\Metadata\Driver;

use Avoo\SerializerTranslation\Configuration\Annotation\Translate;
use Avoo\SerializerTranslation\Configuration\Metadata\ClassMetadata;
use Avoo\SerializerTranslation\Configuration\Metadata\VirtualPropertyMetadata;
use Doctrine\Common\Annotations\Reader as AnnotationsReader;
use Metadata\Driver\DriverInterface;
use Metadata\MergeableClassMetadata;
use Metadata\PropertyMetadata;

/**
 * @author Jérémy Jégou <jejeavo@gmail.com>
 */
class AnnotationDriver implements DriverInterface
{
    /**
     * @var AnnotationsReader
     */
    private $reader;

    /**
     * @param AnnotationsReader $reader
     */
    public function __construct(AnnotationsReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $classMetadata = new ClassMetadata($class->getName());

        foreach ($class->getProperties() as $reflectionProperty) {
            $annotation = $this->reader->getPropertyAnnotation(
                $reflectionProperty,
                'Avoo\\SerializerTranslation\\Configuration\\Annotation\\Translate'
            );

            if (null === $annotation) {
                continue;
            }

            $options = $this->createOptions($annotation);
            $propertyMetadata = new VirtualPropertyMetadata($class->getName(), $reflectionProperty->getName(), $options);
            $classMetadata->addPropertyToTranslate($propertyMetadata);
        }

        return $classMetadata;
    }

    /**
     * Create options
     *
     * @param Translate $annotation
     *
     * @return array
     */
    private function createOptions(Translate $annotation)
    {
        $options = array();

        if (isset($annotation->domain)) {
            $options['domain'] = $annotation->domain;
        }

        if (isset($annotation->locale)) {
            $options['locale'] = $annotation->locale;
        }

        foreach ($annotation->parameters as $key => $value) {
            $options['parameters'][$key] = $value;
        }

        return $options;
    }
}
