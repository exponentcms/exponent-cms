<?php
##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
# Written and Designed by Dave Leffler
#
# This file is part of Exponent
#
# Exponent is free software; you can redistribute
# it and/or modify it under the terms of the GNU
# General Public License as published by the Free
# Software Foundation; either version 2 of the
# License, or (at your option) any later version.
#
# GPL: http://www.gnu.org/licenses/gpl.txt
#
##################################################

// Initialize the Exponent Framework
require_once('exponent.php');

// don't continue unless xmlrpc is turned on
if (!(defined('USE_XMLRPC') && USE_XMLRPC == 1)) {
    exit;
}

// These three files are from the PHP-XMLRPC library v3.0.1
//require_once('external/xmlrpc/xmlrpc.php');
//require_once('external/xmlrpc/xmlrpcs.php');
//require_once('external/xmlrpc/xmlrpc_wrappers.php');

// These three files are the v3 campatibility layer from the PHP-XMLRPC library v4.0.0
require_once('external/xmlrpc/lib/xmlrpc.inc');
require_once('external/xmlrpc/lib/xmlrpcs.inc');
require_once('external/xmlrpc/lib/xmlrpc_wrappers.inc');

/**
 * Used to test usage of object methods in dispatch maps
 */
class xmlrpc_server_methods_container
{
}

/**
 * Internal User Login function
 */
function userLogin($username, $password, $src, $area)
{
    global $db, $user;

    if ($user->isLoggedIn()) {
        return true;
    }

    // This is where you would check to see if the username and password are valid
    // and whether the user has rights to perform this action ($area) 'create' or 'edit' or 'delete'
    // Return true if so. Or false if the login info is wrong.

    // Retrieve the user object from the database.  This may be null, if the username is non-existent.
    $user = new user($db->selectValue('user', 'id', "username='" . $username . "'"));
    $authenticated = $user->authenticate($password);

    if ($authenticated) {
        // Call on the Sessions subsystem to log the user into the site.
        expSession::login($user);
        //Update the last login timestamp for this user.
        $user->updateLastLogin();
    }

    if ($user->isLoggedIn()) {
        return true;
    } else {
        return false;
    }
}

/*
 * Original method from 0.9x to get all existing modules
 */
function exp_getModuleInstancesByType($type = null)
{
    if (empty($type)) {
        return array();
    }

    global $db;

    $refs = $db->selectObjects('sectionref', 'module="' . $type . '"');
    $modules = array();
    foreach ($refs as $ref) {
        if ($ref->refcount > 0) {
            $instance = $db->selectObject('container', 'internal like "%' . $ref->source . '%"');
            $mod = new stdClass();
            $mod->title = !empty($instance->title) ? $instance->title : "Untitled";
            $mod->section = $db->selectvalue('section', 'name', 'id=' . $ref->section);
            $modules[$ref->source][] = $mod;
        }
    }
    return $modules;
}

/**
 * Get List of User Blogs function
 */
$getUsersBlogs_sig = array(
    array(
        $xmlrpcArray,
        $xmlrpcString,
        $xmlrpcString,
        $xmlrpcString
    )
);
$getUsersBlogs_doc = 'Returns a list of weblogs to which an author has posting privileges.';
function getUsersBlogs($xmlrpcmsg)
{
    global $db;

//    $appkey = $xmlrpcmsg->getParam(0)->scalarval();
    $username = $xmlrpcmsg->getParam(1)->scalarval();
    $password = $xmlrpcmsg->getParam(2)->scalarval();

    if (userLogin($username, $password, null, 'create') == true) {
        // setup the list of blogs.
        $structArray = array();

        $blogs = exp_getModuleInstancesByType('blog');
        foreach ($blogs as $src => $blog) {
            $blog_name = (empty($blog[0]->title) ? 'Untitled' : $blog[0]->title) . ' on page ' . $blog[0]->section;
            $loc = expCore::makeLocation('blog', $src);
            $section = $db->selectObject('sectionref', 'source="' . $src . '"');
            $page = $db->selectObject('section', 'id="' . $section->section . '"');
            if (expPermissions::check('create', $loc) || (expPermissions::check('edit', $loc))) {
                $structArray[] = new xmlrpcval(
                    array(
                        'blogid' => new xmlrpcval($src, 'string'),
                        'url' => new xmlrpcval(URL_FULL . $page->sef_name, 'string'),
                        'blogName' => new xmlrpcval($blog_name, 'string'),
                        'isAdmin' => new xmlrpcval(true, 'boolean'),
                        'xmlrpc' => new xmlrpcval(URL_FULL . 'xmlrpc.php', 'string')
                    ), 'struct'
                );
            }
        }
        return new xmlrpcresp(new xmlrpcval($structArray, 'array'));
    } else {
        return new xmlrpcresp(0, 1, "Login Failed");
    }
}

