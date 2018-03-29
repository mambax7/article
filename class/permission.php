<?php
/**
 * Article module for XOOPS
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package         article
 * @since           1.0
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');
require_once __DIR__ . '/../include/vars.php';
mod_loadFunctions('parse', $GLOBALS['artdirname']);

$GLOBALS['perms_global']   = [
    'search' => ['id' => 1, 'title' => art_constant('AM_PERM_SEARCH')],
    'html'   => ['id' => 2, 'title' => art_constant('AM_PERM_HTML')],
    'upload' => ['id' => 3, 'title' => art_constant('AM_PERM_UPLOAD')]
];
$GLOBALS['perms_category'] = [
    'access'   => ['title' => art_constant('AM_PERM_ACCESS'), 'desc' => art_constant('AM_PERM_ACCESS_DESC')],
    'view'     => ['title' => art_constant('AM_PERM_VIEW'), 'desc' => art_constant('AM_PERM_VIEW_DESC')],
    'submit'   => ['title' => art_constant('AM_PERM_SUBMIT'), 'desc' => art_constant('AM_PERM_SUBMIT_DESC')],
    'publish'  => ['title' => art_constant('AM_PERM_PUBLISH'), 'desc' => art_constant('AM_PERM_PUBLISH_DEC')],
    'rate'     => ['title' => art_constant('AM_PERM_RATE'), 'desc' => art_constant('AM_PERM_RATE_DESC')],
    'moderate' => ['title' => art_constant('AM_PERM_MODERATE'), 'desc' => art_constant('AM_PERM_MODERATE_DESC')]
];

// Initializing XoopsGroupPermHandler if not loaded yet
if (!class_exists('XoopsGroupPermHandler')) {
    require_once XOOPS_ROOT_PATH . '/kernel/groupperm.php';
}

art_parse_class('
class [CLASS_PREFIX]PermissionHandler extends XoopsGroupPermHandler
{
    function deleteByCategory($cat_id)
    {
        global $xoopsModule;

        if (is_object($xoopsModule) && $xoopsModule->getVar("dirname") == $GLOBALS["artdirname"]) {
            $module_id = $xoopsModule->getVar("mid") ;
        } else {
            $moduleHandler = xoops_getHandler("module");
            $artModule = $moduleHandler->getByDirname($GLOBALS["artdirname"]);
            $module_id = $artModule->getVar("mid") ;
            unset($artModule);
        }

        $gpermHandler = xoops_getHandler("groupperm");
        $criteria = new \CriteriaCompo(new \Criteria("gperm_modid", $module_id));
        $gperm_names = "(\'" . implode( "\', \'", array_keys( $GLOBALS["perms_category"] ) ) . "\')";
        $criteria->add(new \Criteria("gperm_name", $gperm_names, "IN"));
        $criteria->add(new \Criteria("gperm_itemid", $cat_id));
        $gpermHandler->deleteAll($criteria);

        $this->createPermData();

        return true;
    }

    function getPermission($gperm_name = "access", $id = 0)
    {
        global $xoopsUser, $xoopsModule;

        if ($GLOBALS["xoopsUserIsAdmin"] && $xoopsModule->getVar("dirname") == $GLOBALS["artdirname"]) {
            return true;
        }

        $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : array(XOOPS_GROUP_ANONYMOUS);
        if ( !$groups ) return false;

        if ( !$allowed_groups = $this->getGroups($gperm_name, $id) ) return false;
        return count(array_intersect($allowed_groups, $groups));
    }

    function &getCategories($perm_name = "access")
    {
        global $xoopsUser;

        $ret = array();

        $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : array(XOOPS_GROUP_ANONYMOUS);
        if (count($groups) < 1)    return $ret;

        $_cachedPerms = $this->loadPermData($perm_name);

        $allowed_cats = array();
        foreach ($_cachedPerms as $cat => $cats) {
            if ($cat == 0 || empty($cats)) continue;

            if (array_intersect($groups, $cats)) {
                $allowed_cats[$cat] = 1;
            }
        }
        unset($_cachedPerms);
        $ret = array_keys($allowed_cats);

        return $ret;
    }

    function &getGroups($gperm_name, $id = 0)
    {
        $_cachedPerms = $this->loadPermData($gperm_name);
        $groups = empty($_cachedPerms[$id]) ? array() : $_cachedPerms[$id];
        unset($_cachedPerms);

        return $groups;
    }

    function createPermData($perm_name = "")
    {
        global $xoopsModule;

        if (is_object($xoopsModule) && $xoopsModule->getVar("dirname") == $GLOBALS["artdirname"]) {
            $module_id = $xoopsModule->getVar("mid") ;
        } else {
            $moduleHandler = xoops_getHandler("module");
            $artModule = $moduleHandler->getByDirname($GLOBALS["artdirname"]);
            $module_id = $artModule->getVar("mid") ;
            unset($artModule);
        }

        $categoryHandler = xoops_getModuleHandler("category", $GLOBALS["artdirname"]);
        $cat_ids = $categoryHandler->getIds();

        $gpermHandler = xoops_getHandler("groupperm");
        $memberHandler = xoops_getHandler("member");
        $glist = $memberHandler->getGroupList();

        load_functions("cache");
        $perms = array();

        if ( empty($perm_name) || in_array($perm_name, array_keys($GLOBALS["perms_global"])) ):
        foreach (array_keys($glist) as $i) {
            $ids = $gpermHandler->getItemIds("global", $i, $module_id);
            foreach ($ids as $id) {
                foreach ($GLOBALS["perms_global"] as  $permname => $perm_info) {
                    if ( !empty($perm_name) && $perm_name != $permname ) continue;
                    if ($perm_info["id"] == $id) $perms[$permname][0][] = $i;
                }
            }
        }
        endif;

        if ( empty($perm_name) || $perm_name == "all" || in_array($perm_name, array_keys($GLOBALS["perms_category"])) ):
        foreach (array_keys($GLOBALS["perms_category"]) as $permname) {
            if ( !empty($perm_name) && $perm_name != "all" && $perm_name != $permname) continue;
            foreach (array_keys($glist) as $i) {
                $cats = $gpermHandler->getItemIds($permname, $i, $module_id);
                foreach ($cats as $cat) {
                    $perms[$permname][$cat][] = $i;
                    if (empty($perm_name) || $perm_name == "all") {
                        $perms["all"][$cat][] = $i;
                    }
                }
            }
        }
        endif;

        foreach (array_keys($perms) as $perm) {
            mod_createCacheFile($perms[$perm], "permission_{$perm}", $GLOBALS["artdirname"]);
        }

        $ret = !empty($perm_name) ? @$perms[$perm_name] : $perms;

        return $ret;
    }

    function &loadPermData($perm_name = "access")
    {
        load_functions("cache");
        if (!$perms = mod_loadCacheFile("permission_{$perm_name}", $GLOBALS["artdirname"])) {
            $perms = $this->createPermData($perm_name);
        }

        return $perms;
    }

    function validateRight($perm, $itemid, $groupid, $mid = null)
    {
        if (empty($mid)) {
            if (is_object($GLOBALS["xoopsModule"]) && $GLOBALS["xoopsModule"]->getVar("dirname") == $GLOBALS["artdirname"]) {
                $mid = $GLOBALS["xoopsModule"]->getVar("mid");
            } else {
                $moduleHandler = xoops_getHandler("module");
                $mod = $moduleHandler->getByDirname($GLOBALS["artdirname"]);
                $mid = $mod->getVar("mid");
                unset($mod);
            }
        }
        if ($this->_checkRight($perm, $itemid, $groupid, $mid)) return true;
        $this->addRight($perm, $itemid, $groupid, $mid);

        return true;
    }

    /**
     * Check permission (directly)
     *
     * @param string    $gperm_name    Name of permission
     * @param int       $gperm_itemid  ID of an item
     * @param int/array $gperm_groupid A group ID or an array of group IDs
     * @param int       $gperm_modid   ID of a module
     *
     * @return bool TRUE if permission is enabled
     */
    function _checkRight($gperm_name, $gperm_itemid, $gperm_groupid, $gperm_modid = 1)
    {
        $criteria = new \CriteriaCompo(new \Criteria("gperm_modid", $gperm_modid));
        $criteria->add(new \Criteria("gperm_name", $gperm_name));
        $gperm_itemid = (int)($gperm_itemid);
        if ($gperm_itemid > 0) {
            $criteria->add(new \Criteria("gperm_itemid", $gperm_itemid));
        }
        if (is_array($gperm_groupid)) {
            $criteria2 = new \CriteriaCompo();
            foreach ($gperm_groupid as $gid) {
                $criteria2->add(new \Criteria("gperm_groupid", $gid), "OR");
            }
            $criteria->add($criteria2);
        } else {
            $criteria->add(new \Criteria("gperm_groupid", $gperm_groupid));
        }
        if ($this->getCount($criteria) > 0) {
            return true;
        }

        return false;
    }

    function deleteRight($perm, $itemid, $groupid, $mid = null)
    {
        if (empty($mid)) {
            if (is_object($GLOBALS["xoopsModule"]) && $GLOBALS["xoopsModule"]->getVar("dirname") == $GLOBALS["artdirname"]) {
                $mid = $GLOBALS["xoopsModule"]->getVar("mid");
            } else {
                $moduleHandler = xoops_getHandler("module");
                $mod = $moduleHandler->getByDirname($GLOBALS["artdirname"]);
                $mid = $mod->getVar("mid");
                unset($mod);
            }
        }
        if (is_callable(array(&$this->XoopsGroupPermHandler, "deleteRight"))) {
            return parent::deleteRight($perm, $itemid, $groupid, $mid);
        } else {
            $criteria = new \CriteriaCompo(new \Criteria("gperm_name", $perm));
            $criteria->add(new \Criteria("gperm_groupid", $groupid));
            $criteria->add(new \Criteria("gperm_itemid", $itemid));
            $criteria->add(new \Criteria("gperm_modid", $mid));
            $perms_obj =& $this->getObjects($criteria);
            if (!empty($perms_obj)) {
                foreach ($perms_obj as $perm_obj) {
                    $this->delete($perm_obj);
                }
            }
            unset($criteria, $perms_obj);
        }

        return true;
    }

    function applyTemplate($category, $mid=null)
    {
        $perm_template = $this->getTemplate();
        if (empty($perm_template)) return false;

        if (empty($mid)) {
            if (is_object($GLOBALS["xoopsModule"]) && $GLOBALS["xoopsModule"]->getVar("dirname") == $GLOBALS["artdirname"]) {
                $mid = $GLOBALS["xoopsModule"]->getVar("mid");
            } else {
                $moduleHandler = xoops_getHandler("module");
                $mod = $moduleHandler->getByDirname($GLOBALS["artdirname"]);
                $mid = $mod->getVar("mid");
                unset($mod);
            }
        }

        $memberHandler = xoops_getHandler("member");
        $glist = $memberHandler->getGroupList();
        $perms = array_keys($GLOBALS["perms_category"]);
        foreach (array_keys($glist) as $group) {
            foreach ($perms as $perm) {
                if (!empty($perm_template[$group][$perm])) {
                    $this->validateRight($perm, $category, $group, $mid);
                } else {
                    $this->deleteRight($perm, $category, $group, $mid);
                }
            }
        }

        return true;
    }

    function &getTemplate()
    {
        load_functions("cache");
        $perms = mod_loadCacheFile("perm_template", $GLOBALS["artdirname"]);

        return $perms;
    }

    function setTemplate($perms)
    {
        load_functions("cache");

        return mod_createCacheFile($perms, "perm_template", $GLOBALS["artdirname"]);
    }
}
');
