# XML Container Builder

You can decide to write your defintions using XML. 

Sample file:

``` xml
<?xml version="1.0" encoding="UTF-8"?>
<dependency-injection>
    <!--
        load ini properties.
            attributes:
                - category: (optional) if you use ini categories
    -->
    <ini category="iniCategory">:baseDir/test-props.ini</ini>

    <!--
        add a listener (class)
    -->
    <listener class="Fwk\Di\Tests\Xml\TestXmlListenerOverride" />

    <!--
        add a listener (from another dependency)
    -->
    <listener service="my.listener" />

    <!-- ClassDefinition:
            attributes:
                - name: the definition's name
                - class: full class name (@references and :properties accepted)
                - shared: (optional) is the instance shared ?
                - lazy: (optional)  should the container proxy this class until it is really used ?
     -->
    <class-definition name="myObj" class="Fwk\Di\Definitions\ClassDefinition" shared="true" lazy="false">
        <!-- constructor argument -->
        <argument>\stdClass</argument>

        <!-- method called after instance is created (:properties accepted) -->
        <call method="addArgument">
            <!-- method argument (@references and :properties accepted) -->
            <argument>@dummy.service</argument>
        </call>

        <!-- definition's meta-data -->
        <data>
            <!--
                data parameter: key = value (@references and :properties accepted)
            -->
            <param key="testData">value</param>
        </data>
    </class-definition>

    <!--
        Simple scalar (string) definition
            attributes:
                - name: definition's name
            value: the value (@references and :properties accepted)
    -->
    <definition name="testDef">valueOfDefinition</definition>

    <!--
        Array definition
            attributes:
                - name: definition's name
    -->
    <array-definition name="arrayDef">
        <!-- array parameters.
                attributes:
                    - key: (optional)
                value: the parameter's value (@references and :properties accepted)
        -->
        <param key="debug">true</param>
        <param key="iniProp">:iniProp</param>

        <data>
            <param key="testData">value</param>
        </data>
    </array-definition>
</dependency-injection>
``` 

## How to use

``` php
$builder = new ContainerBuilder();

// create a container
$container = $builder->execute('path/to/file.xml');

// load dependencies into an existing container
$builder->execute('path/to/file.xml', $container);
```

## Extends/Customize XML

Fwk\Di makes use of *Fwk/Xml Maps* feature. The default Map (described above) is ```Fwk\Di\Xml\ContainerXmlMap``` but you're free to use your own:

``` php
$builder = new ContainerBuilder(new MyApp\Xml\ServicesMap());
```