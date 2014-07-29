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

use Metadata\PropertyMetadata;

/**
 * @author Jérémy Jégou <jejeavo@gmail.com>
 */
class VirtualPropertyMetadata extends PropertyMetadata
{
    /**
     * @var array
     */
    public $parameters;

    /**
     * @var null|string
     */
    public $domain;

    /**
     * @var null|string
     */
    public $locale;

    /**
     * Construct
     *
     * @param string $class
     * @param string $name
     * @param array  $translationOptions
     */
    public function __construct($class, $name, $translationOptions = array())
    {
        parent::__construct($class, $name);

        $this->parameters = isset($translationOptions['parameters']) ? $translationOptions['parameters'] : array();
        $this->domain = isset($translationOptions['domain']) ? $translationOptions['domain'] : null;
        $this->locale = isset($translationOptions['locale']) ? $translationOptions['locale'] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array(
            parent::serialize(),
            $this->parameters,
            $this->domain,
            $this->locale
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($str)
    {
        list(
            $parentStr,
            $this->parameters,
            $this->domain,
            $this->locale
            ) = unserialize($str);

        parent::unserialize($parentStr);
    }
} 
