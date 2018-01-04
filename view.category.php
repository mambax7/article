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

/**
 * The notification detection scripts should be removed once absolute url is used in notification_select.php
 *
 */
if (!empty($_POST['not_submit']) && preg_match("/\/notification_update\.php/i", $_SERVER['REQUEST_URI'], $matches)) {
    include XOOPS_ROOT_PATH . '/include/notification_update.php';
    exit();
}

if ($REQUEST_URI_parsed = art_parse_args($args_num, $args, $args_str)) {
    $args['category'] = !empty($args['category']) ? $args['category'] : @$args_num[0];
    $args['type']     = @$args_str[0];
    $args['list']     = ('list' == $args['type']) ? 1 : 0;
    $args['featured'] = ('featured' == $args['type']) ? 1 : 0;
}

$category_id = (int)(empty($_GET['category']) ? @$args['category'] : $_GET['category']);
$start       = (int)(empty($_GET['start']) ? @$args['start'] : $_GET['start']);
$featured    = (int)(!isset($_GET['featured']) ? (empty($args['featured']) ? 0 : 1) : $_GET['featured']);
$list        = (int)(!isset($_GET['list']) ? (empty($args['list']) ? $featured : 1) : $_GET['list']);

$articleHandler  = xoops_getModuleHandler('article', $GLOBALS['artdirname']);
$categoryHandler = xoops_getModuleHandler('category', $GLOBALS['artdirname']);
$category_obj    = $categoryHandler->get($category_id);

/*
 * Global Xoops Entity could be used by blocks or other add-ons
 * Designed by Skalpa for Xoops 2.3+
 */
$xoopsEntity =& $category_obj;
if (empty($category_id) || !$categoryHandler->getPermission($category_obj, 'access')) {
    redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/index.php', 2, art_constant('MD_NOACCESS'));
}

/*
 * Switch to the category's entry article if defined
 */
if ($cat_entry = $category_obj->getVar('cat_entry')) {
    header('location: ' . XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/view.article.php?article=' . $cat_entry . '&category=' . $category_id);
    exit();
}

// restore $_SERVER['REQUEST_URI']
if (!empty($REQUEST_URI_parsed)) {
    $args_REQUEST_URI = [];
    if (!empty($category_id)) {
        $args_REQUEST_URI[] = 'category=' . $category_id;
    }
    if (!empty($start)) {
        $args_REQUEST_URI[] = 'start=' . $start;
    }
    if (!empty($featured)) {
        $args_REQUEST_URI[] = 'featured=1';
    }
    if (!empty($list)) {
        $args_REQUEST_URI[] = 'list=1';
    }
    $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '/modules/' . $GLOBALS['artdirname'] . '/view.category.php')) . '/modules/' . $GLOBALS['artdirname'] . '/view.category.php' . (empty($args_REQUEST_URI) ? '' : '?' . implode('&', $args_REQUEST_URI));
}

// Disable cache for category moderators since we don't have proper cache handling way for them
if (art_isModerator($category_obj)) {
    $xoopsConfig['module_cache'][$xoopsModule->getVar('mid')] = 0;
}
$xoopsOption['xoops_pagetitle']     = $xoopsModule->getVar('name') . ' - ' . $category_obj->getVar('cat_title');
$template                           = $category_obj->getVar('cat_template');
$xoopsOption['template_main']       = art_getTemplate('category', $template);
$xoopsOption['xoops_module_header'] = art_getModuleHeader($template) . '
    <link rel="alternate" type="application/rss+xml" title="' . $xoopsModule->getVar('name') . ' category ' . $category_id . ' rss" href="' . XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/xml.php' . URL_DELIMITER . 'rss/c' . $category_id . '">
    <link rel="alternate" type="application/rss+xml" title="' . $xoopsModule->getVar('name') . ' category ' . $category_id . ' rdf" href="' . XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/xml.php' . URL_DELIMITER . 'rdf/c' . $category_id . '">
    <link rel="alternate" type="application/atom+xml" title="' . $xoopsModule->getVar('name') . ' category ' . $category_id . ' atom" href="' . XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/xml.php' . URL_DELIMITER . 'atom/c' . $category_id . '">
    ';
require_once XOOPS_ROOT_PATH . '/header.php';
include XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/include/vars.php';

