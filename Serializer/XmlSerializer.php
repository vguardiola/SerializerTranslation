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

namespace Avoo\SerializerTranslation\Serializer;

use Avoo\SerializerTranslation\Configuration\Metadata\ClassMetadataInterface;

use Avoo\SerializerTranslation\Configuration\Metadata\VirtualPropertyMetadata;
use JMS\Serializer\XmlSerializationVisitor;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Jérémy Jégou <jejeavo@gmail.com>
 */
class XmlSerializer implements XmlSerializerInterface
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function trans(ClassMetadataInterface $metadataClass, XmlSerializationVisitor $visitor, SerializationContext $context, $data)
    {
        /** @var VirtualPropertyMetadata $propertyMetadata */
        foreach ($metadataClass->getPropertiesToTranslate() as $propertyMetadata) {
            $value = $propertyMetadata->getValue($data);

            if (!empty($value)) {
                $locale = $context->getLocale();

                $value = $this->translator->trans($value, $propertyMetadata->parameters, $propertyMetadata->domain, $locale);
                $propertyMetadata->setValue($data, $value);
            }
        }
    }
}
