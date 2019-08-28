<?php defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;

class plgSystemArticlefrontviewbtnInstallerScript
{
    function preflight($type, $parent)
    {
        if (strtolower($type) === 'uninstall') {
            return true;
        }

        $msg = '';
        $ver = new Version();
        $name = Text::_($parent->manifest->name[0]);
        $minPhpVersion = $parent->manifest->php_minimum[0];

        $minJoomlaVersion = $parent->manifest->attributes()->version[0];
        if (version_compare($ver->getShortVersion(), $minJoomlaVersion, 'lt')) {
            $msg .= Text::sprintf('J_JOOMLA_COMPATIBLE', $name, $minJoomlaVersion);
        }

        if (version_compare(phpversion(), $minPhpVersion, 'lt')) {
            $msg .= Text::sprintf('J_PHP_COMPATIBLE', $name, $minPhpVersion);
        }

        if ($msg) {
            Factory::getApplication()->enqueueMessage($msg, 'error');
            return false;
        }
    }

    public function postflight($type, $parent)
    {
        if (strtolower($type) === 'uninstall') {
            return true;
        }

        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->update('#__extensions')
            ->set('enabled = 1')
            ->where('element = ' . $db->quote('articlefrontviewbtn'))
            ->where('type = ' . $db->quote('plugin'))
            ->where('folder = ' . $db->quote('system'));
        $db->setQuery($query)->execute();
    }
}
