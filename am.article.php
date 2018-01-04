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

$category_id = empty($_GET['category']) ? (empty($_POST['category']) ? 0 : (int)$_POST['category']) : (int)$_GET['category'];
$topic_id    = empty($_GET['topic']) ? (empty($_POST['topic']) ? 0 : (int)$_POST['topic']) : (int)$_GET['topic'];
$article_id  = empty($_GET['article']) ? (empty($_POST['article']) ? 0 : (int)$_POST['article']) : (int)$_GET['article'];
$start       = empty($_GET['start']) ? (empty($_POST['start']) ? 0 : (int)$_POST['start']) : (int)$_GET['start'];
$type        = empty($_GET['type']) ? (empty($_POST['type']) ? '' : $_POST['type']) : $_GET['type'];
$op          = empty($_GET['op']) ? (empty($_POST['op']) ? '' : $_POST['op']) : $_GET['op'];
$art_id_post = empty($_POST['art_id']) ? [] : $_POST['art_id'];
$top_id_post = empty($_POST['top_id']) ? 0 : $_POST['top_id'];
$from        = (int)(@$_POST['from']);

if (!empty($article_id)) {
    $art_id[] = $article_id;
    $cat_id[] = $category_id;
} else {
    $postdata = [];
    $art_id   = array_keys($art_id_post);
}
$count_artid = count($art_id);

if (0 == $count_artid && empty($article_id)) {
    $redirect = empty($from) ? XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/index.php' : XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/admin/admin.article.php';
    redirect_header($redirect, 2, art_constant('MD_INVALID'));
}

if (!empty($topic_id)) {
    $topicHandler = xoops_getModuleHandler('topic', $GLOBALS['artdirname']);
    $topic_obj    = $topicHandler->get($topic_id);
    $category_id  = $topic_obj->getVar('cat_id');
}

$articleHandler = xoops_getModuleHandler('article', $GLOBALS['artdirname']);
if (!empty($article_id)) {
    $criteria = null;
    if ('approve' !== $op && 'terminate' !== $op) {
        $criteria = new Criteria('ac_publish', 0, '>');
    }
    $article_cats = $articleHandler->getCategoryIds($article_id, $criteria);
    if (!is_array($article_cats) || !in_array($category_id, $article_cats)) {
        $category_id = 0;
    }
}

$categoryHandler = xoops_getModuleHandler('category', $GLOBALS['artdirname']);
$category_obj    = $categoryHandler->get($category_id);

if (!$categoryHandler->getPermission($category_obj, 'moderate')) {
    redirect_header('index.php', 2, art_constant('MD_NOACCESS'));
}

$xoopsOption['xoops_pagetitle'] = $xoopsModule->getVar('name') . ' - ' . art_constant('MD_CPARTICLE');
require_once XOOPS_ROOT_PATH . '/header.php';
include XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/include/vars.php';

$update_topic    = false;
$update_category = false;

