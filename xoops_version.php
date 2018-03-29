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
include __DIR__ . '/preloads/autoloader.php';

include __DIR__ . '/include/vars.php';

$modversion = [
    'name'          => art_constant('MI_NAME'),
    'version'       => 2.10,
    'module_status' => 'Beta 2',
    'release_date'  => '2017/01/04',
    'description'   => art_constant('MI_DESC'),
    'credits'       => 'XOOPS Project, The WF-projects, The Xoops China Community',
    'image'         => 'assets/images/logoModule.png',
    'dirname'       => $GLOBALS['artdirname'],
    'author'        => 'Taiwen Jiang (a.k.a. phppp)',
    //    "help"          => "http://xoopsforge.com/modules/article/view.category.php/5"
];

$modversion['help']        = 'page=help';
$modversion['license']     = 'GNU GPL 2.0 or later';
$modversion['license_url'] = 'www.gnu.org/licenses/gpl-2.0.html';
//$modversion['dirmoduleadmin'] = '/Frameworks/moduleclasses/moduleadmin';
//$modversion['icons16']        = '../../Frameworks/moduleclasses/icons/16';
//$modversion['icons32']        = '../../Frameworks/moduleclasses/icons/32';
$modversion['modicons16'] = 'assets/images/icons/16';
$modversion['modicons32'] = 'assets/images/icons/32';
//about
$modversion['module_website_url']  = 'www.xoops.org';
$modversion['module_website_name'] = 'XOOPS';
$modversion['min_php']             = '5.5';
$modversion['min_xoops']           = '2.5.9';
$modversion['min_admin']           = '1.2';
$modversion['min_db']              = ['mysql' => '5.5'];

// Is performing module install/update?
$isModuleAction = (!empty($_POST['fct']) && 'modulesadmin' === $_POST['fct']) ? true : false;
$isModuleAction = (!empty($_POST['module'])) ? true : $isModuleAction;

// database tables
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
$modversion['tables']           = [
    $GLOBALS['ART_DB_PREFIX'] . '_category',
    $GLOBALS['ART_DB_PREFIX'] . '_article',
    $GLOBALS['ART_DB_PREFIX'] . '_topic',
    $GLOBALS['ART_DB_PREFIX'] . '_file',
    $GLOBALS['ART_DB_PREFIX'] . '_trackback',
    $GLOBALS['ART_DB_PREFIX'] . '_tracked',
    $GLOBALS['ART_DB_PREFIX'] . '_pingback',
    $GLOBALS['ART_DB_PREFIX'] . '_artcat',
    $GLOBALS['ART_DB_PREFIX'] . '_arttop',
    $GLOBALS['ART_DB_PREFIX'] . '_spotlight',
    $GLOBALS['ART_DB_PREFIX'] . '_rate',
    $GLOBALS['ART_DB_PREFIX'] . '_text',
    $GLOBALS['ART_DB_PREFIX'] . '_writer'
];

// Admin things
$modversion['hasAdmin']    = 1;
$modversion['system_menu'] = 1;
$modversion['adminindex']  = 'admin/index.php';
$modversion['adminmenu']   = 'admin/menu.php';

// Menu
$modversion['hasMain'] = 1;
$modversion['pages']   = [];
$modversion['pages'][] = ['url' => 'index.php', 'name' => art_constant('MI_PAGE_INDEX')];
$modversion['pages'][] = ['url' => 'view.article.php', 'name' => art_constant('MI_PAGE_ARTICLE')];
$modversion['pages'][] = ['url' => 'view.category.php', 'name' => art_constant('MI_PAGE_CATEGORY')];
$modversion['pages'][] = ['url' => 'view.topic.php', 'name' => art_constant('MI_PAGE_TOPIC')];
$modversion['pages'][] = ['url' => 'view.author.php', 'name' => art_constant('MI_PAGE_AUTHOR')];
$modversion['pages'][] = ['url' => 'list.tag.php', 'name' => art_constant('MI_PAGE_KEYWORD')];
$modversion['pages'][] = ['url' => 'view.blocks.php', 'name' => art_constant('MI_PAGE_BLOCKS')];
$modversion['pages'][] = ['url' => 'view.archive.php', 'name' => art_constant('MI_PAGE_ARCHIVE')];
$modversion['pages'][] = ['url' => 'view.list.php', 'name' => art_constant('MI_PAGE_LIST')];
$modversion['pages'][] = ['url' => 'search.php', 'name' => art_constant('MI_PAGE_SEARCH')];

