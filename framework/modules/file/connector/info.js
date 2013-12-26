/*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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
 */

"use strict";
/**
 * @class elFinder command "info" updated for Exponent CMS
 * Display dialog with file properties.
 *
 * @author Dmitry (dio) Levashov, dio@std42.ru
 **/
elFinder.prototype.commands.info = function () {
    var m = 'msg',
        fm = this.fm,
        spclass = 'elfinder-info-spinner',
        msg = {
            calc : fm.i18n('calc') ,
            size : fm.i18n('size') ,
            unknown : fm.i18n('unknown') ,
            path : fm.i18n('path') ,
            aliasfor : fm.i18n('aliasfor') ,
            modify : fm.i18n('modify') ,
            perms : fm.i18n('perms') ,
            locked : fm.i18n('locked') ,
            dim : fm.i18n('dim') ,
            kind : fm.i18n('kind') ,
            files : fm.i18n('files') ,
            folders : fm.i18n('folders') ,
            items : fm.i18n('items') ,
            yes : fm.i18n('yes') ,
            no : fm.i18n('no') ,
            link : fm.i18n('link') ,
            owner : fm.i18n('owner') ,
            shared : fm.i18n('shared') ,
            title : fm.i18n('title') ,
            alt : fm.i18n('alt')
        };

    this.tpl = {
        main : '<div class="ui-helper-clearfix elfinder-info-title"><span class="elfinder-cwd-icon {class} ui-corner-all"/>{title}</div><table class="elfinder-info-tb">{content}</table>' ,
        itemTitle : '<strong>{name}</strong><span class="elfinder-info-kind">{kind}</span>' ,
        groupTitle : '<strong>{items}: {num}</strong>' ,
        row : '<tr><td>{label} : </td><td>{value}</td></tr>' ,
        spinner : '<span>{text}</span> <span class="' + spclass + ' ' + spclass + '-' + '{id}"/>'
    }

    this.alwaysEnabled = true;
    this.updateOnSelect = false;
    this.shortcuts = [
        {
            pattern : 'ctrl+i'
        }
    ];

    this.init = function () {
        $.each(msg , function (k , v) {
            msg[k] = fm.i18n(v);
        });
    }

    this.getstate = function () {
        return 0;
    }

    this.exec = function (hashes) {
        var files = this.files(hashes);
        if (!files.length) {
            files = this.files([ this.fm.cwd().hash ]);
        }
        var self = this,
            fm = this.fm,
            tpl = this.tpl,
            row = tpl.row,
            cnt = files.length,
            content = [],
            view = tpl.main,
            l = '{label}',
            v = '{value}',
            opts = {
                title : this.title ,
                width : 'auto' ,
                modal : true ,
                close : function () {
                    $(this).elfinderdialog('destroy');
                }
            },
            count = [],
            replSpinner = function (msg) {
                dialog.find('.' + spclass).parent().text(msg);
            },
            replSpinnerById = function (msg , id) {
                dialog.find('.' + spclass + '-' + id).parent().html(msg);
            },
            id = fm.namespace + '-info-' + $.map(files ,function (f) {
                return f.hash
            }).join('-'),
            dialog = fm.getUI().find('#' + id),
            size, tmb, file, title, dcnt;

        if (!cnt) {
            return $.Deferred().reject();
        }

        if (dialog.length) {
            dialog.elfinderdialog('toTop');
            return $.Deferred().resolve();
        }

        if (cnt == 1) {
            file = files[0];

            view = view.replace('{class}' , fm.mime2class(file.mime));
            title = tpl.itemTitle.replace('{name}' , fm.escape(file.i18 || file.name)).replace('{kind}' , fm.mime2kind(file));

            if (file.tmb) {
                tmb = fm.option('tmbUrl') + file.tmb;
            }

            if (!file.read) {
                size = msg.unknown;
            } else if (file.mime != 'directory' || file.alias) {
                size = fm.formatSize(file.size);
            } else {
                /* adding spinner id to separate field updates */
                size = tpl.spinner.replace('{text}' , msg.calc).replace('{id}' , 'size');
                count.push(file.hash);
            }

            content.push(row.replace(l , msg.size).replace(v , size));
            file.alias && content.push(row.replace(l , msg.aliasfor).replace(v , file.alias));
            content.push(row.replace(l , msg.path).replace(v , fm.escape(fm.path(file.hash , true))));
            file.read && content.push(row.replace(l , msg.link).replace(v , '<a href="' + fm.url(file.hash) + '" target="_blank">' + file.name + '</a>'));

            if (file.dim) { // old api
                content.push(row.replace(l , msg.dim).replace(v , file.dim));
            } else if (file.mime.indexOf('image') !== -1) {
                if (file.width && file.height) {
                    content.push(row.replace(l , msg.dim).replace(v , file.width + 'x' + file.height));
                } else {
                    content.push(row.replace(l , msg.dim).replace(v , tpl.spinner.replace('{text}' , msg.calc).replace('{id}' , 'dim')));
                    fm.request({
                        data : {cmd : 'dim' , target : file.hash} ,
                        preventDefault : true
                    })
                        .fail(function () {
//                            replSpinner(msg.unknown);
                            replSpinnerById(msg.unknown , 'dim');
                        })
                        .done(function (data) {
//                            replSpinner(data.dim || msg.unknown);
                            replSpinnerById(data.dim || msg.unknown , 'dim');
                            if (data.dim) {
                                var dim = data.dim.split('x');
                                var rfile = fm.file(file.hash);
                                rfile.width = dim[0];
                                rfile.height = dim[1];
                            }
                        });
                }
            }

            content.push(row.replace(l , msg.modify).replace(v , fm.formatDate(file)));
            content.push(row.replace(l , msg.perms).replace(v , fm.formatPermissions(file)));
            content.push(row.replace(l , msg.locked).replace(v , file.locked ? msg.yes : msg.no));

            // Exponent specific attributes
            var editDesc = true;
            if (file.mime != 'directory') {
                content.push(row.replace(l , 'Owner').replace(v , file.owner));

                var shared = '';
                if (file.shared) shared = ' checked="true"';
                var disableshared = '';
                if (!editDesc) disableshared = ' disabled="true"';
                var fileshared = '<input type="checkbox" id="elfinder-fm-file-shared" class="ui-widget ui-widget-content" title="Change the file\'s Shared status" value="1"' + shared + disableshared + '" />';
                content.push(row.replace(l , 'Shared').replace(v , fileshared));
            }

            if (file.mime.indexOf('image') !== -1) {
                var filetitle = '<div id="elfinder-fm-file-title">' + file.title + '</div>';
                var filealt = '<div id="elfinder-fm-file-alt">' + file.alt + '</div>';
                if (editDesc) {
                    filetitle = '<input size="23" id="elfinder-fm-file-title" class="ui-widget ui-widget-content" value="' + file.title + '" /> <input type="button" id="elfinder-fm-file-title-btn-save" title="Update the file\'s Title" value="' + 'Update' + '" />';
                    filealt = '<textarea cols="20" rows="2" id="elfinder-fm-file-alt" class="ui-widget ui-widget-content">' + file.alt + '</textarea> <input type="button" id="elfinder-fm-file-alt-btn-save" title="Update the file\'s Alt" value="' + 'Update' + '" />';
                }
                content.push(row.replace(l , 'Title').replace(v , filetitle));
                content.push(row.replace(l , 'Alt').replace(v , filealt));
            }

        } else {
            view = view.replace('{class}' , 'elfinder-cwd-icon-group');
            title = tpl.groupTitle.replace('{items}' , msg.items).replace('{num}' , cnt);
            dcnt = $.map(files ,function (f) {
                return f.mime == 'directory' ? 1 : null
            }).length;
            if (!dcnt) {
                size = 0;
                $.each(files , function (h , f) {
                    var s = parseInt(f.size);

                    if (s >= 0 && size >= 0) {
                        size += s;
                    } else {
                        size = 'unknown';
                    }
                });
                content.push(row.replace(l , msg.kind).replace(v , msg.files));
                content.push(row.replace(l , msg.size).replace(v , fm.formatSize(size)));
            } else {
                content.push(row.replace(l , msg.kind).replace(v , dcnt == cnt ? msg.folders : msg.folders + ' ' + dcnt + ', ' + msg.files + ' ' + (cnt - dcnt)))
                content.push(row.replace(l , msg.size).replace(v , tpl.spinner.replace('{text}' , msg.calc)));
                count = $.map(files , function (f) {
                    return f.hash
                });

            }
        }

        view = view.replace('{title}' , title).replace('{content}' , content.join(''));

        dialog = fm.dialog(view , opts);
        dialog.attr('id' , id)

        // Exponent specific commands
        if (editDesc) {
            var checkShared = $('#elfinder-fm-file-shared' , dialog);
            checkShared.click(function () {
                fm.lockfiles({files : [file.hash]});
                fm.request({
                    data : {
                        cmd : 'shared' ,
                        target : file.hash ,
                        content : checkShared[0].checked
                    } ,
//                    notify : {
//                        type : 'save' ,
//                        cnt : 1
//                    }
                })
                    .always(function () {
                        fm.unlockfiles({files : [file.hash]});
                        file.shared = checkShared[0].checked;
                    });
            });

            if (file.mime.indexOf('image') !== -1) {
                var inputTitle = $('#elfinder-fm-file-title' , dialog);
                var btnTitleSave = $('#elfinder-fm-file-title-btn-save' , dialog).button();
                var inputAlt = $('#elfinder-fm-file-alt' , dialog);
                var btnAltSave = $('#elfinder-fm-file-alt-btn-save' , dialog).button();

                btnTitleSave.click(function () {
                    fm.lockfiles({files : [file.hash]});
                    fm.request({
                        data : {
                            cmd : 'title' ,
                            target : file.hash ,
                            content : inputTitle.val()
                        } ,
                        //                    notify : {
                        //                        type : 'save' ,
                        //                        cnt : 1
                        //                    }
                    })
                        .always(function () {
                            fm.unlockfiles({files : [file.hash]})
                            file.title = inputTitle.val();
                        });
                });
                btnAltSave.click(function () {
                    fm.lockfiles({files : [file.hash]});
                    fm.request({
                        data : {
                            cmd : 'alt' ,
                            target : file.hash ,
                            content : inputAlt.val()
                        } ,
                        //                    notify : {
                        //                        type : 'save' ,
                        //                        cnt : 1
                        //                    }
                    })
                        .always(function () {
                            fm.unlockfiles({files : [file.hash]})
                            file.alt = inputAlt.val();
                        });
                });
            }
        }

        // load thumbnail
        if (tmb) {
            $('<img/>')
                .load(function () {
                    dialog.find('.elfinder-cwd-icon').css('background' , 'url("' + tmb + '") center center no-repeat');
                })
                .attr('src' , tmb);
        }

        // send request to count total size
        if (count.length) {
            fm.request({
                data : {cmd : 'size' , targets : count} ,
                preventDefault : true
            })
                .fail(function () {
//                    replSpinner(msg.unknown);
                    replSpinnerById(msg.unknown , 'size');
                })
                .done(function (data) {
                    var size = parseInt(data.size);
//                    replSpinner(size >= 0 ? fm.formatSize(size) : msg.unknown);
                    replSpinnerById(size >= 0 ? fm.formatSize(size) : msg.unknown , 'size');
                });
        }

    }

}
