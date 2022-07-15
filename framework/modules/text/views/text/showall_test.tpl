{*
 * Copyright (c) 2004-2022 OIC Group, Inc.
 *
 * This file is part of Exponent
 *
 * Exponent is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * GPL: http://www.gnu.org/licenses/gpl.txt
 *
 *}

{css unique="bootstrap-test"}
{literal}
    body > .navbar {
      -webkit-transition: background-color .3s ease-in;
      transition: background-color .3s ease-in
    }

    @media (min-width: 768px) {
      body > .navbar-transparent {
        background-color: transparent
      }

      body > .navbar-transparent .navbar-nav > .open > a {
        background-color: transparent !important
      }
    }

    #home {
      padding-top: 0
    }

    #home .navbar-brand {
      padding: 13.5px 15px 12.5px
    }

    #home .navbar-brand > img {
      display: inline;
      margin: 0 10px;
      height: 100%
    }

    #banner {
      /*min-height: 300px;*/
      border-bottom: none
    }

    .table-of-contents {
      margin-top: 1em
    }

    /*.page-header h1 {*/
      /*font-size: 4em*/
    /*}*/

    .page-header {
      margin: 0;
    }

    .bs-component {
      position: relative
    }

    .bs-component .modal {
      position: relative;
      top: auto;
      right: auto;
      left: auto;
      bottom: auto;
      z-index: 1;
      display: block
    }

    .bs-component .modal-dialog {
      width: 90%
    }

    .bs-component .popover {
      position: relative;
      display: inline-block;
      width: 220px;
      margin: 20px
    }

    #source-button {
      position: absolute;
      top: 0;
      right: 0;
      z-index: 100;
      font-weight: 700
    }

    .nav-tabs {
      margin-bottom: 15px
    }

    .progress {
      margin-bottom: 10px
    }

    footer {
      margin: 5em 0
    }

    footer li {
      float: left;
      margin-right: 1.5em;
      margin-bottom: 1.5em
    }

    footer p {
      clear: left;
      margin-bottom: 0
    }

    .section-tout {
      padding: 4em 0 3em;
      border-bottom: 1px solid rgba(0, 0, 0, .05);
      background-color: #eaf1f1
    }

    .section-tout .fa {
      margin-right: .5em
    }

    .section-tout p {
      margin-bottom: 3em
    }

    .section-preview {
      padding: 4em 0 4em
    }

    .section-preview .preview {
      margin-bottom: 4em;
      background-color: #eaf1f1
    }

    .section-preview .preview .image {
      position: relative
    }

    .section-preview .preview .image:before {
      box-shadow: inset 0 0 0 1px rgba(0, 0, 0, .1);
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      content: "";
      pointer-events: none
    }

    .section-preview .preview .options {
      padding: 1em 2em 2em;
      border: 1px solid rgba(0, 0, 0, .05);
      border-top: none;
      text-align: center
    }

    .section-preview .preview .options p {
      margin-bottom: 2em
    }

    .section-preview .dropdown-menu {
      text-align: left
    }

    .section-preview .lead {
      margin-bottom: 2em
    }

    @media (max-width: 767px) {
      .section-preview .image img {
        width: 100%
      }
    }

    .sponsor #carbonads {
      max-width: 240px;
      margin: 0 auto
    }

    .sponsor .carbon-text {
      display: block;
      margin-top: 1em;
      font-size: 12px
    }

    .sponsor .carbon-poweredby {
      float: right;
      margin-top: 1em;
      font-size: 10px
    }

    @media (max-width: 767px) {
      #banner {
        margin-bottom: 2em;
        text-align: center
      }
    }
{/literal}
{/css}
{edebug var=$items}
<div class="module text showall">