$modversion['sub']   = [];
$modversion['sub'][] = ['name' => art_constant('MI_SUBMIT'), 'url' => 'edit.article.php'];
$modversion['sub'][] = ['name' => art_constant('MI_PAGE_BLOCKS'), 'url' => 'view.blocks.php'];
$modversion['sub'][] = ['name' => art_constant('MI_PAGE_ARCHIVE'), 'url' => 'view.archive.php'];
$modversion['sub'][] = ['name' => art_constant('MI_PAGE_LIST'), 'url' => 'view.list.php'];
$modversion['sub'][] = ['name' => art_constant('MI_PAGE_TAGS'), 'url' => 'list.tag.php'];
$modversion['sub'][] = ['name' => art_constant('MI_PAGE_SEARCH'), 'url' => 'search.php'];

if (is_object($GLOBALS['xoopsUser'])) {
    $modversion['sub'][] = ['name' => art_constant('MI_PAGE_MYPAGE'), 'url' => 'view.author.php'];
}

$modversion['onInstall'] = 'include/action.module.php';
$modversion['onUpdate']  = 'include/action.module.php';

// Use smarty
$modversion['use_smarty'] = 1;

/**
 * Templates
 */
if ($isModuleAction) {
    require_once __DIR__ . '/include/functions.render.php';
    $modversion['templates'] = art_getTplPageList('', true);
}

// Blocks
$modversion['blocks'] = [];

/*
 * $options:
 *                    $options[0] - use specified spotlight only
 *                    $options[1] - show editor's note if available
 */
$modversion['blocks'][1] = [
    'file'        => 'blocks.php',
    'name'        => art_constant('MI_SPOTLIGHT'),
    'description' => art_constant('MI_SPOTLIGHT_DESC'),
    'show_func'   => $GLOBALS['ART_VAR_PREFIX'] . '_spotlight_show',
    'options'     => '0|0',
    'edit_func'   => $GLOBALS['ART_VAR_PREFIX'] . '_spotlight_edit',
    'template'    => $GLOBALS['artdirname'] . '_block_spotlight.tpl'
];

/*
 * $options:
 *                        0 - criteria for fetching articles;
 *                        1 - limit for article count;
 *                        2 - bool flag for displaying summary: 0 - none; 1 - summary; 2 - summary and image
 *                        3 - title length;
 *                        4 - time format;
 *                        5 - allowed categories;
 */
$modversion['blocks'][] = [
    'file'        => 'blocks.article.php',
    'name'        => art_constant('MI_ARTICLE'),
    'description' => art_constant('MI_ARTICLE_DESC'),
    'show_func'   => $GLOBALS['ART_VAR_PREFIX'] . '_article_show',
    'options'     => 'time|10|2|0|c|0',
    'edit_func'   => $GLOBALS['ART_VAR_PREFIX'] . '_article_edit',
    'template'    => $GLOBALS['artdirname'] . '_block_article.tpl'
];

$modversion['blocks'][] = [
    'file'        => 'blocks.php',
    'name'        => art_constant('MI_CATEGORY'),
    'description' => art_constant('MI_CATEGORY_DESC'),
    'show_func'   => $GLOBALS['ART_VAR_PREFIX'] . '_category_show',
    'template'    => $GLOBALS['artdirname'] . '_block_category.tpl'
];