/**
 * Create a New Post function
 */
$newPost_sig = array(
    array(
        $xmlrpcBoolean,
        $xmlrpcString,
        $xmlrpcString,
        $xmlrpcString,
        $xmlrpcStruct,
        $xmlrpcBoolean
    )
);
$newPost_doc = 'Post a new item to the blog.';
function newPost($xmlrpcmsg)
{
    $src = $xmlrpcmsg->getParam(0)->scalarval();
    $username = $xmlrpcmsg->getParam(1)->scalarval();
    $password = $xmlrpcmsg->getParam(2)->scalarval();

    if (userLogin($username, $password, $src, 'create') == true) {
        $loc = expCore::makeLocation('blog', $src);
        if (expPermissions::check('create', $loc) || expPermissions::check('edit', $loc)) {
            $content = $xmlrpcmsg->getParam(3);
            $title = $content->structMem('title')->scalarval();
            $description = $content->structMem('description')->scalarval();
            $config = new expConfig($loc);

            //Get and add the categories selected by the user
            if (!empty($config->config['usecategories'])) {
                $categories = array();
                $cats = $content->structMem('categories');
                if (!empty($cats)) {
                    for ($i = 0; $i < $content->structMem('categories')->arraySize(); $i++) {
                        $categories[$i] = $content->structMem('categories')->arrayMem($i)->scalarval();
                    }
                }
                $params['expCat'] = array();
                foreach ($categories as $cat) {
                    $ecat = new expCat($cat);
                    if (empty($ecat->id)) {
                        // cat doesn't exist so add it
                        $ecat->title = $cat;
                        $ecat->module = 'blog';
                        $ecat->update();
                    }
                    $params['expCat'][] = $ecat->id;
                }
            }

            //Get and add the tags set by the user
            if (empty($config->config['disabletags']) && $content->structMemExists('mt_keywords')) {
                $tags = $content->structMem('mt_keywords')->scalarval();
                $tags_arr = explode(",", trim($tags));
                if (!empty($tags_arr)) {
                    foreach ($tags_arr as $tag) {
                        $tagtitle = strtolower(trim($tag));
                        $etag = new expTag($tagtitle);
                        if (empty($etag->id)) {
                            $etag->update(array('title' => $tagtitle));
                        }
                        $params['expTag'][] = $etag->id;
                    }
                }
            }

            if ($content->structMemExists('mt_allow_comments')) {
                $allow_comments = $content->structMem('mt_allow_comments')->scalarval();
                if ($allow_comments == 2 && empty($config->config['usescomments']) && !empty($config->config['disable_item_comments'])) {
                    $params['disable_comments'] = true;
                }
            }
            $params['disable_comments'] = false;

            $published = $xmlrpcmsg->getParam(4)->scalarval();

            $post = new blog();

            $post->title = $title;
            $post->body = htmlspecialchars_decode(htmlentities($description, ENT_NOQUOTES));
            $post->private = (($published) ? 0 : 1);
            $post->location_data = serialize($loc);

            $post->publish = 0;  // set creation date to now

            $post->update($params);

            return new xmlrpcresp(
                new xmlrpcval($post->id, 'string')
            ); // Return the id of the post just inserted into the DB. See mysql_insert_id() in the PHP manual.
        } else {
            return new xmlrpcresp(0, 1, "Login Failed");
        }
    } else {
        return new xmlrpcresp(0, 1, "Login Failed");
    }
}

