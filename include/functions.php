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

// defined('XOOPS_ROOT_PATH') || exit('Restricted access.');

include __DIR__ . '/vars.php';
define($GLOBALS['artdirname'] . '_FUNCTIONS_LOADED', true);

if (!defined('ART_FUNCTIONS')):
    define('ART_FUNCTIONS', 1);

    load_functions();
    mod_loadFunctions('parse', $GLOBALS['artdirname']);
    mod_loadFunctions('url', $GLOBALS['artdirname']);
    mod_loadFunctions('render', $GLOBALS['artdirname']);
    mod_loadFunctions('user', $GLOBALS['artdirname']);
    mod_loadFunctions('rpc', $GLOBALS['artdirname']);
    mod_loadFunctions('time', $GLOBALS['artdirname']);
    //mod_loadFunctions("cache", $GLOBALS["artdirname"]);
    mod_loadFunctions('recon', $GLOBALS['artdirname']);

    /**
     * Function to display messages
     *
     * @var mixed $messages
     * @return bool
     */
    function art_message($message)
    {
        return mod_message($message);
    }

    // Backword compatible
    function art_load_lang_file($filename, $module = '', $default = 'english')
    {
        if (empty($module) && is_object($GLOBALS['xoopsModule'])) {
            $module = $GLOBALS['xoopsModule']->getVar('dirname');
        }

        return xoops_loadLanguage($filename, $module);
    }

    /**
     * Function to set a cookie with module-specified name
     *
     * using customized serialization method
     * @param        $name
     * @param string $string
     * @param int    $expire
     */
    function art_setcookie($name, $string = '', $expire = 0)
    {
        if (is_array($string)) {
            $value = [];
            foreach ($string as $key => $val) {
                $value[] = $key . '|' . $val;
            }
            $string = implode(',', $value);
        }
        $expire = empty($expire) ? 3600 * 24 * 30 : (int)$expire;
        //setcookie($GLOBALS["ART_VAR_PREFIX"].$name, $string, (int)($expire), '/');
        setcookie($name, $string, time() + $expire, '/');
    }

    function art_getcookie($name, $isArray = false)
    {
        //$value = isset($_COOKIE[$GLOBALS["ART_VAR_PREFIX"].$name]) ? $_COOKIE[$GLOBALS["ART_VAR_PREFIX"].$name] : null;
        $value = isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
        if ($isArray) {
            $_value = $value ? explode(',', $value) : [];
            $value  = [];
            foreach ($_value as $string) {
                $key         = substr($string, 0, strpos($string, '|'));
                $val         = substr($string, strpos($string, '|') + 1);
                $value[$key] = $val;
            }
            unset($_value);
        }

        return $value;
    }

    /**
     * Get structured categories
     *
     * @int integer     $pid    parent category ID
     *
     * @param int  $pid
     * @param bool $refresh
     * @return array
     */
    function art_getSubCategory($pid = 0, $refresh = false)
    {
        $list = @mod_loadCacheFile('category', $GLOBALS['artdirname']);
        if (!is_array($list) || $refresh) {
            $list = art_createSubCategoryList();
        }
        if (0 == $pid) {
            return $list;
        } else {
            return @$list[$pid];
        }
    }

    function art_createSubCategoryList()
    {
        $categoryHandler = xoops_getModuleHandler('category', $GLOBALS['artdirname']);
        $criteria        = new CriteriaCompo('1', 1);
        $criteria->setSort('cat_pid ASC, cat_order');
        $criteria->setOrder('ASC');
        $categories_obj = $categoryHandler->getObjects($criteria);
        require_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['artdirname'] . '/class/tree.php';
        $tree           = new artTree($categories_obj, 'cat_id', 'cat_pid');
        $category_array = [];
        foreach (array_keys($categories_obj) as $key) {
            if (!$child = array_keys($tree->getAllChild($categories_obj[$key]->getVar('cat_id')))) {
                continue;
            }
            $category_array[$categories_obj[$key]->getVar('cat_id')] = $child;
        }
        unset($categories_obj, $tree, $criteria);
        mod_createCacheFile($category_array, 'category', $GLOBALS['artdirname']);

        return $category_array;
    }
endif;