$modversion['blocks'][] = [
    'file'        => 'blocks.php',
    'name'        => art_constant('MI_TOPIC'),
    'description' => art_constant('MI_TOPIC_DESC'),
    'show_func'   => $GLOBALS['ART_VAR_PREFIX'] . '_topic_show',
    'options'     => '10|0', // MaxItems|CategoryIds
    'edit_func'   => $GLOBALS['ART_VAR_PREFIX'] . '_topic_edit',
    'template'    => $GLOBALS['artdirname'] . '_block_topic.tpl'
];

$modversion['blocks'][] = [
    'file'        => 'blocks.php',
    'name'        => art_constant('MI_AUTHOR'),
    'description' => art_constant('MI_AUTHOR_DESC'),
    'show_func'   => $GLOBALS['ART_VAR_PREFIX'] . '_author_show',
    'options'     => '10', // MaxItems
    'edit_func'   => $GLOBALS['ART_VAR_PREFIX'] . '_author_edit',
    'template'    => $GLOBALS['artdirname'] . '_block_author.tpl'
];

/*
 * $options:
 *                        0 - display mode: 0 - compact title list; otherwise - column number of categories;
 *                        1 - limit for article count;
 *                        2 - title length;
 *                        3 - time format;
 *                        4 - allowed categories;
 */
$modversion['blocks'][] = [
    'file'        => 'blocks.news.php',
    'name'        => art_constant('MI_BLOCK_RECENTNEWS'),
    'description' => 'Recent news with spotlight',
    'show_func'   => $GLOBALS['ART_VAR_PREFIX'] . '_block_news_show',
    'edit_func'   => $GLOBALS['ART_VAR_PREFIX'] . '_block_news_edit',
    'options'     => '2|5|0|c|0',
    'template'    => $GLOBALS['artdirname'] . '_block_news.tpl',
];

/*
 * $options:
 *                    $options[0] - number of tags to display
 *                    $options[1] - time duration, in days, 0 for all the time
 *                    $options[2] - max font size (px or %)
 *                    $options[3] - min font size (px or %)
 */
$modversion['blocks'][] = [
    'file'        => 'blocks.tag.php',
    'name'        => art_constant('MI_BLOCK_TAG_CLOUD'),
    'description' => 'Show tag cloud',
    'show_func'   => $GLOBALS['ART_VAR_PREFIX'] . '_tag_block_cloud_show',
    'edit_func'   => $GLOBALS['ART_VAR_PREFIX'] . '_tag_block_cloud_edit',
    'options'     => '100|0|150|80',
    'template'    => $GLOBALS['artdirname'] . '_tag_block_cloud.tpl',
];

/*
 * $options:
 *                    $options[0] - number of tags to display
 *                    $options[1] - time duration, in days, 0 for all the time
 *                    $options[2] - sort: a - alphabet; c - count; t - time
 */
$modversion['blocks'][] = [
    'file'        => 'blocks.tag.php',
    'name'        => art_constant('MI_BLOCK_TAG_TOP'),
    'description' => 'Show top tags',
    'show_func'   => $GLOBALS['ART_VAR_PREFIX'] . '_tag_block_top_show',
    'edit_func'   => $GLOBALS['ART_VAR_PREFIX'] . '_tag_block_top_edit',
    'options'     => '50|30|c',
    'template'    => $GLOBALS['artdirname'] . '_tag_block_top.tpl',
];

// Search
$modversion['hasSearch']      = 1;
$modversion['search']['file'] = 'include/search.inc.php';
$modversion['search']['func'] = $GLOBALS['ART_VAR_PREFIX'] . '_search';

// Comments
$modversion['hasComments'] = 1;
//$modversion["comments"]["pageName"] = XOOPS_URL . "/modules/" . $GLOBALS["artdirname"] . "/view.article.php";
$modversion['comments']['pageName']    = 'view.article.php';
$modversion['comments']['itemName']    = 'article';
$modversion['comments']['extraParams'] = ['category'];

