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

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

$current_path = __FILE__;
if (DIRECTORY_SEPARATOR !== '/') {
    $current_path = str_replace(strpos($current_path, "\\\\", 2) ? "\\\\" : DIRECTORY_SEPARATOR, '/', $current_path);
}
$url_arr = explode('/', strstr($current_path, '/modules/'));
include XOOPS_ROOT_PATH . '/modules/' . $url_arr[2] . '/include/vars.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['artdirname'] . '/include/functions.php';

art_parse_function('
function [VAR_PREFIX]_com_update($art_id, $count)
{
    $articleHandler = xoops_getModuleHandler("article", $GLOBALS["artdirname"]);
    $article_obj = articleHandler->get($art_id);
    $article_obj->setVar( "art_comments", $count, true );

    return $articleHandler->insert($article_obj, true);
}

function [VAR_PREFIX]_com_approve(&$comment)
{
    art_define_url_delimiter();
    if (!empty($GLOBALS["xoopsModuleConfig"]["notification_enabled"])) {
        $articleHandler = xoops_getModuleHandler("article", $GLOBALS["artdirname"]);
        $article_obj = articleHandler->get($comment->getVar("com_itemid"));
        $notificationHandler = xoops_getHandler("notification");
        $tags = array();
        $tags["ARTICLE_TITLE"] = $article_obj->getVar("art_title");
        $tags["ARTICLE_URL"] = XOOPS_URL . "/modules/" . $GLOBALS["artdirname"] . "/view.article.php" . URL_DELIMITER . $article_obj->getVar("art_id") . "#comment" . $comment->getVar("com_id");
        $tags["ARTICLE_ACTION"] = art_constant("MD_NOT_ACTION_COMMENT");
        $notificationHandler->triggerEvent("article", $article_obj->getVar("art_id"), "article_monitor", $tags);
        $notificationHandler->triggerEvent("global", 0, "article_monitor", $tags);
    }
}
');
