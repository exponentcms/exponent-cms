<?php
##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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

/**
 * This is the class expPermissions
 *
 * @package Subsystems
 * @subpackage Subsystems
 */
/** @define "BASE" "../../.." */

class expPermissions {

	/** exdoc
	 * Loads permission data from the database for the specified user.
	 *
	 * @param user/object $user the user to load permissions for.
     *
	 * @node Subsystems:expPermissions
	 */
	public static function load($user) {
		global $db, $exponent_permissions_r;
		// The $has_admin boolean will be flipped to true if the user has any administrate permission anywhere.
		// It will be used for figuring out the allowable UI levels.
//		$has_admin = 0;
		// Clear the global permissions array;
		$exponent_permissions_r = array();

		if ($user == null) {
			// If the user is not logged in, they have no permissions.
			return;
		}
		if ($user->is_acting_admin == 0) {
			// Retrieve all of the explicit user permissions, by user id
			foreach ($db->selectObjects('userpermission','uid=' . $user->id) as $obj) {
				$exponent_permissions_r[$obj->module][$obj->source][$obj->internal][$obj->permission] = 1;
			}
			// Retrieve all of the implicit user permissions (by virtue of group membership).
			foreach ($db->selectObjects('groupmembership','member_id='.$user->id) as $memb) {
				foreach ($db->selectObjects('grouppermission','gid=' . $memb->group_id) as $obj) {
					$exponent_permissions_r[$obj->module][$obj->source][$obj->internal][$obj->permission] = 1;
				}
			}
			// Retrieve all of the implicit user permissions (by virtue of subscriptions).
//			foreach ($db->selectObjects('subscriptions_users','user_id='.$user->id) as $memb) {
//				foreach ($db->selectObjects('subscriptionpermission','subscription_id=' . $memb->subscription_id) as $obj) {
//					$exponent_permissions_r[$obj->module][$obj->source][$obj->internal][$obj->permission] = 1;
//				}
//			}
        }
		expSession::set('permissions',$exponent_permissions_r);

	}

	/** exdoc
	 * Looks to the permission data and checks to see
	 * if the current user has been granted the given permission(s)
	 * on the granted the given location.
	 * Returns true if the permission is granted, false if it is not.
	 *
	 * @param string $permission The name of the permission to check
	 * @param object $location The location to check on.  This will be passed
	 *	to getLocationHierarchy (defined by the module) for a full hierarchy
	 *	of permissions.
     *
	 * @return bool
     *
	 * @node Subsystems:expPermissions
	 */
	public static function check($permission,$location) {
		global $exponent_permissions_r, $user, $db, $module_scope;

        if (expModules::controllerExists($location->mod)) {
            $mod = expModules::getController($location->mod);
            if (method_exists($mod, 'checkPermissions')){
                if ($mod->checkPermissions($permission,$location)) return true;
            }
        }

		if (!empty($user->id)) {
			if ($user->isAdmin()) return true;  // admin users always have permissions
		} else {
			return false;  // anonymous/logged-out user never has permission
		}
        $ploc = clone $location;
        $ploc->mod = expModules::controllerExists($ploc->mod) ? expModules::getControllerClassName($ploc->mod) : $ploc->mod;

		if (!is_array($permission)) $permission = array($permission);
        // always check for 'manage' permission
        $permission[] = 'manage';
        // create permission implies edit permission
        if (array_intersect(array('edit'),$permission)) {
            $permission[] = 'create';
        }
        $permission = array_unique($permission);  // strip out duplicates

		if (is_callable(array($ploc->mod,"getLocationHierarchy"))) {  //FIXME this is only available in calendarmodule, may not be needed if there is no 'int' property?
			foreach (call_user_func(array($ploc->mod,"getLocationHierarchy"),$ploc) as $loc) {  //FIXME this is only available in calendarmodule
				foreach ($permission as $perm) {
					if (isset($exponent_permissions_r[$loc->mod][$loc->src][$loc->int][$perm])) {
						return true;
					}
				}
			}
		}
        // check for explicit user (and implicit group/subscription) permission
        foreach ($permission as $perm) {
            if (isset($exponent_permissions_r[$ploc->mod][$ploc->src][$ploc->int][$perm])) {
                return true;
            }
        }

        // exit recursive calls for globally scoped modules
        $module_scope['error'] = false;
        if (!empty($ploc->src) && !empty($module_scope[$ploc->src][$ploc->mod]->scope)) {  // is this the main container?
            $rLoc = $db->selectObject("sectionref","source='" . $ploc->src . "' AND module='" . $ploc->mod . "'");
            if (!empty($rLoc) && $rLoc->refcount == 1000 && $module_scope[$ploc->src][$ploc->mod]->scope == 'global') {
                $module_scope['error'] = true;
                return false;
            }
        }

        // check for permission inherited from container(s)
        $tmpLoc = new stdClass();
        foreach ($permission as $perm) {
            // inclusive container perms
            $tmpLoc->mod = $ploc->mod;
            $tmpLoc->src = $ploc->src;
            $tmpLoc->int = $ploc->int;
            $tmpLoc->mod = (!strpos($tmpLoc->mod,"Controller") && !strpos($tmpLoc->mod,"module")) ? $tmpLoc->mod."Controller" : $tmpLoc->mod;
            $cLoc = expUnserialize($db->selectValue('container','external','internal=\''.serialize($tmpLoc).'\''));
            if (@isset($exponent_permissions_r[$cLoc->mod][$cLoc->src][$cLoc->int][$perm])) {
               return true;
            }
            if (!empty($cLoc)) {
                if (self::check($perm,$cLoc)) {
                    return true;
                }
            }
        }
        if (@$module_scope['error'] === true) {
            $module_scope['error'] = false;
            return false;
        }

        // if this is the global sidebar, then exit since we don't care about page permissions
        $module_scope['error'] = false;
        if (!empty($ploc->src) && !empty($module_scope[$ploc->src][$ploc->mod]->scope)) {  // is this the main container?
            $rLoc = $db->selectObject("sectionref","source='" . $ploc->src . "' AND module='" . $ploc->mod . "'");
            if (!empty($rLoc) && $rLoc->refcount == 1000 && @$module_scope[$ploc->src][$ploc->mod]->scope == 'global') {
                $module_scope['error'] = true;
                return false;
            }
        }

        // check for inherited 'manage' permission from current page and its parents
		if ($ploc->mod != 'navigationController') {
            global $sectionObj;
            if (self::check('manage',expCore::makeLocation('navigationController','',$sectionObj->id))) {
				return true;
			}
		} else {
            // check for recursive inherited page permission
            $page = $db->selectObject("section","id=".$ploc->int);
            if (!empty($page->parent)) {
                // first check for specific 'view' permission
                if (self::check($permission,expCore::makeLocation('navigationController','',$page->parent))) {
                    return true;
                }
                // otherwise check for 'super' permission
//                if (self::check('manage',expCore::makeLocation('navigationController','',$page->parent))) {
//                    return true;
//                }
            }
        }

        return false;
	}