// Comment callback functions
$modversion['comments']['callbackFile']        = 'include/comment.inc.php';
$modversion['comments']['callback']['approve'] = $GLOBALS['ART_VAR_PREFIX'] . '_com_approve';
$modversion['comments']['callback']['update']  = $GLOBALS['ART_VAR_PREFIX'] . '_com_update';

// Configs
// Config categories
$modversion['configcat'][1]['nameid']      = 'module';
$modversion['configcat'][1]['name']        = $GLOBALS['ART_VAR_PREFIXU'] . '_MI_CONFIGCAT_MODULE';
$modversion['configcat'][1]['description'] = $GLOBALS['ART_VAR_PREFIXU'] . '_MI_CONFIGCAT_MODULE_DESC';

$modversion['configcat'][2]['nameid']      = 'article';
$modversion['configcat'][2]['name']        = $GLOBALS['ART_VAR_PREFIXU'] . '_MI_CONFIGCAT_ARTICLE';
$modversion['configcat'][2]['description'] = $GLOBALS['ART_VAR_PREFIXU'] . '_MI_CONFIGCAT_ARTICLE_DESC';

// Config items

// Module-wide
$modversion['config'][] = [
    'name'        => 'do_debug',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DODEBUG',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DODEBUG_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
    'category'    => 'module'
];

$do_urw = true;
if ($isModuleAction) {
    $do_urw = in_array(php_sapi_name(), ['apache', 'apache2handler']);
}
$modversion['config'][] = [
    'name'        => 'do_urw',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DOURLREWRITE',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DOURLREWRITE_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => $do_urw,
    'category'    => 'module'
];

$theme_set = [_NONE => '0'];
if ($isModuleAction) {
    foreach ($GLOBALS['xoopsConfig']['theme_set_allowed'] as $theme) {
        $theme_set[$theme] = $theme;
    }
}
$modversion['config'][] = [
    'name'        => 'theme_set',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_THEMESET',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_THEMESET_DESC',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'options'     => $theme_set,
    'default'     => '',
    'category'    => 'module'
];

$templates = ['default'];
if ($isModuleAction) {
    require_once __DIR__ . '/include/functions.render.php';
    $templates = art_getTemplateList();
}
$modversion['config'][] = [
    'name'        => 'template',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_TEMPLATE',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_TEMPLATE_DESC',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'options'     => $templates,
    'default'     => 'default',
    'category'    => 'module'
];

$modversion['config'][] = [
    'name'        => 'timeformat',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_TIMEFORMAT',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_TIMEFORMAT_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    /*
     'options'         => array(
     "_DATESTRING"        => "l",
     "_MEDIUMDATESTRING"    => "m",
     "_SHORTDATESTRING"    => "s",
     $GLOBALS["ART_VAR_PREFIXU"] . "_MI_TIMEFORMAT_CUSTOM" => "c"),
     */
    'default'     => 'c',
    'category'    => 'module'
];

$modversion['config'][] = [
    'name'        => 'do_spotlight',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DOSPOTLIGHT',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DOSPOTLIGHT_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
    'category'    => 'module'
];

$modversion['config'][] = [
    'name'        => 'display_summary',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DISPLAY_SUMMARY',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DISPLAY_SUMMARY_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
    'category'    => 'module'
];

$modversion['config'][] = [
    'name'        => 'do_rssutf8',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DORSSUTF8',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DORSSUTF8_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
    'category'    => 'module'
];

$modversion['config'][] = [
    'name'        => 'articles_perpage',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_ARTICLESPERPAGE',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_ARTICLESPERPAGE_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 10,
    'category'    => 'module'
];

$modversion['config'][] = [
    'name'        => 'articles_index',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_ARTICLES_INDEX',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_ARTICLESINDEX_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 4,
    'category'    => 'module'
];

$modversion['config'][] = [
    'name'        => 'featured_index',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_FEATURED_INDEX',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_FEATUREDINDEX_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 5,
    'category'    => 'module'
];

$modversion['config'][] = [
    'name'        => 'articles_category',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_ARTICLES_CATEGORY',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_ARTICLES_CATEGORY_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 5,
    'category'    => 'module'
];

