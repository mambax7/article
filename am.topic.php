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
require_once __DIR__ . '/header.php';

$category_id = Xmf\Request::getInt('category', 0);
$topic_id    = Xmf\Request::getInt('topic', 0);
$start       = \Xmf\Request::getInt('start', 0);
$op          = \Xmf\Request::getCmd('op', '');
$top_id      = \Xmf\Request::getInt('top_id', (empty($topic_id) ? false : [$topic_id]), 'POST');
$top_order   = \Xmf\Request::getInt('top_order', false, 'POST');
$from        = \Xmf\Request::hasVar('from', 'POST') ? 1 : 0;
$helper      = \XoopsModules\Article\Helper::getInstance();

if (empty($top_id) && empty($topic_id)) {
    $redirect = empty($from) ? XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/index.php' : XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/admin/admin.topic.php';
    redirect_header($redirect, 2, art_constant('MD_INVALID'));
}

$topicHandler = $helper->getHandler('Topic', $GLOBALS['artdirname']);
if (!empty($topic_id)) {
    $topic_obj   = $topicHandler->get($topic_id);
    $category_id = $topic_obj->getVar('cat_id');
}

$categoryHandler = $helper->getHandler('Category', $GLOBALS['artdirname']);
$category_obj    = $categoryHandler->get($category_id);
if (!$categoryHandler->getPermission($category_obj, 'moderate')) {
    $redirect = empty($from) ? XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/cp.topic.php?category=' . $category_id : XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/admin/admin.topic.php';
    redirect_header($redirect, 2, art_constant('MD_NOACCESS'));
}

$xoops_pagetitle                = $xoopsModule->getVar('name') . ' - ' . art_constant('MD_CPTOPIC');
$xoopsOption['xoops_pagetitle'] = $xoops_pagetitle;
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/include/vars.php';

switch ($op) {
    case 'delete':
        if (empty($_POST['confirm_submit'])) {
            $hiddens['topic']    = $topic_id;
            $hiddens['op']       = 'delete';
            $hiddens['from']     = $from;
            $hiddens['start']    = $start;
            $hiddens['category'] = $category_id;
            $action              = XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/am.topic.php';
            $msg                 = _DELETE . ': ' . $topic_obj->getVar('top_title');
            require_once XOOPS_ROOT_PATH . '/header.php';
            require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/include/vars.php';
            xoops_confirm($hiddens, $action, $msg);
            require_once XOOPS_ROOT_PATH . '/footer.php';
            exit();
        }
        $topicHandler->delete($topic_obj);

        break;
    case 'order':
        for ($i = 0, $iMax = count($top_id); $i < $iMax; ++$i) {
            $top_obj = $topicHandler->get($top_id[$i]);
            if ($top_obj[$i] != $top_obj->getVar('top_order')) {
                $top_obj->setVar('top_order', $top_order[$i]);
                $topicHandler->insert($top_obj);
            }
            unset($top_obj);
        }
        break;
    default:
        break;
}

$redirect = empty($from) ? XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/cp.topic.php?category=' . $category_id . '&amp;start=' . $start : XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/admin/admin.topic.php';
redirect_header($redirect, 2, art_constant('MD_ACTIONDONE'));

require_once __DIR__ . '/footer.php';
