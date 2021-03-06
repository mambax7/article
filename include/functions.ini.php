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

/*

The functions loaded on initializtion
*/

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

if (!defined('ART_FUNCTIONS_INI')):
    define('ART_FUNCTIONS_INI', 1);

    function art_constant($name)
    {
        if (defined($GLOBALS['ART_VAR_PREFIXU'] . '_' . mb_strtoupper($name))) {
            return constant($GLOBALS['ART_VAR_PREFIXU'] . '_' . mb_strtoupper($name));
        }

        return mb_strtolower($name);
    }

    function art_DB_prefix($name, $isRel = false)
    {
        $relative_name = $GLOBALS['ART_DB_PREFIX'] . '_' . $name;
        if ($isRel) {
            return $relative_name;
        }

        return $GLOBALS['xoopsDB']->prefix($relative_name);
    }

    function art_load_object()
    {
        // For backward compat
    }

    function art_load_config()
    {
        static $moduleConfig;
        if (isset($moduleConfig[$GLOBALS['artdirname']])) {
            return $moduleConfig[$GLOBALS['artdirname']];
        }

        //load_functions("config");
        //$moduleConfig[$GLOBALS["artdirname"]] = mod_loadConfig($GLOBALS["artdirname"]);

        if (isset($GLOBALS['xoopsModule']) && is_object($GLOBALS['xoopsModule'])
            && $GLOBALS['xoopsModule']->getVar('dirname', 'n') == $GLOBALS['artdirname']) {
            if (!empty($GLOBALS['xoopsModuleConfig'])) {
                $moduleConfig[$GLOBALS['artdirname']] = $GLOBALS['xoopsModuleConfig'];
            } else {
                return null;
            }
        } else {
            /** @var \XoopsModuleHandler $moduleHandler */
            $moduleHandler = xoops_getHandler('module');
            $module        = $moduleHandler->getByDirname($GLOBALS['artdirname']);

            $configHandler = xoops_getHandler('config');
            $criteria      = new \CriteriaCompo(new \Criteria('conf_modid', $module->getVar('mid')));
            $configs       = $configHandler->getConfigs($criteria);
            foreach (array_keys($configs) as $i) {
                $moduleConfig[$GLOBALS['artdirname']][$configs[$i]->getVar('conf_name')] = $configs[$i]->getConfValueForOutput();
            }
            unset($configs);
        }
        if ($customConfig = @require_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['artdirname'] . '/include/plugin.php') {
            $moduleConfig[$GLOBALS['artdirname']] = array_merge($moduleConfig[$GLOBALS['artdirname']], $customConfig);
        }

        return $moduleConfig[$GLOBALS['artdirname']];
    }

    function art_define_url_delimiter()
    {
        if (defined('URL_DELIMITER')) {
            if (!in_array(URL_DELIMITER, ['?', '/'])) {
                die('Exit on security');
            }
        } else {
            $moduleConfig = art_load_config();
            if (empty($moduleConfig['do_urw'])) {
                define('URL_DELIMITER', '?');
            } else {
                define('URL_DELIMITER', '/');
            }
        }
    }
endif;