$modversion['config'][] = [
    'name'        => 'featured_category',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_FEATURED_CATEGORY',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_FEATUREDCATEGORY_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 5,
    'category'    => 'module'
];

$modversion['config'][] = [
    'name'        => 'topics_max',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_TOPIC_MAX',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_TOPIC_MAX_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 10,
    'category'    => 'module'
];

$modversion['config'][] = [
    'name'        => 'topic_expire',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_TOPIC_EXPIRE',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_TOPIC_EXPIRE_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 0,
    'category'    => 'module'
];

// For artcile uploaded files: image, attachment, html ...
$modversion['config'][] = [
    'name'        => 'path_file',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_PATHFILE',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_PATHFILE_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => 'uploads/' . $GLOBALS['artdirname'] . '/file',
    'category'    => 'module'
];

// For utility images: category
$modversion['config'][] = [
    'name'        => 'path_image',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_PATHIMAGE',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_PATHIMAGE_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => 'uploads/' . $GLOBALS['artdirname'] . '/image',
    'category'    => 'module'
];

$modversion['config'][] = [
    'name'        => 'header',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_HEADER',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_HEADER_DESC',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => sprintf('%s :: %s - %s', $xoopsConfig['sitename'], art_constant('MI_NAME'), art_constant('MI_DESC')),
    'category'    => 'module'
];

$modversion['config'][] = [
    'name'        => 'sponsor',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_SPONSOR',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_SPONSOR_DESC',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => 'https://xoops.org XOOPS Projects',
    'category'    => 'module'
];

// Article
/*
 $modversion["config"][] = array(
 "name"             => "do_form_advance",
 "title"         => $GLOBALS["ART_VAR_PREFIXU"] . "_MI_DOFORMADVANCE",
 "description"     => $GLOBALS["ART_VAR_PREFIXU"] . "_MI_DOFORMADVANCE_DESC",
 "formtype"         => "yesno",
 "valuetype"     => "int",
 "default"         => 1,
 "category"         => "article"
 );
 */

$forum_options = [_NONE => 0];
if ($isModuleAction) {
    /** @var XoopsModuleHandler $moduleHandler */
    $moduleHandler = xoops_getHandler('module');
    $newbb         = $moduleHandler->getByDirname('newbb');
    if (is_object($newbb) && $newbb->getVar('isactive')) {
        $forumHandler = xoops_getModuleHandler('forum', 'newbb', true);
        /* the acient NewBB module is not supported */
        if (is_object($forumHandler) && method_exists($forumHandler, 'getForumsByCategory')):
            $forums = $forumHandler->getForumsByCategory(0, '', false);
        foreach (array_keys($forums) as $c) {
            foreach (array_keys($forums[$c]) as $f) {
                $forum_options[$forums[$c][$f]['title']] = $f;
                if (!isset($forums[$c][$f]['sub'])) {
                    continue;
                }
                foreach (array_keys($forums[$c][$f]['sub']) as $s) {
                    $forum_options['-- ' . $forums[$c][$f]['sub'][$s]['title']] = $s;
                }
            }
        }
        unset($forums);
        endif;
        unset($newbb);
    }
}
$modversion['config'][] = [
    'name'        => 'forum',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_FORUM',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_FORUM_DESC',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'options'     => $forum_options,
    'default'     => 0,
    'category'    => 'article'
];

$modversion['config'][] = [
    'name'        => 'disclaimer',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DISCLAIMER',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DISCLAIMER_DESC',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => art_constant('MI_DISCLAIMER_TEXT'),
    'category'    => 'article'
];

// For sending out
$modversion['config'][] = [
    'name'        => 'do_trackback',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DOTRACKBACK',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DOTRACKBACK_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
    'category'    => 'article'
];

$modversion['config'][] = [
    'name'        => 'do_trackbackutf8',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DOTRACKBACKUTF8',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DOTRACKBACKUTF8_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
    'category'    => 'article'
];