if (!empty($topic_id) && 'terminate' === $op) {
    foreach ($art_id as $id) {
        $articleHandler->terminateTopic($id, $topic_id);
    }
    $message      = art_constant('MD_ACTIONDONE');
    $update_topic = true;
} elseif (!empty($category_id)) {
    switch ($op) {
        case 'update_time':
            if ('featured' === $type) {
                $status = 2;
                $time   = time();
            } else {
                $status = 1;
                $time   = 0;
            }
            foreach ($art_id as $id) {
                $articleHandler->setCategoryStatus($id, $category_id, $status, $time);
            }
            break;
        case 'terminate':
            foreach ($art_id as $id) {
                $articleHandler->terminateCategory($id, $category_id);
            }
            $update_topic    = true;
            $update_category = true;
            break;
        case 'approve':
            $valid_id = [];
            foreach ($art_id as $id) {
                $old_category = $articleHandler->getCategoryArray($id);
                if (!empty($old_category[$category_id])) {
                    continue;
                }
                $articleHandler->publishCategory($id, $category_id);
                $valid_id[]      = $id;
                $update_category = true;
            }
            $art_id = $valid_id;

            break;
        case 'feature':
            foreach ($art_id as $id) {
                $old_category =& $articleHandler->getCategoryArray($id);
                if (isset($old_category[$category_id]) && 2 == $old_category[$category_id]) {
                    continue;
                }
                $articleHandler->featureCategory($id, $category_id);
            }
            break;
        case 'unfeature':
            foreach ($art_id as $id) {
                $old_category = $articleHandler->getCategoryArray($id);
                if (!isset($old_category[$category_id]) && $old_category[$category_id] < 2) {
                    continue;
                }
                $articleHandler->unfeatureCategory($id, $category_id);
            }
            break;
        case 'registertopic':
            $valid_id = [];
            $criteria = new Criteria('art_id', '(' . implode(',', $art_id) . ')', 'IN');
            $arts     =& $articleHandler->getAll($criteria, ['uid']);
            $criteria = new CriteriaCompo(new Criteria('1', 1));
            foreach (array_keys($arts) as $aid) {
                $old_topic =& $articleHandler->getTopicIds($aid, $criteria);
                if (in_array($top_id_post, $old_topic)) {
                    continue;
                }
                $articleHandler->registerTopic($arts[$aid], $top_id_post);
                $update_topic = true;
                $valid_id[]   = $aid;
            }
            $art_id = $valid_id;
            break;
    }
    $message = art_constant('MD_ACTIONDONE');
}
if ('rate' === $op) {
    if ($xoopsUserIsAdmin) {
        $art_id_valid = $art_id;
    } else {
        $criteria     = new Criteria('art_id', '(' . implode(',', $art_id) . ')', 'IN');
        $arts         = $articleHandler->getAll($criteria, ['cat_id'], false);
        $art_id_valid = [];
        foreach ($arts as $aid => $art) {
            if (art_isModerator($art['cat_id'])) {
                $art_id_valid[] = $aid;
            }
        }
    }
    if ($art_id_valid) {
        $rateHandler = xoops_getModuleHandler('rate', $GLOBALS['artdirname']);
        $rateHandler->deleteByArticle($art_id_valid);
        $articleHandler->updateAll('art_rating', 0, new Criteria('art_id', '(' . implode(',', $art_id_valid) . ')', 'IN'), true);
        $articleHandler->updateAll('art_rates', 0, new Criteria('art_id', '(' . implode(',', $art_id_valid) . ')', 'IN'), true);
    }
    $message = art_constant('MD_ACTIONDONE');
}
/*
 } elseif (art_isAdministrator()) {
 for ($i=0;$i<$count_artid;++$i) {
 switch ($op) {
 case "terminate":
 $articleHandler->terminateCategory($art_id[$i], $cat_id[$i]);
 $update_topic = true;
 $update_category = true;
 break;
 case "approve":
 $articleHandler->publishCategory($art_id[$i], $cat_id[$i]);
 $update_category = true;

 if (!empty($xoopsModuleConfig['notification_enabled'])) {
 $notificationHandler = xoops_getHandler('notification');
 $tags = array();
 $tags['ARTICLE_ACTION'] = art_constant("MD_NOT_ACTION_PUBLISHED");
 $article_obj = $articleHandler->get($art_id[$i]);
 $category_obj = $categoryHandler->get($cat_id[$i]);
 $tags['ARTICLE_TITLE'] = $article_obj->getVar("art_title");
 $tags['ARTICLE_URL'] = XOOPS_URL . '/modules/' . $GLOBALS["artdirname"] . '/view.article.php'.URL_DELIMITER.'' .$art_id[$i].'/c'.$cat_id[$i];
 $tags['CATEGORY_TITLE'] = $category_obj->getVar("cat_title");
 $notificationHandler->triggerEvent('global', 0, 'article_new', $tags);
 $notificationHandler->triggerEvent('global', 0, 'article_monitor', $tags);
 $notificationHandler->triggerEvent('category', $cat_id[$i], 'article_new', $tags);
 $notificationHandler->triggerEvent('article', $art_id[$i], 'article_approve', $tags);
 unset($article_obj, $category_obj);
 }

 break;
 case "feature":
 $articleHandler->featureCategory($art_id[$i], $cat_id[$i]);

 if (!empty($xoopsModuleConfig['notification_enabled'])) {
 $notificationHandler = xoops_getHandler('notification');
 $tags = array();
 $tags['ARTICLE_ACTION'] = art_constant("MD_NOT_ACTION_FEATURED");
 $article_obj = $articleHandler->get($art_id[$i]);
 $category_obj = $categoryHandler->get($cat_id[$i]);
 $tags['ARTICLE_TITLE'] = $article_obj->getVar("art_title");
 $tags['ARTICLE_URL'] = XOOPS_URL . '/modules/' . $GLOBALS["artdirname"] . '/view.article.php'.URL_DELIMITER.'' .$art_id[$i].'/c'.$cat_id[$i];
 $tags['CATEGORY_TITLE'] = $category_obj->getVar("cat_title");
 $notificationHandler->triggerEvent('global', 0, 'article_new', $tags);
 $notificationHandler->triggerEvent('global', 0, 'article_monitor', $tags);
 $notificationHandler->triggerEvent('category', $cat_id[$i], 'article_new', $tags);
 $notificationHandler->triggerEvent('article', $art_id[$i], 'article_monitor', $tags);
 unset($article_obj, $category_obj);
 }

 break;
 case "unfeature":
 $articleHandler->unfeatureCategory($art_id[$i], $cat_id[$i]);
 break;
 }
 }
 $message = art_constant("MD_ACTIONDONE");
 }
 */

if ($update_topic || $update_category) {
    foreach ($art_id as $id) {
        $art_obj = $articleHandler->get($id);
        if (!is_object($art_obj) || 0 == @$art_obj->getVar('art_id')) {
            continue;
        }
        if ($update_topic) {
            $articleHandler->updateTopics($art_obj);
        }
        if ($update_category) {
            $articleHandler->updateCategories($art_obj);
        }
        unset($art_obj);
    }
}

$redirect = empty($from) ? XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/cp.article.php?category=' . $category_id . '&amp;topic=' . $topic_id . '&amp;start=' . $start . '&amp;type=' . $type : XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/admin/admin.article.php';
$message  = empty($message) ? art_constant('MD_INVALID') : $message;
redirect_header($redirect, 2, $message);

require_once XOOPS_ROOT_PATH . '/footer.php';
