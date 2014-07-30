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
use JMS\Serializer\Exception\XmlErrorException;
use Metadata\Driver\AbstractFileDriver;

/**
 * @author Jérémy Jégou <jejeavo@gmail.com>
 */
class XmlDriver extends AbstractFileDriver
{
    const NAMESPACE_URI = 'https://github.com/avoo/SerializerTranslationBundle';

    /**
     * {@inheritdoc}
     */
    protected function loadMetadataFromFile(\ReflectionClass $class, $file)
    {
        $previous = libxml_use_internal_errors(true);
        $root     = simplexml_load_file($file);
        libxml_use_internal_errors($previous);

        if (false === $root) {
            throw new XmlErrorException(libxml_get_last_error());
        }

        $name = $class->getName();
        if (!$exists = $root->xpath("./class[@name = '" . $name . "']")) {
            throw new \RuntimeException(sprintf('Expected metadata for class %s to be defined in %s.', $name, $file));
        }

        $classMetadata = new ClassMetadata($name);
        $classMetadata->fileResources[] = $file;
        $classMetadata->fileResources[] = $class->getFileName();
        $elements = $exists[0]->children();

        foreach ($elements->property as $property) {
            if (isset($property->children(self::NAMESPACE_URI)->translate)) {
                $translateNode = $property->children(self::NAMESPACE_URI)->translate;
                $options = $this->createOptions($translateNode);

                $propertyMetadata = new VirtualPropertyMetadata($class->getName(), (string) $property->attributes()->name, $options);
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
        return 'xml';
    }

    /**
     * Create options
     *
     * @param \SimpleXMLElement $node
     *
     * @return array
     */
    private function createOptions(\SimpleXMLElement $node)
    {
        $options = array();

        if (isset($node->attributes()->domain)) {
            $options['domain'] = (string) $node->attributes()->domain;
        }

        if (isset($node->attributes()->locale)) {
            $options['locale'] = (string) $node->attributes()->locale;
        }

        foreach ($node->parameter as $parameter) {
            $options['parameters'][(string) $parameter->attributes()->name] = (string) $parameter->attributes()->value;
        }

        return $options;
    }
}
