<?php

namespace Avoo\SerializerTranslation\Configuration\Annotation;

/**
 * @Annotation
 *
 * @author Jérémy Jégou <jejeavo@gmail.com>
 */
class Translate
{
    /**
     * @var array
     */
    public $parameters = array();

    /**
     * @var string
     */
    public $domain = 'messages';

    /**
     * @var string
     */
    public $locale = 'en';
}