// For receiving
$modversion['config'][] = [
    'name'        => 'trackback_option',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_TRACKBACK_OPTION',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_TRACKBACK_OPTION_DESC',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => 0,
    'options'     => [art_constant('MI_MODERATION') => 0, _ALL => 1, _NONE => 2],
    'category'    => 'article'
];

$modversion['config'][] = [
    'name'        => 'do_ping',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DOPINGBACK',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DOPINGBACK_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
    'category'    => 'article'
];

// For draft
$modversion['config'][] = [
    'name'        => 'article_expire',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_ARTICLE_EXPIRE',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_ARTICLE_EXPIRE_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 14,
    'category'    => 'article'
];

$modversion['config'][] = [
    'name'        => 'do_counter',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DOCOUNTER',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DOCOUNTER_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
    'category'    => 'article'
];

$modversion['config'][] = [
    'name'        => 'do_footnote',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DOFOOTNOTE',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DOFOOTNOTE_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
    'category'    => 'article'
];

$modversion['config'][] = [
    'name'        => 'do_sibling',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DOSIBLING',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DOSIBLING_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
    'category'    => 'article'
];

$modversion['config'][] = [
    'name'        => 'sibling_length',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_SIBLINGLENGTH',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_SIBLINGLENGTH_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 0,
    'category'    => 'article'
];

$modversion['config'][] = [
    'name'        => 'do_subtitle',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DOSUBTITLE',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DOSUBTITLE_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
    'category'    => 'article'
];

$modversion['config'][] = [
    'name'        => 'do_heading',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DOHEADING',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DOHEADING_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
    'category'    => 'article'
];

$modversion['config'][] = [
    'name'        => 'do_rate',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DORATE',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DORATE_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
    'category'    => 'article'
];

$modversion['config'][] = [
    'name'        => 'do_keywords',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DOKEYWORDS',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_DOKEYWORDS_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
    'category'    => 'article'
];

$modversion['config'][] = [
    'name'        => 'keywords',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_KEYWORDS',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_KEYWORDS_DESC',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => "https://xoops.org XOOPS Official\nhttps://xoops.org xoops\nhttps://xoops.org.cn XOOPS CHINA\nhttps://xoops.org CMS\nhttp://www.php.net php\nhttp://mysql.com MySQL",
    'category'    => 'article'
];

$modversion['config'][] = [
    'name'        => 'length_excerpt',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_LENGTHEXCERPT',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_LENGTHEXCERPT_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 255,
    'category'    => 'article'
];

$modversion['config'][] = [
    'name'        => 'url_forum',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_URLFORUM',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_URLFORUM_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => 'newbb/viewtopic.php?topic_id=%d&amp;forum=%d',
    'category'    => 'article'
];

$modversion['config'][] = [
    'name'        => 'pings',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_PING',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_PING_DESC',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => '',
    'category'    => 'article'
];

$modversion['config'][] = [
    'name'        => 'copyright',
    'title'       => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_COPYRIGHT',
    'description' => $GLOBALS['ART_VAR_PREFIXU'] . '_MI_COPYRIGHT_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => 'Copyright&copy; %s & ' . $xoopsConfig['sitename'],
    'category'    => 'article'
];

// Notification

$modversion['notification']                = [];
$modversion['hasNotification']             = 1;
$modversion['notification']['lookup_file'] = 'include/notification.inc.php';
$modversion['notification']['lookup_func'] = $GLOBALS['ART_VAR_PREFIX'] . '_notify_iteminfo';

$i = 0;
++$i;
$modversion['notification']['category'][$i]['name']           = 'global';
$modversion['notification']['category'][$i]['title']          = art_constant('MI_GLOBAL_NOTIFY');
$modversion['notification']['category'][$i]['description']    = art_constant('MI_GLOBAL_NOTIFYDSC');
$modversion['notification']['category'][$i]['subscribe_from'] = ['index.php'];
$modversion['notification']['category'][$i]['allow_bookmark'] = 1;

