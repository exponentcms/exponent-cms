<h1>{$demo->name} {'Toolbar Configuration'|gettext}</h1>
<p>Using the '{$demo->skin}' skin.<p>
{control type="editor" name="xxx" label="" value="this is an example of what this editor toolbar configuration looks like"|gettext toolbar=$demo}
{control type="buttongroup" name="done" cancel="Done"|gettext returntype="manageable"}