$subcategories        = [];
$articles_featured_id = [];
if (empty($list) && empty($featured)) {
    $categories      =& $categoryHandler->getChildCategories($category_id);
    $criteria        = new CriteriaCompo(new Criteria('ac.ac_publish', 0, '>'));
    $counts_article  =& $categoryHandler->getArticleCounts(array_keys($categories), $criteria);
    $counts_category =& $categoryHandler->getCategoryCounts(array_keys($categories), 'access');
    foreach ($categories as $id => $cat) {
        $subcategories[] = [
            'id'         => $cat->getVar('cat_id'),
            'title'      => $cat->getVar('cat_title'),
            'articles'   => @(int)$counts_article[$cat->getVar('cat_id')],
            'categories' => @(int)$counts_category[$cat->getVar('cat_id')]
        ];
    }
    unset($criteria);
    $criteria = new CriteriaCompo(new Criteria('ac.ac_feature', 0, '>'));
    $criteria->setSort('ac.ac_feature');
    $criteria->setOrder('DESC');
    $articles_featured_id = $articleHandler->getIdsByCategory($category_obj, $xoopsModuleConfig['featured_category'], 0, $criteria);
}

if (empty($featured)) {
    $criteria           = new CriteriaCompo(new Criteria('ac.ac_publish', 0, '>'));
    $field_article_time = 'ac.ac_publish';
} else {
    $criteria           = new CriteriaCompo(new Criteria('ac.ac_feature', 0, '>'));
    $field_article_time = 'ac.ac_feature';
}
$articles_perpage = (empty($start)
                     && empty($list)) ? $xoopsModuleConfig['articles_category'] : $xoopsModuleConfig['articles_perpage'];
$criteria->setSort($field_article_time);
$criteria->setOrder('DESC');
$articles_id =& $articleHandler->getIdsByCategory($category_obj, $articles_perpage, $start, $criteria);
$art_ids     = array_merge($articles_featured_id, $articles_id);
$art_ids     = array_unique($art_ids);
if (count($art_ids) > 0) {
    $criteria     = new Criteria('art_id', '(' . implode(',', $art_ids) . ')', 'IN');
    $tags         = [
        'uid',
        'writer_id',
        'art_title',
        'art_summary',
        'art_image',
        'art_pages',
        'art_categories',
        'art_time_publish',
        'art_counter',
        'art_comments',
        'art_trackbacks'
    ];
    $articles_obj = $articleHandler->getAll($criteria, $tags);
} else {
    $articles_obj = [];
}

$author_array = [];
$writer_array = [];
$users        = [];
$writers      = [];
foreach (array_keys($articles_obj) as $id) {
    $author_array[]                                        = $articles_obj[$id]->getVar('uid');
    $writer_array[$articles_obj[$id]->getVar('writer_id')] = 1;
}
$moderators = $category_obj->getVar('cat_moderator');
if (is_array($moderators)) {
    $author_array = array_merge($author_array, $moderators);
}

if (!empty($author_array)) {
    require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/include/functions.author.php';
    $users = art_getAuthorNameFromId($author_array, true, true);
}

if (!empty($writer_array)) {
    require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/include/functions.author.php';
    $writers = art_getWriterNameFromIds(array_keys($writer_array));
}

$category_moderators = [];
foreach ($moderators as $id) {
    $category_moderators[$id] = $users[$id];
}

$articles = [];
foreach ($articles_id as $id) {
    $_article = [
        'id'         => $id,
        'title'      => $articles_obj[$id]->getVar('art_title'),
        'author'     => @$users[$articles_obj[$id]->getVar('uid')],
        'writer'     => @$writers[$articles_obj[$id]->getVar('writer_id')],
        'time'       => $articles_obj[$id]->getTime($xoopsModuleConfig['timeformat']),
        'image'      => $articles_obj[$id]->getImage(),
        'counter'    => $articles_obj[$id]->getVar('art_counter'),
        'comments'   => $articles_obj[$id]->getVar('art_comments'),
        'trackbacks' => $articles_obj[$id]->getVar('art_trackbacks')
    ];
    if (!empty($xoopsModuleConfig['display_summary'])) {
        $_article['summary'] = $articles_obj[$id]->getSummary(true);
    }
    $articles[$id] = $_article;
    unset($_article);
}

$features = [];
if (empty($list) && count($articles_featured_id) > 0) {
    foreach ($articles_featured_id as $id) {
        $_article = $articles[$id];
        if (empty($xoopsModuleConfig['display_summary']) && empty($_article['summary'])) {
            $_article['summary'] = $articles_obj[$id]->getSummary(true);
        }
        $features[] = $_article;
        unset($_article, $articles[$id]);
    }
}

