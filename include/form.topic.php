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

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

/** @var Article\Helper $helper */
$helper = Article\Helper::getInstance();

//require(XOOPS_ROOT_PATH . "/class/xoopstree.php");
require_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['artdirname'] . '/class/xoopsformloader.php';

// Form
$form_art = new \XoopsThemeForm(art_constant('MD_TOPIC') . ' ' . $topic_obj->getVar('top_title'), 'formtopic', XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/action.topic.php');
$form_art->setExtra('enctype="multipart/form-data"');

// Title
$form_art->addElement(new \XoopsFormText(art_constant('MD_TITLE'), 'top_title', 50, 255, $topic_obj->getVar('top_title')), true);

// Description
$form_art->addElement(new \XoopsFormTextArea(art_constant('MD_DESCRIPTION'), 'top_description', $topic_obj->getVar('top_description')));

// Parent category
if (art_isAdministrator()) {
    $categoryHandler = $helper->getHandler('Category', $GLOBALS['artdirname']);
    $tags            = ['cat_id', 'cat_pid', 'cat_title', 'cat_order'];
    $categories      = $categoryHandler->getAllByPermission('moderate', $tags);
    $mytree          = new Article\Tree($categories, 'cat_id');
    $box             = $mytree->makeSelBox('cat_id', '--', $topic_obj->getVar('cat_id'));
    $form_art->addElement(new \XoopsFormLabel(art_constant('MD_CATEGORY'), $box));
} else {
    $form_art->addElement(new \XoopsFormHidden('cat_id', $topic_obj->getVar('cat_id')));
}

// Order
$form_art->addElement(new \XoopsFormText(art_constant('MD_ORDER'), 'top_order', 20, 20, $topic_obj->getVar('top_order')));

// expire
$top_expire = $topic_obj->isNew() ? (empty($helper->getConfig('topic_expire')) ? 365 : $helper->getConfig('topic_expire')) * 3600 * 24 + time() : $topic_obj->getVar('top_expire');
$form_art->addElement(new \XoopsFormDateTime(art_constant('MD_EXPIRATION'), 'top_expire', 15, (int)$top_expire));

// Template set
$templates = art_getTemplateList('topic');
if (count($templates) > 0) {
    $template_option_tray = new \XoopsFormElementTray(art_constant('MD_TEMPLATE_SELECT'), '<br>');
    $template_select      = new \XoopsFormSelect('', 'top_template', $topic_obj->getVar('top_template'));
    $template_select->addOptionArray($templates);
    $template_option_tray->addElement($template_select);
    $form_art->addElement($template_option_tray);
}

// Sponsor links
$form_art->addElement(new \XoopsFormTextArea(art_constant('MD_SPONSOR'), 'top_sponsor', $topic_obj->getVar('top_sponsor', 'e')));
//$form_art->addElement(new \XoopsFormLabel(art_constant("MD_SPONSOR_DESC"), art_constant("MD_SPONSOR_DESC_TEXT")));

$form_art->addElement(new \XoopsFormHidden('top_id', $topic_obj->getVar('top_id')));
$form_art->addElement(new \XoopsFormHidden('from', $from));

$buttonTray = new \XoopsFormElementTray('', '');
$buttonTray->addElement(new \XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
$cancel_button = new \XoopsFormButton('', 'cancel', _CANCEL, 'button');
if (!empty($from)) {
    $extra = 'admin/admin.topic.php';
} elseif (!$topic_obj->getVar('top_id')) {
    $extra = 'view.category.php?category=' . (int)$category_id;
} else {
    $extra = 'view.topic.php?topic=' . $topic_obj->getVar('cat_id');
}
$cancel_button->setExtra("onclick='window.document.location=\"" . $extra . "\"'");
$buttonTray->addElement($cancel_button);
$form_art->addElement($buttonTray);

$form_art->display();
