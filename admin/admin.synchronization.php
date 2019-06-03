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
require_once __DIR__ . '/admin_header.php';

xoops_cp_header();
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/include/vars.php';
//loadModuleAdminMenu(9);
$helper = \XoopsModules\Article\Helper::getInstance();

$type = @$_GET['type'];
//if (!empty($_GET['type'])) {
$start = (int)(@$_GET['start']);

switch ($type) {
    case 'category':
        $categoryHandler = $helper->getHandler('Category', $xoopsModule->getVar('dirname', 'n'));
        if ($start >= ($count = $categoryHandler->getCount())) {
            break;
        }

        $limit    = \Xmf\Request::getInt('limit', 20, 'GET');
        $criteria = new \Criteria('');
        $criteria->setStart($start);
        $criteria->setLimit($limit);
        $categories_obj = $categoryHandler->getAll($criteria);
        foreach (array_keys($categories_obj) as $key) {
            $categoryHandler->updateTrack($categories_obj[$key]);
            $categoryHandler->_setLastArticleIds($categories_obj[$key]);
        }

        redirect_header("admin.synchronization.php?type={$type}&amp;start=" . ($start + $limit) . "&amp;limit={$limit}", 2, art_constant('AM_SYNC_SYNCING') . " {$count}: {$start} - " . ($start + $limit));

        break;
    case 'article':

        $articleHandler = $helper->getHandler('Article', $xoopsModule->getVar('dirname', 'n'));
        if ($start >= ($count = $articleHandler->getCount())) {
            break;
        }

        $limit    = \Xmf\Request::getInt('limit', 100, 'GET');
        $criteria = new \Criteria('');
        $criteria->setStart($start);
        $criteria->setLimit($limit);
        $articles_obj = $articleHandler->getAll($criteria);
        $rates        = [];
        $tbs          = [];

        $sql    = '    SELECT art_id, COUNT(*) AS art_rates, SUM(rate_rating) AS art_rating ' . '    FROM ' . art_DB_prefix('rate') . '    WHERE art_id IN(' . implode(',', array_keys($articles_obj)) . ')' . '    GROUP BY art_id';
        $result = $xoopsDB->query($sql);
        while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
            $rates[$myrow['art_id']] = ['art_rates' => $myrow['art_rates'], 'art_rating' => $myrow['art_rating']];
        }

        $sql    = '    SELECT art_id, COUNT(*) AS art_trackbacks ' . '    FROM ' . art_DB_prefix('trackback') . '    WHERE art_id IN (' . implode(', ', array_keys($articles_obj)) . ')' . '        AND tb_status > 0' . '    GROUP BY art_id';
        $result = $xoopsDB->query($sql);
        while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
            $tbs[$myrow['art_id']] = $myrow['art_trackbacks'];
        }

        foreach (array_keys($articles_obj) as $key) {
            $articleHandler->updateCategories($articles_obj[$key]);
            $articleHandler->updateTopics($articles_obj[$key]);
            $articleHandler->updateKeywords($articles_obj[$key]);

            $pages_all = $articles_obj[$key]->getPages(false, true);
            if (serialize($pages_all) != serialize($articles_obj[$key]->getPages())) {
                $articles_obj[$key]->setVar('art_pages', $pages_all, true);
            }
            if ((int)(@$rates[$key]['art_rates']) != $articles_obj[$key]->getVar('art_rates')) {
                $articles_obj[$key]->setVar('art_rates', (int)(@$rates[$key]['art_rates']), true);
            }
            if ((int)(@$rates[$key]['art_rating']) != $articles_obj[$key]->getVar('art_rating')) {
                $articles_obj[$key]->setVar('art_rating', (int)(@$rates[$key]['art_rating']), true);
            }
            if ((int)(@$tbs[$key]) != $articles_obj[$key]->getVar('art_trackbacks')) {
                $articles_obj[$key]->setVar('art_trackbacks', (int)(@$tbs[$key]), true);
            }

            $articleHandler->insert($articles_obj[$key]);
        }

        redirect_header("admin.synchronization.php?type={$type}&amp;start=" . ($start + $limit) . "&amp;limit={$limit}", 2, art_constant('AM_SYNC_SYNCING') . " {$count}: {$start} - " . ($start + $limit));

    // no break
    case 'misc':
    default:
        mod_loadFunctions('recon', $xoopsModule->getVar('dirname', 'n'));
        art_synchronization();
        break;
}

$form = '<fieldset><legend style="font-weight: bold; color: #900;">' . art_constant('AM_SYNC_TITLE') . '</legend>';

$form .= '<form action="admin.synchronization.php" method="get">';
$form .= '<div style="padding: 10px 2px;">';
$form .= '<h2>' . art_constant('AM_SYNC_CATEGORY') . '</h2>';
$form .= '<input type="hidden" name="type" value="category">';
$form .= art_constant('AM_SYNC_ITEMS') . '<input type="text" name="limit" value="20"> ';
$form .= '<input type="submit" name="submit" value=' . _SUBMIT . '>';
$form .= '</div>';
$form .= '</form>';

$form .= '<form action="admin.synchronization.php" method="get">';
$form .= '<div style="padding: 10px 2px;">';
$form .= '<h2>' . art_constant('AM_SYNC_ARTICLE') . '</h2>';
$form .= '<input type="hidden" name="type" value="article">';
$form .= art_constant('AM_SYNC_ITEMS') . '<input type="text" name="limit" value="100"> ';
$form .= '<input type="submit" name="submit" value=' . _SUBMIT . '>';
$form .= '</div>';
$form .= '</form>';

$form .= '<form action="admin.synchronization.php" method="get">';
$form .= '<div style="padding: 10px 2px;">';
$form .= '<h2>' . art_constant('AM_SYNC_MISC') . '</h2>';
$form .= '<input type="hidden" name="type" value="misc">';
$form .= '<input type="submit" name="submit" value=' . _SUBMIT . '>';
$form .= '</div>';
$form .= '</form>';

$form .= '</fieldset>';

echo $form;
xoops_cp_footer();
