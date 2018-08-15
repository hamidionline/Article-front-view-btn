<?php defined( '_JEXEC' ) or die( 'Restricted access' );

class plgSystemArticlefrontviewbtnInstallerScript
{
    public function postflight($type, $parent)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->update('#__extensions')
            ->set('enabled = 1')
            ->where('element = '.$db->quote('articlefrontviewbtn'))
            ->where('type = '.$db->quote('plugin'))
            ->where('folder = '.$db->quote('system'))
        ;
        $db->setQuery($query)->execute();
    }
}