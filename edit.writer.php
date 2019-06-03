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

require_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['artdirname'] . '/class/xoopsformloader.php';
require_once XOOPS_ROOT_PATH . '/class/pagenav.php';

xoops_loadLanguage('user');

// Disable cache
$xoopsConfig['module_cache'][$xoopsModule->getVar('mid')] = 0;
require_once XOOPS_ROOT_PATH . '/header.php';

$writer_id         = \Xmf\Request::getInt('writer_id', 0, 'GET');
$writer_id         = \Xmf\Request::getInt('writer_id', $writer_id, 'POST');
$start             = isset($_GET['start']) ? $_GET['start'] : 0;
$_REQUEST['query'] = isset($_REQUEST['query']) ? trim($_REQUEST['query']) : '';
$limit             = 200;

$writerHandler = $helper->getHandler('Writer', $GLOBALS['artdirname']);

if (!empty($_POST['submit_writer'])) {
    $writer_obj = $writerHandler->get($writer_id);

    if (art_isAdministrator()
        || (is_object($xoopsUser)
            && ($writerHandler->getVar('uid') == $xoopsUser->getVar('uid')
                || $writer_obj->isNew()))) {
        foreach ([
                     'writer_name',
                     'writer_profile',
                 ] as $tag) {
            if (@$_POST[$tag] != $writer_obj->getVar($tag)) {
                $writer_obj->setVar($tag, $_POST[$tag]);
            }
        }

        if (art_isAdministrator() && !empty($_FILES['userfile']['name'])) {
            $error_upload  = '';
            $writer_avatar = '';
            require_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['artdirname'] . '/class/uploader.php';
            $uploader = new Article\Uploader(XOOPS_ROOT_PATH . '/' . $helper->getConfig('path_image'));
            if ($uploader->fetchMedia($_POST['xoops_upload_file'][0])) {
                if (!$uploader->upload()) {
                    $error_upload = $uploader->getErrors();
                } elseif (file_exists($uploader->getSavedDestination())) {
                    $writer_avatar = $uploader->getSavedFileName();
                }
            } else {
                $error_upload = $uploader->getErrors();
            }
        }
        $writer_avatar = empty($writer_avatar) ? (empty($_POST['writer_avatar']) ? '' : $_POST['writer_avatar']) : $writer_avatar;
        if ($writer_avatar != $writer_obj->getVar('writer_avatar')) {
            $writer_obj->setVar('writer_avatar', $writer_avatar);
        }

        $writer_id = $writerHandler->insert($writer_obj);
    }
}

//$name_parent = $_REQUEST["target"];
$name_current = 'users';
echo $js_adduser = '
    <script type="text/javascript">
    function addusers()
    {
        var sel_current = xoopsGetElementById("' . $name_current . '");
        var sel_str="";
        var num = 0;
        for (var i = 0; i < sel_current.options.length; i++) {
            if (sel_current.options[i].selected && sel_current.options[i].value >0) {
                var len=sel_current.options[i].text.length+sel_current.options[i].value.length;
                sel_str +=len+":"+sel_current.options[i].value+":"+sel_current.options[i].text;
                num ++;
            }
        }
        if (num==0) {
            return false;
        }
        sel_str = num+":"+sel_str;
        window.opener.addusers(sel_str);
        window.close();
        window.opener.focus();

        return true;
    }
    </script>
';

if (!empty($_REQUEST['search'])) {
    $form_user = new \XoopsThemeForm(_MA_SEARCH_SELECTUSER, 'selectusers', xoops_getenv('PHP_SELF'), 'post', true);

    $criteria = new \CriteriaCompo();
    $text     = empty($_REQUEST['query']) ? '%' : $myts->addSlashes(trim($_REQUEST['query']));
    $criteria->add(new \Criteria('writer_name', $text, 'LIKE'));
    $criteria->setLimit($limit);
    $criteria->setStart($start);
    $select_form = new \XoopsFormSelect('', $name_current, [], 1);
    $select_form->addOption('', _SELECT);
    $select_form->addOptionArray($writerHandler->getList($criteria));

    $user_select_tray = new \XoopsFormElementTray(_MA_SEARCH_USERLIST, '<br>');
    $user_select_tray->addElement($select_form);

    $usercount       = $writerHandler->getCount($criteria);
    $nav_extra       = 'query=' . $_REQUEST['query'] . '&amp;search=1';
    $nav             = new \XoopsPageNav($usercount, $limit, $start, 'start', $nav_extra);
    $user_select_nav = new \XoopsFormLabel(sprintf(_MA_SEARCH_COUNT, $usercount), $nav->renderNav(4));
    $user_select_tray->addElement($user_select_nav);

    $add_button = new \XoopsFormButton('', '', _ADD, 'button');
    $add_button->setExtra('onclick="javascript: addusers();"');

    $edit_button = new \XoopsFormButton('', 'edit', _EDIT, 'button');
    $edit_button->setExtra('onclick="this.submit();"');

    $close_button = new \XoopsFormButton('', '', _CLOSE, 'button');
    $close_button->setExtra('onclick="window.close()"');

    $buttonTray = new \XoopsFormElementTray('');
    $buttonTray->addElement($add_button);
    $buttonTray->addElement(new \XoopsFormButton('', 'edit', _EDIT, 'submit'));
    $buttonTray->addElement(new \XoopsFormButton('', '', _CANCEL, 'reset'));
    $buttonTray->addElement($close_button);

    $form_user->addElement($user_select_tray);

    //$form_user->addElement(new \XoopsFormHidden('target', $_REQUEST["target"]));
    $form_user->addElement($buttonTray);
    $form_user->display();
}

$form_sel = new \XoopsThemeForm(_MA_LOOKUP_USER, 'searchuser', xoops_getenv('PHP_SELF'), 'post', true);

$searchtext = new \XoopsFormText(_MA_SEARCH_TEXT, 'query', 60, 255, @$_REQUEST['query']);
$searchtext->setDescription(_MA_SEARCH_TEXT_DESC);
$form_sel->addElement($searchtext);

$close_button = new \XoopsFormButton('', '', _CLOSE, 'button');
$close_button->setExtra('onclick="window.close()"');

$buttonTray = new \XoopsFormElementTray('');
$buttonTray->addElement(new \XoopsFormButton('', 'search', _SEARCH, 'submit'));
$buttonTray->addElement($close_button);

//$form_sel->addElement(new \XoopsFormHidden('target', $_REQUEST["target"]));
$form_sel->addElement($buttonTray);
$form_sel->display();

if (!empty($_POST['edit']) && !empty($_POST[$name_current])) {
    $writer_id = \Xmf\Request::getInt($name_current, 0, 'POST')[0];
}
$writer_obj = $writerHandler->get($writer_id);
if (art_isAdministrator()
    || (is_object($xoopsUser)
        && ($writerHandler->getVar('uid') == $xoopsUser->getVar('uid')
            || $writer_obj->isNew()))) {
    require_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['artdirname'] . '/include/form.writer.php';
}
$xoopsOption['output_type'] = 'plain';
require_once __DIR__ . '/footer.php';
