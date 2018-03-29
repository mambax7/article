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

//if (!defined('XOOPS_ROOT_PATH') || !is_object($xoopsModule)) {
//    return false;
//    exit();
//}

if (art_parse_args($args_num, $args, $args_str)) {
    $args['start'] = @$args_num[0];
}
$start = (int)(empty($_GET['start']) ? @$args['start'] : $_GET['start']);

$categoryHandler = xoops_getModuleHandler('category', $GLOBALS['artdirname']);
$articleHandler  = xoops_getModuleHandler('article', $GLOBALS['artdirname']);

if (!$categories_obj = $categoryHandler->getAllByPermission('access', ['cat_title', 'cat_pid'])) {
    redirect_header('index.php', 2, art_constant('MD_NOACCESS'));
}
$categories_id = array_keys($categories_obj);

// Get spotlight if enabled && isFirstPage
if (empty($start) && !empty($helper->getConfig('do_spotlight'))) {
    $spotlightHandler     = xoops_getModuleHandler('spotlight', $GLOBALS['artdirname']);
    $sp_data              = $spotlightHandler->getContent();
    $article_spotlight_id = $sp_data['art_id'];
}
// Get featured articles if enabled && isFirstPage
if (empty($start) && $helper->getConfig('featured_index')) {
    $criteria             = new \CriteriaCompo(new \Criteria('ac.ac_feature', 0, '>'));
    $articles_featured_id = $articleHandler->getIdsByCategory($categories_id, $helper->getConfig('featured_index'), 0, $criteria);
} else {
    $articles_featured_id = [];
}

$art_ids_special = $articles_featured_id;
if (!empty($article_spotlight_id)) {
    $art_ids_special[] = $article_spotlight_id;
}
$art_criteria   = new \CriteriaCompo(new \Criteria('ac.ac_publish', 0, '>'));
$articles_count = $articleHandler->getCountByCategory($categories_id, $art_criteria);

if (!empty($art_ids_special)) {
    $art_criteria->add(new \Criteria('ac.art_id', '(' . implode(',', $art_ids_special) . ')', 'NOT IN'));
}
$art_ids_index = $articleHandler->getIdsByCategory($categories_id, $helper->getConfig('articles_perpage'), $start, $art_criteria);

// Ids of spotligh and featured
$art_ids = array_unique(array_merge($art_ids_index, $art_ids_special));
if (count($art_ids) > 0) {
    $criteria = new \Criteria('art_id', '(' . implode(',', $art_ids) . ')', 'IN');
    $tags     = [
        'uid',
        'writer_id',
        'art_title',
        'art_image',
        'art_pages',
        'art_categories',
        'art_time_publish',
        'art_counter',
        'art_comments',
        'art_trackbacks'
    ];
    if (!empty($helper->getConfig('display_summary'))) {
        $tags[] = 'art_summary';
    }
    $articles_obj = $articleHandler->getAll($criteria, $tags);
} else {
    $articles_obj = [];
}

$author_array = [];
$writer_array = [];
$users        = [];
$writers      = [];
foreach (array_keys($articles_obj) as $id) {
    $author_array[$articles_obj[$id]->getVar('uid')]       = 1;
    $writer_array[$articles_obj[$id]->getVar('writer_id')] = 1;
}
if (!empty($author_array)) {
    require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/include/functions.author.php';
    $users = art_getAuthorNameFromId(array_keys($author_array), true, true);
}

if (!empty($writer_array)) {
    require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/include/functions.author.php';
    $writers = art_getWriterNameFromIds(array_keys($writer_array));
}

$articles = [];
foreach (array_keys($articles_obj) as $id) {
    $_article = [
        'id'      => $id,
        'title'   => $articles_obj[$id]->getVar('art_title'),
        'author'  => @$users[$articles_obj[$id]->getVar('uid')],
        'writer'  => @$writers[$articles_obj[$id]->getVar('writer_id')],
        'time'    => $articles_obj[$id]->getTime($helper->getConfig('timeformat')),
        'image'   => $articles_obj[$id]->getImage(),
        'counter' => $articles_obj[$id]->getVar('art_counter'),
        'summary' => $articles_obj[$id]->getSummary(!empty($helper->getConfig('display_summary')))
    ];
    $cats     = array_unique($articles_obj[$id]->getCategories());
    foreach ($cats as $catid) {
        if (0 == $catid || !isset($categories_obj[$catid])) {
            continue;
        }
        $_article['categories'][$catid] = [
            'id'    => $catid,
            'title' => $categories_obj[$catid]->getVar('cat_title')
        ];
    }
    $articles[$id] = $_article;
    unset($_article);
}

$spotlight = [];
if (!empty($article_spotlight_id) && isset($articles[$article_spotlight_id])) {
    $spotlight         = $articles[$article_spotlight_id];
    $spotlight['note'] = $sp_data['sp_note'];
    if (empty($helper->getConfig('display_summary')) && empty($spotlight['summary'])) {
        $spotlight['summary'] = $articles_obj[$article_spotlight_id]->getSummary(true);
    }
    if (empty($spotlight['image'])) {
        $spotlight['image'] = $sp_data['image'];
    }
}

// an article can only be marked as feature from its basic category
$features = [];
foreach ($articles_featured_id as $id) {
    $_article = $articles[$id];
    if (empty($helper->getConfig('display_summary')) && empty($_article['summary'])) {
        $_article['summary'] = $articles_obj[$id]->getSummary(true);
    }
    $features[] = $_article;
    unset($_article);
}

$articles_index = [];
foreach ($art_ids_index as $id) {
    $articles_index[] =& $articles[$id];
}

foreach (array_keys($categories_obj) as $id) {
    if ($categories_obj[$id]->getVar('cat_pid')) {
        unset($categories_obj[$id]);
    }
}

$categories = [];
$topics     = [];

if (empty($start)):

    if (count($categories_obj) > 0) {
        $criteria        = new \CriteriaCompo(new \Criteria('ac.ac_publish', 0, '>'));
        $counts_article  = $categoryHandler->getArticleCounts(array_keys($categories_obj), $criteria);
        $counts_category = $categoryHandler->getCategoryCounts(array_keys($categories_obj), 'access');

        foreach ($categories_obj as $id => $category) {
            $categories[] = [
                'id'         => $id,
                'title'      => $category->getVar('cat_title'),
                'articles'   => @(int)$counts_article[$id],
                'categories' => @(int)$counts_category[$id]
            ];
        }
    }
endif;

unset($articles_obj, $categories_obj);

if ($articles_count > $helper->getConfig('articles_perpage')) {
    include XOOPS_ROOT_PATH . '/class/pagenav.php';
    $nav     = new \XoopsPageNav($articles_count, $helper->getConfig('articles_perpage'), $start, 'start');
    $pagenav = $nav->renderNav(4);
} else {
    $pagenav = '';
}

$xoopsTpl->assign('header', $helper->getConfig('header'));

$xoopsTpl->assign_by_ref('spotlight', $spotlight);
$xoopsTpl->assign_by_ref('features', $features);
$xoopsTpl->assign_by_ref('articles', $articles_index);
$xoopsTpl->assign_by_ref('categories', $categories);
$xoopsTpl->assign_by_ref('pagenav', $pagenav);

// Loading module meta data, NOT THE RIGHT WAY DOING IT
$xoopsTpl->assign('xoops_module_header', $xoopsOption['xoops_module_header']);
$xoopsTpl->assign('xoops_pagetitle', $xoopsOption['xoops_pagetitle']);

require_once __DIR__ . '/footer.php';
