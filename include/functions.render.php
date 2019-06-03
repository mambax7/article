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

require_once __DIR__ . '/vars.php';
define($GLOBALS['artdirname'] . '_FUNCTIONS_RENDER_LOADED', true);

/**
 * Function to get template file of a specified style of a specified page
 *
 * @param mixed      $page  page name
 * @param null|mixed $style template style
 * @return string template file name, using default style if style is invalid
 */
function art_getTemplate($page = 'index', $style = null)
{
    global $xoops;

    $template_dir = $xoops->path("modules/{$GLOBALS['artdirname']}/templates/");
    $style        = empty($style) ? '' : '_' . $style;
    $file_name    = "{$GLOBALS['artdirname']}_{$page}{$style}.tpl";
    if (file_exists($template_dir . $file_name)) {
        return $file_name;
    }
    if (!empty($style)) {
        $style     = '';
        $file_name = "{$GLOBALS['artdirname']}_{$page}{$style}.tpl";
        if (file_exists($template_dir . $file_name)) {
            return $file_name;
        }
    }

    return null;
}

/**
 * Function to get a list of template files of a page, indexed by file name
 *
 * @param mixed       $page page name
 * @param bool|boolen $refresh
 * @return array@internal param boolen $refresh recreate the data
 */
function &art_getTemplateList($page = 'index', $refresh = false)
{
    $TplFiles = art_getTplPageList($page, $refresh);
    $template = [];
    if (isset($TplFiles)) {
        foreach (array_keys($TplFiles) as $temp) {
            $template[$temp] = $temp;
        }
    }

    return $template;
}

/**
 * Function to get CSS file URL of a style
 *
 * The hardcoded path is not desirable for theme switch, however, we have to keep it before getting a good solution for cache
 *
 * @param mixed $style
 * @return string file URL, false if not found
 */
function art_getCss($style = 'default')
{
    global $xoops;

    if (is_readable($xoops->path('modules/' . $GLOBALS['artdirname'] . '/assets/css/style_' . mb_strtolower($style) . '.css'))) {
        return $xoops->path('modules/' . $GLOBALS['artdirname'] . '/assets/css/style_' . mb_strtolower($style) . '.css', true);
    }

    return $xoops->path('modules/' . $GLOBALS['artdirname'] . '/assets/css/style.css', true);
}

/**
 * Function to module header for a page with specified style
 *
 * @param mixed $style
 * @return string
 */
function art_getModuleHeader($style = 'default')
{
    $xoops_module_header = '<link rel="stylesheet" type="text/css" href="' . art_getCss($style) . '">';

    return $xoops_module_header;
}

/**
 * Function to get a list of template files of a page, indexed by style
 *
 * @param mixed $page page name
 *
 * @param bool  $refresh
 * @return array
 */
function &art_getTplPageList($page = '', $refresh = true)
{
    $list = null;

    $cache_file = empty($page) ? 'template-list' : 'template-page';
    /*
     load_functions("cache");
     $list = mod_loadCacheFile($cache_file, $GLOBALS["artdirname"]);
     */

    xoops_load('xoopscache');
    $key  = $GLOBALS['artdirname'] . "_{$cache_file}";
    $list = \XoopsCache::read($key);

    if (!is_array($list) || $refresh) {
        $list = art_template_lookup(!empty($page));
    }

    $ret = empty($page) ? $list : @$list[$page];

    return $ret;
}

function &art_template_lookup($index_by_page = false)
{
    require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';

    $files = \XoopsLists::getHtmlListAsArray(XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['artdirname'] . '/templates/');
    $list  = [];
    foreach ($files as $file => $name) {
        // The valid file name must be: art_article_mytpl.tpl OR art_category-1_your-trial.tpl
        if (preg_match('/^' . $GLOBALS['ART_VAR_PREFIX'] . "_([^_]*)(_(.*))?\.(html|tpl|xotpl)$/i", $name, $matches)) {
            if (empty($matches[1])) {
                continue;
            }
            if (empty($matches[3])) {
                $matches[3] = 'default';
            }
            if (empty($index_by_page)) {
                $list[] = ['file' => $name, 'description' => $matches[3]];
            } else {
                $list[$matches[1]][$matches[3]] = $name;
            }
        }
    }

    $cache_file = empty($index_by_page) ? 'template-list' : 'template-page';
    xoops_load('xoopscache');
    $key = $GLOBALS['artdirname'] . "_{$cache_file}";
    XoopsCache::write($key, $list);

    //load_functions("cache");
    //mod_createCacheFile($list, $cache_file, $GLOBALS["artdirname"]);
    return $list;
}

function &art_htmlSpecialChars(&$text)
{
    $text = preg_replace(['/&amp;/i', '/&nbsp;/i'], ['&', '&amp;nbsp;'], htmlspecialchars($text, ENT_QUOTES | ENT_HTML5));

    return $text;
}

function &art_displayTarea(&$text, $html = 1, $smiley = 1, $xcode = 1, $image = 1, $br = 1)
{
    $myts = \MyTextSanitizer::getInstance();
    if (1 != $html) {
        // html not allowed
        $text = art_htmlSpecialChars($text);
    }
    $text = $myts->codePreConv($text, $xcode);
    $text = $myts->makeClickable($text);
    if (0 != $smiley) {
        // process smiley
        $text = $myts->smiley($text);
    }
    if (0 != $xcode) {
        // decode xcode
        $text = $myts->xoopsCodeDecode($text, $image);
    }
    if (0 != $br) {
        $text = $myts->nl2Br($text);
    }
    $text = $myts->codeConv($text, $xcode, $image);

    return $text;
}

/**
 * Function to filter text
 *
 * @param $document
 * @return string filtered text
 */
function &art_html2text(&$document)
{
    $document = strip_tags($document);

    return $document;
}
