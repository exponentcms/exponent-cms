{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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

{css unique="nav-tree"}
    .vakata-context, #jstree-marker {
        z-index:999;
    }
{/css}
<div class="module navigation manager-hierarchy">
    {clear}
    <blockquote>
        <strong>{'Drag and drop'|gettext}</strong> {'tree items re-order the site hierarchy (main menu).'|gettext}
        <ul>
            <li>{'Dropping an item on a name places it under that menu.'|gettext}</li>
            <li>{'Dropping an item between names places it next to that menu item.'|gettext}</li>
        </ul>
        <strong>{'Right click on a tree item'|gettext}</strong> {'for a context menu of options.'|gettext}
    </blockquote>
    {permissions}
        {if $user->isAdmin()}
               <div class="module-actions">
                   {icon class="add" action=edit_section parent='0' text='Create a New Top Level Page'|gettext}
               </div>
        {/if}
    {/permissions}
    {icon class=refresh action=scriptaction name='refresh-tree' text='Refresh'|gettext}
    {icon class=expand action=scriptaction name='expand-tree' text='Expand all'|gettext}
    {icon class=compress action=scriptaction name='collapse-tree' text='Collapse all'|gettext}
    {br}
    <div id="nav_jstree"></div>
</div>

{script unique="navtree" jquery="jstree,jquery-impromptu" bootstrap="modal,transition"}
{literal}
    $(document).ready(function(){
        var usr = {/literal}{obj2json obj=$user}{literal}; //user

        $('#nav_jstree').jstree({
            'core' : {
                'data' : {
                    'url' : eXp.PATH_RELATIVE+"index.php?ajax_action=1&module=navigation&action=returnChildrenAsJSON2&json=1",
                    'data' : function (node) {
                        return { 'id' : node.id };
                    }
                },
                'check_callback' : true,
                'themes' : {
                    'name': 'proton',
                    'responsive' : true,
                    'url' : true,
                    'dots' : false,
                    'variant' : 'small',
                },
                'strings' : {
                    'Loading ...' : '{/literal}{'Loading Pages'|gettext}{literal} ...'
                }
            },
            'contextmenu' : {
                'items' : {
                    "add" : {
                        "icon"				: 'fa fa-lg fa-fw fa-plus-circle text-success',
                        "_disabled"			: function (data) {
                            var inst = $.jstree.reference(data.reference),
                                obj = inst.get_node(data.reference);
                            if(inst.is_disabled(obj)) {
                                return true;
                            }
                        },
                        "label"				: "{/literal}{'Add a Sub-page here'|gettext}{literal}",
                        "action"			: false,
                        "submenu" : {
                            "add-content" : {
                                "icon"				: 'addpage',
                                "label"				: "Add Content Page",
                                "action"			: function (data) {
                                    var inst = $.jstree.reference(data.reference),
                                        obj = inst.get_node(data.reference);
                                    window.location=eXp.PATH_RELATIVE+"index.php?module=navigation&action=edit_contentpage&parent="+obj.id;
                                }
                            },
                            "add-external" : {
                                "icon"				: 'addextpage',
                                "label"				: "{/literal}{'Add External Web-site Link (Page) here'|gettext}{literal}",
                                "action"			: function (data) {
                                    var inst = $.jstree.reference(data.reference),
                                        obj = inst.get_node(data.reference);
                                    window.location=eXp.PATH_RELATIVE+"index.php?module=navigation&action=edit_externalalias&parent="+obj.id;
                                }
                            },
                            "add-alias" : {
                                "icon"				: 'addintpage',
                                "label"				: "{/literal}{'Add Page Alias (Page) here'|gettext}{literal}",
                                "action"			: function (data) {
                                    var inst = $.jstree.reference(data.reference),
                                        obj = inst.get_node(data.reference);
                                    window.location=eXp.PATH_RELATIVE+"index.php?module=navigation&action=edit_internalalias&parent="+obj.id;
                                }
                            },
                            "move-standalone" : {
                                "icon"				: 'addsapage',
                                "label"				: "{/literal}{'Move Stand-alone Page to here'|gettext}{literal}",
                                "action"			: function (data) {
                                    var inst = $.jstree.reference(data.reference),
                                        obj = inst.get_node(data.reference);
                                    window.location=eXp.PATH_RELATIVE+"index.php?module=navigation&action=move_standalone&parent="+obj.id;
                                }
                            }
                        }
                    },
    				"view" : {
                        "icon"				: 'fa fa-lg fa-fw fa-search',
    					"label"				: "{/literal}{'View this Page'|gettext}{literal}",
                        "action"			: function (data) {
                            var inst = $.jstree.reference(data.reference),
                                obj = inst.get_node(data.reference);
                            window.location=eXp.PATH_RELATIVE+"index.php?section="+obj.id;
                        }
    				},
    				"edit" : {
                        "icon"				: 'fa fa-lg fa-fw fa-edit',
                        "_disabled"			: function (data) {
                            var inst = $.jstree.reference(data.reference),
                                obj = inst.get_node(data.reference);
                            if(inst.is_disabled(obj)) {
                                return true;
                            }
                        },
    					"label"				: "{/literal}{'Edit this Page'|gettext}{literal}",
                        "action"			: function (data) {
                            var inst = $.jstree.reference(data.reference),
                                obj = inst.get_node(data.reference);
                            if (obj.original.alias_type==0){
                                window.location=eXp.PATH_RELATIVE+"index.php?module=navigation&action=edit_contentpage&id="+obj.id;
                            } else if (obj.original.alias_type==1){
                                window.location=eXp.PATH_RELATIVE+"index.php?module=navigation&action=edit_externalalias&id="+obj.id;
                            } else if (obj.original.alias_type==3){
                                window.location=eXp.PATH_RELATIVE+"index.php?module=navigation&action=edit_freeform&id="+obj.id;
                            } else {
                                window.location=eXp.PATH_RELATIVE+"index.php?module=navigation&action=edit_internalalias&id="+obj.id;
                            }
                        }
    				},
    				"remove" : {
    					"icon"				: 'fa fa-lg fa-fw fa-times-circle text-danger',
                        "separator_after"	: true,
                        "_disabled"			: function (data) {
                            var inst = $.jstree.reference(data.reference),
                                obj = inst.get_node(data.reference);
                            if(inst.is_disabled(obj)) {
                                return true;
                            }
                        },
    					"label"				: "{/literal}{'Delete this Page'|gettext}{literal}",
    					"action"			: function (data) {
    						var inst = $.jstree.reference(data.reference),
    							obj = inst.get_node(data.reference);
                            if (obj.original.alias_type==0){
                                var message = "{/literal}{"Deleting a content page moves it to the Standalone Page Manager, removing it from the Site Hierarchy. If there are any sub-pages to this section, those will also be moved"|gettext}{literal}";
                                var btn = {"{/literal}{'Move to Standalone'|gettext}{literal}": true, "No": false};
                            } else {
                                var message = "{/literal}{"Deleting an internal alias page or external link page permanently removes it from the system."|gettext}{literal}";
                                var btn = {"{/literal}{'Delete Page'|gettext}{literal}": true, "No": false};
                            }
                            $.prompt(message, {
                                title: "{/literal}{'Remove'|gettext}{literal} \""+obj.text+"\" {/literal}{'from hierarchy'|gettext}{literal}",
                                buttons: btn,
                                submit: function(e,v,m,f){
                                    // use e.preventDefault() to prevent closing when needed or return false.
                                    if (v) {
                                        if (obj.original.alias_type == 0)
                                            window.location=eXp.PATH_RELATIVE+"index.php?module=navigation&action=remove&id="+obj.id;
                                        else
                                            window.location=eXp.PATH_RELATIVE+"index.php?module=navigation&action=delete&id="+obj.id;
                                    }
                                }
                            });
    					}
    				},
                    "manage-user" : {
                        "separator_before"	: true,
                        "icon"				: 'fa fa-lg fa-fw fa-user',
                        "_disabled"			: function (data) {
                            var inst = $.jstree.reference(data.reference),
                                obj = inst.get_node(data.reference);
                            if(inst.is_disabled(obj)) {
                                return true;
                            } else if (!(usr.is_acting_admin==1 || usr.is_admin==1)) {
                                return true;
                            }
                        },
                        "label"				: "{/literal}{'Manage User Permissions'|gettext}{literal}",
                        "action"			: function (data) {
                            var inst = $.jstree.reference(data.reference),
                                obj = inst.get_node(data.reference);
                            window.location=eXp.PATH_RELATIVE+"index.php?controller=users&action=userperms&mod=navigation&int="+obj.id;
                        }
                    },
                    "manage-group" : {
                        "icon"				: 'fa fa-lg fa-fw fa-users',
                        "_disabled"			: function (data) {
                            var inst = $.jstree.reference(data.reference),
                                obj = inst.get_node(data.reference);
                            if(inst.is_disabled(obj)) {
                                return true;
                            } else if (!(usr.is_acting_admin==1 || usr.is_admin==1)) {
                                return true;
                            }
                        },
                        "label"				: "{/literal}{'Manage Group Permissions'|gettext}{literal}",
                        "action"			: function (data) {
                            var inst = $.jstree.reference(data.reference),
                                obj = inst.get_node(data.reference);
                            window.location=eXp.PATH_RELATIVE+"index.php?controller=users&action=groupperms&mod=navigation&int="+obj.id;
                        }
                    }
                }
            },
            'plugins' : ['dnd','contextmenu','state']
        }).on('move_node.jstree', function (e, data) {
            $.post(eXp.PATH_RELATIVE+"index.php?ajax_action=1&module=navigation&action=DragnDropReRank2", { 'id' : data.node.id, 'parent' : data.parent, 'position' : data.position })
                .fail(function () {
                    data.instance.refresh();
                });
        }).on('select_node.jstree', function (e, data) {  // selecting a node opens/closes it
            data.instance.toggle_node(data.node);
        });

        $('#refresh-tree').on('click', function(){
            $('#nav_jstree').jstree().refresh();
        });
        $('#expand-tree').on('click', function(){
            $('#nav_jstree').jstree().open_all();
        });
        $('#collapse-tree').on('click', function(){
            $('#nav_jstree').jstree().close_all();
        });
    })
{/literal}
{/script}
