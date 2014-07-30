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

use Avoo\SerializerTranslation\Configuration\Metadata\ClassMetadata;
use Avoo\SerializerTranslation\Configuration\Metadata\VirtualPropertyMetadata;
use Metadata\Driver\AbstractFileDriver;
use Symfony\Component\Yaml\Yaml;

/**
 * Yaml driver
 *
 * @author Jérémy Jégou <jejeavo@gmail.com>
 */
class YamlDriver extends AbstractFileDriver
{
    /**
     * {@inheritdoc}
     */
    protected function loadMetadataFromFile(\ReflectionClass $class, $file)
    {
        $config = Yaml::parse(file_get_contents($file));

        if (!isset($config[$name = $class->getName()])) {
            throw new \RuntimeException(sprintf('Expected metadata for class %s to be defined in %s.', $name, $file));
        }

        $config = $config[$name];
        $classMetadata = new ClassMetadata($name);
        $classMetadata->fileResources[] = $file;
        $classMetadata->fileResources[] = $class->getFileName();

        if (!isset($config['properties'])) {
            return $classMetadata;
        }

        foreach ($config['properties'] as $key => $property) {
            if (isset($property['translate'])) {
                $options = $this->createOptions($property);
                $propertyMetadata = new VirtualPropertyMetadata($class->getName(), $key, $options);
                $classMetadata->addPropertyToTranslate($propertyMetadata);
            }
        }

        return $classMetadata;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExtension()
    {
        return 'yml';
    }

    /**
     * Create options
     *
     * @param array $property
     *
     * @return array
     */
    private function createOptions($property)
    {
        $options = array();
        if (is_array($property['translate'])) {
            $options = $property['translate'];
        }

        return $options;
    }
}
