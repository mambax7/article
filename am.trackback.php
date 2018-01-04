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

$category_id  = empty($_GET['category']) ? (empty($_POST['category']) ? 0 : (int)$_POST['category']) : (int)$_GET['category'];
$trackback_id = empty($_GET['trackback']) ? (empty($_POST['trackback']) ? 0 : (int)$_POST['trackback']) : (int)$_GET['trackback'];
$start        = empty($_GET['start']) ? (empty($_POST['start']) ? 0 : (int)$_POST['start']) : (int)$_GET['start'];
$op           = empty($_GET['op']) ? (empty($_POST['op']) ? '' : $_POST['op']) : $_GET['op'];
$tb_id        = empty($_POST['tb_id']) ? (empty($trackback_id) ? [] : [$trackback_id]) : $_POST['tb_id'];
$from         = empty($_POST['from']) ? 0 : 1;

if (empty($tb_id)) {
    $redirect = empty($from) ? XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/index.php' : XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/admin/admin.trackback.php';
    redirect_header($redirect, 2, art_constant('MD_INVALID'));
}

$trackbackHandler = xoops_getModuleHandler('trackback', $GLOBALS['artdirname']);
if (!empty($trackback_id)) {
    $trackback_obj = $trackbackHandler->get($trackback_id);
}

$categoryHandler = xoops_getModuleHandler('category', $GLOBALS['artdirname']);
$category_obj    = $categoryHandler->get($category_id);

if (!$categoryHandler->getPermission($category_obj, 'moderate')) {
    redirect_header('index.php', 2, art_constant('MD_NOACCESS'));
}

$xoops_pagetitle                = $xoopsModule->getVar('name') . ' - ' . art_constant('MD_CPTRACKBACK');
$xoopsOption['xoops_pagetitle'] = $xoops_pagetitle;
require_once XOOPS_ROOT_PATH . '/header.php';
include XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/include/vars.php';

switch ($op) {
    case 'approve':
        $trackbackHandler->approveIds($tb_id);
        break;
    case 'delete':
        $trackbackHandler->deleteIds($tb_id);
        break;
}

$articleHandler = xoops_getModuleHandler('article', $GLOBALS['artdirname']);
foreach ($tb_id as $id) {
    $tb_obj   = $trackbackHandler->get($id);
    $criteria = new CriteriaCompo(new Criteria('art_id', $tb_obj->getVar('art_id')));
    $criteria->add(new Criteria('tb_status', 0, '>'));
    $count       = $trackbackHandler->getCount($criteria);
    $article_obj = $articleHandler->get($tb_obj->getVar('art_id'));
    if (!$article_obj->getVar('art_id')) {
        continue;
    }
    if ($count > $article_obj->getVar('art_trackbacks')) {
        $article_obj->setVar('art_trackbacks', $count);
        $articleHandler->insert($article_obj);
    }

    if (!empty($xoopsModuleConfig['notification_enabled']) && 'approve' === $op) {
        $notificationHandler    = xoops_getHandler('notification');
        $tags                   = [];
        $tags['ARTICLE_TITLE']  = $article_obj->getVar('art_title');
        $tags['ARTICLE_URL']    = XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/view.article.php' . URL_DELIMITER . $article_obj->getVar('art_id') . '#tb' . $tb_obj->getVar('tb_id');
        $tags['ARTICLE_ACTION'] = art_constant('MD_NOT_ACTION_TRACKBACK');
        $notificationHandler->triggerEvent('article', $article_id, 'article_monitor', $tags);
        $notificationHandler->triggerEvent('global', 0, 'article_monitor', $tags);
    }
    unset($tb_obj);
    unset($article_obj);
}

$message  = art_constant('MD_ACTIONDONE');
$redirect = empty($from) ? XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/cp.trackback.php?category=' . $category_id . '&amp;start=' . $start : XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/admin/admin.trackback.php';
redirect_header($redirect, 2, $message);

require_once __DIR__ . '/footer.php';