	/** exdoc
	 * Checks to see if the given user has been given a specific permission.
     * Handles explicit checks (actually assigned to the user)  implicit  and inherited checks
	 * (assigned to a group the user belongs to).  Returns true if the permission is granted, false if it is not.
	 *
	 * @param user/object $user The user to check permission on
	 * @param string $permission The name of the permission to check
	 * @param object $location The location to check on.
	 * @param boolean $explicitOnly Whether to check for explicit/implicit assignment or inherited.
	 *
	 * @return bool
     *
	 * @node Subsystems:expPermissions
	 */
	public static function checkUser($user,$permission,$location,$explicitOnly = false) {
		global $db, $module_scope;

		if ($user == null) {
            return false;
        } elseif ($user->is_acting_admin) {
            return true;
        }
        $ploc = clone $location;
        $ploc->mod = expModules::controllerExists($ploc->mod) ? expModules::getControllerClassName($ploc->mod) : $ploc->mod;
        // check for explicit user permission
		$explicit = $db->selectObject("userpermission","uid=" . $user->id . " AND module='" . $ploc->mod . "' AND source='" . $ploc->src . "' AND internal='" . $ploc->int . "' AND permission='$permission'");
		if ($explicitOnly || $explicit) return !empty($explicit);

        // Calculate inherited permissions if we don't already have explicit/implicit perms
        if (is_callable(array($ploc->mod,"getLocationHierarchy"))) {  //FIXME this is only available in calendarmodule
            foreach (call_user_func(array($ploc->mod,"getLocationHierarchy"),$ploc) as $loc) {  //FIXME this is only available in calendarmodule
                if ($db->selectObject("userpermission","uid=" . $user->id . " AND module='" . $loc->mod . "' AND source='" . $loc->src . "' AND internal='" . $loc->int . "' AND permission='$permission'")) {
                    return true;
                }
            }
        }

        // check for implicit group permission
        $memberships = $db->selectObjects("groupmembership","member_id=".$user->id);
        foreach ($memberships as $memb) {
            $group = $db->selectObject("group","id=".$memb->group_id);
            if (self::checkGroup($group,$permission,$ploc))
                return true;
        }

        // exit recursive calls for globally scoped modules
        $module_scope['error'] = false;
        if (!empty($ploc->src) && !empty($module_scope[$ploc->src][$ploc->mod]->scope)) {  // is this the main container?
            $rLoc = $db->selectObject("sectionref","source='" . $ploc->src . "' AND module='" . $ploc->mod . "'");
            if (!empty($rLoc) && $rLoc->refcount == 1000 && $module_scope[$ploc->src][$ploc->mod]->scope == 'global') {
                $module_scope['error'] = true;
                return false;
            }
        }

        // check for inherited container permission
        $perms = array();
        $perms[] = $permission;
        // account for old-style container perms
//        $perms[] = 'administrate';
//        if ($permission == 'post' || $permission == 'create') {
//            $perms[] = 'add_module';
//        } elseif ($permission == 'edit')  {
//            $perms[] = 'add_module';
//            $perms[] = 'edit_module';
//        } elseif ($permission == 'delete')  {
//            $perms[] = 'delete_module';
//        } elseif ($permission == 'configure')  {
//            $perms[] = 'order_modules';
//        }
        $tmpLoc = new stdClass();
        foreach ($perms as $perm) {
            $tmpLoc->mod = $ploc->mod;
            $tmpLoc->src = $ploc->src;
            $tmpLoc->int = $ploc->int;
            $tmpLoc->mod = (!strpos($tmpLoc->mod,"Controller") && !strpos($tmpLoc->mod,"module")) ? $tmpLoc->mod."Controller" : $tmpLoc->mod;
            $cLoc = expUnserialize($db->selectValue('container','external','internal=\''.serialize($tmpLoc).'\''));
            if (!empty($cLoc) && $db->selectObject("userpermission","uid=" . $user->id . " AND module='" . $cLoc->mod . "' AND source='" . $cLoc->src . "' AND internal='" . $cLoc->int . "' AND permission='$perm'")) {
               return true;
            }
            if (!empty($cLoc)) {
                if (self::checkUser($user,$perm,$cLoc)) {
                    return true;
                }
            }
        }
        if (@$module_scope['error'] == true) {
            $module_scope['error'] = false;
            return false;
        }

        // if this is the global sidebar, then exit since we don't care about page permissions
        $module_scope['error'] = false;
        if (!empty($ploc->src) && !empty($module_scope[$ploc->src][$ploc->mod]->scope)) {  // is this the main container?
            $rLoc = $db->selectObject("sectionref","source='" . $ploc->src . "' AND module='" . $ploc->mod . "'");
            if (!empty($rLoc) && $rLoc->refcount == 1000 && @$module_scope[$ploc->src][$ploc->mod]->scope == 'global') {
                $module_scope['error'] = true;
                return false;
            }
        }

        // check for inherited 'manage' permission from its page
        if ($ploc->mod != 'navigationController') {
            $tmpLoc->mod = $ploc->mod;
            $tmpLoc->src = $ploc->src;
            $tmpLoc->int = $ploc->int;
            $tmpLoc->mod = (!strpos($tmpLoc->mod,"Controller") && !strpos($tmpLoc->mod,"module")) ? $tmpLoc->mod."Controller" : $tmpLoc->mod;
            foreach ($db->selectObjects('sectionref',"is_original=1 AND module='".$tmpLoc->mod."' AND source='".$tmpLoc->src."'") as $secref) {
                if (self::checkUser($user,'manage',expCore::makeLocation('navigationController','',$secref->section))) {
                    return true;
                }
            }
        } else {
            // check for recursive inherited page permission
            $page = $db->selectObject("section","id=".$ploc->int);
            if (!empty($page->parent)) {
                // first check for specific 'view' permission
                if (self::checkUser($user,$permission,expCore::makeLocation('navigationController','',$page->parent))) {
                    return true;
                }
                // otherwise check for 'super' permission
                if (self::checkUser($user,'manage',expCore::makeLocation('navigationController','',$page->parent))) {
                    return true;
                }
            }
        }

		return false;
	}

