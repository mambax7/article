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
$topic_id    = \Xmf\Request::getInt('topic', 0, 'GET');
$start       = \Xmf\Request::getInt('start', 0, 'GET');
$from        = (!empty($_GET['from']) || !empty($_POST['from'])) ? 1 : 0;

$categoryHandler = $helper->getHandler('Category', $GLOBALS['artdirname']);
$topicHandler    = $helper->getHandler('Topic', $GLOBALS['artdirname']);

$isAdmin = art_isAdministrator();

if (!empty($topic_id)) {
    $topic_obj    = $topicHandler->get($topic_id);
    $category_id  = $topic_obj->getVar('cat_id');
    $category_obj = $categoryHandler->get($category_id);
    $allowed_type = ['all'];
} else {
    if (!empty($category_id)) {
        $category_obj   = $categoryHandler->get($category_id);
        $categories_obj = [$category_id => $category_obj];
    } else {
        $categories_obj = $categoryHandler->getAllByPermission('moderate', ['cat_title', 'cat_pid']);
    }
    $categories_id = array_keys($categories_obj);
    $topic_obj     = false;
    $allowed_type  = ['published', 'registered', 'featured', 'submitted', 'all'];
}

if ((!empty($category_id) && !$categoryHandler->getPermission($category_obj, 'moderate'))
    || (empty($category_id)
        && !$isAdmin)) {
    redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/', 2, art_constant('MD_NOACCESS'));
}

$xoopsOption['xoops_pagetitle']     = $xoopsModule->getVar('name') . ' - ' . art_constant('MD_CPARTICLE');
$template                           = empty($topic_obj) ? (empty($category_obj) ? $helper->getConfig('template') : $category_obj->getVar('cat_template')) : $topic_obj->getVar('top_template');
$xoopsOption['template_main']       = art_getTemplate('cparticle', $template);
$xoopsOption['xoops_module_header'] = art_getModuleHeader($template);
// Disable cache
$xoopsConfig['module_cache'][$xoopsModule->getVar('mid')] = 0;
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/include/vars.php';

$type       = empty($_GET['type']) ? '' : (in_array($_GET['type'], $allowed_type) ? $_GET['type'] : '');
$byCategory = true;
switch ($type) {
    case 'submitted':
        $type_name = art_constant('MD_SUBMITTED');
        $criteria  = new \CriteriaCompo(new \Criteria('art_time_publish', 0));
        $criteria->add(new \Criteria('art_time_submit', 0, '>'));
        $byCategory = false;
        break;
    case 'registered':
        $type_name = art_constant('MD_REGISTERED');
        $criteria  = new \CriteriaCompo(new \Criteria('ac.ac_publish', 0));
        break;
    case 'featured':
        $type_name = art_constant('MD_FEATURED');
        $criteria  = new \CriteriaCompo(new \Criteria('ac.ac_feature', 0, '>'));
        break;
    case 'published':
        $type      = 'published';
        $type_name = art_constant('MD_PUBLISHED');
        $criteria  = new \CriteriaCompo(new \Criteria('ac.ac_publish', 0, '>'));
        $criteria->add(new \Criteria('ac.ac_feature', 0));
        break;
    case 'all':
    default:
        $type_name = _ALL;
        $criteria  = new \CriteriaCompo();
        break;
}

$articleHandler = $helper->getHandler('Article', $GLOBALS['artdirname']);

if (!empty($topic_id)) {
    $articles_count = $topicHandler->getArticleCount($topic_id);
    $tags           = ['a.art_summary', 'a.art_title', 'a.uid', 'at.at_time', 'a.cat_id'];
    $articles_array = $articleHandler->getByTopic($topic_id, $helper->getConfig('articles_perpage'), $start, null, $tags, false);
} elseif ($byCategory) {
    $articles_count = $categoryHandler->getArticleCount($categories_id, $criteria);
    $tags           = [
        'a.cat_id AS basic_cat_id',
        'a.art_summary',
        'a.art_title',
        'a.uid',
        'a.art_time_submit',
        'a.art_time_publish',
        'ac.cat_id',
        'ac.ac_register',
        'ac.ac_publish',
        'ac.ac_feature',
    ];
    $articles_array = $categoryHandler->getArticles($categories_id, $helper->getConfig('articles_perpage'), $start, $criteria, $tags, false);
} else {
    $articles_count = $articleHandler->getCount($criteria);
    $tags           = [
        'cat_id',
        'art_summary',
        'art_title',
        'art_time_submit',
        'art_time_publish',
        'art_summary',
        'uid',
    ];
    $criteria->setStart($start);
    $criteria->setLimit($helper->getConfig('articles_perpage'));
    $articles_array = $articleHandler->getAll($criteria, $tags, false);
}