{if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
{permissions}
    <div class="module-actions">
        {if $permissions.create}
            {icon class=add action=edit rank=1 text="Add text at the top"|gettext}
        {/if}
        {if $permissions.manage}
            {ddrerank items=$items model="text" label="Text Items"|gettext}
        {/if}
    </div>
{/permissions}
{if $config.moduledescription != ""}
    {$config.moduledescription}
{/if}
{$myloc=serialize($__loc)}

    {foreach from=$items item=item name=items}
        <div class="item{if !$item->approved && $smarty.const.ENABLE_WORKFLOW} unapproved{/if}">
            {if $item->title}<{$config.item_level|default:'h2'}>{$item->title}</{$config.item_level|default:'h2'}>{/if}
            {permissions}
                <div class="item-actions">
                    {if $permissions.edit || ($permissions.create && $item->poster == $user->id)}
                        {if $item->revision_id > 1 && $smarty.const.ENABLE_WORKFLOW}<span class="revisionnum approval" title="{'Viewing Revision #'|gettext}{$item->revision_id}">{$item->revision_id}</span>{/if}
                        {if $myloc != $item->location_data}
                            {if $permissions.manage}
                                {icon action=merge id=$item->id title="Merge Aggregated Content"|gettext}
                            {else}
                                {icon img='arrow_merge.png' title="Merged Content"|gettext}
                            {/if}
                        {/if}
                        {icon action=edit record=$item}
                    {/if}
                    {if $permissions.delete || ($permissions.create && $item->poster == $user->id)}
                        {icon action=delete record=$item}
                    {/if}
                    {if !$item->approved && $smarty.const.ENABLE_WORKFLOW && $permissions.approve && ($permissions.edit || ($permissions.create && $item->poster == $user->id))}
                        {icon action=approve record=$item}
                    {/if}
                </div>
            {/permissions}
            <div class="bodycopy">
                {if $config.ffloat != "Below"}
                    {filedisplayer view="`$config.filedisplay`" files=$item->expFile record=$item}
                {/if}
                {$item->body}
                {if $config.ffloat == "Below"}
                    {filedisplayer view="`$config.filedisplay`" files=$item->expFile record=$item}
                {/if}
            </div>
        </div>
        {clear}
        {permissions}
			<div class="module-actions">
				{if $permissions.create}
					{icon class=add action=edit rank=$item->rank+1 text="Add more text here"|gettext}
				{/if}
			</div>
        {/permissions}
    {/foreach}
</div>

<div class="container">
  <div class="page-header" id="banner">
      <div class="row">
        <div class="col-lg-12">
          <div class="page-header">
            <h1 id="navbar">Bootstrap 3 Samples</h1>
          </div>
        </div>
      </div>

    <div class="row">
      <div class="col-sm-12">
        <div class="btn-group table-of-contents">
          <a class="btn btn-default" href="#navbar">Navbar</a>
          <a class="btn btn-default" href="#buttons">Buttons</a>
          <a class="btn btn-default" href="#typography">Typography</a>
          <a class="btn btn-default" href="#tables">Tables</a>
          <a class="btn btn-default" href="#forms">Forms</a>
          <a class="btn btn-default" href="#navs">Navs</a>
          <a class="btn btn-default" href="#indicators">Indicators</a>
          <a class="btn btn-default" href="#progress-bars">Progress bars</a>
          <a class="btn btn-default" href="#containers">Containers</a>
          <a class="btn btn-default" href="#dialogs">Dialogs</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Navbar
  ================================================== -->
  <div class="bs-docs-section clearfix">
    <div class="row">
      <div class="col-lg-12">
        <div class="page-header">
          <h1 id="navbar">Navbar</h1>
        </div>

        <div class="bs-component">
          <nav class="navbar navbar-default">
            <div class="container-fluid">
              <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">Brand</a>
              </div>

              <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                  <li class="active"><a href="#">Link <span class="sr-only">(current)</span></a></li>
                  <li><a href="#">Link</a></li>
                  <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Dropdown <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                      <li><a href="#">Action</a></li>
                      <li><a href="#">Another action</a></li>
                      <li><a href="#">Something else here</a></li>
                      <li class="divider"></li>
                      <li><a href="#">Separated link</a></li>
                      <li class="divider"></li>
                      <li><a href="#">One more separated link</a></li>
                    </ul>
                  </li>
                </ul>
                <form class="navbar-form navbar-left" role="search">
                  <div class="form-group">
                    <input type="text" class="form-control" placeholder="Search">
                  </div>
                  <button type="submit" class="btn btn-default">Submit</button>
                </form>
                <ul class="nav navbar-nav navbar-right">
                  <li><a href="#">Link</a></li>
                </ul>
              </div>
            </div>
          </nav>
        </div>

        <div class="bs-component">
          <nav class="navbar navbar-inverse">
            <div class="container-fluid">
              <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-2">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">Brand</a>
              </div>

              <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-2">
                <ul class="nav navbar-nav">
                  <li class="active"><a href="#">Link <span class="sr-only">(current)</span></a></li>
                  <li><a href="#">Link</a></li>
                  <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Dropdown <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                      <li><a href="#">Action</a></li>
                      <li><a href="#">Another action</a></li>
                      <li><a href="#">Something else here</a></li>
                      <li class="divider"></li>
                      <li><a href="#">Separated link</a></li>
                      <li class="divider"></li>
                      <li><a href="#">One more separated link</a></li>
                    </ul>
                  </li>
                </ul>
                <form class="navbar-form navbar-left" role="search">
                  <div class="form-group">
                    <input type="text" class="form-control" placeholder="Search">
                  </div>
                  <button type="submit" class="btn btn-default">Submit</button>
                </form>
                <ul class="nav navbar-nav navbar-right">
                  <li><a href="#">Link</a></li>
                </ul>
              </div>
            </div>
          </nav>
        </div><!-- /example -->

      </div>
    </div>
  </div>


  <!-- Buttons
  ================================================== -->
  <div class="bs-docs-section">
    <div class="page-header">
      <div class="row">
        <div class="col-lg-12">
          <h1 id="buttons">Buttons</h1>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-7">

        <p class="bs-component">
          <a href="#" class="btn btn-default">Default</a>
          <a href="#" class="btn btn-primary">Primary</a>
          <a href="#" class="btn btn-success">Success</a>
          <a href="#" class="btn btn-info">Info</a>
          <a href="#" class="btn btn-warning">Warning</a>
          <a href="#" class="btn btn-danger">Danger</a>
          <a href="#" class="btn btn-link">Link</a>
        </p>

        <p class="bs-component">
          <a href="#" class="btn btn-default disabled">Default</a>
          <a href="#" class="btn btn-primary disabled">Primary</a>
          <a href="#" class="btn btn-success disabled">Success</a>
          <a href="#" class="btn btn-info disabled">Info</a>
          <a href="#" class="btn btn-warning disabled">Warning</a>
          <a href="#" class="btn btn-danger disabled">Danger</a>
          <a href="#" class="btn btn-link disabled">Link</a>
        </p>


        <div style="margin-bottom: 15px;">
          <div class="btn-toolbar bs-component" style="margin: 0;">
            <div class="btn-group">
              <a href="#" class="btn btn-default">Default</a>
              <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="#">Action</a></li>
                <li><a href="#">Another action</a></li>
                <li><a href="#">Something else here</a></li>
                <li class="divider"></li>
                <li><a href="#">Separated link</a></li>
              </ul>
            </div>

            <div class="btn-group">
              <a href="#" class="btn btn-primary">Primary</a>
              <a href="#" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="#">Action</a></li>
                <li><a href="#">Another action</a></li>
                <li><a href="#">Something else here</a></li>
                <li class="divider"></li>
                <li><a href="#">Separated link</a></li>
              </ul>
            </div>

            <div class="btn-group">
              <a href="#" class="btn btn-success">Success</a>
              <a href="#" class="btn btn-success dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="#">Action</a></li>
                <li><a href="#">Another action</a></li>
                <li><a href="#">Something else here</a></li>
                <li class="divider"></li>
                <li><a href="#">Separated link</a></li>
              </ul>
            </div>

            <div class="btn-group">
              <a href="#" class="btn btn-info">Info</a>
              <a href="#" class="btn btn-info dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="#">Action</a></li>
                <li><a href="#">Another action</a></li>
                <li><a href="#">Something else here</a></li>
                <li class="divider"></li>
                <li><a href="#">Separated link</a></li>
              </ul>
            </div>

            <div class="btn-group">
              <a href="#" class="btn btn-warning">Warning</a>
              <a href="#" class="btn btn-warning dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="#">Action</a></li>
                <li><a href="#">Another action</a></li>
                <li><a href="#">Something else here</a></li>
                <li class="divider"></li>
                <li><a href="#">Separated link</a></li>
              </ul>
            </div>
          </div>
        </div>

        <p class="bs-component">
          <a href="#" class="btn btn-primary btn-lg">Large button</a>
          <a href="#" class="btn btn-primary">Default button</a>
          <a href="#" class="btn btn-primary btn-sm">Small button</a>
          <a href="#" class="btn btn-primary btn-xs">Mini button</a>
        </p>

      </div>
      <div class="col-lg-5">

        <p class="bs-component">
          <a href="#" class="btn btn-default btn-lg btn-block">Block level button</a>
        </p>


        <div class="bs-component" style="margin-bottom: 15px;">
          <div class="btn-group btn-group-justified">
            <a href="#" class="btn btn-default">Left</a>
            <a href="#" class="btn btn-default">Middle</a>
            <a href="#" class="btn btn-default">Right</a>
          </div>
        </div>

        <div class="bs-component" style="margin-bottom: 15px;">
          <div class="btn-toolbar">
            <div class="btn-group">
              <a href="#" class="btn btn-default">1</a>
              <a href="#" class="btn btn-default">2</a>
              <a href="#" class="btn btn-default">3</a>
              <a href="#" class="btn btn-default">4</a>
            </div>

            <div class="btn-group">
              <a href="#" class="btn btn-default">5</a>
              <a href="#" class="btn btn-default">6</a>
              <a href="#" class="btn btn-default">7</a>
            </div>

            <div class="btn-group">
              <a href="#" class="btn btn-default">8</a>
              <div class="btn-group">
                <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                  Dropdown
                  <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                  <li><a href="#">Dropdown link</a></li>
                  <li><a href="#">Dropdown link</a></li>
                  <li><a href="#">Dropdown link</a></li>
                 </ul>
              </div>
            </div>
          </div>
        </div>

        <div class="bs-component">
          <div class="btn-group-vertical">
              <a href="#" class="btn btn-default">Button</a>
              <a href="#" class="btn btn-default">Button</a>
              <a href="#" class="btn btn-default">Button</a>
              <a href="#" class="btn btn-default">Button</a>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Typography
  ================================================== -->
  <div class="bs-docs-section">
    <div class="row">
      <div class="col-lg-12">
        <div class="page-header">
          <h1 id="typography">Typography</h1>
        </div>
      </div>
    </div>

    <!-- Headings -->

    <div class="row">
      <div class="col-lg-4">
        <div class="bs-component">
          <h1>Heading 1</h1>
          <h2>Heading 2</h2>
          <h3>Heading 3</h3>
          <h4>Heading 4</h4>
          <h5>Heading 5</h5>
          <h6>Heading 6</h6>
          <p class="lead">Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="bs-component">
          <h2>Example body text</h2>
          <p>Nullam quis risus eget <a href="#">urna mollis ornare</a> vel eu leo. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nullam id dolor id nibh ultricies vehicula.</p>
          <p><small>This line of text is meant to be treated as fine print.</small></p>
          <p>The following snippet of text is <strong>rendered as bold text</strong>.</p>
          <p>The following snippet of text is <em>rendered as italicized text</em>.</p>
          <p>An abbreviation of the word attribute is <abbr title="attribute">attr</abbr>.</p>
        </div>

      </div>
      <div class="col-lg-4">
        <div class="bs-component">
          <h2>Emphasis classes</h2>
          <p class="text-muted">Fusce dapibus, tellus ac cursus commodo, tortor mauris nibh.</p>
          <p class="text-primary">Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
          <p class="text-warning">Etiam porta sem malesuada magna mollis euismod.</p>
          <p class="text-danger">Donec ullamcorper nulla non metus auctor fringilla.</p>
          <p class="text-success">Duis mollis, est non commodo luctus, nisi erat porttitor ligula.</p>
          <p class="text-info">Maecenas sed diam eget risus varius blandit sit amet non magna.</p>
        </div>

      </div>
        <div class="col-lg-4">
          <div class="bs-component">
            <p><mark>Marker</mark></p>

            <p><big>Big</big></p>

            <p><small>Small</small></p>

            <p><tt>Typewriter</tt></p>
          </div>

        </div>
          <div class="col-lg-4">
            <div class="bs-component">

            <p><code>Computer Code</code></p>

            <p><kbd>Keyboard Phrase</kbd></p>

            <p><samp>Sample Text</samp></p>

            <p><var>Variable</var></p>
            </div>

          </div>
            <div class="col-lg-4">
              <div class="bs-component">

            <p><del>Deleted Text</del></p>

            <p><ins>Inserted Text</ins></p>

            <p><cite>Cited Work</cite></p>

            <p><q>Inline Quotation</q></p>
          </div>
        </div>
    </div>

    <!-- Blockquotes -->

    <div class="row">
      <div class="col-lg-12">
        <h2 id="type-blockquotes">Blockquotes</h2>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-6">
        <div class="bs-component">
          <blockquote>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.</p>
            <small>Someone famous in <cite title="Source Title">Source Title</cite></small>
          </blockquote>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="bs-component">
          <blockquote class="blockquote-reverse">
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.</p>
            <small>Someone famous in <cite title="Source Title">Source Title</cite></small>
          </blockquote>
        </div>
      </div>
    </div>
  </div>

  <!-- Tables
  ================================================== -->
  <div class="bs-docs-section">

    <div class="row">
      <div class="col-lg-12">
        <div class="page-header">
          <h1 id="tables">Tables</h1>
        </div>

        <div class="bs-component">
          <table class="table table-striped table-hover ">
            <thead>
              <tr>
                <th>#</th>
                <th>Column heading</th>
                <th>Column heading</th>
                <th>Column heading</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td>Default content</td>
                <td>Column content</td>
                <td>Column content</td>
              </tr>
              <tr>
                <td>2</td>
                <td>Default content</td>
                <td>Column content</td>
                <td>Column content</td>
              </tr>
              <tr class="info">
                <td>3</td>
                <td>Info content</td>
                <td>Column content</td>
                <td>Column content</td>
              </tr>
              <tr class="success">
                <td>4</td>
                <td>Success content</td>
                <td>Column content</td>
                <td>Column content</td>
              </tr>
              <tr class="danger">
                <td>5</td>
                <td>Danger content</td>
                <td>Column content</td>
                <td>Column content</td>
              </tr>
              <tr class="warning">
                <td>6</td>
                <td>Warning content</td>
                <td>Column content</td>
                <td>Column content</td>
              </tr>
              <tr class="active">
                <td>7</td>
                <td>Active content</td>
                <td>Column content</td>
                <td>Column content</td>
              </tr>
            </tbody>
          </table>
        </div><!-- /example -->
      </div>
    </div>
  </div>

  <!-- Forms
  ================================================== -->
  <div class="bs-docs-section">
    <div class="row">
      <div class="col-lg-12">
        <div class="page-header">
          <h1 id="forms">Forms</h1>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-6">
        <div class="well bs-component">
          <form class="form-horizontal">
            <fieldset>
              <legend>Legend</legend>
              <div class="form-group">
                <label for="inputEmail" class="col-lg-2 control-label">Email</label>
                <div class="col-lg-10">
                  <input type="text" class="form-control" id="inputEmail" placeholder="Email">
                </div>
              </div>
              <div class="form-group">
                <label for="inputPassword" class="col-lg-2 control-label">Password</label>
                <div class="col-lg-10">
                  <input type="password" class="form-control" id="inputPassword" placeholder="Password">
                  <div class="checkbox">
                    <label>
                      <input type="checkbox"> Checkbox
                    </label>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="textArea" class="col-lg-2 control-label">Textarea</label>
                <div class="col-lg-10">
                  <textarea class="form-control" rows="3" id="textArea"></textarea>
                  <span class="help-block">A longer block of help text that breaks onto a new line and may extend beyond one line.</span>
                </div>
              </div>
              <div class="form-group">
                <label class="col-lg-2 control-label">Radios</label>
                <div class="col-lg-10">
                  <div class="radio">
                    <label>
                      <input type="radio" name="optionsRadios" id="optionsRadios1" value="option1" checked="">
                      Option one is this
                    </label>
                  </div>
                  <div class="radio">
                    <label>
                      <input type="radio" name="optionsRadios" id="optionsRadios2" value="option2">
                      Option two can be something else
                    </label>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="select" class="col-lg-2 control-label">Selects</label>
                <div class="col-lg-10">
                  <select class="form-control" id="select">
                    <option>1</option>
                    <option>2</option>
                    <option>3</option>
                    <option>4</option>
                    <option>5</option>
                  </select>
                  <br>
                  <select multiple="" class="form-control">
                    <option>1</option>
                    <option>2</option>
                    <option>3</option>
                    <option>4</option>
                    <option>5</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <div class="col-lg-10 col-lg-offset-2">
                  <button type="reset" class="btn btn-default">Cancel</button>
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </div>
            </fieldset>
          </form>
        </div>
      </div>
      <div class="col-lg-4 col-lg-offset-1">

          <form class="bs-component">
            <div class="form-group">
              <label class="control-label" for="focusedInput">Focused input</label>
              <input class="form-control" id="focusedInput" type="text" value="This is focused...">
            </div>

            <div class="form-group">
              <label class="control-label" for="disabledInput">Disabled input</label>
              <input class="form-control" id="disabledInput" type="text" placeholder="Disabled input here..." disabled="">
            </div>

            <div class="form-group has-warning">
              <label class="control-label" for="inputWarning">Input warning</label>
              <input type="text" class="form-control" id="inputWarning">
            </div>

            <div class="form-group has-error">
              <label class="control-label" for="inputError">Input error</label>
              <input type="text" class="form-control" id="inputError">
            </div>

            <div class="form-group has-success">
              <label class="control-label" for="inputSuccess">Input success</label>
              <input type="text" class="form-control" id="inputSuccess">
            </div>

            <div class="form-group">
              <label class="control-label" for="inputLarge">Large input</label>
              <input class="form-control input-lg" type="text" id="inputLarge">
            </div>

            <div class="form-group">
              <label class="control-label" for="inputDefault">Default input</label>
              <input type="text" class="form-control" id="inputDefault">
            </div>

            <div class="form-group">
              <label class="control-label" for="inputSmall">Small input</label>
              <input class="form-control input-sm" type="text" id="inputSmall">
            </div>

            <div class="form-group">
              <label class="control-label">Input addons</label>
              <div class="input-group">
                <span class="input-group-addon">$</span>
                <input type="text" class="form-control">
                <span class="input-group-btn">
                  <button class="btn btn-default" type="button">Button</button>
                </span>
              </div>
            </div>
          </form>

      </div>
    </div>
  </div>

  <!-- Navs
  ================================================== -->
  <div class="bs-docs-section">

    <div class="row">
      <div class="col-lg-12">
        <div class="page-header">
          <h1 id="navs">Navs</h1>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-4">
        <h2 id="nav-tabs">Tabs</h2>
        <div class="bs-component">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#home" data-toggle="tab">Home</a></li>
            <li><a href="#profile" data-toggle="tab">Profile</a></li>
            <li class="disabled"><a>Disabled</a></li>
            <li class="dropdown">
              <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                Dropdown <span class="caret"></span>
              </a>
              <ul class="dropdown-menu">
                <li><a href="#dropdown1" data-toggle="tab">Action</a></li>
                <li class="divider"></li>
                <li><a href="#dropdown2" data-toggle="tab">Another action</a></li>
              </ul>
            </li>
          </ul>
          <div id="myTabContent" class="tab-content">
            <div class="tab-pane fade active in" id="home">
              <p>Raw denim you probably haven't heard of them jean shorts Austin. Nesciunt tofu stumptown aliqua, retro synth master cleanse. Mustache cliche tempor, williamsburg carles vegan helvetica. Reprehenderit butcher retro keffiyeh dreamcatcher synth. Cosby sweater eu banh mi, qui irure terry richardson ex squid. Aliquip placeat salvia cillum iphone. Seitan aliquip quis cardigan american apparel, butcher voluptate nisi qui.</p>
            </div>
            <div class="tab-pane fade" id="profile">
              <p>Food truck fixie locavore, accusamus mcsweeney's marfa nulla single-origin coffee squid. Exercitation +1 labore velit, blog sartorial PBR leggings next level wes anderson artisan four loko farm-to-table craft beer twee. Qui photo booth letterpress, commodo enim craft beer mlkshk aliquip jean shorts ullamco ad vinyl cillum PBR. Homo nostrud organic, assumenda labore aesthetic magna delectus mollit.</p>
            </div>
            <div class="tab-pane fade" id="dropdown1">
              <p>Etsy mixtape wayfarers, ethical wes anderson tofu before they sold out mcsweeney's organic lomo retro fanny pack lo-fi farm-to-table readymade. Messenger bag gentrify pitchfork tattooed craft beer, iphone skateboard locavore carles etsy salvia banksy hoodie helvetica. DIY synth PBR banksy irony. Leggings gentrify squid 8-bit cred pitchfork.</p>
            </div>
            <div class="tab-pane fade" id="dropdown2">
              <p>Trust fund seitan letterpress, keytar raw denim keffiyeh etsy art party before they sold out master cleanse gluten-free squid scenester freegan cosby sweater. Fanny pack portland seitan DIY, art party locavore wolf cliche high life echo park Austin. Cred vinyl keffiyeh DIY salvia PBR, banh mi before they sold out farm-to-table VHS viral locavore cosby sweater.</p>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <h2 id="nav-pills">Pills</h2>
        <div class="bs-component">
          <ul class="nav nav-pills">
            <li class="active"><a href="#">Home</a></li>
            <li><a href="#">Profile</a></li>
            <li class="disabled"><a href="#">Disabled</a></li>
            <li class="dropdown">
              <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                Dropdown <span class="caret"></span>
              </a>
              <ul class="dropdown-menu">
                <li><a href="#">Action</a></li>
                <li><a href="#">Another action</a></li>
                <li><a href="#">Something else here</a></li>
                <li class="divider"></li>
                <li><a href="#">Separated link</a></li>
              </ul>
            </li>
          </ul>
        </div>
        <br>
        <div class="bs-component">
          <ul class="nav nav-pills nav-stacked">
            <li class="active"><a href="#">Home</a></li>
            <li><a href="#">Profile</a></li>
            <li class="disabled"><a href="#">Disabled</a></li>
            <li class="dropdown">
              <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                Dropdown <span class="caret"></span>
              </a>
              <ul class="dropdown-menu">
                <li><a href="#">Action</a></li>
                <li><a href="#">Another action</a></li>
                <li><a href="#">Something else here</a></li>
                <li class="divider"></li>
                <li><a href="#">Separated link</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
      <div class="col-lg-4">
        <h2 id="nav-breadcrumbs">Breadcrumbs</h2>
        <div class="bs-component">
          <ul class="breadcrumb">
            <li class="active">Home</li>
          </ul>

          <ul class="breadcrumb">
            <li><a href="#">Home</a></li>
            <li class="active">Library</li>
          </ul>

          <ul class="breadcrumb">
            <li><a href="#">Home</a></li>
            <li><a href="#">Library</a></li>
            <li class="active">Data</li>
          </ul>
        </div>

      </div>
    </div>


    <div class="row">
      <div class="col-lg-4">
        <h2 id="pagination">Pagination</h2>
        <div class="bs-component">
          <ul class="pagination">
            <li class="disabled"><a href="#">&laquo;</a></li>
            <li class="active"><a href="#">1</a></li>
            <li><a href="#">2</a></li>
            <li><a href="#">3</a></li>
            <li><a href="#">4</a></li>
            <li><a href="#">5</a></li>
            <li><a href="#">&raquo;</a></li>
          </ul>

          <ul class="pagination pagination-lg">
            <li class="disabled"><a href="#">&laquo;</a></li>
            <li class="active"><a href="#">1</a></li>
            <li><a href="#">2</a></li>
            <li><a href="#">3</a></li>
            <li><a href="#">&raquo;</a></li>
          </ul>

          <ul class="pagination pagination-sm">
            <li class="disabled"><a href="#">&laquo;</a></li>
            <li class="active"><a href="#">1</a></li>
            <li><a href="#">2</a></li>
            <li><a href="#">3</a></li>
            <li><a href="#">4</a></li>
            <li><a href="#">5</a></li>
            <li><a href="#">&raquo;</a></li>
          </ul>
        </div>
      </div>
      <div class="col-lg-4">
        <h2 id="pager">Pager</h2>
        <div class="bs-component">
          <ul class="pager">
            <li><a href="#">Previous</a></li>
            <li><a href="#">Next</a></li>
          </ul>

          <ul class="pager">
            <li class="previous disabled"><a href="#">&larr; Older</a></li>
            <li class="next"><a href="#">Newer &rarr;</a></li>
          </ul>
        </div>
      </div>
      <div class="col-lg-4">

      </div>
    </div>
  </div>

  <!-- Indicators
  ================================================== -->
  <div class="bs-docs-section">

    <div class="row">
      <div class="col-lg-12">
        <div class="page-header">
          <h1 id="indicators">Indicators</h1>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12">
        <h2>Alerts</h2>
        <div class="bs-component">
          <div class="alert alert-dismissible alert-warning">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h4>Warning!</h4>
            <p>Best check yo self, you're not looking too good. Nulla vitae elit libero, a pharetra augue. Praesent commodo cursus magna, <a href="#" class="alert-link">vel scelerisque nisl consectetur et</a>.</p>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-4">
        <div class="bs-component">
          <div class="alert alert-dismissible alert-danger">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
             <h4>Danger!!</h4>
            <strong>Oh snap!</strong> <a href="#" class="alert-link">Change a few things up</a> and try submitting again.
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="bs-component">
          <div class="alert alert-dismissible alert-success">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
             <h4>Success</h4>
            <strong>Well done!</strong> You successfully read <a href="#" class="alert-link">this important alert message</a>.
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="bs-component">
          <div class="alert alert-dismissible alert-info">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
             <h4>Info</h4>
            <strong>Heads up!</strong> This <a href="#" class="alert-link">alert needs your attention</a>, but it's not super important.
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-4">
        <h2>Labels</h2>
        <div class="bs-component" style="margin-bottom: 40px;">
          <span class="label label-default">Default</span>
          <span class="label label-primary">Primary</span>
          <span class="label label-success">Success</span>
          <span class="label label-warning">Warning</span>
          <span class="label label-danger">Danger</span>
          <span class="label label-info">Info</span>
        </div>
      </div>
      <div class="col-lg-4">
        <h2>Badges</h2>
        <div class="bs-component">
          <ul class="nav nav-pills">
            <li class="active"><a href="#">Home <span class="badge">42</span></a></li>
            <li><a href="#">Profile <span class="badge"></span></a></li>
            <li><a href="#">Messages <span class="badge">3</span></a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- Progress bars
  ================================================== -->
  <div class="bs-docs-section">

    <div class="row">
      <div class="col-lg-12">
        <div class="page-header">
          <h1 id="progress-bars">Progress bars</h1>
        </div>

        <h3 id="progress-basic">Basic</h3>
        <div class="bs-component">
          <div class="progress">
            <div class="progress-bar" style="width: 60%;"></div>
          </div>
        </div>

        <h3 id="progress-alternatives">Contextual alternatives</h3>
        <div class="bs-component">
          <div class="progress">
            <div class="progress-bar progress-bar-info" style="width: 20%"></div>
          </div>

          <div class="progress">
            <div class="progress-bar progress-bar-success" style="width: 40%"></div>
          </div>

          <div class="progress">
            <div class="progress-bar progress-bar-warning" style="width: 60%"></div>
          </div>

          <div class="progress">
            <div class="progress-bar progress-bar-danger" style="width: 80%"></div>
          </div>
        </div>

        <h3 id="progress-striped">Striped</h3>
        <div class="bs-component">
          <div class="progress progress-striped">
            <div class="progress-bar progress-bar-info" style="width: 20%"></div>
          </div>

          <div class="progress progress-striped">
            <div class="progress-bar progress-bar-success" style="width: 40%"></div>
          </div>

          <div class="progress progress-striped">
            <div class="progress-bar progress-bar-warning" style="width: 60%"></div>
          </div>

          <div class="progress progress-striped">
            <div class="progress-bar progress-bar-danger" style="width: 80%"></div>
          </div>
        </div>

        <h3 id="progress-animated">Animated</h3>
        <div class="bs-component">
          <div class="progress progress-striped active">
            <div class="progress-bar" style="width: 45%"></div>
          </div>
        </div>

        <h3 id="progress-stacked">Stacked</h3>
        <div class="bs-component">
          <div class="progress">
            <div class="progress-bar progress-bar-success" style="width: 35%"></div>
            <div class="progress-bar progress-bar-warning" style="width: 20%"></div>
            <div class="progress-bar progress-bar-danger" style="width: 10%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Containers
  ================================================== -->
  <div class="bs-docs-section">

    <div class="row">
      <div class="col-lg-12">
        <div class="page-header">
          <h1 id="containers">Containers</h1>
        </div>
        <div class="bs-component">
          <div class="jumbotron">
            <h1>Jumbotron</h1>
            <p>This is a simple hero unit, a simple jumbotron-style component for calling extra attention to featured content or information.</p>
            <p><a class="btn btn-primary btn-lg">Learn more</a></p>
          </div>
        </div>
      </div>
    </div>


    <div class="row">
      <div class="col-lg-12">
        <h2>List groups</h2>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-4">
        <div class="bs-component">
          <ul class="list-group">
            <li class="list-group-item">
              <span class="badge">14</span>
              Cras justo odio
            </li>
            <li class="list-group-item">
              <span class="badge">2</span>
              Dapibus ac facilisis in
            </li>
            <li class="list-group-item">
              <span class="badge">1</span>
              Morbi leo risus
            </li>
          </ul>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="bs-component">
          <div class="list-group">
            <a href="#" class="list-group-item active">
              Cras justo odio
            </a>
            <a href="#" class="list-group-item">Dapibus ac facilisis in
            </a>
            <a href="#" class="list-group-item">Morbi leo risus
            </a>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="bs-component">
          <div class="list-group">
            <a href="#" class="list-group-item">
              <h4 class="list-group-item-heading">List group item heading</h4>
              <p class="list-group-item-text">Donec id elit non mi porta gravida at eget metus. Maecenas sed diam eget risus varius blandit.</p>
            </a>
            <a href="#" class="list-group-item">
              <h4 class="list-group-item-heading">List group item heading</h4>
              <p class="list-group-item-text">Donec id elit non mi porta gravida at eget metus. Maecenas sed diam eget risus varius blandit.</p>
            </a>
          </div>
        </div>
      </div>
    </div>


    <div class="row">
      <div class="col-lg-12">
        <h2>Panels</h2>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-4">
        <div class="bs-component">
          <div class="panel panel-default">
            <div class="panel-body">
              Basic panel
            </div>
          </div>

          <div class="panel panel-default">
            <div class="panel-heading">Panel heading</div>
            <div class="panel-body">
              Panel content
            </div>
          </div>

          <div class="panel panel-default">
            <div class="panel-body">
              Panel content
            </div>
            <div class="panel-footer">Panel footer</div>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="bs-component">
          <div class="panel panel-primary">
            <div class="panel-heading">
              <h3 class="panel-title">Panel primary</h3>
            </div>
            <div class="panel-body">
              Panel content
            </div>
          </div>

          <div class="panel panel-success">
            <div class="panel-heading">
              <h3 class="panel-title">Panel success</h3>
            </div>
            <div class="panel-body">
              Panel content
            </div>
          </div>

          <div class="panel panel-warning">
            <div class="panel-heading">
              <h3 class="panel-title">Panel warning</h3>
            </div>
            <div class="panel-body">
              Panel content
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="bs-component">
          <div class="panel panel-danger">
            <div class="panel-heading">
              <h3 class="panel-title">Panel danger</h3>
            </div>
            <div class="panel-body">
              Panel content
            </div>
          </div>

          <div class="panel panel-info">
            <div class="panel-heading">
              <h3 class="panel-title">Panel info</h3>
            </div>
            <div class="panel-body">
              Panel content
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12">
        <h2>Wells</h2>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-4">
        <div class="bs-component">
          <div class="well">
            Look, I'm in a well!
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="bs-component">
          <div class="well well-sm">
            Look, I'm in a small well!
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="bs-component">
          <div class="well well-lg">
            Look, I'm in a large well!
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Dialogs
  ================================================== -->
  <div class="bs-docs-section">

    <div class="row">
      <div class="col-lg-12">
        <div class="page-header">
          <h1 id="dialogs">Dialogs</h1>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-6">
        <h2>Modals</h2>
        <div class="bs-component">
          <div class="modal">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                  <h4 class="modal-title">Modal title</h4>
                </div>
                <div class="modal-body">
                  <p>One fine body…</p>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-primary">Save changes</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <h2>Popovers</h2>
        <div class="bs-component">
          <button type="button" class="btn btn-default" data-container="body" data-toggle="popover" data-placement="left" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus.">Left</button>

          <button type="button" class="btn btn-default" data-container="body" data-toggle="popover" data-placement="top" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus.">Top</button>

          <button type="button" class="btn btn-default" data-container="body" data-toggle="popover" data-placement="bottom" data-content="Vivamus
          sagittis lacus vel augue laoreet rutrum faucibus.">Bottom</button>

          <button type="button" class="btn btn-default" data-container="body" data-toggle="popover" data-placement="right" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus.">Right</button>
        </div>
        <h2>Tooltips</h2>
        <div class="bs-component">
          <button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="left" title="" data-original-title="Tooltip on left">Left</button>

          <button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="top" title="" data-original-title="Tooltip on top">Top</button>

          <button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Tooltip on bottom">Bottom</button>

          <button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="right" title="" data-original-title="Tooltip on right">Right</button>
        </div>
      </div>
    </div>
  </div>

  <div id="source-modal" class="modal fade">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Source Code</h4>
        </div>
        <div class="modal-body">
          <pre></pre>
        </div>
      </div>
    </div>
  </div>

</div>
{script unique="bootstrap" bootstrap="affix,alert,button,carousel,collapse,dropdown,modal,tooltip,popover,scrollspy,tab,transition"}
{literal}
    (function(){
      $(window).scroll(function () {
          var top = $(document).scrollTop();
          // $('.splash').css({
          //   'background-position': '0px -'+(top/3).toFixed(2)+'px'
          // });
          if(top > 50)
            $('#home > .navbar').removeClass('navbar-transparent');
          else
            $('#home > .navbar').addClass('navbar-transparent');
      });

      $("a[href='#']").click(function(e) {
        e.preventDefault();
      });

      var $button = $("<div id='source-button' class='btn btn-primary btn-xs'>&lt; &gt;</div>").click(function(){
        var html = $(this).parent().html();
        html = cleanSource(html);
        $("#source-modal pre").text(html);
        $("#source-modal").modal();
      });

      $('.bs-component [data-toggle="popover"]').popover();
      $('.bs-component [data-toggle="tooltip"]').tooltip();

      $(".bs-component").hover(function(){
        $(this).append($button);
        $button.show();
      }, function(){
        $button.hide();
      });

      function cleanSource(html) {
        html = html.replace(/×/g, "&times;")
                   .replace(/«/g, "&laquo;")
                   .replace(/»/g, "&raquo;")
                   .replace(/←/g, "&larr;")
                   .replace(/→/g, "&rarr;");

        var lines = html.split(/\n/);

        lines.shift();
        lines.splice(-1, 1);

        var indentSize = lines[0].length - lines[0].trim().length,
            re = new RegExp(" {" + indentSize + "}");

        lines = lines.map(function(line){
          if (line.match(re)) {
            line = line.substring(indentSize);
          }

          return line;
        });

        lines = lines.join("\n");

        return lines;
      }

    })();
{/literal}
{/script}
