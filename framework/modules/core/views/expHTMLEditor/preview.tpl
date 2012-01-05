<h1>{$demo->name} {'Toolbar Configuration Preview'|gettext}</h1>
<p>Using the '{$demo->skin}' skin.<p>
{control type="editor" name="xxx" label="" value="This is an example of what this editor toolbar configuration looks and works like"|gettext toolbar=$demo->id}
{control type="buttongroup" name="done" cancel="Done"|gettext returntype="manageable"}