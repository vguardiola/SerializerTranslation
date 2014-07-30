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
use Avoo\SerializerTranslation\Expression\ExpressionEvaluator;
use JMS\Serializer\JsonSerializationVisitor;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

/**
 * @author Jérémy Jégou <jejeavo@gmail.com>
 */
class JsonSerializer implements JsonSerializerInterface
{
    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var ExpressionEvaluator
     */
    private $expressionEvaluator;

    /**
     * Construct
     *
     * @param Translator          $translator
     * @param ExpressionEvaluator $expressionEvaluator
     */
    public function __construct(Translator $translator, ExpressionEvaluator $expressionEvaluator)
    {
        $this->translator = $translator;
        $this->expressionEvaluator = $expressionEvaluator;
    }

    /**
     * {@inheritdoc}
     */
    public function trans(ClassMetadataInterface $metadataClass, JsonSerializationVisitor $visitor, SerializationContext $context, $data)
    {
        /** @var VirtualPropertyMetadata $propertyMetadata */
        foreach ($metadataClass->getPropertiesToTranslate() as $propertyMetadata) {
            $value = $propertyMetadata->getValue($data);

            if (!empty($value)) {
                $locale = $propertyMetadata->locale;

                if (is_null($locale)) {
                    $locale = $context->getLocale();
                }

                $parameters = $this->expressionEvaluator->evaluateArray($propertyMetadata->parameters, $data);

                $value = $this->translator->trans($value, $parameters, $propertyMetadata->domain, $locale);
                $propertyMetadata->setValue($data, $this->expressionEvaluator->evaluate($value, $data));
            }
        }
    }
}
