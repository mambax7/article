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

$category_id = \Xmf\Request::getInt('category', 0, 'GET');
$start       = \Xmf\Request::getInt('start', 0, 'GET');
$from        = (!empty($_GET['from']) || !empty($_POST['from'])) ? 1 : 0;
$type        = empty($_GET['type']) ? '' : mb_strtolower($_GET['type']);

$isAdmin         = art_isAdministrator();
$categoryHandler = $helper->getHandler('Category', $GLOBALS['artdirname']);
$category_obj    = $categoryHandler->get($category_id);
if ((!empty($category_id) && !$categoryHandler->getPermission($category_obj, 'moderate'))
    || (empty($category_id)
        && !$isAdmin)) {
    redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/', 2, art_constant('MD_NOACCESS'));
}

$xoopsOption['xoops_pagetitle']     = $xoopsModule->getVar('name') . ' - ' . art_constant('MD_CPTOPIC');
$template                           = $category_obj->getVar('cat_template');
$xoopsOption['template_main']       = art_getTemplate('cptopic', $template);
$xoopsOption['xoops_module_header'] = art_getModuleHeader($template);
// Disable cache
$xoopsConfig['module_cache'][$xoopsModule->getVar('mid')] = 0;
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/include/vars.php';

$topicHandler = $helper->getHandler('Topic', $GLOBALS['artdirname']);
$tags         = ['top_id', 'top_title', 'top_order', 'top_time', 'top_expire'];
switch ($type) {
    case 'expired':
        $criteria  = new \CriteriaCompo(new \Criteria('top_expire', time(), '<'));
        $type_name = art_constant('MD_EXPIRED');
        break;
    case 'all':
        $criteria  = null;
        $type_name = _ALL;
        break;
    case 'active':
    default:
        $type      = 'active';
        $type_name = art_constant('MD_ACTIVE');
        $criteria  = new \CriteriaCompo(new \Criteria('top_expire', time(), '>'));
        break;
}

$topics_count = $topicHandler->getCountByCategory($category_id, $criteria);
$topics_obj   = $topicHandler->getByCategory($category_id, $helper->getConfig('topics_max'), $start, $criteria, $tags);
$pagenav      = '';
if ($topics_count > $helper->getConfig('articles_perpage')) {
    require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
    $nav     = new \XoopsPageNav($topics_count, $helper->getConfig('topics_max'), $start, 'start', 'category=' . $category_id . '&amp;from=' . $from . '&amp;type=' . $type);
    $pagenav = $nav->renderNav(4);
}

$topics = [];
foreach ($topics_obj as $id => $topic) {
    $topics[$id] = [
        'id'     => $id,
        'title'  => $topic->getVar('top_title'),
        'order'  => $topic->getVar('top_order'),
        'time'   => $topic->getTime($helper->getConfig('timeformat')),
        'expire' => $topic->getExpire(),
    ];
}

$category_data = [];
if (!empty($category_id)) {
    $category_data = [
        'id'    => $category_obj->getVar('cat_id'),
        'title' => $category_obj->getVar('cat_title'),
    ];
}

if (!empty($category_id)) {
    $xoopsTpl->assign('tracks', $categoryHandler->getTrack($category_obj, true));
}
$xoopsTpl->assign('modulename', $xoopsModule->getVar('name'));

$xoopsTpl->assign('category', $category_data);
$xoopsTpl->assign('topics', $topics);
$xoopsTpl->assign('pagenav', $pagenav);
$xoopsTpl->assign('start', $start);
$xoopsTpl->assign('type', $type);
$xoopsTpl->assign('type_name', $type_name);
$xoopsTpl->assign('from', $from);

// Loading module meta data, NOT THE RIGHT WAY DOING IT
$xoopsTpl->assign('xoops_module_header', $xoopsOption['xoops_module_header']);
$xoopsTpl->assign('xoops_pagetitle', $xoopsOption['xoops_pagetitle']);

require_once __DIR__ . '/footer.php';
