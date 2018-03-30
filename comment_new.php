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
$com_itemid = \Xmf\Request::getInt('com_itemid', 0, 'GET');
if ($com_itemid > 0) {
    $articleHandler = xoops_getModuleHandler('article', $GLOBALS['artdirname']);
    $article_obj    = $articleHandler->get($com_itemid);
    $com_replytitle = $article_obj->getVar('art_title');
    require_once XOOPS_ROOT_PATH . '/include/comment_new.php';
}
