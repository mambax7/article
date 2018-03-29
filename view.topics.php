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
/** @var Article\Helper $helper */
$helper = Article\Helper::getInstance();

include __DIR__ . '/header.php';

if (art_parse_args($args_num, $args, $args_str)) {
    $args['category'] = !empty($args['category']) ? $args['category'] : @$args_num[0];
}

$category_id = (int)(empty($_GET['category']) ? @$args['category'] : $_GET['category']);
$start       = (int)(empty($_GET['start']) ? @$args['start'] : $_GET['start']);

$topicHandler    = xoops_getModuleHandler('topic', $GLOBALS['artdirname']);
$categoryHandler = xoops_getModuleHandler('category', $GLOBALS['artdirname']);
$category        = $categoryHandler->get($category_id);
if (!$categoryHandler->getPermission($category, 'access')) {
    redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/index.php', 2, art_constant('MD_NOACCESS'));
}

if (!empty($xoopsUser)) {
    $xoopsOption['cache_group'] = implode(',', $xoopsUser->groups());
}
$xoopsOption['xoops_pagetitle']     = $xoopsModule->getVar('name') . ' - ' . $category->getVar('cat_title');
$template                           = $category->getVar('cat_template');
$xoopsOption['template_main']       = art_getTemplate('topics', $template);
$xoopsOption['xoops_module_header'] = art_getModuleHeader($template);
require_once XOOPS_ROOT_PATH . '/header.php';
include XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/include/vars.php';

$topicHandler = xoops_getModuleHandler('topic', $GLOBALS['artdirname']);
$criteria     = new \CriteriaCompo(new \Criteria('top_expire', time(), '>'));
$criteria->setSort('top_time');
$criteria->setOrder('DESC');
$topic_array =& $topicHandler->getByCategory($category_id, $helper->getConfig('topics_max'), 0, $criteria);
$topics      = [];
if (count($topic_array) > 0) {
    $counts =& $topicHandler->getArticleCounts(array_keys($topic_array));
    foreach ($topic_array as $id => $topic) {
        $topics[] = [
            'id'          => $id,
            'title'       => $topic->getVar('top_title'),
            'description' => $topic->getVar('top_description'),
            'articles'    => @(int)$counts[$id]
        ];
    }
}

$count_topic = $topicHandler->getCountByCategory($category_id, $criteria);
if ($count_topic > $helper->getConfig('articles_perpage')) {
    include XOOPS_ROOT_PATH . '/class/pagenav.php';
    $nav     = new \XoopsPageNav($count_topic, $helper->getConfig('topics_max'), $start, 'start', 'category=' . $category_id);
    $pagenav = $nav->renderNav(5);
} else {
    $pagenav = '';
}

$category_data = [];
if (!$category->isNew()) {
    $category_data = [
        'id'          => $category->getVar('cat_id'),
        'title'       => $category->getVar('cat_title'),
        'description' => $category->getVar('cat_description')
    ];
}
$xoopsTpl->assign('modulename', $xoopsModule->getVar('name'));
$xoopsTpl->assign('tracks', $categoryHandler->getTrack($category));
$xoopsTpl->assign_by_ref('category', $category_data);
$xoopsTpl->assign_by_ref('topics', $topics);
$xoopsTpl->assign_by_ref('pagenav', $pagenav);

// Loading module meta data, NOT THE RIGHT WAY DOING IT
$xoopsTpl->assign('xoops_module_header', $xoopsOption['xoops_module_header']);
$xoopsTpl->assign('xoops_pagetitle', $xoopsOption['xoops_pagetitle']);

require_once __DIR__ . '/footer.php';
