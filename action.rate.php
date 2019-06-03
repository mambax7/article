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

$rate        = (int)(@$_POST['rate']);
$article_id  = (int)(@$_POST['article']);
$category_id = (int)(@$_POST['category']);
$page        = (int)(@$_POST['page']);
$helper      = \XoopsModules\Article\Helper::getInstance();
if (empty($article_id)) {
    redirect_header('javascript:history.go(-1);', 1, art_constant('MD_INVALID'));
}

$articleHandler  = $helper->getHandler('Article', $GLOBALS['artdirname']);
$categoryHandler = $helper->getHandler('Category', $GLOBALS['artdirname']);
$article_obj     = $articleHandler->get($article_id);
if (!$categoryHandler->getPermission($category_id, 'rate')) {
    $message = art_constant('MD_NOACCESS');
} else {
    $uid      = is_object($xoopsUser) ? $xoopsUser->getVar('uid') : 0;
    $criteria = new \CriteriaCompo(new \Criteria('art_id', $article_id));
    $ip       = art_getIP();
    if ($uid > 0) {
        $criteria->add(new \Criteria('uid', $uid));
    } else {
        $criteria->add(new \Criteria('rate_ip', $ip));
        $criteria->add(new \Criteria('rate_time', time() - 24 * 3600, '>'));
    }
    $rateHandler = $helper->getHandler('Rate', $GLOBALS['artdirname']);
    if ($count = $rateHandler->getCount($criteria)) {
        $message = art_constant('MD_ALREADYRATED');
    } else {
        $rate_obj = $rateHandler->create();
        $rate_obj->setVar('art_id', $article_id);
        $rate_obj->setVar('uid', $uid);
        $rate_obj->setVar('rate_ip', $ip);
        $rate_obj->setVar('rate_rating', $rate);
        $rate_obj->setVar('rate_time', time());
        if (!$rate_id = $rateHandler->insert($rate_obj)) {
            redirect_header('javascript:history.go(-1);', 1, art_constant('MD_NOTSAVED'));
        }
        $article_obj = $articleHandler->get($article_id);
        $article_obj->setVar('art_rating', $article_obj->getVar('art_rating') + $rate, true);
        $article_obj->setVar('art_rates', $article_obj->getVar('art_rates') + 1, true);
        $articleHandler->insert($article_obj, true);
        $message = art_constant('MD_ACTIONDONE');
    }
}
redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/view.article.php' . URL_DELIMITER . $article_id . '/p' . $page . '/c' . $category_id, 2, $message);

require_once __DIR__ . '/footer.php';
