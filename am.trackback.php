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

use XoopsModules\Article;

require_once __DIR__ . '/header.php';

/** @var Article\Helper $helper */
$helper = Article\Helper::getInstance();

$category_id  = \Xmf\Request::getInt('category', 0); //empty($_GET['category']) ? (empty($_POST['category']) ? 0 : \Xmf\Request::getInt('category', 0, 'POST')) : \Xmf\Request::getInt('category', 0, 'GET');
$trackback_id = \Xmf\Request::getInt('trackback', 0); //empty($_GET['trackback']) ? (empty($_POST['trackback']) ? 0 : \Xmf\Request::getInt('trackback', 0, 'POST')) : \Xmf\Request::getInt('trackback', 0, 'GET');
$start        = \Xmf\Request::getInt('start', 0); //empty($_GET['start']) ? (empty($_POST['start']) ? 0 : \Xmf\Request::getInt('start', 0, 'POST')) : \Xmf\Request::getInt('start', 0, 'GET');
$op           = \Xmf\Request::getCmd('op', 'default');
$tb_id        = \Xmf\Request::getArray('tb_id', [], 'POST'); //empty($_POST['tb_id']) ? (empty($trackback_id) ? [] : [$trackback_id]) : $_POST['tb_id'];
$from         = \Xmf\Request::hasVar('from', 'POST') ? 1 : 0;

if (empty($tb_id)) {
    $redirect = empty($from) ? XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/index.php' : XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/admin/admin.trackback.php';
    redirect_header($redirect, 2, art_constant('MD_INVALID'));
}

$trackbackHandler = $helper->getHandler('Trackback', $GLOBALS['artdirname']);
if (!empty($trackback_id)) {
    $trackback_obj = $trackbackHandler->get($trackback_id);
}

$categoryHandler = $helper->getHandler('Category', $GLOBALS['artdirname']);
$category_obj    = $categoryHandler->get($category_id);

if (!$categoryHandler->getPermission($category_obj, 'moderate')) {
    redirect_header('index.php', 2, art_constant('MD_NOACCESS'));
}

$xoops_pagetitle                = $xoopsModule->getVar('name') . ' - ' . art_constant('MD_CPTRACKBACK');
$xoopsOption['xoops_pagetitle'] = $xoops_pagetitle;
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/include/vars.php';

switch ($op) {
    case 'approve':
        $trackbackHandler->approveIds($tb_id);
        break;
    case 'delete':
        $trackbackHandler->deleteIds($tb_id);
        break;
}

$articleHandler = $helper->getHandler('Article', $GLOBALS['artdirname']);
foreach ($tb_id as $id) {
    $tb_obj   = $trackbackHandler->get($id);
    $criteria = new \CriteriaCompo(new \Criteria('art_id', $tb_obj->getVar('art_id')));
    $criteria->add(new \Criteria('tb_status', 0, '>'));
    $count       = $trackbackHandler->getCount($criteria);
    $article_obj = $articleHandler->get($tb_obj->getVar('art_id'));
    if (!$article_obj->getVar('art_id')) {
        continue;
    }
    if ($count > $article_obj->getVar('art_trackbacks')) {
        $article_obj->setVar('art_trackbacks', $count);
        $articleHandler->insert($article_obj);
    }

    if (!empty($helper->getConfig('notification_enabled')) && 'approve' === $op) {
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
