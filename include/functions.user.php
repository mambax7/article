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

include __DIR__ . '/vars.php';
define($GLOBALS['artdirname'] . '_FUNCTIONS_USER_LOADED', true);

if (!defined('ART_FUNCTIONS_USER')):
    define('ART_FUNCTIONS_USER', 1);

    load_functions('user');

    /**
     * Function to a list of user names associated with their user IDs
     * @param      $uid
     * @param int  $usereal
     * @param bool $linked
     * @return array
     */
    function &art_getUnameFromId($uid, $usereal = 0, $linked = false)
    {
        if (!is_array($uid)) {
            $uid = [$uid];
        }
        xoops_load('XoopsUserUtility');
        $ids = XoopsUserUtility::getUnameFromIds($uid, $usereal, $linked);

        return $ids;
    }

    /**
     * Function to check if a user is an administrator of the module
     *
     * @param int $user
     * @param int $mid
     * @return bool
     */
    function art_isAdministrator($user = -1, $mid = 0)
    {
        global $xoopsUser, $xoopsModule;

        if (is_numeric($user) && -1 == $user) {
            $user = $xoopsUser;
        }
        if (!is_object($user) && (int)$user < 1) {
            return false;
        }
        $uid = is_object($user) ? $user->getVar('uid') : (int)$user;

        if (!$mid) {
            if (is_object($xoopsModule) && $GLOBALS['artdirname'] == $xoopsModule->getVar('dirname')) {
                $mid = $xoopsModule->getVar('mid');
            } else {
                $moduleHandler = xoops_getHandler('module');
                $art_module    = $moduleHandler->getByDirname($GLOBALS['artdirname']);
                $mid           = $art_module->getVar('mid');
                unset($art_module);
            }
        }

        if (is_object($xoopsModule) && $mid == $xoopsModule->getVar('mid') && is_object($xoopsUser)
            && $uid == $xoopsUser->getVar('uid')) {
            return $GLOBALS['xoopsUserIsAdmin'];
        }

        $memberHandler = xoops_getHandler('member');
        $groups        = $memberHandler->getGroupsByUser($uid);

        $modulepermHandler = xoops_getHandler('groupperm');

        return $modulepermHandler->checkRight('module_admin', $mid, $groups);
    }

    /**
     * Function to check if a user is a moderator of a category
     *
     * @param     $category
     * @param int $user
     * @return bool
     */
    function art_isModerator(&$category, $user = -1)
    {
        global $xoopsUser;

        if (!is_object($category)) {
            $cat_id = (int)$category;
            if (0 == $cat_id) {
                return false;
            }
            $categoryHandler = xoops_getModuleHandler('category', $GLOBALS['artdirname']);
            $category        = $categoryHandler->get($cat_id);
        }

        if (is_numeric($user) && -1 == $user) {
            $user = $xoopsUser;
        }
        if (!is_object($user) && (int)$user < 1) {
            return false;
        }
        $uid = is_object($user) ? $user->getVar('uid') : (int)$user;

        return in_array($uid, $category->getVar('cat_moderator'));
    }

    // Adapted from PMA_getIp() [phpmyadmin project]
    function art_getIP($asString = false)
    {
        return mod_getIP($asString);
    }
endif;
