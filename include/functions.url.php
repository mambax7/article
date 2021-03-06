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
define($GLOBALS['artdirname'] . '_FUNCTIONS_URL_LOADED', true);

if (!defined('ART_FUNCTIONS_URL')):
    define('ART_FUNCTIONS_URL', 1);

    $GLOBALS['art_args'] = [
        'a' => 'article',
        'c' => 'category',
        'k' => 'keyword',
        'p' => 'page',
        's' => 'start',
        't' => 'topic',
        'u' => 'uid',
    ];

    /**
     * Build an URL with the specified request params
     *
     * By calling $xoops->buildUrl
     * @param       $url
     * @param array $params
     * @return string
     */
    function art_buildUrl($url, $params = []/*, $params_string = array(), $params_numeric = array()*/)
    {
        $url = $GLOBALS['xoops']->buildUrl($url);
        if (!empty($params)) {
            $args = array_flip($GLOBALS['art_args']);
            foreach ($params as $k => $v) {
                if (isset($args[$k])) {
                    $params[$k] = $args[$k] . rawurlencode($v);
                } else {
                    $params[$k] = rawurlencode($v);
                }
            }
            art_define_url_delimiter();
            $url .= URL_DELIMITER . implode('&amp;', $params);
        }

        return $url;
    }

    /**
     * Function to parse arguments for a page according to $_SERVER['REQUEST_URI']
     *
     * @param mixed $args_numeric array of numeric variable values
     * @param mixed $args         array of indexed variables: name and value
     * @var mixed   $args_string  array of string variable values
     * @return bool true on args parsed
     */

    /* known issues:
     * - "/" in a string
     * - "&" in a string
     */
    function art_parseUrl(&$args_numeric, &$args, &$args_string)
    {
        $args_abb     = $GLOBALS['art_args'];
        $args         = [];
        $args_numeric = [];
        $args_string  = [];
        if (preg_match("/[^\?]*\.php[\/|\?]([^\?]*)/i", $_SERVER['REQUEST_URI'], $matches)) {
            $vars = preg_split("/[\/|&]/", $matches[1]);
            $vars = array_map('trim', $vars);
            if (count($vars) > 0) {
                foreach ($vars as $var) {
                    if (is_numeric($var)) {
                        $args_numeric[] = $var;
                    } elseif (false === mb_strpos($var, '=')) {
                        if (is_numeric(mb_substr($var, 1))) {
                            $args[$args_abb[mb_strtolower($var[0])]] = (int)mb_substr($var, 1);
                        } else {
                            $args_string[] = urldecode($var);
                        }
                    } else {
                        parse_str($var, $args);
                    }
                }
            }
        }

        return (count($args) + count($args_numeric) + count($args_string));
    }

    /**
     * Function to get linked image located in module file folder
     *
     * @param mixed       $imageName image file name
     * @param null|mixed  $imagePath full path to the image file if different from preset folder
     * @param null|mixed  $size      size parameters for pseudo thumbnail
     * @param mixed       $alt       alter string
     * @return string linked image tag
     */
    function art_getImageLink($imageName, $imagePath = null, $size = null, $alt = '')
    {
        global $xoopsModuleConfig, $xoopsModule;
        $helper = \XoopsModules\Article\Helper::getInstance();

        if (empty($imageName)) {
            return null;
        }

        if (empty($size['width']) && empty($size['height'])):
            return '<img src="' . art_getImageUrl($imageName, $imagePath) . '" alt="' . $alt . '">';
        endif;

        if (empty($imagePath)) {
            $moduleConfig = art_load_config();
            $path_image   = $helper->getConfig('path_image');
            $imageFile    = XOOPS_ROOT_PATH . '/' . $path_image . '/' . htmlspecialchars($imageName, ENT_QUOTES | ENT_HTML5);
            $imageUrl     = XOOPS_URL . '/' . $path_image . '/' . htmlspecialchars($imageName, ENT_QUOTES | ENT_HTML5);
        } else {
            if (!preg_match('/^' . preg_quote(XOOPS_ROOT_PATH, '/') . '/', $imagePath)) {
                $imagePath = XOOPS_ROOT_PATH . '/' . $imagePath;
            }
            $imageFile = htmlspecialchars($imagePATH . '/' . $imageName, ENT_QUOTES | ENT_HTML5);
            $imageUrl  = htmlspecialchars(XOOPS_URL . '/' . preg_replace('/^' . preg_quote(XOOPS_ROOT_PATH, '/') . '/', '', $imagePath) . '/' . $imageName, ENT_QUOTES | ENT_HTML5);
        }
        $imageSizeString = '';
        if (!$imageSize = @getimagesize($imageFile)) {
        } elseif (!empty($size['width']) && $size['width'] < $imageSize[0]) {
            $imageSizeString = 'width: ' . $size['width'] . 'px';
        } elseif (!empty($size['height']) && $size['height'] < $imageSize[1]) {
            $imageSizeString = 'height: ' . $size['height'] . 'px';
        }
        $link = '<img src="' . $imageUrl . '" style="' . $imageSizeString . '" alt="' . $alt . '">';

        return $link;
    }

    /**
     * Function to get url of an file located in module file folder
     *
     * @param mixed       $imageName image file name
     * @param null|mixed  $imagePath full path to the image file if different from preset folder
     * @return string image url
     */
    function art_getFileUrl($imageName, $imagePath = null)
    {
        global $xoopsModuleConfig, $xoopsModule;

        if (empty($imageName)) {
            return null;
        }

        if (empty($imagePath)) {
            //            $moduleConfig = art_load_config();
            $moduleDirName = basename(dirname(__DIR__));
            /** @var \XoopsModules\Article\Helper $helper */
            $helper     = \XoopsModules\Article\Helper::getInstance();
            $path_image = $helper->getConfig('path_file');
            $imageUrl   = XOOPS_URL . '/' . $path_image . '/' . htmlspecialchars($imageName, ENT_QUOTES | ENT_HTML5);
        } else {
            $imageUrl = htmlspecialchars(XOOPS_URL . '/' . preg_replace('/^' . preg_quote(XOOPS_ROOT_PATH, '/') . '/', '', $imagePath) . '/' . $imageName, ENT_QUOTES | ENT_HTML5);
        }

        return $imageUrl;
    }

    /**
     * Function to get url of an image located in module utility image folder
     *
     * @param mixed       $imageName image file name
     * @param null|mixed  $imagePath full path to the image file if different from preset folder
     * @return string image url
     */
    function art_getImageUrl($imageName, $imagePath = null)
    {
        global $xoopsModuleConfig, $xoopsModule;
        $helper = \XoopsModules\Article\Helper::getInstance();

        if (empty($imageName)) {
            return null;
        }

        if (empty($imagePath)) {
            $moduleConfig = art_load_config();
            $path_image   = $helper->getConfig('path_image');
            $imageUrl     = XOOPS_URL . '/' . $path_image . '/' . htmlspecialchars($imageName, ENT_QUOTES | ENT_HTML5);
        } else {
            $imageUrl = htmlspecialchars(XOOPS_URL . '/' . preg_replace('/^' . preg_quote(XOOPS_ROOT_PATH, '/') . '/', '', $imagePath) . '/' . $imageName, ENT_QUOTES | ENT_HTML5);
        }

        return $imageUrl;
    }
endif;