/**
 * Edit a Post function
 */
$editPost_sig = array(
    array(
        $xmlrpcBoolean,
        $xmlrpcString,
        $xmlrpcString,
        $xmlrpcString,
        $xmlrpcStruct,
        $xmlrpcBoolean
    )
);
$editPost_doc = 'Edit an item on the blog.';
function editPost($xmlrpcmsg)
{
    global $user;

    $postid = $xmlrpcmsg->getParam(0)->scalarval();
    $username = $xmlrpcmsg->getParam(1)->scalarval();
    $password = $xmlrpcmsg->getParam(2)->scalarval();

    $post = new blog($postid);
    $loc = unserialize($post->location_data);
    if (userLogin($username, $password, $loc->src, 'edit') == true) {
        if (expPermissions::check('edit', $loc) || (expPermissions::check(
                    'create',
                    $loc
                ) && $post->poster == $user->id)
        ) {
            $content = $xmlrpcmsg->getParam(3);
            $title = $content->structMem('title')->scalarval();
            $description = $content->structMem('description')->scalarval();
            $config = new expConfig($loc);

            //Get and add the categories selected by the user
            if (!empty($config->config['usecategories'])) {
                $categories = array();
                $cats = $content->structMem('categories');
                if (!empty($cats)) {
                    for ($i = 0; $i < $content->structMem('categories')->arraySize(); $i++) {
                        $categories[$i] = $content->structMem('categories')->arrayMem($i)->scalarval();
                    }
                }
                $params['expCat'] = array();
                foreach ($categories as $cat) {
                    $ecat = new expCat($cat);
                    if (empty($ecat->id)) {
                        // cat doesn't exist so add it
                        $ecat->title = $cat;
                        $ecat->module = 'blog';
                        $ecat->update();
                    }
                    $params['expCat'][] = $ecat->id;
                }
            }

            //Get and add the tags set by the user
            if (empty($config->config['disabletags']) && $content->structMemExists('mt_keywords')) {
                $tags = $content->structMem('mt_keywords')->scalarval();
                $tags_arr = explode(",", trim($tags));
                if (!empty($tags_arr)) {
                    foreach ($tags_arr as $tag) {
                        $tagtitle = strtolower(trim($tag));
                        $etag = new expTag($tagtitle);
                        if (empty($etag->id)) {
                            $etag->update(array('title' => $tagtitle));
                        }
                        $params['expTag'][] = $etag->id;
                    }
                }
            }

            if ($content->structMemExists('mt_allow_comments')) {
                $allow_comments = $content->structMem('mt_allow_comments')->scalarval();
                if ($allow_comments == 2 && empty($config->config['usescomments']) && !empty($config->config['disable_item_comments'])) {
                    $params['disable_comments'] = true;
                }
            }
            $params['disable_comments'] = false;

            $published = $xmlrpcmsg->getParam(4)->scalarval();

            $post->title = $title;
            $post->body = htmlspecialchars_decode(htmlentities($description, ENT_NOQUOTES));
            $post->private = (($published) ? 0 : 1);
            $post->location_data = serialize($loc);

            $post->update($params);

            return new xmlrpcresp(new xmlrpcval(true, 'boolean'));
        } else {
            return new xmlrpcresp(0, 1, "Login Failed");
        }
    } else {
        return new xmlrpcresp(0, 1, "Login Failed");
    }
}

/**
 * Get a Post function
 */