$articles = [];
if (count($articles_array) > 0) {
    $author_array = [];
    foreach ($articles_array as $id => $artcile) {
        if ($artcile['uid'] > 0) {
            $author_array[$artcile['uid']] = 1;
        }
    }

    if (!empty($author_array)) {
        mod_loadFunctions('author');
        $users = art_getAuthorNameFromId(array_keys($author_array), true, true);
    }

    foreach ($articles_array as $id => $article) {
        $_article = [
            'id'                => $article['art_id'],
            'cat_id'            => empty($article['basic_cat_id']) ? $article['cat_id'] : $article['basic_cat_id'],
            'title'             => $article['art_title'],
            'submit'            => art_formatTimestamp(@$article['art_time_submit']),
            'publish'           => art_formatTimestamp(@$article['art_time_publish']),
            'register_category' => art_formatTimestamp(@$article['ac_register']),
            'time_topic'        => art_formatTimestamp(@$article['at_time']),
            'summary'           => $article['art_summary'],
            'author'            => $users[$article['uid']],
        ];
        if (!empty($article['ac_feature'])) {
            $_article['feature_category'] = art_formatTimestamp($article['ac_feature']);
        }
        if (!empty($article['ac_publish'])) {
            $_article['publish_category'] = art_formatTimestamp($article['ac_publish']);
        }

        if (!empty($category_obj)) {
            $_article['category'] = ['id' => $category_obj->getVar('cat_id')];
        } else {
            $_article['category'] = [
                'id'    => $article['cat_id'],
                'title' => $categories_obj[$article['cat_id']]->getVar('cat_title'),
            ];
        }
        if ((!empty($category_obj) && $category_obj->getVar('cat_id') == @$article['cat_id']) || $isAdmin) {
            $_article['admin'] = 1;
        }
        $articles[] = $_article;
        unset($_article);
    }
}

if ($articles_count > $helper->getConfig('articles_perpage')) {
    require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
    $nav     = new \XoopsPageNav($articles_count, $helper->getConfig('articles_perpage'), $start, 'start', 'category=' . $category_id . '&amp;topic=' . $topic_id . '&amp;type=' . $type . '&amp;from=' . $from);
    $pagenav = $nav->renderNav(4);
} else {
    $pagenav = '';
}

$category_data = [];
$topic_data    = [];
if (!empty($topic_obj)) {
    $topic_data = [
        'id'          => $topic_id,
        'title'       => $topic_obj->getVar('top_title'),
        'description' => $topic_obj->getVar('top_description'),
        'articles'    => $articles_count,
    ];
} elseif (!empty($category_obj)) {
    $category_data = [
        'id'          => $category_obj->getVar('cat_id'),
        'title'       => $category_obj->getVar('cat_title'),
        'description' => $category_obj->getVar('cat_description'),
        'articles'    => $articles_count,
    ];
}

$categories = [];
$topics     = [];
if (empty($topic_obj)) {
    $subCategories_obj = $categoryHandler->getChildCategories($category_id, 'moderate');
    foreach ($subCategories_obj as $id => $cat) {
        $categories[] = [
            'id'    => $id,
            'title' => $cat->getVar('cat_title'),
        ];
    }
    unset($subCategories_obj);
    if (!empty($category_id)) {
        $criteria   = new \CriteriaCompo(new \Criteria('top_expire', time(), '>'));
        $topics_obj = $topicHandler->getByCategory($category_id, $helper->getConfig('topics_max'), 0, $criteria, ['top_title']);
        foreach ($topics_obj as $id => $topic) {
            $topics[] = [
                'id'    => $id,
                'title' => $topic->getVar('top_title'),
            ];
        }
        unset($topics_obj);
    }
}

if (!empty($category_obj)) {
    $xoopsTpl->assign('tracks', $categoryHandler->getTrack($category_obj, true));
}

$xoopsTpl->assign('modulename', $xoopsModule->getVar('name'));
$xoopsTpl->assign('category', $category_data);
$xoopsTpl->assign('topic', $topic_data);
$xoopsTpl->assign('articles', $articles);
$xoopsTpl->assign('pagenav', $pagenav);
$xoopsTpl->assign('start', $start);
$xoopsTpl->assign('type', $type);
$xoopsTpl->assign('type_name', $type_name);
$xoopsTpl->assign('categories', $categories);
$xoopsTpl->assign('topics', $topics);
$xoopsTpl->assign('from', $from);

// Loading module meta data, NOT THE RIGHT WAY DOING IT
$xoopsTpl->assign('xoops_module_header', $xoopsOption['xoops_module_header']);
$xoopsTpl->assign('xoops_pagetitle', $xoopsOption['xoops_pagetitle']);

require_once __DIR__ . '/footer.php';