$count_featured = 0;
$topics         = [];
if (empty($list) && empty($featured)) {
    $topicHandler = xoops_getModuleHandler('topic', $GLOBALS['artdirname']);
    $criteria     = new CriteriaCompo(new Criteria('top_expire', time(), '>'));
    $tags         = ['top_title'];
    $topic_array  =& $topicHandler->getByCategory($category_obj->getVar('cat_id'), $xoopsModuleConfig['topics_max'], 0, $criteria, $tags);
    if (count($topic_array) > 0) {
        $counts =& $topicHandler->getArticleCounts(array_keys($topic_array));
        foreach ($topic_array as $id => $topic) {
            $topics[] = [
                'id'       => $id,
                'title'    => $topic->getVar('top_title'),
                'articles' => @(int)$counts[$id]
            ];
        }
    }
    $criteria       = new CriteriaCompo(new Criteria('ac.ac_feature', 0, '>'));
    $count_featured = $categoryHandler->getArticleCount($category_obj, $criteria);
}

if (empty($featured)) {
    $criteria = new CriteriaCompo(new Criteria('ac.ac_publish', 0, '>'));
} else {
    $criteria = new CriteriaCompo(new Criteria('ac.ac_feature', 0, '>'));
}
$count_article    = $categoryHandler->getArticleCount($category_obj, $criteria);
$articles_perpage = $xoopsModuleConfig['articles_perpage'];
$pagenav          = '';
if (!empty($list) && ($count_article > $articles_perpage)) {
    include XOOPS_ROOT_PATH . '/class/pagenav.php';
    $nav     = new XoopsPageNav($count_article, $articles_perpage, $start, 'start', 'category=' . $category_obj->getVar('cat_id') . (empty($list) ? '' : '&amp;list=1') . (empty($featured) ? '' : '&amp;featured=1'));
    $pagenav = $nav->renderNav(4);
} elseif (empty($list) && ($count_article > $xoopsModuleConfig['articles_category'])) {
    $pagenav = '<a href="' . XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/view.category.php' . URL_DELIMITER . $category_obj->getVar('cat_id') . '/list' . (!empty($featured) ? '/featured' : '') . '">' . _MORE . '</a>';
}

$category_data = [];
if (!$category_obj->isNew()) {
    if (empty($list)) {
        $category_data = [
            'id'          => $category_obj->getVar('cat_id'),
            'title'       => $category_obj->getVar('cat_title'),
            'description' => $category_obj->getVar('cat_description'),
            'image'       => $category_obj->getImage(),
            'moderators'  => $category_moderators,
            'articles'    => $count_article
        ];
    } else {
        $category_data = [
            'id'          => $category_obj->getVar('cat_id'),
            'title'       => $category_obj->getVar('cat_title'),
            'description' => $category_obj->getVar('cat_description'),
            'articles'    => $count_article
        ];
    }
}

$xoopsTpl->assign('isadmin', $categoryHandler->getPermission($category_obj, 'moderate'));
$xoopsTpl->assign('ismember', is_object($xoopsUser));

$xoopsTpl->assign('modulename', $xoopsModule->getVar('name'));
$xoopsTpl->assign_by_ref('tracks', $categoryHandler->getTrack($category_obj, true));
$xoopsTpl->assign_by_ref('category', $category_data);
$xoopsTpl->assign_by_ref('features', $features);
$xoopsTpl->assign_by_ref('articles', $articles);
$xoopsTpl->assign_by_ref('categories', $subcategories);
$xoopsTpl->assign_by_ref('topics', $topics);
$xoopsTpl->assign_by_ref('sponsors', $category_obj->getSponsor());

$xoopsTpl->assign_by_ref('pagenav', $pagenav);
$xoopsTpl->assign_by_ref('featured', $featured);
$xoopsTpl->assign('count_featured', $count_featured);

// Loading module meta data, NOT THE RIGHT WAY DOING IT
$xoopsTpl->assign('xoops_module_header', $xoopsOption['xoops_module_header']);
$xoopsTpl->assign('xoops_pagetitle', $xoopsOption['xoops_pagetitle']);

// for notification_select
//$_SERVER['REQUEST_URI'] = XOOPS_URL."/modules/".$GLOBALS["artdirname"]."/view.category.php";
$_GET['category'] = $category_obj->getVar('cat_id');
require_once __DIR__ . '/footer.php';