++$i;
$modversion['notification']['category'][$i]['name']           = 'category';
$modversion['notification']['category'][$i]['title']          = art_constant('MI_CATEGORY_NOTIFY');
$modversion['notification']['category'][$i]['description']    = art_constant('MI_CATEGORY_NOTIFYDSC');
$modversion['notification']['category'][$i]['subscribe_from'] = ['view.category.php'];
$modversion['notification']['category'][$i]['item_name']      = 'category';
$modversion['notification']['category'][$i]['allow_bookmark'] = 1;

++$i;
$modversion['notification']['category'][$i]['name']           = 'article';
$modversion['notification']['category'][$i]['title']          = art_constant('MI_ARTICLE_NOTIFY');
$modversion['notification']['category'][$i]['description']    = art_constant('MI_ARTICLE_NOTIFYDSC');
$modversion['notification']['category'][$i]['subscribe_from'] = ['view.article.php'];
$modversion['notification']['category'][$i]['item_name']      = 'article';
$modversion['notification']['category'][$i]['allow_bookmark'] = 1;

$i = 0;
++$i;
$modversion['notification']['event'][$i]['name']          = 'article_submit';
$modversion['notification']['event'][$i]['category']      = 'global';
$modversion['notification']['event'][$i]['admin_only']    = 1;
$modversion['notification']['event'][$i]['title']         = art_constant('MI_GLOBAL_ARTICLESUBMIT_NOTIFY');
$modversion['notification']['event'][$i]['caption']       = art_constant('MI_GLOBAL_ARTICLESUBMIT_NOTIFYCAP');
$modversion['notification']['event'][$i]['description']   = art_constant('MI_GLOBAL_ARTICLESUBMIT_NOTIFYDSC');
$modversion['notification']['event'][$i]['mail_template'] = 'global_articlesubmit_notify';
$modversion['notification']['event'][$i]['mail_subject']  = art_constant('MI_GLOBAL_ARTICLESUBMIT_NOTIFYSBJ');

++$i;
$modversion['notification']['event'][$i]['name']          = 'article_new';
$modversion['notification']['event'][$i]['category']      = 'global';
$modversion['notification']['event'][$i]['title']         = art_constant('MI_GLOBAL_NEWARTICLE_NOTIFY');
$modversion['notification']['event'][$i]['caption']       = art_constant('MI_GLOBAL_NEWARTICLE_NOTIFYCAP');
$modversion['notification']['event'][$i]['description']   = art_constant('MI_GLOBAL_NEWARTICLE_NOTIFYDSC');
$modversion['notification']['event'][$i]['mail_template'] = 'global_newarticle_notify';
$modversion['notification']['event'][$i]['mail_subject']  = art_constant('MI_GLOBAL_NEWARTICLE_NOTIFYSBJ');

++$i;
$modversion['notification']['event'][$i]['name']          = 'article_monitor';
$modversion['notification']['event'][$i]['category']      = 'global';
$modversion['notification']['event'][$i]['invisible']     = 1;
$modversion['notification']['event'][$i]['title']         = art_constant('MI_GLOBAL_ARTICLEMONITOR_NOTIFY');
$modversion['notification']['event'][$i]['caption']       = art_constant('MI_GLOBAL_ARTICLEMONITOR_NOTIFYCAP');
$modversion['notification']['event'][$i]['description']   = art_constant('MI_GLOBAL_ARTICLEMONITOR_NOTIFYDSC');
$modversion['notification']['event'][$i]['mail_template'] = 'global_articlemonitor_notify';
$modversion['notification']['event'][$i]['mail_subject']  = art_constant('MI_GLOBAL_ARTICLEMONITOR_NOTIFYSBJ');