$getPost_sig = array(
    array(
        $xmlrpcStruct,
        $xmlrpcString,
        $xmlrpcString,
        $xmlrpcString
    )
);
$getPost_doc = 'Get an item on the blog.';
function getPost($xmlrpcmsg)
{
    global $user;

    $postid = $xmlrpcmsg->getParam(0)->scalarval();
    $username = $xmlrpcmsg->getParam(1)->scalarval();
    $password = $xmlrpcmsg->getParam(2)->scalarval();

    //convert $postid to $src
    $post = new blog(intval($postid));
    $loc = unserialize($post->location_data);
    if (userLogin($username, $password, $loc->src, 'edit') == true) {
        if (expPermissions::check('edit', $loc) || (expPermissions::check(
                    'create',
                    $loc
                ) && $post->poster == $user->id)
        ) {
            $cat = array();
            foreach ($post->expCat as $pcat) {
//                $expcat = new expCat($pcat->id);
                $cat[] = $pcat->title;
            }
            $selectedtags = '';
            foreach ($post->expTag as $tag) {
                $selectedtags .= $tag->title . ', ';
            }
            return new xmlrpcresp(
                new xmlrpcval(
                    array(
                        'postid' => new xmlrpcval($post->id, 'string'),
                        'dateCreated' => new xmlrpcval(date('c',$post->publish), 'dateTime.iso8601'),
//                        'link' => new xmlrpcval(makeLink(array('controller'=>'blog', 'action'=>'show', 'title'=>$post->sef_url)), 'string'),
                        'title' => new xmlrpcval($post->title, 'string'),
                        'description' => new xmlrpcval($post->body, 'string'),
                        'categories' => php_xmlrpc_encode($cat),
                        'wp_author_id' => new xmlrpcval(user::getUserAttribution($post->poster), 'string'),
                        'mt_keywords' => new xmlrpcval($selectedtags, 'string'),
                        'publish' => new xmlrpcval((($post->private) ? 0 : 1), 'boolean'),
                    ), 'struct'
                )
            );

        } else {
            return new xmlrpcresp(0, 1, "Login Failed");
        }
    } else {
        return new xmlrpcresp(0, 1, "Login Failed");
    }
}

/**
 * Delete a Post function
 */
$deletePost_sig = array(
    array(
        $xmlrpcBoolean,
        $xmlrpcString,
        $xmlrpcString,
        $xmlrpcString,
        $xmlrpcString,
        $xmlrpcBoolean
    )
);
$deletePost_doc = 'Deletes a post.';
function deletePost($xmlrpcmsg)
{
    global $user;

//    $appkey=$xmlrpcmsg->getParam(0)->scalarval();
    $postid = $xmlrpcmsg->getParam(1)->scalarval();
    $username = $xmlrpcmsg->getParam(2)->scalarval();
    $password = $xmlrpcmsg->getParam(3)->scalarval();
//    $published = $xmlrpcmsg->getParam(4)->scalarval();

    //convert $postid to $src
    $post = new blog($postid);
    $loc = unserialize($post->location_data);
    if (userLogin($username, $password, $loc->src, 'delete') == true) {
        if (expPermissions::check('delete', $loc) || (expPermissions::check(
                    'create',
                    $loc
                ) && $post->poster == $user->id)
        ) {
            $post->delete();
        }

        return new xmlrpcresp(new xmlrpcval(true, 'boolean'));
    } else {
        return new xmlrpcresp(0, 1, "Login Failed");
    }
}

/**
 * Get a List of Recent Posts function
 */
