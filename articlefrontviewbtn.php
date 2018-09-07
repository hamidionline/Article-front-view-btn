<?php defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Toolbar\Toolbar;

class plgSystemArticlefrontviewbtn extends CMSPlugin
{
    public function onBeforeRender()
    {
        $app = Factory::getApplication();

        if ( Factory::getDocument()->getType() != 'html' )
        {
            return false;
        }

        if ( !$app->isAdmin() )
        {
            return false;
        }

        $option = $app->input->get( 'option' );
        $view   = $app->input->get( 'view' );
        $layout = $app->input->get( 'layout' );
        $id     = $app->input->get( 'id' );
        
        if ( $layout == 'edit' && $id && $option && $view )
        {
            $title = 'See what the site looks like';
            
            $url = 'index.php?option=' . $option . '&view=' . $view . '&id=' . $id;
            
            $url = substr( Route::_( $url ), strlen( Uri::base( true ) ) + 1 );
            $url = SiteApplication::getRouter( 'site' )->build( $url )->toString();
            $url = Uri::root() . substr( $url, strlen( Uri::base( true ) ) + 1 );

            $html = '<a href="' . $url . '" target="_blank" class="btn btn-small"><span class="icon-eye"></span> ' . $title . '</a>';
            $toolbar = ToolBar::getInstance( 'toolbar' );
            $toolbar->appendButton( 'Custom', $html );
        }
        else
        {
            return false;
        }
    }
}
