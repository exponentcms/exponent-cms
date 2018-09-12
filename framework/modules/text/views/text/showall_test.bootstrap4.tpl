{*
 * Copyright (c) 2004-2018 OIC Group, Inc.
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
    pre {
      background: #f7f7f9;
    }

    @media (min-width: 768px) {
      body > .navbar-transparent {
        box-shadow: none;
        .navbar-nav > .open > a {
          box-shadow: none;
        }
      }
    }

    #home, #help {
      font-size: 0.9rem;
      .navbar {
        background: rgb(52,154,237);
        background: linear-gradient(145deg, rgba(52,154,237,1) 50%, rgba(52,216,237,1) 100%);
        transition: box-shadow 200ms ease-in;
      }
      .navbar-transparent {
        background: none !important;
        box-shadow: none;
      }
      .navbar-brand {
        .nav-link {
          display: inline-block;
          margin-right: -30px;
        }
        img {
          display: inline-block;
          margin: 0 10px;
          width: 30px;
        }
      }
      .nav-link {
        text-transform: uppercase;
        font-weight: 500;
        color: #fff;
      }
    }

    #home {
      padding-top: 0px;
      .btn {
        padding: 0.6rem 0.55rem 0.5rem;
        box-shadow: none;
        font-size: 0.7rem;
        font-weight: 500;
      }
    }

    .bs-docs-section {
      margin-top: 2em;
      .page-header h1 {
        padding: 2rem 0;
        font-size: 3rem;
      }
    }

    .dropdown-menu.show[aria-labelledby="themes"] {
      display: flex;
      width: 420px;
      flex-wrap: wrap;

      .dropdown-item {
        width: 33.333%;

        &:first-child {
          width: 100%;
        }
      }
    }

    .bs-component {
      position: relative;
      + .bs-component {
        margin-top: 1rem;
      }
      .card {
        margin-bottom: 1rem;
      }
      .modal {
        position: relative;
        top: auto;
        right: auto;
        left: auto;
        bottom: auto;
        z-index: 1;
        display: block;
      }
      .modal-dialog {
        width: 90%;
      }
      .popover {
        position: relative;
        display: inline-block;
        width: 220px;
        margin: 20px;
      }
    }

    #source-button {
      position: absolute;
      top: 0;
      right: 0;
      z-index: 100;
      font-weight: bold;
    }

    #source-modal {
      pre {
        max-height: calc(100vh - 11rem);
        background-color: rgba(0,0,0,0.7);
        color: rgba(255,255,255,0.7);
      }
    }

    .nav-tabs {
      margin-bottom: 15px;
    }

    .progress {
      margin-bottom: 10px;
    }

    #footer {
      margin: 5em 0;
      li {
        float: left;
        margin-right: 1.5em;
        margin-bottom: 1.5em;
      }
      p {
        clear: left;
        margin-bottom: 0;
      }
    }

    .splash {
      padding: 12em 0 6em;
      background: rgb(52,154,237);
      background: linear-gradient(145deg, rgba(52,154,237,1) 50%, rgba(52,216,237,1) 100%);
      color: #fff;
      text-align: center;
      .logo {
        width: 160px;
      }
      h1 {
        font-size: 3em;
        color: #fff;
      }
      #social {
        margin: 2em 0 3em;
      }
      .alert {
        margin: 2em 0;
        border: none;
      }
      .sponsor a {
        color: #fff;
      }
    }

    .section-tout {
      padding: 6em 0 1em;
      border-bottom: 1px solid rgba(0, 0, 0, 0.05);
      background-color: #eaf1f1;
      .fa {
        margin-right: 0.2em;
      }
      p {
        margin-bottom: 5em;
      }
    }

    .section-preview {
      padding: 4em 0 4em;
      .preview {
        margin-bottom: 4em;
        background-color: #eaf1f1;
        img {
          max-width: 100%;
        }
        .image {
          position: relative;
          &:before {
            box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.1);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            content: "";
            pointer-events: none;
          }
        }
        .options {
          padding: 2em;
          border: 1px solid rgba(0, 0, 0, 0.05);
          border-top: none;
          text-align: center;
          p {
            margin-bottom: 2em;
          }
        }
      }
      .dropdown-menu {
        text-align: left;
      }
      .lead {
        margin-bottom: 2em;
      }
    }

    @media (max-width: 767px) {
      .section-preview .image img {
        width: 100%;
      }
    }

    .sponsor {
      img {
        max-width: 100%;
      }
      #carbonads {
        max-width: 240px;
        margin: 0 auto;
      }
      .carbon-text {
        display: block;
        margin-top: 1em;
        font-size: 12px;
      }
      .carbon-poweredby {
        float: right;
        margin-top: 1em;
        font-size: 10px;
      }
    }

    @media (max-width: 767px) {
      .splash {
        padding-top: 8em;
        .logo {
          width: 100px;
        }
        h1 {
          font-size: 2em;
        }
      }
      #banner {
        margin-bottom: 2em;
        text-align: center;
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
      <div class="col-lg-8 col-md-7 col-sm-6">
        <h1>Bootstrap 4 Samples</h1>
      </div>
    </div>
  </div>

    <!-- Navbar
    ================================================== -->
    <div class="bs-docs-section clearfix">
      <div class="row">
        <div class="col-lg-12">
          <div class="page-header">
            <h1 id="navbars">Navbars</h1>
          </div>

          <div class="bs-component">
            <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
              <a class="navbar-brand" href="#">Navbar</a>
              <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
              </button>

              <div class="collapse navbar-collapse" id="navbarColor01">
                <ul class="navbar-nav mr-auto">
                  <li class="nav-item active">
                    <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">Features</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">Pricing</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">About</a>
                  </li>
                </ul>
                <form class="form-inline my-2 my-lg-0">
                  <input class="form-control mr-sm-2" type="text" placeholder="Search">
                  <button class="btn btn-secondary my-2 my-sm-0" type="submit">Search</button>
                </form>
              </div>
            </nav>
          </div>

          <div class="bs-component">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
              <a class="navbar-brand" href="#">Navbar</a>
              <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor02" aria-controls="navbarColor02" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
              </button>

              <div class="collapse navbar-collapse" id="navbarColor02">
                <ul class="navbar-nav mr-auto">
                  <li class="nav-item active">
                    <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">Features</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">Pricing</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">About</a>
                  </li>
                </ul>
                <form class="form-inline my-2 my-lg-0">
                  <input class="form-control mr-sm-2" type="text" placeholder="Search">
                  <button class="btn btn-secondary my-2 my-sm-0" type="submit">Search</button>
                </form>
              </div>
            </nav>
          </div>

          <div class="bs-component">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
              <a class="navbar-brand" href="#">Navbar</a>
              <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor03" aria-controls="navbarColor03" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
              </button>

              <div class="collapse navbar-collapse" id="navbarColor03">
                <ul class="navbar-nav mr-auto">
                  <li class="nav-item active">
                    <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">Features</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">Pricing</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">About</a>
                  </li>
                </ul>
                <form class="form-inline my-2 my-lg-0">
                  <input class="form-control mr-sm-2" type="text" placeholder="Search">
                  <button class="btn btn-secondary my-2 my-sm-0" type="submit">Search</button>
                </form>
              </div>
            </nav>
          </div>

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
            <button type="button" class="btn btn-primary">Primary</button>
            <button type="button" class="btn btn-secondary">Secondary</button>
            <button type="button" class="btn btn-success">Success</button>
            <button type="button" class="btn btn-info">Info</button>
            <button type="button" class="btn btn-warning">Warning</button>
            <button type="button" class="btn btn-danger">Danger</button>
            <button type="button" class="btn btn-link">Link</button>
          </p>

          <p class="bs-component">
            <button type="button" class="btn btn-primary disabled">Primary</button>
            <button type="button" class="btn btn-secondary disabled">Secondary</button>
            <button type="button" class="btn btn-success disabled">Success</button>
            <button type="button" class="btn btn-info disabled">Info</button>
            <button type="button" class="btn btn-warning disabled">Warning</button>
            <button type="button" class="btn btn-danger disabled">Danger</button>
            <button type="button" class="btn btn-link disabled">Link</button>
          </p>

          <p class="bs-component">
            <button type="button" class="btn btn-outline-primary">Primary</button>
            <button type="button" class="btn btn-outline-secondary">Secondary</button>
            <button type="button" class="btn btn-outline-success">Success</button>
            <button type="button" class="btn btn-outline-info">Info</button>
            <button type="button" class="btn btn-outline-warning">Warning</button>
            <button type="button" class="btn btn-outline-danger">Danger</button>
          </p>

          <div class="bs-component">
            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
              <button type="button" class="btn btn-primary">Primary</button>
              <div class="btn-group" role="group">
                <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                  <a class="dropdown-item" href="#">Dropdown link</a>
                  <a class="dropdown-item" href="#">Dropdown link</a>
                </div>
              </div>
            </div>

            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
              <button type="button" class="btn btn-success">Success</button>
              <div class="btn-group" role="group">
                <button id="btnGroupDrop2" type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                <div class="dropdown-menu" aria-labelledby="btnGroupDrop2">
                  <a class="dropdown-item" href="#">Dropdown link</a>
                  <a class="dropdown-item" href="#">Dropdown link</a>
                </div>
              </div>
            </div>

            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
              <button type="button" class="btn btn-info">Info</button>
              <div class="btn-group" role="group">
                <button id="btnGroupDrop3" type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                <div class="dropdown-menu" aria-labelledby="btnGroupDrop3">
                  <a class="dropdown-item" href="#">Dropdown link</a>
                  <a class="dropdown-item" href="#">Dropdown link</a>
                </div>
              </div>
            </div>

            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
              <button type="button" class="btn btn-danger">Danger</button>
              <div class="btn-group" role="group">
                <button id="btnGroupDrop4" type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                <div class="dropdown-menu" aria-labelledby="btnGroupDrop4">
                  <a class="dropdown-item" href="#">Dropdown link</a>
                  <a class="dropdown-item" href="#">Dropdown link</a>
                </div>
              </div>
            </div>
          </div>

          <div class="bs-component">
            <button type="button" class="btn btn-primary btn-lg">Large button</button>
            <button type="button" class="btn btn-primary">Default button</button>
            <button type="button" class="btn btn-primary btn-sm">Small button</button>
          </div>

        </div>
        <div class="col-lg-5">

          <p class="bs-component">
            <button type="button" class="btn btn-primary btn-lg btn-block">Block level button</button>
          </p>

          <div class="bs-component" style="margin-bottom: 15px;">
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
              <label class="btn btn-primary active">
                <input type="checkbox" checked autocomplete="off"> Active
              </label>
              <label class="btn btn-primary">
                <input type="checkbox" autocomplete="off"> Check
              </label>
              <label class="btn btn-primary">
                <input type="checkbox" autocomplete="off"> Check
              </label>
            </div>
          </div>

          <div class="bs-component" style="margin-bottom: 15px;">
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
              <label class="btn btn-primary active">
                <input type="radio" name="options" id="option1" autocomplete="off" checked> Active
              </label>
              <label class="btn btn-primary">
                <input type="radio" name="options" id="option2" autocomplete="off"> Radio
              </label>
              <label class="btn btn-primary">
                <input type="radio" name="options" id="option3" autocomplete="off"> Radio
              </label>
            </div>
          </div>

          <div class="bs-component">
            <div class="btn-group-vertical" data-toggle="buttons">
              <button type="button" class="btn btn-primary">Button</button>
              <button type="button" class="btn btn-primary">Button</button>
              <button type="button" class="btn btn-primary">Button</button>
              <button type="button" class="btn btn-primary">Button</button>
              <button type="button" class="btn btn-primary">Button</button>
              <button type="button" class="btn btn-primary">Button</button>
            </div>
          </div>

          <div class="bs-component" style="margin-bottom: 15px;">
            <div class="btn-group" role="group" aria-label="Basic example">
              <button type="button" class="btn btn-secondary">Left</button>
              <button type="button" class="btn btn-secondary">Middle</button>
              <button type="button" class="btn btn-secondary">Right</button>
            </div>
          </div>

          <div class="bs-component" style="margin-bottom: 15px;">
            <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
              <div class="btn-group mr-2" role="group" aria-label="First group">
                <button type="button" class="btn btn-secondary">1</button>
                <button type="button" class="btn btn-secondary">2</button>
                <button type="button" class="btn btn-secondary">3</button>
                <button type="button" class="btn btn-secondary">4</button>
              </div>
              <div class="btn-group mr-2" role="group" aria-label="Second group">
                <button type="button" class="btn btn-secondary">5</button>
                <button type="button" class="btn btn-secondary">6</button>
                <button type="button" class="btn btn-secondary">7</button>
              </div>
              <div class="btn-group" role="group" aria-label="Third group">
                <button type="button" class="btn btn-secondary">8</button>
              </div>
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
            <h3>
              Heading
              <small class="text-muted">with muted text</small>
            </h3>
            <p class="lead">Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="bs-component">
            <h2>Example body text</h2>
            <p>Nullam quis risus eget <a href="#">urna mollis ornare</a> vel eu leo. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nullam id dolor id nibh ultricies vehicula.</p>
            <p><small>This line of text is meant to be treated as fine print.</small></p>
            <p>The following is <strong>rendered as bold text</strong>.</p>
            <p>The following is <em>rendered as italicized text</em>.</p>
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
      </div>

      <!-- Blockquotes -->

      <div class="row">
        <div class="col-lg-12">
          <h2 id="type-blockquotes">Blockquotes</h2>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-4">
          <div class="bs-component">
            <blockquote class="blockquote">
              <p class="mb-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.</p>
              <footer class="blockquote-footer">Someone famous in <cite title="Source Title">Source Title</cite></footer>
            </blockquote>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="bs-component">
            <blockquote class="blockquote text-center">
              <p class="mb-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.</p>
              <footer class="blockquote-footer">Someone famous in <cite title="Source Title">Source Title</cite></footer>
            </blockquote>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="bs-component">
            <blockquote class="blockquote text-right">
              <p class="mb-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.</p>
              <footer class="blockquote-footer">Someone famous in <cite title="Source Title">Source Title</cite></footer>
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
            <table class="table table-hover">
              <thead>
                <tr>
                  <th scope="col">Type</th>
                  <th scope="col">Column heading</th>
                  <th scope="col">Column heading</th>
                  <th scope="col">Column heading</th>
                </tr>
              </thead>
              <tbody>
                <tr class="table-active">
                  <th scope="row">Active</th>
                  <td>Column content</td>
                  <td>Column content</td>
                  <td>Column content</td>
                </tr>
                <tr>
                  <th scope="row">Default</th>
                  <td>Column content</td>
                  <td>Column content</td>
                  <td>Column content</td>
                </tr>
                <tr class="table-primary">
                  <th scope="row">Primary</th>
                  <td>Column content</td>
                  <td>Column content</td>
                  <td>Column content</td>
                </tr>
                <tr class="table-secondary">
                  <th scope="row">Secondary</th>
                  <td>Column content</td>
                  <td>Column content</td>
                  <td>Column content</td>
                </tr>
                <tr class="table-success">
                  <th scope="row">Success</th>
                  <td>Column content</td>
                  <td>Column content</td>
                  <td>Column content</td>
                </tr>
                <tr class="table-danger">
                  <th scope="row">Danger</th>
                  <td>Column content</td>
                  <td>Column content</td>
                  <td>Column content</td>
                </tr>
                <tr class="table-warning">
                  <th scope="row">Warning</th>
                  <td>Column content</td>
                  <td>Column content</td>
                  <td>Column content</td>
                </tr>
                <tr class="table-info">
                  <th scope="row">Info</th>
                  <td>Column content</td>
                  <td>Column content</td>
                  <td>Column content</td>
                </tr>
                <tr class="table-light">
                  <th scope="row">Light</th>
                  <td>Column content</td>
                  <td>Column content</td>
                  <td>Column content</td>
                </tr>
                <tr class="table-dark">
                  <th scope="row">Dark</th>
                  <td>Column content</td>
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
          <div class="bs-component">
            <form>
              <fieldset>
                <legend>Legend</legend>
                <div class="form-group row">
                  <label for="staticEmail" class="col-sm-2 col-form-label">Email</label>
                  <div class="col-sm-10">
                    <input type="text" readonly class="form-control-plaintext" id="staticEmail" value="email@example.com">
                  </div>
                </div>
                <div class="form-group">
                  <label for="exampleInputEmail1">Email address</label>
                  <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
                  <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                </div>
                <div class="form-group">
                  <label for="exampleInputPassword1">Password</label>
                  <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
                </div>
                <div class="form-group">
                  <label for="exampleSelect1">Example select</label>
                  <select class="form-control" id="exampleSelect1">
                    <option>1</option>
                    <option>2</option>
                    <option>3</option>
                    <option>4</option>
                    <option>5</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="exampleSelect2">Example multiple select</label>
                  <select multiple class="form-control" id="exampleSelect2">
                    <option>1</option>
                    <option>2</option>
                    <option>3</option>
                    <option>4</option>
                    <option>5</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="exampleTextarea">Example textarea</label>
                  <textarea class="form-control" id="exampleTextarea" rows="3"></textarea>
                </div>
                <div class="form-group">
                  <label for="exampleInputFile">File input</label>
                  <input type="file" class="form-control-file" id="exampleInputFile" aria-describedby="fileHelp">
                  <small id="fileHelp" class="form-text text-muted">This is some placeholder block-level help text for the above input. It's a bit lighter and easily wraps to a new line.</small>
                </div>
                <fieldset class="form-group">
                  <legend>Radio buttons</legend>
                  <div class="form-check">
                    <label class="form-check-label">
                      <input type="radio" class="form-check-input" name="optionsRadios" id="optionsRadios1" value="option1" checked>
                      Option one is this and that&mdash;be sure to include why it's great
                    </label>
                  </div>
                  <div class="form-check">
                  <label class="form-check-label">
                      <input type="radio" class="form-check-input" name="optionsRadios" id="optionsRadios2" value="option2">
                      Option two can be something else and selecting it will deselect option one
                    </label>
                  </div>
                  <div class="form-check disabled">
                  <label class="form-check-label">
                      <input type="radio" class="form-check-input" name="optionsRadios" id="optionsRadios3" value="option3" disabled>
                      Option three is disabled
                    </label>
                  </div>
                </fieldset>
                <fieldset class="form-group">
                  <legend>Checkboxes</legend>
                  <div class="form-check">
                    <label class="form-check-label">
                      <input class="form-check-input" type="checkbox" value="" checked>
                      Option one is this and that&mdash;be sure to include why it's great
                    </label>
                  </div>
                  <div class="form-check disabled">
                    <label class="form-check-label">
                      <input class="form-check-input" type="checkbox" value="" disabled>
                      Option two is disabled
                    </label>
                  </div>
                </fieldset>
                <button type="submit" class="btn btn-primary">Submit</button>
              </fieldset>
            </form>
          </div>
        </div>
        <div class="col-lg-4 offset-lg-1">

          <form class="bs-component">
            <div class="form-group">
              <fieldset disabled>
                <label class="control-label" for="disabledInput">Disabled input</label>
                <input class="form-control" id="disabledInput" type="text" placeholder="Disabled input here..." disabled="">
              </fieldset>
            </div>

            <div class="form-group">
              <fieldset>
                <label class="control-label" for="readOnlyInput">Readonly input</label>
                <input class="form-control" id="readOnlyInput" type="text" placeholder="Readonly input here…" readonly>
              </fieldset>
            </div>

            <div class="form-group has-success">
              <label class="form-control-label" for="inputSuccess1">Valid input</label>
              <input type="text" value="correct value" class="form-control is-valid" id="inputValid">
              <div class="valid-feedback">Success! You've done it.</div>
            </div>

            <div class="form-group has-danger">
              <label class="form-control-label" for="inputDanger1">Invalid input</label>
              <input type="text" value="wrong value" class="form-control is-invalid" id="inputInvalid">
              <div class="invalid-feedback">Sorry, that username's taken. Try another?</div>
            </div>

            <div class="form-group">
              <label class="col-form-label col-form-label-lg" for="inputLarge">Large input</label>
              <input class="form-control form-control-lg" type="text" placeholder=".form-control-lg" id="inputLarge">
            </div>

            <div class="form-group">
              <label class="col-form-label" for="inputDefault">Default input</label>
              <input type="text" class="form-control" placeholder="Default input" id="inputDefault">
            </div>

            <div class="form-group">
              <label class="col-form-label col-form-label-sm" for="inputSmall">Small input</label>
              <input class="form-control form-control-sm" type="text" placeholder=".form-control-sm" id="inputSmall">
            </div>

            <div class="form-group">
              <label class="control-label">Input addons</label>
              <div class="form-group">
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                  </div>
                  <input type="text" class="form-control" aria-label="Amount (to the nearest dollar)">
                  <div class="input-group-append">
                    <span class="input-group-text">.00</span>
                  </div>
                </div>
              </div>
            </div>
          </form>

          <div class="bs-component">
            <fieldset>
              <legend>Custom forms</legend>
              <div class="form-group">
                <div class="custom-control custom-radio">
                  <input type="radio" id="customRadio1" name="customRadio" class="custom-control-input" checked>
                  <label class="custom-control-label" for="customRadio1">Toggle this custom radio</label>
                </div>
                <div class="custom-control custom-radio">
                  <input type="radio" id="customRadio2" name="customRadio" class="custom-control-input">
                  <label class="custom-control-label" for="customRadio2">Or toggle this other custom radio</label>
                </div>
                <div class="custom-control custom-radio">
                  <input type="radio" id="customRadio3" name="customRadio" class="custom-control-input" disabled>
                  <label class="custom-control-label" for="customRadio3">Disabled custom radio</label>
                </div>
              </div>
              <div class="form-group">
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input" id="customCheck1" checked>
                  <label class="custom-control-label" for="customCheck1">Check this custom checkbox</label>
                </div>
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input" id="customCheck2" disabled>
                  <label class="custom-control-label" for="customCheck2">Disabled custom checkbox</label>
                </div>
              </div>
              <div class="form-group">
                <select class="custom-select">
                  <option selected>Open this select menu</option>
                  <option value="1">One</option>
                  <option value="2">Two</option>
                  <option value="3">Three</option>
                </select>
              </div>
              <div class="form-group">
                <div class="input-group mb-3">
                  <div class="custom-file">
                    <input type="file" class="custom-file-input" id="inputGroupFile02">
                    <label class="custom-file-label" for="inputGroupFile02">Choose file</label>
                  </div>
                  <div class="input-group-append">
                    <span class="input-group-text" id="">Upload</span>
                  </div>
                </div>
              </div>
            </fieldset>
          </div>

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

      <div class="row" style="margin-bottom: 2rem;">
        <div class="col-lg-6">
          <h2 id="nav-tabs">Tabs</h2>
          <div class="bs-component">
            <ul class="nav nav-tabs">
              <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#home">Home</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#profile">Profile</a>
              </li>
              <li class="nav-item">
                <a class="nav-link disabled" href="#">Disabled</a>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Dropdown</a>
                <div class="dropdown-menu">
                  <a class="dropdown-item" href="#">Action</a>
                  <a class="dropdown-item" href="#">Another action</a>
                  <a class="dropdown-item" href="#">Something else here</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="#">Separated link</a>
                </div>
              </li>
            </ul>
            <div id="myTabContent" class="tab-content">
              <div class="tab-pane fade show active" id="home">
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

        <div class="col-lg-6">
          <h2 id="nav-pills">Pills</h2>
          <div class="bs-component">
            <ul class="nav nav-pills">
              <li class="nav-item">
                <a class="nav-link active" href="#">Active</a>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Dropdown</a>
                <div class="dropdown-menu">
                  <a class="dropdown-item" href="#">Action</a>
                  <a class="dropdown-item" href="#">Another action</a>
                  <a class="dropdown-item" href="#">Something else here</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="#">Separated link</a>
                </div>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">Link</a>
              </li>
              <li class="nav-item">
                <a class="nav-link disabled" href="#">Disabled</a>
              </li>
            </ul>
          </div>
          <br>
          <div class="bs-component">
            <ul class="nav nav-pills flex-column">
              <li class="nav-item">
                <a class="nav-link active" href="#">Active</a>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Dropdown</a>
                <div class="dropdown-menu">
                  <a class="dropdown-item" href="#">Action</a>
                  <a class="dropdown-item" href="#">Another action</a>
                  <a class="dropdown-item" href="#">Something else here</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="#">Separated link</a>
                </div>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">Link</a>
              </li>
              <li class="nav-item">
                <a class="nav-link disabled" href="#">Disabled</a>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-6">
          <h2 id="nav-breadcrumbs">Breadcrumbs</h2>
          <div class="bs-component">
            <ol class="breadcrumb">
              <li class="breadcrumb-item active">Home</li>
            </ol>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Library</li>
            </ol>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item"><a href="#">Library</a></li>
              <li class="breadcrumb-item active">Data</li>
            </ol>
          </div>
        </div>

        <div class="col-lg-6">
          <h2 id="pagination">Pagination</h2>
          <div class="bs-component">
            <div>
              <ul class="pagination">
                <li class="page-item disabled">
                  <a class="page-link" href="#">&laquo;</a>
                </li>
                <li class="page-item active">
                  <a class="page-link" href="#">1</a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="#">2</a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="#">3</a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="#">4</a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="#">5</a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="#">&raquo;</a>
                </li>
              </ul>
            </div>

            <div>
              <ul class="pagination pagination-lg">
                <li class="page-item disabled">
                  <a class="page-link" href="#">&laquo;</a>
                </li>
                <li class="page-item active">
                  <a class="page-link" href="#">1</a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="#">2</a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="#">3</a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="#">4</a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="#">5</a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="#">&raquo;</a>
                </li>
              </ul>
            </div>

            <div>
              <ul class="pagination pagination-sm">
                <li class="page-item disabled">
                  <a class="page-link" href="#">&laquo;</a>
                </li>
                <li class="page-item active">
                  <a class="page-link" href="#">1</a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="#">2</a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="#">3</a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="#">4</a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="#">5</a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="#">&raquo;</a>
                </li>
              </ul>
            </div>

          </div>
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
              <h4 class="alert-heading">Warning!</h4>
              <p class="mb-0">Best check yo self, you're not looking too good. Nulla vitae elit libero, a pharetra augue. Praesent commodo cursus magna, <a href="#" class="alert-link">vel scelerisque nisl consectetur et</a>.</p>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-4">
          <div class="bs-component">
            <div class="alert alert-dismissible alert-danger">
              <button type="button" class="close" data-dismiss="alert">&times;</button>
              <strong>Oh snap!</strong> <a href="#" class="alert-link">Change a few things up</a> and try submitting again.
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="bs-component">
            <div class="alert alert-dismissible alert-success">
              <button type="button" class="close" data-dismiss="alert">&times;</button>
              <strong>Well done!</strong> You successfully read <a href="#" class="alert-link">this important alert message</a>.
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="bs-component">
            <div class="alert alert-dismissible alert-info">
              <button type="button" class="close" data-dismiss="alert">&times;</button>
              <strong>Heads up!</strong> This <a href="#" class="alert-link">alert needs your attention</a>, but it's not super important.
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-4">
          <div class="bs-component">
            <div class="alert alert-dismissible alert-primary">
              <button type="button" class="close" data-dismiss="alert">&times;</button>
              <strong>Oh snap!</strong> <a href="#" class="alert-link">Change a few things up</a> and try submitting again.
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="bs-component">
            <div class="alert alert-dismissible alert-secondary">
              <button type="button" class="close" data-dismiss="alert">&times;</button>
              <strong>Well done!</strong> You successfully read <a href="#" class="alert-link">this important alert message</a>.
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="bs-component">
            <div class="alert alert-dismissible alert-light">
              <button type="button" class="close" data-dismiss="alert">&times;</button>
              <strong>Heads up!</strong> This <a href="#" class="alert-link">alert needs your attention</a>, but it's not super important.
            </div>
          </div>
        </div>
      </div>
      <div>
        <h2>Badges</h2>
        <div class="bs-component" style="margin-bottom: 40px;">
          <span class="badge badge-primary">Primary</span>
          <span class="badge badge-secondary">Secondary</span>
          <span class="badge badge-success">Success</span>
          <span class="badge badge-danger">Danger</span>
          <span class="badge badge-warning">Warning</span>
          <span class="badge badge-info">Info</span>
          <span class="badge badge-light">Light</span>
          <span class="badge badge-dark">Dark</span>
        </div>
        <div class="bs-component">
          <span class="badge badge-pill badge-primary">Primary</span>
          <span class="badge badge-pill badge-secondary">Secondary</span>
          <span class="badge badge-pill badge-success">Success</span>
          <span class="badge badge-pill badge-danger">Danger</span>
          <span class="badge badge-pill badge-warning">Warning</span>
          <span class="badge badge-pill badge-info">Info</span>
          <span class="badge badge-pill badge-light">Light</span>
          <span class="badge badge-pill badge-dark">Dark</span>
        </div>
      </div>
    </div>

    <!-- Progress
    ================================================== -->
    <div class="bs-docs-section">

      <div class="row">
        <div class="col-lg-12">
          <div class="page-header">
            <h1 id="progress">Progress</h1>
          </div>

          <h3 id="progress-basic">Basic</h3>
          <div class="bs-component">
            <div class="progress">
              <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
          </div>

          <h3 id="progress-alternatives">Contextual alternatives</h3>
          <div class="bs-component">
            <div class="progress">
              <div class="progress-bar bg-success" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="progress">
              <div class="progress-bar bg-info" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="progress">
              <div class="progress-bar bg-warning" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="progress">
              <div class="progress-bar bg-danger" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
          </div>

          <h3 id="progress-multiple">Multiple bars</h3>
          <div class="bs-component">
            <div class="progress">
              <div class="progress-bar" role="progressbar" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
              <div class="progress-bar bg-success" role="progressbar" style="width: 30%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
              <div class="progress-bar bg-info" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
          </div>

          <h3 id="progress-striped">Striped</h3>
          <div class="bs-component">
            <div class="progress">
              <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 10%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="progress">
              <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="progress">
              <div class="progress-bar progress-bar-striped bg-info" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="progress">
              <div class="progress-bar progress-bar-striped bg-warning" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="progress">
              <div class="progress-bar progress-bar-striped bg-danger" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
          </div>

          <h3 id="progress-animated">Animated</h3>
          <div class="bs-component">
            <div class="progress">
              <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%"></div>
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
              <h1 class="display-3">Hello, world!</h1>
              <p class="lead">This is a simple hero unit, a simple jumbotron-style component for calling extra attention to featured content or information.</p>
              <hr class="my-4">
              <p>It uses utility classes for typography and spacing to space content out within the larger container.</p>
              <p class="lead">
                <a class="btn btn-primary btn-lg" href="#" role="button">Learn more</a>
              </p>
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
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Cras justo odio
                <span class="badge badge-primary badge-pill">14</span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Dapibus ac facilisis in
                <span class="badge badge-primary badge-pill">2</span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Morbi leo risus
                <span class="badge badge-primary badge-pill">1</span>
              </li>
            </ul>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="bs-component">
            <div class="list-group">
              <a href="#" class="list-group-item list-group-item-action active">
                Cras justo odio
              </a>
              <a href="#" class="list-group-item list-group-item-action">Dapibus ac facilisis in
              </a>
              <a href="#" class="list-group-item list-group-item-action disabled">Morbi leo risus
              </a>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="bs-component">
            <div class="list-group">
              <a href="#" class="list-group-item list-group-item-action flex-column align-items-start active">
                <div class="d-flex w-100 justify-content-between">
                  <h5 class="mb-1">List group item heading</h5>
                  <small>3 days ago</small>
                </div>
                <p class="mb-1">Donec id elit non mi porta gravida at eget metus. Maecenas sed diam eget risus varius blandit.</p>
                <small>Donec id elit non mi porta.</small>
              </a>
              <a href="#" class="list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-between">
                  <h5 class="mb-1">List group item heading</h5>
                  <small class="text-muted">3 days ago</small>
                </div>
                <p class="mb-1">Donec id elit non mi porta gravida at eget metus. Maecenas sed diam eget risus varius blandit.</p>
                <small class="text-muted">Donec id elit non mi porta.</small>
              </a>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-12">
          <h2>Cards</h2>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-4">
          <div class="bs-component">
            <div class="card text-white bg-primary mb-3" style="max-width: 20rem;">
              <div class="card-header">Header</div>
              <div class="card-body">
                <h4 class="card-title">Primary card title</h4>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
              </div>
            </div>
            <div class="card bg-secondary mb-3" style="max-width: 20rem;">
              <div class="card-header">Header</div>
              <div class="card-body">
                <h4 class="card-title">Secondary card title</h4>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
              </div>
            </div>
            <div class="card text-white bg-success mb-3" style="max-width: 20rem;">
              <div class="card-header">Header</div>
              <div class="card-body">
                <h4 class="card-title">Success card title</h4>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
              </div>
            </div>
            <div class="card text-white bg-danger mb-3" style="max-width: 20rem;">
              <div class="card-header">Header</div>
              <div class="card-body">
                <h4 class="card-title">Danger card title</h4>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
              </div>
            </div>
            <div class="card text-white bg-warning mb-3" style="max-width: 20rem;">
              <div class="card-header">Header</div>
              <div class="card-body">
                <h4 class="card-title">Warning card title</h4>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
              </div>
            </div>
            <div class="card text-white bg-info mb-3" style="max-width: 20rem;">
              <div class="card-header">Header</div>
              <div class="card-body">
                <h4 class="card-title">Info card title</h4>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
              </div>
            </div>
            <div class="card bg-light mb-3" style="max-width: 20rem;">
              <div class="card-header">Header</div>
              <div class="card-body">
                <h4 class="card-title">Light card title</h4>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
              </div>
            </div>
            <div class="card text-white bg-dark mb-3" style="max-width: 20rem;">
              <div class="card-header">Header</div>
              <div class="card-body">
                <h4 class="card-title">Dark card title</h4>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="bs-component">
            <div class="card border-primary mb-3" style="max-width: 20rem;">
              <div class="card-header">Header</div>
              <div class="card-body">
                <h4 class="card-title">Primary card title</h4>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
              </div>
            </div>
            <div class="card border-secondary mb-3" style="max-width: 20rem;">
              <div class="card-header">Header</div>
              <div class="card-body">
                <h4 class="card-title">Secondary card title</h4>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
              </div>
            </div>
            <div class="card border-success mb-3" style="max-width: 20rem;">
              <div class="card-header">Header</div>
              <div class="card-body">
                <h4 class="card-title">Success card title</h4>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
              </div>
            </div>
            <div class="card border-danger mb-3" style="max-width: 20rem;">
              <div class="card-header">Header</div>
              <div class="card-body">
                <h4 class="card-title">Danger card title</h4>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
              </div>
            </div>
            <div class="card border-warning mb-3" style="max-width: 20rem;">
              <div class="card-header">Header</div>
              <div class="card-body">
                <h4 class="card-title">Warning card title</h4>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
              </div>
            </div>
            <div class="card border-info mb-3" style="max-width: 20rem;">
              <div class="card-header">Header</div>
              <div class="card-body">
                <h4 class="card-title">Info card title</h4>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
              </div>
            </div>
            <div class="card border-light mb-3" style="max-width: 20rem;">
              <div class="card-header">Header</div>
              <div class="card-body">
                <h4 class="card-title">Light card title</h4>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
              </div>
            </div>
            <div class="card border-dark mb-3" style="max-width: 20rem;">
              <div class="card-header">Header</div>
              <div class="card-body">
                <h4 class="card-title">Dark card title</h4>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="bs-component">
            <div class="card mb-3">
              <h3 class="card-header">Card header</h3>
              <div class="card-body">
                <h5 class="card-title">Special title treatment</h5>
                <h6 class="card-subtitle text-muted">Support card subtitle</h6>
              </div>
              <img style="height: 200px; width: 100%; display: block;" src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22318%22%20height%3D%22180%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20318%20180%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_158bd1d28ef%20text%20%7B%20fill%3Argba(255%2C255%2C255%2C.75)%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A16pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_158bd1d28ef%22%3E%3Crect%20width%3D%22318%22%20height%3D%22180%22%20fill%3D%22%23777%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%22129.359375%22%20y%3D%2297.35%22%3EImage%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E" alt="Card image">
              <div class="card-body">
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
              </div>
              <ul class="list-group list-group-flush">
                <li class="list-group-item">Cras justo odio</li>
                <li class="list-group-item">Dapibus ac facilisis in</li>
                <li class="list-group-item">Vestibulum at eros</li>
              </ul>
              <div class="card-body">
                <a href="#" class="card-link">Card link</a>
                <a href="#" class="card-link">Another link</a>
              </div>
              <div class="card-footer text-muted">
                2 days ago
              </div>
            </div>
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Card title</h4>
                <h6 class="card-subtitle mb-2 text-muted">Card subtitle</h6>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                <a href="#" class="card-link">Card link</a>
                <a href="#" class="card-link">Another link</a>
              </div>
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
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <p>Modal body text goes here.</p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary">Save changes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <h2>Popovers</h2>
          <div class="bs-component" style="margin-bottom: 3em;">
            <button type="button" class="btn btn-secondary" title="Popover Title" data-container="body" data-toggle="popover" data-placement="left" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus.">Left</button>

            <button type="button" class="btn btn-secondary" title="Popover Title" data-container="body" data-toggle="popover" data-placement="top" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus.">Top</button>

            <button type="button" class="btn btn-secondary" title="Popover Title" data-container="body" data-toggle="popover" data-placement="bottom" data-content="Vivamus
            sagittis lacus vel augue laoreet rutrum faucibus.">Bottom</button>

            <button type="button" class="btn btn-secondary" title="Popover Title" data-container="body" data-toggle="popover" data-placement="right" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus.">Right</button>
          </div>
          <h2>Tooltips</h2>
          <div class="bs-component">
            <button type="button" class="btn btn-secondary" data-toggle="tooltip" data-placement="left" title="Tooltip on left">Left</button>

            <button type="button" class="btn btn-secondary" data-toggle="tooltip" data-placement="top" title="Tooltip on top">Top</button>

            <button type="button" class="btn btn-secondary" data-toggle="tooltip" data-placement="bottom" title="Tooltip on bottom">Bottom</button>

            <button type="button" class="btn btn-secondary" data-toggle="tooltip" data-placement="right" title="Tooltip on right">Right</button>
          </div>
        </div>
      </div>
    </div>

    <div id="source-modal" class="modal fade">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Source Code</h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          </div>
          <div class="modal-body">
            <pre contenteditable></pre>
          </div>
        </div>
      </div>
    </div>

</div>
{script unique="bootstrap" bootstrap="alert,button,carousel,collapse,dropdown,modal,tooltip,popover,scrollspy,tab"}
{literal}
    (function(){
      $(window).scroll(function () {
          var top = $(document).scrollTop();
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
