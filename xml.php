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

ob_start();
require_once __DIR__ . '/header.php';

/** @var Article\Helper $helper */
$helper = Article\Helper::getInstance();

error_reporting(0);
$xoopsLogger->activated = false;

if (art_parse_args($args_num, $args, $args_str)) {
    $args['article'] = !empty($args['article']) ? $args['article'] : @$args_num[0];
    $args['type']    = @$args_str[0];
}

$article_id  = \Xmf\Request::getInt('article', @$args['article'], 'GET');
$category_id = \Xmf\Request::getInt('category', @$args['category'], 'GET');
$start       = \Xmf\Request::getInt('page', @$args['page'], 'GET');
$uid         = \Xmf\Request::getInt('uid', @$args['uid'], 'GET');
$type        = \Xmf\Request::getString('type', \Xmf\Request::getString('op', @$args['type'], 'GET'), 'GET');
$type        = mb_strtoupper($type);

$categoryHandler = $helper->getHandler('Category', $GLOBALS['artdirname']);
$articleHandler  = $helper->getHandler('Article', $GLOBALS['artdirname']);
$article_obj     = $articleHandler->get($article_id);
if ($article_id > 0) {
    $criteria       = new \CriteriaCompo(new \Criteria('ac.ac_publish', 0, '>'));
    $categories_obj = $categoryHandler->getByArticle($article_id, $criteria);
    if (0 == count($categories_obj) || !in_array($category_id, array_keys($categories_obj))) {
        $category_id = 0;
    }
    unset($categories_obj);
    $category_id = empty($category_id) ? $article_obj->getVar('cat_id') : $category_id;
}

$category_obj = $categoryHandler->get($category_id);

$_uid     = empty($xoopsUser) ? 0 : $xoopsUser->getVar('uid');
$isAuthor = ($article_obj->isNew() || $_uid == $article_obj->getVar('uid'));
$isAdmin  = $categoryHandler->getPermission($category_obj, 'moderate');

if ($isAuthor || ($isAdmin && null !== $articleHandler->getCategoryStatus($category_id, $article_id))) {
} elseif (empty($category_id) || !$categoryHandler->getPermission($category_obj, 'view')
          || !$article_obj->getVar('art_time_publish')) {
    art_trackback_response(1, art_constant('MD_NOACCESS'));
}

if ('RDF' === $type) {
    $type = 'RSS1.0';
}
if ('RSS' === $type) {
    $type = 'RSS0.91';
}

$valid_format = ['RSS0.91', 'RSS1.0', 'RSS2.0', 'PIE0.1', 'MBOX', 'OPML', 'ATOM', 'ATOM0.3', 'HTML', 'JS'];
if (empty($type) || !in_array($type, $valid_format)) {
    $type = 'RSS';
}

if ($article_id > 0) {
    $myuid    = empty($xoopsUser) ? 0 : $xoopsUser->getVar('uid');
    $isAuthor = ($article_obj->isNew() || $myuid == $article_obj->getVar('uid'));
    $isAdmin  = $categoryHandler->getPermission($category_obj, 'moderate');

    if ($isAuthor || ($isAdmin && null !== $articleHandler->getCategoryStatus($category_id, $article_id))) {
    } elseif (empty($category_id) || !$categoryHandler->getPermission($category_obj, 'view')
              || !$article_obj->getVar('art_time_publish')) {
        art_trackback_response(1, art_constant('MD_NOACCESS'));
    }
}
$xml_charset = empty($helper->getConfig('do_rssutf8')) ? _CHARSET : 'UTF-8';

require_once XOOPS_ROOT_PATH . '/class/template.php';

