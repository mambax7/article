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

if (empty($helper->getConfig('do_counter'))) {
    return;
}
$article_id = empty($_GET['article']) ? 0 : (int)$_GET['article'];
if (empty($article_id)) {
    return;
}
if (art_getcookie('art_' . $article_id) > 0) {
    return;
}

$articleHandler = xoops_getModuleHandler('article', $xoopsModule->getVar('dirname'));
$article_obj    = $articleHandler->get($article_id);
$article_obj->setVar('art_counter', $article_obj->getVar('art_counter') + 1, true);
$articleHandler->insert($article_obj, true);
art_setcookie('art_' . $article_id, time());

return;