	/** exdoc
	 * Grants the specified permission to the specified user, on the given location
	 *
	 * @param user/object $user The user to grant the permission to
	 * @param string $permission The name of the permission to grant
	 * @param object $location The location to grant the permission on
     *
	 * @node Subsystems:expPermissions
	 */
	public static function grant($user,$permission,$location) {
		if ($user !== null) {
			if (!self::checkUser($user,$permission,$location)) {
				$obj = new stdClass();
				$obj->uid = $user->id;
//				$obj->module = $location->mod;
                $obj->module = expModules::controllerExists($location->mod) ? expModules::getControllerClassName($location->mod) : $location->mod;
				$obj->source = $location->src;
				$obj->internal = $location->int;
				$obj->permission = $permission;

				global $db;
                $db->delete("userpermission", " uid='" . $obj->uid . "' module = '" . $obj->module . "' AND source='" . $obj->source . "' AND internal='" . $obj->internal . "'");
				$db->insertObject($obj,"userpermission");
			}
		}
	}

	/** exdoc
	 * Checks to see if the given group has been given a specific permission on a location.
	 * Returns true if the permission is granted, false if it is not.
	 *
	 * @param group/object $group The group to check
	 * @param string $permission The name of the permission to check
	 * @param object $location The location to check on.
	 * @param bool $explicitOnly
     *
	 * @return bool
     *
	 * @node Subsystems:expPermissions
	 */
	public static function checkGroup($group,$permission,$location,$explicitOnly = false) {
		global $db, $module_scope;

		if ($group == null) return false;
        $ploc = clone $location;
        $ploc->mod = expModules::controllerExists($ploc->mod) ? expModules::getControllerClassName($ploc->mod) : $ploc->mod;
        // check for explicit group permission
		$explicit = $db->selectObject("grouppermission","gid=" . $group->id . " AND module='" . $ploc->mod . "' AND source='" . $ploc->src . "' AND internal='" . $ploc->int . "' AND permission='$permission'");
		if ($explicitOnly || $explicit) return !empty($explicit);

        // exit recursive calls for globally scoped modules
        $module_scope['error'] = false;
        if (!empty($ploc->src) && !empty($module_scope[$ploc->src][$ploc->mod]->scope)) {  // is this the main container?
            $rLoc = $db->selectObject("sectionref","source='" . $ploc->src . "' AND module='" . $ploc->mod . "'");
            if (!empty($rLoc) && $rLoc->refcount == 1000 && $module_scope[$ploc->src][$ploc->mod]->scope == 'global') {
                $module_scope['error'] = true;
                return false;
            }
        }

        // check for inherited container permission
        $perms = array();
        $perms[] = $permission;
        // account for old-style container perms
//        $perms[] = 'administrate';
//        if ($permission == 'post' || $permission == 'create') {
//            $perms[] = 'add_module';
//        } elseif ($permission == 'edit')  {
//            $perms[] = 'add_module';
//            $perms[] = 'edit_module';
//        } elseif ($permission == 'delete')  {
//            $perms[] = 'delete_module';
//        } elseif ($permission == 'configure')  {
//            $perms[] = 'order_modules';
//        }
        $tmpLoc = new stdClass();
        foreach ($perms as $perm) {
            $tmpLoc->mod = $ploc->mod;
            $tmpLoc->src = $ploc->src;
            $tmpLoc->int = $ploc->int;
            $tmpLoc->mod = (!strpos($tmpLoc->mod,"Controller") && !strpos($tmpLoc->mod,"module")) ? $tmpLoc->mod."Controller" : $tmpLoc->mod;
            $cLoc = expUnserialize($db->selectValue('container','external','internal=\''.serialize($tmpLoc).'\''));
            if (!empty($cLoc) && $db->selectObject("grouppermission","gid=" . $group->id . " AND module='" . $cLoc->mod . "' AND source='" . $cLoc->src . "' AND internal='" . $cLoc->int . "' AND permission='$perm'")) {
               return true;
            }
            if (!empty($cLoc)) {
                if (self::checkGroup($group,$perm,$cLoc)) {
                    return true;
                }
            }
        }
        if (@$module_scope['error'] == true) {
            $module_scope['error'] = false;
            return false;
        }

        // if this is the global sidebar, then exit since we don't care about page permissions
        $module_scope['error'] = false;
        if (!empty($ploc->src) && !empty($module_scope[$ploc->src][$location->mod]->scope)) {  // is this the main container?
            $rLoc = $db->selectObject("sectionref","source='" . $ploc->src . "' AND module='" . $ploc->mod . "'");
            if (!empty($rLoc) && $rLoc->refcount == 1000 && @$module_scope[$ploc->src][$ploc->mod]->scope == 'global') {
                $module_scope['error'] = true;
                return false;
            }
        }

        // check for inherited 'manage' permission from its page
        if ($ploc->mod != 'navigationController') {
            $tmpLoc->mod = $ploc->mod;
            $tmpLoc->src = $ploc->src;
            $tmpLoc->int = $ploc->int;
            $tmpLoc->mod = (!strpos($tmpLoc->mod,"Controller") && !strpos($tmpLoc->mod,"module")) ? $tmpLoc->mod."Controller" : $tmpLoc->mod;
            foreach ($db->selectObjects('sectionref',"is_original=1 AND module='".$tmpLoc->mod."' AND source='".$tmpLoc->src."'") as $secref) {
                if (self::checkGroup($group,'manage',expCore::makeLocation('navigationController','',$secref->section))) {
                    return true;
                }
            }
        } else {
            // check for recursive inherited page permission
            $page = $db->selectObject("section","id=".$ploc->int);
            if (!empty($page->parent)) {
                // first check for specific 'view' permission
                if (self::checkGroup($group,$permission,expCore::makeLocation('navigationController','',$page->parent))) {
                    return true;
                }
                // otherwise check for 'super' permission
                if (self::checkGroup($group,'manage',expCore::makeLocation('navigationController','',$page->parent))) {
                    return true;
                }
            }
        }

		return false;
	}

