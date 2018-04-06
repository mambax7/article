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
require_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['artdirname'] . '/class/uploader.php';

include XOOPS_ROOT_PATH . '/header.php';
include XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/include/vars.php';

$top_id       = \Xmf\Request::getInt('top_id', 0, 'POST');
$topicHandler = xoops_getModuleHandler('topic', $GLOBALS['artdirname']);
$topic        = $topicHandler->get($top_id);

if (empty($_POST['submit'])) {
    redirect_header('index.php', 2, art_constant('MD_INVALID'));
}

$categoryHandler = xoops_getModuleHandler('category', $GLOBALS['artdirname']);
$category        =& $categoryHandler->get(\Xmf\Request::getInt('cat_id', 0, 'POST'));
if (!$categoryHandler->getPermission($category, 'moderate')) {
    redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/index.php', 2, art_constant('MD_NOACCESS'));
}

foreach ([
             'cat_id',
             'top_order',
             'top_expire',
             'top_title',
             'top_description',
             'top_template',
             'top_sponsor'
         ] as $tag) {
    if (@$_POST[$tag] != $topic->getVar($tag)) {
        $topic->setVar($tag, $_POST[$tag]);
    }
}
if (isset($_POST['top_expire'])) {
    $expire     = $_POST['top_expire'];
    $top_expire = strtotime($expire['date']) + $expire['time'];
    $offset     = $xoopsUser->timezone() - $xoopsConfig['server_TZ'];
    $top_expire = $top_expire - ($offset * 3600);
} else {
    $top_expire = 0;
}
if ($top_expire != $topic->getVar('top_expire')) {
    $topic->setVar('top_expire', $top_expire);
}
if ($topic->isNew()) {
    $topic->setVar('top_time', time());
}

$top_id_new = $topicHandler->insert($topic);

if (empty($from)) {
    $redirect = XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/view.category.php' . URL_DELIMITER . $category->getVar('cat_id');
} else {
    $redirect = XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/admin/admin.topic.php';
}

$message = $top_id_new ? art_constant('MD_SAVED') : art_constant('MD_INSERTERROR');
redirect_header($redirect, 2, $message);

include XOOPS_ROOT_PATH . '/footer.php';