$tpl                 = new \XoopsTpl();
$tpl->caching        = 2;
$tpl->cache_lifetime = 3600;
load_functions('cache');
$xoopsCachedTemplateId = md5(mod_generateCacheId() . str_replace(XOOPS_URL, '', $_SERVER['REQUEST_URI']));
if (!$tpl->is_cached('db:system_dummy.tpl', $xoopsCachedTemplateId)) {
    if (!empty($article_id)) {
        $source = 'article';
    } elseif (!empty($category_id)) {
        $source = 'category';
    } elseif (!empty($uid)) {
        $source = 'author';
    } else {
        $source = 'index';
    }

    $items = [];

    switch ($source) {
        case 'article':
            $category_id  = empty($category_id) ? $article_obj->getVar('cat_id') : $category_id;
            $category_obj = $categoryHandler->get($category_id);

            if (empty($category_obj) || !$categoryHandler->getPermission($category_obj, 'view')) {
                art_trackback_response(1, art_constant('MD_NOACCESS'));
            }

            $pagetitle = art_constant('MD_ARTICLE');
            $rssdesc   = art_constant('MD_XMLDESC_ARTICLE');

            $author = $article_obj->getAuthor(true);
            $text   = $article_obj->getText($page);

            $content = art_constant('MD_CATEGORY') . ': ' . $category_obj->getVar('cat_title');
            if ($text['title']) {
                $content .= '<br>' . art_constant('MD_SUBTITLE') . ': ' . $text['title'];
            }
            $source_author = $author['author'];
            $source_source = $article_obj->getVar('art_source');
            if (!empty($source_author) || !empty($source_source)) {
                $content .= '<br>' . art_constant('MD_SOURCE') . ': ' . $source_author . '(' . $source_source . ')';
            }
            if ($article_obj->getVar('art_keywords')) {
                $content .= '<br>' . art_constant('MD_KEYWORDS') . ': ' . $article_obj->getVar('art_keywords');
            }
            if ($summary = $article_obj->getSummary()) {
                $content .= '<br>' . art_constant('MD_SUMMARY') . ': ' . $summary;
            }
            $content  .= $text['body'] . '<br>';
            $items[]  = [
                'title'                     => $article_obj->getVar('art_title'),
                'link'                      => XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/view.article.php' . URL_DELIMITER . $article_obj->getVar('art_id') . '/c' . $category_id,
                'description'               => $content,
                'descriptionHtmlSyndicated' => true,
                'date'                      => $article_obj->getTime('rss'),
                'source'                    => XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/',
                'author'                    => $author['name'],
            ];
            $xml_link = XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/view.article.php' . URL_DELIMITER . $article_obj->getVar('art_id') . '/c' . $category_id;

            break;
        case 'category':
            $category_obj = $categoryHandler->get($category_id);

            if (empty($category_obj) || !$categoryHandler->getPermission($category_obj, 'access')) {
                art_trackback_response(1, art_constant('MD_NOACCESS'));
            }
            $pagetitle = art_constant('MD_CATEGORY');
            $rssdesc   = sprintf(art_constant('MD_XMLDESC_CATEGORY'), $category_obj->getVar('cat_title'));

            $criteria     = new \CriteriaCompo(new \Criteria('ac.ac_publish', 0, '>'));
            $articles_obj = $articleHandler->getByCategory($category_id, $helper->getConfig('articles_perpage'), 0, $criteria, [
                'a.art_title',
                'a.art_time_publish',
                'a.art_keywords',
                'a.art_summary',
                'a.uid',
                'a.art_source',
            ]);

            foreach ($articles_obj as $id => $article) {
                $uids[$article->getVar('uid')] = 1;
            }
            $users = art_getUnameFromId(array_keys($uids));

            foreach ($articles_obj as $id => $article) {
                $content = art_constant('MD_TITLE') . ': ' . $article->getVar('art_title') . '<br>';
                $content .= art_constant('MD_SUMMARY') . ': ' . $article->getSummary(true);
                $items[] = [
                    'title'                     => $article->getVar('art_title'),
                    'link'                      => XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/view.article.php' . URL_DELIMITER . $article->getVar('art_id') . '/c' . $category_id,
                    'description'               => $content,
                    'descriptionHtmlSyndicated' => true,
                    'date'                      => $article->getTime('rss'),
                    'source'                    => XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/',
                    'author'                    => $users[$article->getVar('uid')],
                ];
            }
            $xml_link = XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/view.category.php' . URL_DELIMITER . $category_id;
            break;
        case 'author':
            $author_name = \XoopsUser::getUnameFromId($uid);
            $pagetitle   = art_constant('MD_AUTHOR');
            $rssdesc     = sprintf(art_constant('MD_XMLDESC_AUTHOR'), $author_name);

            $categories_obj = $categoryHandler->getAllByPermission('access', ['cat_title', 'cat_moderator']);
            $categories_id  = array_keys($categories_obj);
            if (0 == count($categories_id)) {
                $items = [];
                break;
            }
            $criteria = new \CriteriaCompo(new \Criteria('a.uid', $uid));
            $criteria->add(new \Criteria('ac.ac_publish', 0, '>'));
            $articles_obj = $articleHandler->getByCategory($categories_id, $helper->getConfig('articles_perpage'), 0, $criteria, [
                'a.art_title',
                'a.cat_id',
                'a.art_time_publish',
                'a.art_keywords',
                'a.art_summary',
                'a.art_source',
            ]);
            foreach ($articles_obj as $id => $article) {
                $content = art_constant('MD_CATEGORY') . ': ' . $categories_obj[$article->getVar('cat_id')]->getVar('cat_title') . '<br>';
                $content .= art_constant('MD_TITLE') . ': ' . $article->getVar('art_title') . '<br>';
                $content .= art_constant('MD_SUMMARY') . ': ' . $article->getSummary(true);
                $items[] = [
                    'title'                     => $article->getVar('art_title'),
                    'link'                      => XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/view.article.php' . URL_DELIMITER . $article->getVar('art_id') . '/c' . $article->getVar('cat_id'),
                    'description'               => $content,
                    'descriptionHtmlSyndicated' => true,
                    'date'                      => $article->getTime('rss'),
                    'source'                    => XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/',
                    'author'                    => $author_name,
                ];
            }
            $xml_link = XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/view.author.php' . URL_DELIMITER . $uid;
            break;
        case 'index':
        default:
            $pagetitle = art_constant('MD_INDEX');
            $rssdesc   = art_constant('MD_XMLDESC_INDEX');

            $categories_obj = $categoryHandler->getAllByPermission('access', ['cat_title', 'cat_moderator']);
            $categories_id  = array_keys($categories_obj);
            if (0 == count($categories_id)) {
                $items = [];
                break;
            }
            $criteria = new \CriteriaCompo(new \Criteria('cat_id', '(' . implode(',', $categories_id) . ')', 'IN'));
            $criteria->add(new \Criteria('art_time_publish', 0, '>'));
            $criteria->setLimit($helper->getConfig('articles_perpage'));
            $articles_obj = $articleHandler->getAll($criteria, [
                'art_title',
                'uid',
                'cat_id',
                'art_time_publish',
                'art_keywords',
                'art_summary',
                'art_source',
            ]);
            /*
             $articles_obj = $articleHandler->getPublished(
             $xoopsModuleConfig["articles_perpage"],
             0,
             $criteria,
             array("art_title", "uid", "cat_id", "art_time_publish", "art_keywords", "art_summary", "art_author", "art_source")
             );
             */
            foreach ($articles_obj as $id => $article) {
                $uids[$article->getVar('uid')] = 1;
            }
            $users = art_getUnameFromId(array_keys($uids));

            foreach ($articles_obj as $id => $article) {
                $content = art_constant('MD_CATEGORY') . ': ' . $categories_obj[$article->getVar('cat_id')]->getVar('cat_title') . '<br>';
                $content .= art_constant('MD_TITLE') . ': ' . $article->getVar('art_title') . '<br>';
                $content .= art_constant('MD_SUMMARY') . ': ' . $article->getSummary(true);
                $items[] = [
                    'title'                     => $article->getVar('art_title'),
                    'link'                      => XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/view.article.php' . URL_DELIMITER . $article->getVar('art_id') . '/c' . $article->getVar('cat_id'),
                    'description'               => $content,
                    'descriptionHtmlSyndicated' => true,
                    'date'                      => $article->getTime('rss'),
                    'source'                    => XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/',
                    'author'                    => $users[$article->getVar('uid')],
                ];
            }
            $xml_link = XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/';
            break;
    }

    $xml_charset = empty($helper->getConfig('do_rssutf8')) ? _CHARSET : 'UTF-8';

    $xmlHandler = $helper->getHandler('xml', $GLOBALS['artdirname']);
    $xml        = $xmlHandler->create($type);
    $xml->setVar('encoding', $xml_charset);
    $xml->setVar('title', $xoopsConfig['sitename'] . ' :: ' . $pagetitle, 'UTF-8', $xml_charset, true);
    $xml->setVar('description', $rssdesc, true);
    $xml->setVar('descriptionHtmlSyndicated', true);
    $xml->setVar('link', $xml_link);
    $xml->setVar('syndicationURL', XOOPS_URL . '/' . xoops_getenv('PHP_SELF'));
    $xml->setVar('webmaster', checkEmail($xoopsConfig['adminmail'], true));
    $xml->setVar('editor', checkEmail($xoopsConfig['adminmail'], true));
    $xml->setVar('category', $xoopsModule->getVar('name'), true);
    $xml->setVar('generator', $xoopsModule->getInfo('version'));
    $xml->setVar('language', _LANGCODE);

    $dimention = @getimagesize(XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['artdirname'] . '/' . $xoopsModule->getInfo('image'));
    $image     = [
        'width'       => @$dimention[0],
        'height'      => @$dimention[1],
        'title'       => $xoopsConfig['sitename'] . ' :: ' . $pagetitle,
        'url'         => XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/' . $xoopsModule->getInfo('image'),
        'link'        => XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/',
        'description' => $rssdesc,
    ];
    $xml->setImage($image);

    /*
     $item = array(
     "title" => $datatitle,
     "link" => $dataurl,
     "description" => $datadesc,
     "descriptionHtmlSyndicated" => true,
     "date" => $datadate,
     "source" => $datasource,
     "author" => $dataauthor
     );
     */
    $xml->addItems($items);

    //$dummy_content = $xmlHandler->display($xml, XOOPS_CACHE_PATH."/".$GLOBALS["artdirname"].".xml.tmp");
    $dummy_content = $xmlHandler->display($xml);

    $tpl->assign_by_ref('dummy_content', $dummy_content);
}
//$content = ob_get_contents();
ob_end_clean();

header('Content-Type:text/xml; charset=' . $xml_charset);
$tpl->display('db:system_dummy.tpl', $xoopsCachedTemplateId);