$getRecentPosts_sig = array(
    array(
        $xmlrpcArray,
        $xmlrpcString,
        $xmlrpcString,
        $xmlrpcString,
        $xmlrpcInt
    )
);
$getRecentPosts_doc = 'Get the recent posts on the blog.';
function getRecentPosts($xmlrpcmsg)
{
    global $user;

    $src = $xmlrpcmsg->getParam(0)->scalarval();
    $username = $xmlrpcmsg->getParam(1)->scalarval();
    $password = $xmlrpcmsg->getParam(2)->scalarval();

    if (userLogin($username, $password, $src, 'edit') == true) {
        $loc = expCore::makeLocation('blog', $src);
        if (expPermissions::check('edit', $loc)) {
            $numposts = $xmlrpcmsg->getParam(3)->scalarval();

            // If this module has been configured to aggregate then setup the where clause to pull
            // posts from the proper blogs.
            $config = new expConfig($loc);
            $where = "location_data='" . serialize($loc) . "'";
            if (!empty($config->config['aggregate'])) {
                foreach ($config->config['aggregate'] as $source) {
                    $tmploc = expCore::makeLocation('blog', $source);
                    $where .= " OR location_data='" . serialize($tmploc) . "'";
                }
            }
            $where .= '';
            $blog = new blog();
            $posts = $blog->find('all', $where, 'publish DESC', $numposts);
            $structArray = array();
            for ($i = 0, $iMax = count($posts); $i < $iMax; $i++) {
                if (expPermissions::check('edit', $loc) || (expPermissions::check(
                            'create',
                            $loc
                        ) && $posts[$i]->poster == $user->id)
                ) {
//                    $desc = substr(strip_tags($posts[$i]->body), 0, 253) . "...";  // attempt to reduce length of reply
                    $desc = $posts[$i]->body;
                    if (NO_XMLRPC_DESC) {  // MS Word had an issue when content is over a certain length
                        $desc = substr(strip_tags($posts[$i]->body), 0, 12) . "...";  // attempt to reduce length of reply
                    }
                    $cat = array();
                    foreach ($posts[$i]->expCat as $pcat) {
//                        $expcat = new expCat($pcat->id);
                        $cat[] = $pcat->title;
                    }
//                    $selectedtags = '';
//                    foreach ($posts[$i]->expTag as $tag) {
//                        $selectedtags .= $tag->title . ', ';
//                    }
                    $structArray[] = new xmlrpcval(
                        array(
                            'postid' => new xmlrpcval($posts[$i]->id, 'string'),
                            'dateCreated' => new xmlrpcval(date('c',$posts[$i]->publish), 'dateTime.iso8601'),
//                            'link' => new xmlrpcval(makeLink(array('controller'=>'blog', 'action'=>'show', 'title'=>$posts[$i]->sef_url)), 'string'),
                            'title' => new xmlrpcval($posts[$i]->title, 'string'),
                            'description' => new xmlrpcval($desc, 'string'),
//                            'userid' => new xmlrpcval($post->poster, 'string'),
                            'categories' => php_xmlrpc_encode($cat),
//                            'mt_keywords' => new xmlrpcval($selectedtags, 'string'),
                            'publish' => new xmlrpcval((($posts[$i]->private) ? 0 : 1), 'boolean')
                        ), 'struct'
                    );
                }
            }
            return new xmlrpcresp(new xmlrpcval($structArray, 'array')); // Return type is struct[] (array of struct)
        } else {
            return new xmlrpcresp(0, 1, "Login Failed");
        }
    } else {
        return new xmlrpcresp(0, 1, "Login Failed");
    }
}

/**
 * Get a List of Categories function
 */
$getCategories_sig = array(
    array(
        $xmlrpcArray,
        $xmlrpcString,
        $xmlrpcString,
        $xmlrpcString
    )
);
$getCategories_doc = 'Get the categories on the blog.';
function getCategories($xmlrpcmsg)
{
    $src = $xmlrpcmsg->getParam(0)->scalarval();
    $username = $xmlrpcmsg->getParam(1)->scalarval();
    $password = $xmlrpcmsg->getParam(2)->scalarval();

    if (userLogin($username, $password, $src, 'create') == true) {
        $loc = expCore::makeLocation('blog', $src);
        $config = new expConfig($loc);
        $structArray = array();
        if (!empty($config->config['usecategories'])) {
            $expcat = new expCat();
            $cats = $expcat->find('all', "module='blog'");
            foreach ($cats as $cat) {
                $structArray[] = new xmlrpcval(
                    array(
                        'categoryId' => new xmlrpcval($cat->id, 'string'),
                        'title' => new xmlrpcval($cat->title, 'string'),
                        'description' => new xmlrpcval($cat->title, 'string')
                    ), 'struct'
                );
            }
        }
        return new xmlrpcresp(new xmlrpcval($structArray, 'array')); // Return type is struct[] (array of struct)
    } else {
        return new xmlrpcresp(0, 1, 'Login Failed');
    }
}

