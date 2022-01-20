<?php defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Language\Text;

class plgSystemArticlefrontviewbtn extends CMSPlugin
{
    protected $app;
    protected $db;
    protected $autoloadLanguage = true;
    private $unset = [];

    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);

        $unsetList = $this->params->get('unsetList', []);

        $i = 0;
        if (!is_array($unsetList)) {
            foreach ($unsetList as $unsetItem) {
                if (!is_array($unsetItem->options)) {
                    foreach ($unsetItem->options as $option) {
                        $this->unset[$i][$option->prm] = !$option->tp ? $option->val : 0;
                    }
                    $i++;
                }
            }
        }
    }

    public function onBeforeRender()
    {
        if (
            Factory::getDocument()->getType() != 'html' ||
            !$this->app->isClient('administrator') ||
            $this->checkUnsetExtension()
        ) {
            return false;
        }

        $url = $this->getUrlInDefaultExtension();
        if (!$url) {
            return false;
        }

        $url = substr(Route::_($url), strlen(Uri::base(true)) + 1);
        $url = SiteApplication::getRouter('site')->build($url)->toString();

        $html = '<a href="' . $url . '" target="_blank" class="btn btn-small"><span class="icon-eye"></span> ' . Text::_('PLG_SYSTEM_ARTICLEFRONTVIEWBTN_TITLE') . '</a>';
        ToolBar::getInstance('toolbar')->appendButton('Custom', $html);
    }

    private function getItemid($option, $view, $id)
    {
        $items = Factory::getApplication()->getMenu('site')->getItems('component', $option);
        foreach ($items as $item) {
            if ($item->query['view'] === $view && isset($item->query['id']) && $item->query['id'] === $id) {
                return $item->id;
            }
        }
        return 0;
    }

    private function getUrlInDefaultExtension()
    {
        $unsetOptions = [
            'com_admin',
            'com_advancedmodules',
            'com_associations',
            'com_banners',
            'com_fields',
            'com_finder',
            'com_languages',
            'com_messages',
            'com_modules',
            'com_newsfeeds',
            'com_plugins',
            'com_privacy',
            'com_redirect',
            'com_templates',
            'com_users'
        ];

        $option = $this->app->input->get('option');
        if (in_array($option, $unsetOptions)) {
            return false;
        }

        $view   = $this->app->input->get('view');
        $layout = $this->app->input->get('layout', 'default');
        $id     = $this->app->input->get('id', 0);
        if ($layout == 'edit' && $id && $option && $view) {
            switch ($option) {
                case 'com_menus':
                    return $view === 'item' ? 'index.php?Itemid=' . $id : false;
                    break;
                case 'com_content':
                    $menuId = $this->getItemid($option, $view, $id);
                    $table = Table::getInstance('Content');
                    $table->load($id);
                    $catid  = $table->catid;
                    unset($table);
                    return 'index.php?option=' . $option . '&view=' . $view . '&id=' . $id . ($catid ? '&catid=' . $catid : '') . ($menuId ? '&Itemid=' . $menuId : '');
                    break;
                case 'com_categories':
                    $menuId = $this->getItemid($option, $view, $id);
                    $extension = $this->app->input->get('extension', '');
                    return 'index.php?option=' . ($extension ? $extension : 'com_categories') . '&view=' . $view . '&id=' . $id . ($menuId ? '&Itemid=' . $menuId : '');
                    break;
                default:
                    $menuId = $this->getItemid($option, $view, $id);
                    $catid  = $this->app->input->get('catid', 0);
                    return 'index.php?option=' . $option . '&view=' . $view . '&id=' . $id . ($catid ? '&catid=' . $catid : '') . ($menuId ? '&Itemid=' . $menuId : '');
            }
        } else {
            return false;
        }
    }

    private function checkUnsetExtension()
    {
        if ($this->unset) {
            foreach ($this->unset as $unset) {
                $prms = [];
                $check = true;
                foreach ($unset as $key => $val) {
                    $prms[$key] = $this->app->input->get($key);
                    if (strtolower($prms[$key]) != strtolower($unset[$key])) {
                        $check = false;
                        break;
                    }
                }
                if ($check) {
                    return true;
                }
            }
        }
        return false;
    }
}
