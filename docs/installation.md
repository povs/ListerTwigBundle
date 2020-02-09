# Installation

Requirements:
- Php `>=7.1`
- Symfony `>=4`
- Doctrine ORM
- Twig

Install package via `composer`:
>Package is not yet available via composer.

Register bundle in your `bundles.php` file

``` php
Povs\ListerBundle\PovsListerBundle::class => ['all' => true]
Povs\ListerBundle\PovsListerTwigBundle::class => ['all' => true]
```

Add configuration to `config/packages/povs_lister.yaml`

``` yaml
#ListerBundle config
povs_lister:
    types:
        list: Povs\ListerTwigBundle\Type\ListType\TwigListType
        #list: Povs\ListerTwigBundle\Type\ListType\AjaxListType
        export: Povs\ListerBundle\Type\ListType\CsvListType

    list_config:
         form_configuration:
             allow_extra_fields: true #requires for filtering to work - allows extra fields to be added to the form
             csrf_protection: false #disables csrf protection for filter fields.
         type_configuration:
             list:
                 theme: '@PovsListerTwig\default_theme.html.twig'
                 #theme: '@PovsListerTwig\bootstrap_4_theme.html.twig'
                 form_theme: null
                 default_length: 20
                 length_options: [20,50,100]
                 allow_export: true
                 export_types: [export] 
                 export_limit:
                     export: 10000

#ListerTwigBundle config
povs_lister_twig:
    #Which types should be parsed as "view" types
    view_types: ['list']
    #Which types should be automatically resolved by request
    resolvable_types: ['list', 'export']
    default_type: 'list'
    request:
        #Request parameter name by which type will be passed
        type: 'lister_type'
```