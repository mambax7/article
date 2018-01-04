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

include __DIR__ . '/header.php';

$isadmin = art_isAdministrator();
if (!$isadmin) {
    redirect_header('index.php', 2, art_constant('MD_NOACCESS'));
}

$category_id = empty($_GET['category']) ? (empty($_POST['category']) ? 0 : (int)$_POST['category']) : (int)$_GET['category'];
$op          = empty($_GET['op']) ? (empty($_POST['op']) ? '' : $_POST['op']) : $_GET['op'];
$cat_id      = empty($_POST['cat_id']) ? false : $_POST['cat_id'];
$cat_order   = empty($_POST['cat_order']) ? false : $_POST['cat_order'];
$from        = empty($_POST['from']) ? 0 : 1;

if (empty($cat_id) && empty($category_id)) {
    $redirect = empty($from) ? XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/index.php' : XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/admin/admin.category.php';
    redirect_header($redirect, 2, art_constant('MD_INVALID'));
}

$xoops_pagetitle                = $xoopsModule->getVar('name') . ' - ' . art_constant('MD_CPCATEGORY');
$xoopsOption['xoops_pagetitle'] = $xoops_pagetitle;
require_once XOOPS_ROOT_PATH . '/header.php';
include XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/include/vars.php';

$categoryHandler = xoops_getModuleHandler('category', $GLOBALS['artdirname']);
$message         = art_constant('MD_ACTIONDONE');

switch ($op) {
    case 'delete':
        $category_obj = $categoryHandler->get($category_id);

        if (empty($_POST['confirm_submit'])) {
            $hiddens['category'] = $category_id;
            $hiddens['op']       = $op;
            $hiddens['from']     = $from;
            $action              = XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/am.category.php';
            $msg                 = _DELETE . ': ' . $category_obj->getVar('cat_title');
            $msg                 .= '<br>' . (empty($xoopsModuleConfig['category_delete_forced']) ? art_constant('MD_MOVE_CATEGORYANDARTICLE') : art_constant('MD_DELETE_CATEGORYANDARTICLE')) . '<br>' . art_constant('MD_CONFIG_CATEGORYANDARTICLE');
            require_once XOOPS_ROOT_PATH . '/header.php';
            xoops_confirm($hiddens, $action, $msg);
            require_once XOOPS_ROOT_PATH . '/footer.php';
            exit();
        }

        $categoryHandler->delete($category_obj, true, @$xoopsModuleConfig['category_delete_forced']);
        break;
    case 'order':
        for ($i = 0, $iMax = count($cat_id); $i < $iMax; ++$i) {
            $cat_obj = $categoryHandler->get($cat_id[$i]);
            if ($cat_order[$i] != $cat_obj->getVar('cat_order')) {
                $cat_obj->setVar('cat_order', $cat_order[$i]);
                $categoryHandler->insert($cat_obj);
            }
            unset($cat_obj);
        }
        break;
}

$redirect = empty($from) ? XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/cp.category.php' : XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/admin/admin.category.php';
redirect_header($redirect, 2, $message);

require_once __DIR__ . '/footer.php';