function getCategories_mt($xmlrpcmsg)
{
    $src = $xmlrpcmsg->getParam(0)->scalarval();
    $username = $xmlrpcmsg->getParam(1)->scalarval();
    $password = $xmlrpcmsg->getParam(2)->scalarval();

    if (userLogin($username, $password, $src, 'create') == true) {
        $loc = expCore::makeLocation('blog', $src);
        $config = new expConfig($loc);
        $structArray = array();
        if (!empty($config->config['usecategories'])) {
            $expcat = new expCat();
            $cats = $expcat->find('all', "module='blog'");
            foreach ($cats as $cat) {
                $structArray[] = new xmlrpcval(
                    array(
                        'categoryId' => new xmlrpcval($cat->id, 'string'),
                        'categoryName' => new xmlrpcval($cat->title, 'string')
                    ), 'struct'
                );
            }
        }
        return new xmlrpcresp(new xmlrpcval($structArray, 'array')); // Return type is struct[] (array of struct)
    } else {
        return new xmlrpcresp(0, 1, 'Login Failed');
    }
}

/**
 * Get a List of Tags function
 */
$getTerms_sig = array(
    array(
        $xmlrpcArray,
        $xmlrpcString,
        $xmlrpcString,
        $xmlrpcString
    )
);
$getTerms_doc = 'Get the Tags on the blog.';
function getTerms($xmlrpcmsg)
{
    $src = $xmlrpcmsg->getParam(0)->scalarval();
    $username = $xmlrpcmsg->getParam(1)->scalarval();
    $password = $xmlrpcmsg->getParam(2)->scalarval();

    if (userLogin($username, $password, $src, 'create') == true) {
        $loc = expCore::makeLocation('blog', $src);
        $config = new expConfig($loc);
        $structArray = array();
        if (empty($config->config['disabletags'])) {
            $taglist = expTag::getAllTags();
            $tags_arr = explode(",", trim($taglist));
            if (!empty($tags_arr)) {
                foreach ($tags_arr as $tag) {
                    $tagtitle = substr(strtolower(trim($tag)),1,-1);
                    $etag = new expTag($tagtitle);
                    $structArray[] = new xmlrpcval(
                        array(
                            'tag_id' => new xmlrpcval($etag->id, 'int'),
                            'name' => new xmlrpcval($etag->title, 'string'),
                        ), 'struct'
                    );
                }
            }
        }
        return new xmlrpcresp(new xmlrpcval($structArray, 'array')); // Return type is struct[] (array of struct)
    } else {
        return new xmlrpcresp(0, 1, 'Login Failed');
    }
}

/**
 * Get a List of Authors function
 */
$getAuthors_sig = array(
    array(
        $xmlrpcArray,
        $xmlrpcString,
        $xmlrpcString,
        $xmlrpcString
    )
);
$getAuthors_doc = 'Get the Authors on the blog.';
function getAuthors($xmlrpcmsg)
{
    $src = $xmlrpcmsg->getParam(0)->scalarval();
    $username = $xmlrpcmsg->getParam(1)->scalarval();
    $password = $xmlrpcmsg->getParam(2)->scalarval();

    if (userLogin($username, $password, $src, 'create') == true) {
        $loc = expCore::makeLocation('blog', $src);
        $structArray = array();
        $userlist = user::getAllUsers();
        foreach ($userlist as $usr) {
            $structArray[] = new xmlrpcval(
                array(
                    'user_id' => new xmlrpcval($usr->id, 'string'),
                    'user_login' => new xmlrpcval($usr->username, 'string'),
                    'display_name' => new xmlrpcval(user::getUserAttribution($usr->id), 'string'),
                ), 'struct'
            );
        }
        return new xmlrpcresp(new xmlrpcval($structArray, 'array')); // Return type is struct[] (array of struct)
    } else {
        return new xmlrpcresp(0, 1, 'Login Failed');
    }
}

/**
 * Upload a Media File function
 */