	/** exdoc
	 * Grants the specified permission to the specified user group, on the given location
	 *
	 * @param group/object $group The group to grant the permission to
	 * @param string $permission The name of the permission to grant
	 * @param object $location The location to grant the permission on
     *
	 * @node Subsystems:expPermissions
	 */
	public static function grantGroup($group,$permission,$location) {
		if ($group !== null) {
			if (!self::checkGroup($group,$permission,$location)) {
				$obj = new stdClass();
				$obj->gid = $group->id;
//				$obj->module = $location->mod;
                $obj->module = expModules::controllerExists($location->mod) ? expModules::getControllerClassName($location->mod) : $location->mod;
				$obj->source = $location->src;
				$obj->internal = $location->int;
				$obj->permission = $permission;

				global $db;
		        $db->delete("grouppermission", " gid='" . $obj->gid . "' module = '" . $obj->module . "' AND source='" . $obj->source . "' AND internal='" . $obj->internal . "'");
				$db->insertObject($obj,"grouppermission");
//				echo "In groupGrant</br>";
			}
		}
	}

	/** exdoc
	 * Removes all permissions from a user, on a specific location.
	 *
	 * @param user/object $user The user to remove all permissions from
	 * @param object $location The location to remove all permission on
     *
	 * @return mixed
     *
	 * @node Subsystems:expPermissions
	 */
	public static function revokeAll($user,$location) {
		global $db;

        $mod = expModules::controllerExists($location->mod) ? expModules::getControllerClassName($location->mod) : $location->mod;
		return $db->delete("userpermission","uid=" . $user->id . " AND module='" . $mod . "' AND source='" . $location->src . "' AND internal='" . $location->int . "'");
	}