++$i;
$modversion['notification']['event'][$i]['name']          = 'article_trackback';
$modversion['notification']['event'][$i]['category']      = 'global';
$modversion['notification']['event'][$i]['admin_only']    = 1;
$modversion['notification']['event'][$i]['title']         = art_constant('MI_GLOBAL_ARTICLETRACKBACK_NOTIFY');
$modversion['notification']['event'][$i]['caption']       = art_constant('MI_GLOBAL_ARTICLETRACKBACK_NOTIFYCAP');
$modversion['notification']['event'][$i]['description']   = art_constant('MI_GLOBAL_ARTICLETRACKBACK_NOTIFYDSC');
$modversion['notification']['event'][$i]['mail_template'] = 'global_trackback_notify';
$modversion['notification']['event'][$i]['mail_subject']  = art_constant('MI_GLOBAL_ARTICLETRACKBACK_NOTIFYSBJ');

++$i;
$modversion['notification']['event'][$i]['name']     = 'article_submit';
$modversion['notification']['event'][$i]['category'] = 'category';
//$modversion["notification"]["event"][$i]["admin_only"] = 1;
$modversion['notification']['event'][$i]['title']         = art_constant('MI_CATEGORY_ARTICLESUBMIT_NOTIFY');
$modversion['notification']['event'][$i]['caption']       = art_constant('MI_CATEGORY_ARTICLESUBMIT_NOTIFYCAP');
$modversion['notification']['event'][$i]['description']   = art_constant('MI_CATEGORY_ARTICLESUBMIT_NOTIFYDSC');
$modversion['notification']['event'][$i]['mail_template'] = 'category_articlesubmit_notify';
$modversion['notification']['event'][$i]['mail_subject']  = art_constant('MI_CATEGORY_ARTICLESUBMIT_NOTIFYSBJ');

++$i;
$modversion['notification']['event'][$i]['name']          = 'article_new';
$modversion['notification']['event'][$i]['category']      = 'category';
$modversion['notification']['event'][$i]['title']         = art_constant('MI_CATEGORY_NEWARTICLE_NOTIFY');
$modversion['notification']['event'][$i]['caption']       = art_constant('MI_CATEGORY_NEWARTICLE_NOTIFYCAP');
$modversion['notification']['event'][$i]['description']   = art_constant('MI_CATEGORY_NEWARTICLE_NOTIFYDSC');
$modversion['notification']['event'][$i]['mail_template'] = 'category_newarticle_notify';
$modversion['notification']['event'][$i]['mail_subject']  = art_constant('MI_CATEGORY_NEWARTICLE_NOTIFYSBJ');

++$i;
$modversion['notification']['event'][$i]['name']          = 'article_approve';
$modversion['notification']['event'][$i]['category']      = 'article';
$modversion['notification']['event'][$i]['title']         = art_constant('MI_ARTICLE_ARTICLEAPPROVE_NOTIFY');
$modversion['notification']['event'][$i]['caption']       = art_constant('MI_ARTICLE_ARTICLEAPPROVE_NOTIFYCAP');
$modversion['notification']['event'][$i]['description']   = art_constant('MI_ARTICLE_ARTICLEAPPROVE_NOTIFYDSC');
$modversion['notification']['event'][$i]['mail_template'] = 'article_approve_notify';
$modversion['notification']['event'][$i]['mail_subject']  = art_constant('MI_ARTICLE_ARTICLEAPPROVE_NOTIFYSBJ');

++$i;
$modversion['notification']['event'][$i]['name']          = 'article_monitor';
$modversion['notification']['event'][$i]['category']      = 'article';
$modversion['notification']['event'][$i]['title']         = art_constant('MI_ARTICLE_ARTICLEMONITOR_NOTIFY');
$modversion['notification']['event'][$i]['caption']       = art_constant('MI_ARTICLE_ARTICLEMONITOR_NOTIFYCAP');
$modversion['notification']['event'][$i]['description']   = art_constant('MI_ARTICLE_ARTICLEMONITOR_NOTIFYDSC');
$modversion['notification']['event'][$i]['mail_template'] = 'article_monitor_notify';
$modversion['notification']['event'][$i]['mail_subject']  = art_constant('MI_ARTICLE_ARTICLEMONITOR_NOTIFYSBJ');