$newMediaObject_sig = array(
    array(
        $xmlrpcStruct,
        $xmlrpcString,
        $xmlrpcString,
        $xmlrpcString,
        $xmlrpcStruct
    )
);
$newMediaObject_doc = 'Upload media files onto the blog server.';
function newMediaObject($xmlrpcmsg)
{
//    $src = $xmlrpcmsg->getParam(0)->scalarval();  //NOTE new?
    $username = $xmlrpcmsg->getParam(1)->scalarval();
    $password = $xmlrpcmsg->getParam(2)->scalarval();

    if (userLogin($username, $password, null, 'create') == true) {
        $file = $xmlrpcmsg->getParam(3);
        $filename = $file->structMem('name')->scalarval();
        $filename = substr($filename, (strrpos($filename, "/") + 1));
        $filename = expFile::fixName($filename);
        $type = $file->structMem('type')->scalarval(); // The type of the file
        $bits = $file->structMem('bits')->serialize();
        $bits = str_replace("<value><base64>", "", $bits);
        $bits = str_replace("</base64></value>", "", $bits);
        $dest = UPLOAD_DIRECTORY_RELATIVE;
        $uploaddir = BASE . $dest;
        //Check to see if the directory exists.  If not, create the directory structure.
        if (!file_exists(BASE . $dest)) {
            expFile::makeDirectory($dest);
        }
        if (fwrite(fopen($uploaddir . $filename, "wb"), base64_decode($bits)) == false) {
            return new xmlrpcresp(0, 1, "File Failed to Write");
        } else {
            return new xmlrpcresp(
                new xmlrpcval(
                    array('url' => new xmlrpcval(URL_FULL . $dest . urlencode($filename), 'string')), 'struct'
                )
            );
        }
    } else {
        return new xmlrpcresp(0, 1, "Login Failed");
    }
}

/**
 * Create XML-RPC Server function
 */
$o = new xmlrpc_server_methods_container;
$a = array(
    'metaWeblog.getUsersBlogs' => array(
        'function' => 'getUsersBlogs',
        'docstring' => $getUsersBlogs_doc,
        'signature' => $getUsersBlogs_sig
    ),
    "metaWeblog.newPost" => array(
        "function" => "newPost",
        "signature" => $newPost_sig,
        "docstring" => $newPost_doc
    ),
    "metaWeblog.editPost" => array(
        "function" => "editPost",
        "signature" => $editPost_sig,
        "docstring" => $editPost_doc
    ),
    "metaWeblog.getPost" => array(
        "function" => "getPost",
        "signature" => $getPost_sig,
        "docstring" => $getPost_doc
    ),
    'metaWeblog.deletePost' => array(
        "function" => "deletePost",
        "signature" => $deletePost_sig,
        "docstring" => $deletePost_doc
    ),
    "metaWeblog.getRecentPosts" => array(
        "function" => "getRecentPosts",
        "signature" => $getRecentPosts_sig,
        "docstring" => $getRecentPosts_doc
    ),
    "metaWeblog.getCategories" => array(
        "function" => "getCategories",
        "signature" => $getCategories_sig,
        "docstring" => $getCategories_doc
    ),
    "metaWeblog.newMediaObject" => array(
        "function" => "newMediaObject",
        "signature" => $newMediaObject_sig,
        "docstring" => $newMediaObject_doc
    ),
//    'blogger.getUserInfo' => array(
//        'function' => 'getUserInfo',
//        'docstring' => 'Returns information about an author in the system.',
//        'signature' => array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString))
//    ),
    'blogger.getUsersBlogs' => array(
        'function' => 'getUsersBlogs',
        'docstring' => $getUsersBlogs_doc,
        'signature' => $getUsersBlogs_sig
    ),
    'blogger.deletePost' => array(
        "function" => "deletePost",
        "signature" => $deletePost_sig,
        "docstring" => $deletePost_doc
    ),
    "mt.getCategoryList" => array(
        "function" => "getCategories_mt",
        "signature" => $getCategories_sig,
        "docstring" => $getCategories_doc
    ),
    "wp.getTags" => array(
        "function" => "getTerms",
        "signature" => $getTerms_sig,
        "docstring" => $getTerms_doc
    ),
    "wp.getAuthors" => array(
        "function" => "getAuthors",
        "signature" => $getAuthors_sig,
        "docstring" => $getAuthors_doc
    ),
);

$s = new xmlrpc_server($a, false);
$s->setdebug(2);

$s->service();
?>