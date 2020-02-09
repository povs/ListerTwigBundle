# Themes

Themes helps to customize how list should be rendered.

By default TwigListerBundle comes with two themes:
- `@PovsListerTwig\default_theme.html.twig` renders list as a table. Has no styling classes.
- `@PovsListerTwig\bootstrap_4_theme.html.twig` renders list as a table with bootstrap 4 styling classes.


## Creating your own theme

### Twig function

Core themes functionality comes from `list_view(ViewInterface $list, string $blockName = 'list_parent', bool $rewritable = false)` twig function.
This function will first look for a block passed via `$blockName` in the main template, if block is not found - theme template.

> If first argument passed is instance of `Povs\ListerBundle\View` it will also look for a block named `{$blockName}_{$fieldId}`
>> For example `list_body_field_username` where `list_body_field` is block name and `username` is field id.

> `Main template` is a template passed via `generateView()` method in ViewListerInterface when building a list.

argument | type | defaultValue | description
--- | --- | --- | ---
$list | ViewInterface | - | View object that implements ViewInterface
$blockName | string | 'list_parent' | block name to render
$rewritable | bool | true | whether this block can be rewritten in template.  


### Creating a theme

For this example lets create a theme that renders list data as a blocks.

```twig 

{# Extends default theme #}
{% extends '@PovsListerTwig/default_theme.html.twig' %}

{# Initial block. Rendered when list_view(list) is called in main template. #}
{% block list_parent %}
    {# @var list \Povs\ListerBundle\View\ListView #}
    <div {% if list_data['ajax'] %}class="js-povs-lister-ajax"{% endif %}>
        <div>
            {# calls 'list_filter' block from default theme #}
            {{ list_view(list, 'list_filter') }}
        </div>
        {# 
           Ajax type functionallity. 
           Element with class js-povs-lister-ajax-update will by updated via ajax 
        #}
        {% if list_data['ajax'] %}
            <div {% if list_data['ajax'] %}class="js-povs-lister-ajax-update"{% endif %}></div>
        {% else %}
            <div>
                {# calls 'list_blocks' block from default theme #}
                {{ list_view(list, 'list_blocks') }}
            </div>
        {% endif %}
    </div>
{% endblock %}


{% block list_blocks %}
    {# @var list \Povs\ListerBundle\View\ListView #}
    {% if list.pager.total > 0 %}
        <div>
            {{ list_view(list, 'list_util') }}
        </div>
        <div>
            {{ list_view(list, 'list_length_options') }}
        </div>

        {# fetches all rows from the listView and renders 'list_block' block passing row view to it. #}
        {% for row in list.bodyRows %}
            {{ list_view(row, 'list_block') }}
        {% endfor %}

        <div>
            {{ list_view(list, 'list_pagination') }}
        </div>
        <div>
            {{ list_view(list, 'list_information') }}
        </div>
    {% else %}
        {{ 'no_data'|trans({}, 'povs_lister') }}
    {% endif %}
{% endblock %}

{% block list_block %}
    {# @var list \Povs\ListerBundle\View\RowView #}

    <div style="display: inline-block; border: 1px solid black; width: 200px; margin-top: 10px; padding: 10px;">
        {{ list_view(list, 'list_block_content') }}
    </div>
{% endblock %}

{% block list_block_content %}
    {# @var list \Povs\ListerBundle\View\RowView #}

    {% for field in list.fields %}
        {{ list_view(field, 'list_field') }}
    {% endfor %}
{% endblock %}

{% block list_field %}
    {# @var list \Povs\ListerBundle\View\FieldView #}
    <b>{{ list.label }}:</b> {{ list.value }}<br>
{% endblock %}
```

> All blocks in this theme (list_block, list_block_content, list_field, etc..) can be overwritten in the main template. If you do not want that to be possible, pass false as a third argument when rendering a block via `list_view` function.


### Using theme

To use this theme change `type_configuration`:

```yaml
povs_lister:
    ...
    list_config:
        type_configuration:
            list:
                theme: 'blocks_theme.html.twig'
```


