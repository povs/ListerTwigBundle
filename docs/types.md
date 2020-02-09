# Types

ListerTwigBundle comes with two types

### TwigListType

Twig list type renders list as html and returns it via string or response.
 - `generateView` returns view as response
 - `getView` returns view as string

*available settings*:

Name | type | default | description
--- | --- | --- | ---
theme | string | '@PovsListerTwig/default_theme.html.twig' | Lister theme to use
form_theme | string | null | [Form](https://symfony.com/doc/current/form/form_themes.html) theme to use
default_length | int | 20 | Default list length (how much to show per page)
length_options | array | [20,50,100] | List length options
export_types | array | [] | Available export types
export_limit | array | [] | Array of key:value pairs where key is export type name and value is limit
allow_export | bool | false | Whether to allow export.

*available options*:

Name | type | default | description
--- | --- | --- | ---
template | string | null | twig template name
context | array | [] | array of parameters that will be passed to template


### Ajax List type

Ajax list type checks whether request has ajax-request header. If so it returns html content in block passed via `block` setting.
Otherwise return value is the same as `Twig list type`.

*available settings*:

Name | type | default | description
--- | --- | --- | ---
theme | string | '@PovsListerTwig/default_theme.html.twig' | Lister theme to use
form_theme | string | null | [Form](https://symfony.com/doc/current/form/form_themes.html) theme to use
default_length | int | 20 | Default list length (how much to show per page)
length_options | array | [20,50,100] | List length options
export_types | array | [] | Available export types
export_limit | array | [] | Array of key:value pairs where key is export type name and value is limit
allow_export | bool | false | Whether to allow export.
block | string | list_table | block name to refresh via ajax

*available options*:

Name | type | default | description
--- | --- | --- | ---
template | string | null | twig template name
context | array | [] | array of parameters that will be passed to template

#### Javascript

> To use ajax list type you will need some javascript. 
> ListerTwigBundle comes with simple javascript object that covers all the logic needed to use lister with ajax.

To use it you will need to include it in your twig or add it to your assets build system.

```` twig 
<script src="{{ asset('bundles/povslistertwig/js/ajax_type.js') }}"></script>
````

Then on page load call init function

```` javascript
ListerAjax.init()
````

##### Events

All events are called on element with class `js-povs-lister-ajax`
You can overwrite it with changing `ListerAjax.selectors.ajaxLister` property before calling `init()` function.

Event name | params |description 
--- | --- | ---
povs_lister_ajax_pre_update | | called before update
povs_lister_ajax_post_update | response, url | called after update with response and request url.
povs_lister_ajax_error | | called when error have occurred.

```javascript
let element = document.querySelector('.js-povs-lister-ajax');

element.addEventListener('povs_lister_ajax_pre_update', function() {
    addLoader(); //Adds some loader
});

element.addEventListener('povs_lister_ajax_post_update', function() {
    hideLoader(); //Hides loader after table has loaded
});

element.addEventListener('povs_lister_ajax_error', function() {
    showError(); //Informs user about an error
});
```