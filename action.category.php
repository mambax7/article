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
require_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['artdirname'] . '/class/uploader.php';

$cat_id = \Xmf\Request::getInt('cat_id', 0, 'POST');
$from   = empty($_POST['from']) ? 0 : 1;

require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/include/vars.php';

/** @var Article\Helper $helper */
$helper = Article\Helper::getInstance();

$categoryHandler = $helper->getHandler('Category', $GLOBALS['artdirname']);
$category        = $categoryHandler->get($cat_id);

if (empty($_POST['submit'])) {
    redirect_header('index.php', 2, art_constant('MD_INVALID'));
}

if (!$categoryHandler->getPermission($category, 'moderate')) {
    redirect_header('index.php', 2, art_constant('MD_NOACCESS'));
}

foreach ([
             'cat_description',
             'cat_template',
             'cat_entry',
             'cat_sponsor',
         ] as $tag) {
    if (@$_POST[$tag] != $category->getVar($tag)) {
        $category->setVar($tag, $_POST[$tag]);
    }
}

if (art_isAdministrator()) {
    foreach (['cat_title', 'cat_order'] as $tag) {
        if ($_POST[$tag] != $category->getVar($tag)) {
            $category->setVar($tag, $_POST[$tag]);
        }
    }

    $cat_pid = @$_POST['cat_pid'];
    if ($cat_pid != $category->getVar('cat_pid') && $cat_pid != $category->getVar('cat_id')) {
        require_once XOOPS_ROOT_PATH . '/class/xoopstree.php';
        $mytree  = new \XoopsObjectTree(art_DB_prefix('category'), 'cat_id', 'cat_pid');
        $idarray = $mytree->getAllChildId($category->getVar('cat_id'));
        if (!in_array($cat_pid, $idarray)) {
            $category->setVar('cat_pid', $cat_pid);
        }
    }
    $category->setVar('cat_moderator', empty($_POST['cat_moderator']) ? [] : $_POST['cat_moderator']);
}

$error_upload = '';
$cat_image    = '';
if (!empty($_FILES['userfile']['name'])) {
    $uploader = new Article\Uploader(XOOPS_ROOT_PATH . '/' . $helper->getConfig('path_image'));
    if ($uploader->fetchMedia($_POST['xoops_upload_file'][0])) {
        if (!$uploader->upload()) {
            $error_upload = $uploader->getErrors();
        } elseif (file_exists($uploader->getSavedDestination())) {
            $cat_image = $uploader->getSavedFileName();
        }
    } else {
        $error_upload = $uploader->getErrors();
    }
}
$cat_image = empty($cat_image) ? (empty($_POST['cat_image']) ? '' : $_POST['cat_image']) : $cat_image;
if ($cat_image != $category->getVar('cat_image')) {
    $category->setVar('cat_image', $cat_image);
}

$cat_id_new = $categoryHandler->insert($category);

if (empty($from)) {
    $redirect = XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/view.category.php' . URL_DELIMITER . $cat_id_new;
} else {
    $redirect = XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/admin/admin.category.php';
}
$message = $cat_id_new ? art_constant('MD_SAVED') : art_constant('MD_INSERTERROR');
redirect_header($redirect, 2, $message);

require_once XOOPS_ROOT_PATH . '/footer.php';
