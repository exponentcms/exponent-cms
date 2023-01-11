/*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
    config.allowedContent = true;
    config.protectedSource.push( /<i class[\s\S]*?\>/g );  // allow <i> opening tag
    config.protectedSource.push( /<\/i>/g );  // allow </i> closing tag

    /* CKEditor Bootstrap 3 Styles */
    if (CKEDITOR.stylesSet.get( 'default') === null) {
        CKEDITOR.stylesSet.add('default', [
            /* Typography */
            {
                name: "Paragraph Lead",
                element: "p",
                attributes: {
                    "class": "lead"
                }
            }
            ,
            /* Blockquotes */
            {
                name: "Blockquote",
                element: "blockquote",
            }
            ,
            {
                name: "Blockquote Reversed",
                element: "blockquote",
                attributes: {
                    "class": "blockquote-reverse"
                }
            }
            ,
            /* Lists */
            {
                name: "Unstyled List",
                element: "ul",
                attributes: {
                    "class": "list-unstyled"
                }
            }
            ,
            {
                name: "List inline",
                element: "ul",
                attributes: {
                    "class": "list-inline"
                }
            }
            ,
            /* Tables */
            {
                name: "Table",
                element: "table",
                attributes: {
                    "class": "table"
                }
            }
            ,
            {
                name: "Table Striped rows",
                element: "table",
                attributes: {
                    "class": "table table-striped"
                }
            }
            ,
            {
                name: "Table Bordered",
                element: "table",
                attributes: {
                    "class": "table table-bordered"
                }
            }
            ,
            {
                name: "Table Hover rows",
                element: "table",
                attributes: {
                    "class": "table table-hover"
                }
            }
            ,
            {
                name: "Table Condensed",
                element: "table",
                attributes: {
                    "class": "table table-condensed"
                }
            }
            ,
            /* Images */
            {
                name: "Image responsive",
                type: 'widget',
                widget: 'image',
                attributes: {
                    "class": "img-responsive"
                }
            }
            ,
            {
                name: "Image rounded shape",
                type: 'widget',
                widget: 'image',
                attributes: {
                    "class": "img-rounded"
                }
            }
            ,
            {
                name: "Image circle shape",
                type: 'widget',
                widget: 'image',
                attributes: {
                    "class": "img-circle"
                }
            }
            ,
            {
                name: "Image thumbnail shape",
                type: 'widget',
                widget: 'image',
                attributes: {
                    "class": "img-thumbnail"
                }
            },

            /* CKEditor Default Styles */
            {name: 'Marker', element: 'mark'},

            {name: 'Big', element: 'big'},
            {name: 'Small', element: 'small'},
            {name: 'Typewriter', element: 'tt'},

            {name: 'Computer Code', element: 'code'},
            {name: 'Keyboard Phrase', element: 'kbd'},
            {name: 'Sample Text', element: 'samp'},
            {name: 'Variable', element: 'var'},

            {name: 'Deleted Text', element: 'del'},
            {name: 'Inserted Text', element: 'ins'},

            {name: 'Cited Work', element: 'cite'},
            {name: 'Inline Quotation', element: 'q'},

            //{ name: 'Language: RTL',	element: 'span', attributes: { 'dir': 'rtl' } },
            //{ name: 'Language: LTR',	element: 'span', attributes: { 'dir': 'ltr' } },

            /* Object styles */

            {
                name: 'Styled image (left)',
                type: 'widget',
                widget: 'image',
                attributes: {'class': 'image-left'}
            },

            {
                name: 'Styled image (right)',
                type: 'widget',
                widget: 'image',
                attributes: {'class': 'image-right'}
            },

            {
                name: 'Styled image (center)',
                type: 'widget',
                widget: 'image',
                attributes: {'class': 'image-center'}
            },

            {
                name: 'Compact Table',
                element: 'table',
                attributes: {
                    cellpadding: '5',
                    cellspacing: '0',
                    border: '1',
                    bordercolor: '#ccc'
                },
                styles: {
                    'border-collapse': 'collapse'
                }
            },

            {
                name: 'Borderless Table',
                element: 'table',
                styles: {'border-style': 'hidden', 'background-color': '#E6E6FA'}
            },
            {name: 'Square Bulleted List', element: 'ul', styles: {'list-style-type': 'square'}},

            /* Widget styles */

            {name: 'Clean Image', type: 'widget', widget: 'image', attributes: {'class': 'image-clean'}},
            {name: 'Grayscale Image', type: 'widget', widget: 'image', attributes: {'class': 'image-grayscale'}},

            {name: 'Featured Snippet', type: 'widget', widget: 'codeSnippet', attributes: {'class': 'code-featured'}},

            {name: 'Featured Formula', type: 'widget', widget: 'mathjax', attributes: {'class': 'math-featured'}},

            {name: '240p', type: 'widget', widget: 'embedSemantic', attributes: {'class': 'embed-240p'}, group: 'size'},
            {name: '360p', type: 'widget', widget: 'embedSemantic', attributes: {'class': 'embed-360p'}, group: 'size'},
            {name: '480p', type: 'widget', widget: 'embedSemantic', attributes: {'class': 'embed-480p'}, group: 'size'},
            {name: '720p', type: 'widget', widget: 'embedSemantic', attributes: {'class': 'embed-720p'}, group: 'size'},
            {
                name: '1080p',
                type: 'widget',
                widget: 'embedSemantic',
                attributes: {'class': 'embed-1080p'},
                group: 'size'
            },

            // Adding space after the style name is an intended workaround. For now, there
            // is no option to create two styles with the same name for different widget types. See https://dev.ckeditor.com/ticket/16664.
            {name: '240p ', type: 'widget', widget: 'embed', attributes: {'class': 'embed-240p'}, group: 'size'},
            {name: '360p ', type: 'widget', widget: 'embed', attributes: {'class': 'embed-360p'}, group: 'size'},
            {name: '480p ', type: 'widget', widget: 'embed', attributes: {'class': 'embed-480p'}, group: 'size'},
            {name: '720p ', type: 'widget', widget: 'embed', attributes: {'class': 'embed-720p'}, group: 'size'},
            {name: '1080p ', type: 'widget', widget: 'embed', attributes: {'class': 'embed-1080p'}, group: 'size'}
        ]);
    }

    CKEDITOR.dtd.$removeEmpty['span'] = false;  // allow empty <span> tags for font awesome
    CKEDITOR.dtd.$removeEmpty['i'] = false;  // allow empty <i> tags for font awesome
};
