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

namespace Avoo\SerializerTranslation\Serializer\EventSubscriber;

use Avoo\SerializerTranslation\Configuration\Metadata\ClassMetadataInterface;
use Avoo\SerializerTranslation\Serializer\SerializationContext;
use Avoo\SerializerTranslation\Serializer\JsonSerializerInterface;
use Avoo\SerializerTranslation\Serializer\XmlSerializerInterface;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use Metadata\MetadataFactoryInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Jérémy Jégou <jejeavo@gmail.com>
 */
class EventSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'event'  => Events::PRE_SERIALIZE,
            'method' => 'onPreSerialize',
        );
    }

    /**
     * @var JsonSerializerInterface|XmlSerializerInterface
     */
    private $serializer;

    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var Container
     */
    private $container;

    /**
     * @param JsonSerializerInterface|XmlSerializerInterface  $serializer
     * @param MetadataFactoryInterface $metadataFactory
     * @param Container                $container
     */
    public function __construct(
        $serializer,
        MetadataFactoryInterface $metadataFactory,
        Container $container
    ) {
        $this->serializer = $serializer;
        $this->metadataFactory = $metadataFactory;
        $this->container = $container;
    }

    public function onPreSerialize(ObjectEvent $event)
    {
        $object  = $event->getObject();
        $context = $event->getContext();

        if (!$context instanceof SerializationContext) {
            $context = new SerializationContext($context);
        }

        $request = $this->container->get('request');
        $locale = $request ? $request->getPreferredLanguage() : $this->container->getParameter('locale', $context->getLocale());
        $context->setLocale($locale);

        /** @var ClassMetadataInterface $metadataClass */
        $metadataClass = $this->metadataFactory->getMetadataForClass(get_class($object));

        if (!is_null($metadataClass) && count($metadataClass->getPropertiesToTranslate())) {
            $this->serializer->trans($metadataClass, $event->getVisitor(), $context, $object);
        }
    }
}
