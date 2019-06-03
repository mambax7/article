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

// trackback is done by a POST
$art_id     = explode('/', $_SERVER['REQUEST_URI']);
$article_id = (int)$art_id[count($art_id) - 1];
$url        = $_POST['url'];
$title      = $_POST['title'];
$excerpt    = $_POST['excerpt'];
$blog_name  = $_POST['blog_name'];
$charset    = trim($_POST['charset']);

if (2 == $helper->getConfig('trackback_option')) {
    art_trackback_response(1, 'Trackback is closed');
}
if (!mb_strlen($title . $url . $blog_name)) {
    art_trackback_response(1, art_constant('MD_INVALID'));
}

if (!empty($article_id) && !empty($url)) {
    $trackbackHandler = $helper->getHandler('Trackback', $GLOBALS['artdirname']);
    $criteria         = new \CriteriaCompo(new \Criteria('art_id', $article_id));
    $criteria->add(new \Criteria('tb_url', $url));
    if ($trackbackHandler->getCount($criteria) > 0) {
        art_trackback_response(1, 'We already have a ping from that URI for this article.');
    }

    $charset   = empty($charset) ? 'utf-8' : $charset;
    $title     = \XoopsLocal::convert_encoding($title, _CHARSET, $charset);
    $excerpt   = \XoopsLocal::convert_encoding($excerpt, _CHARSET, $charset);
    $blog_name = \XoopsLocal::convert_encoding($blog_name, _CHARSET, $charset);
    $tb_status = (int)$helper->getConfig('trackback_option');

    $trackback_obj = $trackbackHandler->create();
    $trackback_obj->setVar('art_id', $article_id);
    $trackback_obj->setVar('tb_time', time());
    $trackback_obj->setVar('tb_title', $title);
    $trackback_obj->setVar('tb_url', $url);
    $trackback_obj->setVar('tb_excerpt', $excerpt);
    $trackback_obj->setVar('tb_blog_name', $blog_name);
    $trackback_obj->setVar('tb_ip', art_getIP());
    $trackback_obj->setVar('tb_status', $tb_status);

    $result = $trackbackHandler->insert($trackback_obj);

    $criteria = new \CriteriaCompo(new \Criteria('art_id', $article_id));
    $criteria->add(new \Criteria('tb_status', 0, '>'));
    $count          = $trackbackHandler->getCount($criteria);
    $articleHandler = $helper->getHandler('Article', $GLOBALS['artdirname']);
    $article_obj    = $articleHandler->get($article_id);
    if ($count > $article_obj->getVar('art_trackbacks')) {
        $article_obj->setVar('art_trackbacks', $count);
        $articleHandler->insert($article_obj);
    }

    art_trackback_response(0);

    if (!empty($helper->getConfig('notification_enabled')) && $result) {
        $notificationHandler   = xoops_getHandler('notification');
        $tags                  = [];
        $tags['ARTICLE_TITLE'] = $article_obj->getVar('art_title');
        $tags['ARTICLE_URL']   = XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/view.article.php' . URL_DELIMITER . $article_obj->getVar('art_id') . '#tb' . $trackback_obj->getVar('tb_id');
        if ($trackback_obj->getVar('tb_status')) {
            $tags['ARTICLE_ACTION'] = art_constant('MD_NOT_ACTION_TRACKBACK');
            $notificationHandler->triggerEvent('article', $article_id, 'article_monitor', $tags);
            $notificationHandler->triggerEvent('global', 0, 'article_monitor', $tags);
        } else {
            $tags['TRACKBACK_URL'] = XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/cp.trackback.php?article=' . $article_obj->getVar('art_id');
            $notificationHandler->triggerEvent('global', 0, 'article_trackback', $tags);
        }
    }
}
