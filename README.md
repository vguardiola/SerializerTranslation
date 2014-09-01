SerializerTranslation
=====================
[![Build Status]
(https://scrutinizer-ci.com/g/avoo/SerializerTranslation/badges/build.png?b=master)](https://scrutinizer-ci.com/g/avoo/SerializerTranslation/build-status/master)
[![Scrutinizer Code Quality]
(https://scrutinizer-ci.com/g/avoo/SerializerTranslation/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/avoo/SerializerTranslation/?branch=master)
[![Latest Stable Version]
(https://poser.pugx.org/avoo/serializer-translation/v/stable.svg)](https://packagist.org/packages/avoo/serializer-translation)
[![License]
(https://poser.pugx.org/avoo/serializer-translation/license.svg)](https://packagist.org/packages/avoo/serializer-translation)

This is a PHP library based on JMS Serializer, and add translation option configuration for any properties.

* [Installation](#installation)
* [Default Usage](#default-usage)
* [With custom parameters](#with-custom-parameters)
  - [XML](#xml)
  - [YAML](#yaml)
  - [Annotations](#annotations)

Installation
------------

Require [`avoo/serializer-translation-bundle`](https://packagist.org/packages/avoo/serializer-translation-bundle)
into your `composer.json` file:


``` json
{
    "require": {
        "avoo/serializer-translation-bundle": "@dev-master"
    }
}
```

Register the bundle in `app/AppKernel.php`:

``` php
// app/AppKernel.php
public function registerBundles()
{
    return array(
        // ...
        new Avoo\SerializerTranslationBundle\AvooSerializerTranslationBundle(),
    );
}
```


Default Configuration
-----------------------

``` yaml
# app/config/config.yml

avoo_serializer_translation:
    metadata:
        cache:                file
        file_cache:
            dir:              %kernel.cache_dir%/avoo
```


Default Usage
-------------

For example, you want to translate `acme.foo.bar` from BDD.

#### Default activation

Into your translation file:

``` yaml
# Acme/DemoBundle/Resources/translations/messages.en.yml

acme:
    foo.bar: "Welcome."
```

```xml
<?xml version="1.0" encoding="UTF-8" ?>
<serializer>
    <class name="Acme\DemoBundle\Entity\Sample" exclusion-policy="ALL" xml-root-name="sample" xmlns:a="https://github.com/avoo/SerializerTranslationBundle">
        <property name="property" expose="true" type="string">
            <a:translate />
        </property>
    </class>
</serializer>
```

### YAML

```yaml
Acme\DemoBundle\Entity\Sample:
    exclusion_policy: ALL
    xml_root_name: sample
    properties:
        property:
            expose: true
            type: string
            translate: true
```

### Annotations

**Important:** The annotation need to be defined.

```php
use Avoo\SerializerTranslation\Configuration\Annotation as AvooSerializer;

/**
 * @var string $property
 *
 * @AvooSerializer\Translate()
 */
 protected $property;
```

#### Json output sample

```json
{
    "property": "welcome."
}
```


With custom parameters
-------------

#### With custom parameters

Into your translation file:

``` yaml
# Acme/DemoBundle/Resources/translations/messages.en.yml

acme:
    foo.bar: "welcome %foo%"
```

### XML

```xml
<?xml version="1.0" encoding="UTF-8" ?>
<serializer>
    <class name="Acme\DemoBundle\Entity\Sample" exclusion-policy="ALL" xml-root-name="sample" xmlns:a="https://github.com/avoo/SerializerTranslationBundle">
        <property name="property" expose="true" type="string">
            <a:translate domain="messages" locale="en">
                <a:parameter name="%foo%" value="expr(object.getProperty())" />
            </a:translate>
        </property>
    </class>
</serializer>
```

### YAML

```yaml
Acme\DemoBundle\Entity\Sample:
    exclusion_policy: ALL
    xml_root_name: sample
    properties:
        property:
            expose: true
            type: string
            translate:
                parameters:
                    %foo%: expr(object.getProperty())
                locale: en
                domain: messages
```

### Annotations

```php
use Avoo\SerializerTranslation\Configuration\Annotation as AvooSerializer;

/**
 * @var string $property
 *
 * @AvooSerializer\Translate(
 *     parameters = {
 *         "%foo%" = "expr(object.getProperty())"
 *     },
 *     domain = "messages",
 *     locale = "en"
 * )
 */
 protected $property;
```

#### Json output sample

```json
{
    "property": "welcome Superman",
}
```

License
-------

This bundle is released under the MIT license. See the complete license in the bundle:

[License](https://github.com/avoo/SerializerTranslation/blob/master/LICENSE)