	/** exdoc
	 * Removes all permissions from a group, on a specific location.
	 *
	 * @param group/object $group The group to remove all permissions from
	 * @param object $location The location to remove all permission on
     *
	 * @return mixed
     *
	 * @node Subsystems:expPermissions
	 */
	public static function revokeAllGroup($group,$location) {
		global $db;

        $mod = expModules::controllerExists($location->mod) ? expModules::getControllerClassName($location->mod) : $location->mod;
		return $db->delete('grouppermission','gid=' . $group->id . " AND module='" . $mod . "' AND source='" . $location->src . "' AND internal='" . $location->int . "'");
	}

    /** exdoc
     * Removes all user and group permissions, on a specific location.
     *
     * @param object $location
     *
     * @return bool
     *
     * @node Subsystems:expPermissions
     */
   	public static function revokeComplete($location) {
   		global $db;

        $mod = expModules::controllerExists($location->mod) ? expModules::getControllerClassName($location->mod) : $location->mod;
   		$db->delete("userpermission","module='".$mod."' AND source='".$location->src."'");
   		$db->delete("grouppermission","module='".$mod."' AND source='".$location->src."'");
   		return true;
   	}

	/** exdoc
	 * This call will force all active session to reload their
	 * permission data.  This is useful if permissions are assigned
	 * or revoked, and is required to see these changes.
     *
	 * @node Subsystems:expPermissions
	 */
	public static function triggerRefresh() {
		global $db;
		$obj = new stdClass();
		$obj->refresh = 1;
		$db->updateObject($obj,'sessionticket','true'); // force a global refresh
	}

	/** exdoc
	 * This call will force all active sessions for the given user to
	 * reload their permission data.  This is useful if permissions
	 * are assigned or revoked, and is required to see these changes.
     *
     * @param user/object $user
     *
	 * @node Subsystems:expPermissions
	 */
	public static function triggerSingleRefresh($user) {  //FIXME not currently used
		global $db;
		$obj = new stdClass();
		$obj->refresh = 1;
		$db->updateObject($obj,'sessionticket','uid='.$user->id); // force a global refresh
	}

}

?>