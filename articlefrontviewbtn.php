<?php defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgSystemArticlefrontviewbtn extends JPlugin
{
    public function onBeforeRender()
    {
        $app = JFactory::getApplication();

        if (JFactory::getDocument()->getType() != 'html')
        {
            return false;
        }

        if (!$app->isAdmin())
        {
            return false;
        }

        $option = $app->input->get('option');
        $view   = $app->input->get('view');
        $layout = $app->input->get('layout');
        $id     = $app->input->get('id');
        if ($option == 'com_content' && $view == 'article' && $layout == 'edit' && $id)
        {
            $html = '<a href="' . JUri::root() . 'index.php?option=com_content&view=article&id=' . $id . '" target="_blank" class="btn btn-small"><span class="icon-eye"></span> See what the site looks like</a>';
            $toolbar = JToolBar::getInstance('toolbar');
            $toolbar->appendButton('Custom', $html);
        }
        else
        {
            return false;
        }
    }
}