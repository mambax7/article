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

include __DIR__ . '/header.php';

/*
 * Set groups for cache purposes
 * Group-based cache is available with XOOPS 2.2*
 * Will be re-implemented in 2.30+
 */




if (!empty($xoopsUser)) {
    $xoopsOption['cache_group'] = implode(',', $xoopsUser->groups());
}

$GLOBALS['xoopsOption']['template_main'] = art_getTemplate('index', $helper->getConfig('template'));
$xoops_module_header                     = art_getModuleHeader($helper->getConfig('template')) . '
    <link rel="alternate" type="application/rss+xml" title="' . $xoopsModule->getVar('name') . ' rss" href="' . XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/xml.php' . URL_DELIMITER . 'rss">
    <link rel="alternate" type="application/rss+xml" title="' . $xoopsModule->getVar('name') . ' rdf" href="' . XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/xml.php' . URL_DELIMITER . 'rdf">
    <link rel="alternate" type="application/atom+xml" title="' . $xoopsModule->getVar('name') . ' atom" href="' . XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/xml.php' . URL_DELIMITER . 'atom">
    ';

$xoopsOption['xoops_module_header'] = $xoops_module_header;
require_once XOOPS_ROOT_PATH . '/header.php';
include XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/include/vars.php';

// Dispatch upon templates
if (!empty($helper->getConfig('template')) && 'default' !== $helper->getConfig('template')) {
    if (@include XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/index.' . $helper->getConfig('template') . '.php') {
        include __DIR__ . '/footer.php';

        return;
    }
}

// Following part will not be executed if cache enabled

// Instantiate the handlers
$categoryHandler = xoops_getModuleHandler('category', $GLOBALS['artdirname']);
$articleHandler  = xoops_getModuleHandler('article', $GLOBALS['artdirname']);

$articles_index_id    = [];
$articles_category_id = [];
$categories_id        = [];

$categories_obj = $categoryHandler->getChildCategories();
if (!empty($categories_obj)):
    $categories_id = array_keys($categories_obj);
    if (!empty($helper->getConfig('articles_category'))) {
        foreach ($categories_obj as $id => $cat_obj) {
            $articles_category_id = array_merge($articles_category_id, $categoryHandler->getLastArticleIds($cat_obj, $helper->getConfig('articles_category')));
        }
    }
endif;

// Get spotlight if enabled && isFirstPage
if (!empty($helper->getConfig('do_spotlight'))) {
    $spotlightHandler     = xoops_getModuleHandler('spotlight', $GLOBALS['artdirname']);
    $sp_data              = $spotlightHandler->getContent();
    $article_spotlight_id = $sp_data['art_id'];
    //$article_spotlight_image = $sp_data["sp_image"];
}
// Get featured articles if enabled && isFirstPage
if ($helper->getConfig('featured_index')) {
    if ('news' === $helper->getConfig('template')) {
        $criteria = new \CriteriaCompo(new \Criteria('ac.ac_publish', 0, '>'));
    } else {
        $criteria = new \CriteriaCompo(new \Criteria('ac.ac_feature', 0, '>'));
    }
    $articles_featured_id = $articleHandler->getIdsByCategory($categories_id, $helper->getConfig('featured_index'), 0, $criteria);
} else {
    $articles_featured_id = [];
}

$art_ids = $articles_featured_id;
if (!empty($article_spotlight_id)) {
    $art_ids[] = $article_spotlight_id;
}
// Ids of spotligh and featured
$art_ids_special = $art_ids;
if (count($articles_index_id)) {
    $art_ids = array_merge($art_ids, $articles_index_id);
}
if (count($articles_category_id)) {
    $art_ids = array_merge($art_ids, $articles_category_id);
}
$art_ids = array_unique($art_ids);
if (count($art_ids) > 0) {
    $criteria     = new \Criteria('art_id', '(' . implode(',', $art_ids) . ')', 'IN');
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
        'art_comments'
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
    $cats     = $articles_obj[$id]->getCategories();
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
// Exclude spotlight and features
$articles_index_id = array_diff($articles_index_id, $art_ids_special);
foreach ($articles_index_id as $id) {
    $_article = $articles[$id];
    if (!empty($helper->getConfig('display_summary')) && empty($_article['summary'])) {
        $_article['summary'] = $articles_obj[$id]->getSummary(true);
    }
    $articles_index[] = $_article;
    unset($_article);
}

$categories = [];
$topics     = [];
if (count($categories_obj) > 0) {
    $criteria        = new \CriteriaCompo(new \Criteria('ac.ac_publish', 0, '>'));
    $counts_article  = $categoryHandler->getArticleCounts(array_keys($categories_obj), $criteria);
    $counts_category = $categoryHandler->getCategoryCounts(array_keys($categories_obj), 'access');

    foreach ($categories_obj as $id => $category) {
        $cat                  = [
            'id'             => $id,
            'title'          => $category->getVar('cat_title'),
            'image'          => $category->getImage(),
            'count_article'  => @(int)$counts_article[$id],
            'count_category' => @(int)$counts_category[$id]
        ];
        $articles_category_id = [];
        if (!empty($helper->getConfig('articles_index'))) {
            $articles_category_id = $categoryHandler->getLastArticleIds($category, $helper->getConfig('articles_index'));
        }
        if (is_array($articles_category_id) && count($articles_category_id) > 0) {
            foreach ($articles_category_id as $art_id) {
                if (!isset($articles[$art_id])) {
                    continue;
                }
                $_article = $articles[$art_id];
                if (!empty($helper->getConfig('display_summary')) && empty($_article['summary'])) {
                    $_article['summary'] = $articles_obj[$art_id]->getSummary(true);
                }
                $cat['articles'][] =& $_article;
                unset($_article);
            }
        }
        $categories[] =& $cat;
        unset($cat);
    }

    $topicHandler = xoops_getModuleHandler('topic', $GLOBALS['artdirname']);
    $criteria     = new \CriteriaCompo(new \Criteria('top_expire', time(), '>'));
    $criteria->setSort('top_time');
    $criteria->setOrder('DESC');
    $tags       = ['top_title'];
    $topics_obj = $topicHandler->getByCategory(array_keys($categories_obj), $helper->getConfig('topics_max'), 0, $criteria, $tags);
    foreach ($topics_obj as $topic) {
        $topics[] = [
            'id'    => $topic->getVar('top_id'),
            'title' => $topic->getVar('top_title')//"description" =>  $topic->getVar("top_description")
        ];
    }
    unset($topics_obj);
}

$sponsors = [];
if (!empty($helper->getConfig('sponsor'))) {
    $sponsors = art_parseLinks($helper->getConfig('sponsor'));
}
unset($articles_obj, $categories_obj);

$xoopsTpl->assign('header', $helper->getConfig('header'));
$xoopsTpl->assign_by_ref('spotlight', $spotlight);
$xoopsTpl->assign_by_ref('features', $features);
$xoopsTpl->assign_by_ref('articles', $articles_index);
$xoopsTpl->assign_by_ref('categories', $categories);
$xoopsTpl->assign_by_ref('topics', $topics);
$xoopsTpl->assign_by_ref('sponsors', $sponsors);

// Loading module meta data, NOT THE RIGHT WAY DOING IT
$xoopsTpl->assign('xoops_module_header', $xoopsOption['xoops_module_header']);

require_once __DIR__ . '/footer.php';
