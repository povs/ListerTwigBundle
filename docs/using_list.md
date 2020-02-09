# Using list

`ViewListerInterface` is responsible for building lists with auto resolvable types.

Methods:
- BuildList(string $list, ?string $type = null, array $parameters = []): self
   - `$list` fully qualified list class name
   - `$type` type name, if null list type will automatically be resolved from request
   - `$parameters` array that is passed to list `setParameters` method 
   - Will return self
- generateView(string $template, array $parameters = []): Response
   - `$template` twig template name. Check how template should look below.
   - `$parameters` array of parameters passed to the view
   - Will return response object with list html view
- getView(string $template, array $parameters = []): string 
   - parameters are same as `generateView` method
   - Will return html view as string
- setOptions(string $type, array $options): self 
   - If you have built your own type and it requires other options than default ones, you can set it via this method. Where:
     - `$type` fully qualified list class name
     - `$options` array of options

Example usage:

```` php 
use Povs\ListerTwigBundle\Declaration\ViewListerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class MyListController extends AbstractController
{
    private $lister;

    //If u're not using autowire inject it with tag 'povs.view_lister'
    public function __construct(ViewListerInterface $lister)
    {
        $this->lister = $lister;
    }

    public function listResponse(): Response
    {
        //Will automatically resolve list type by request and returns appropriate response (i.e. html view, export csv)
        return $this->lister->buildList(MyList::class, null, ['custom_parameter' => true])
            ->generateView('lists/my_list.html.twig', [
                'view_parameter' => 'view_parameter_value'
            ]);
    }
}
````


## Rendering list

All templates generated with `ViewListerInterface` receives `view` object called `list`.

To render list use twig function `list_view` which accepts three parameters:
 - `$view` - view object named `list`
 - `$blockName` - (string) block name to render from theme. Defaults to 'list_parent'
 - `$rewritable` - (bool) whether this block should be rewritable. Defaults to true
 
Example:

```` twig
#Extends some base view
{% extends 'base.html.twig' %}

{% block body %}
    <h1>test list</h1>
    
    #renders list
    {{ list_view(list) }}
{% endblock %}
````

### Overwriting theme blocks

You can overwrite any block that exists in [theme](themes.md)

> When calling block with the same name than block you're overwriting, pass false as a third argument to avoid infinite loop.

```` twig
#Extends some base view
{% extends 'base.html.twig' %}

{% block body %}
    <h1>test list</h1>
    
    #renders list
    {{ list_view(list) }}

    #Overwrites list_util block. Renders original list_util content and adds "Create new" button
    {% block list_util %}
        #Renders parent content. It's important to pass false as a third parameter to avoid infinite loops.
        {{ list_view(list, 'list_util', false) }}

        #Adds "Create new" button
        <a href="...">Create new</a>
    {% endblock %}

    #You can overwrite speicific field blocks by adding _{field_id} suffix to the block name. 
    #Overwrites list_body_field block for field with id "username".
    {% block list_body_field_username %}
        {# @var list \Povs\ListerBundle\View\FieldView #}
        <td class="username-field">
            {{ list.value|raw }}
        </td>
    {% endblock %}

    #Overwrites list_header_field block for field with id "username"
    {% block list_header_field_username %}
        {# @var list \Povs\ListerBundle\View\FieldView #}
        <td class="username-header">
            {{ list.value|raw }}
        </td>
    {% endblock %}
{% endblock %}
````

