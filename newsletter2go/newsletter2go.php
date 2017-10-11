<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class Newsletter2Go extends Module
{
    public function __construct()
    {
        $this->module_key = '0372c81a8fe76ebddb8ec637278afe98';
        $this->name = 'newsletter2go';
        $this->tab = 'advertising_marketing';
        $this->version = '4.0.01';
        $this->author = 'Newsletter2Go';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;
        $this->controllers = array('Export');
        parent::__construct();
        $this->displayName = $this->l('Newsletter2Go email marketing');
        $this->description = $this->l('Adds email marketing functionality to your E-commerce platform. Easily synchronize your contacts and send product newsletters');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        if (!Configuration::get('NEWSLETTER2GO_NAME')) {
            $this->warning = $this->l('No name provided');
        }
    }

    public function install()
    {
        // Install Tabs
        $tab = new Tab();
        // Need a foreach for the language
        $tab->name[(int)Configuration::get('PS_LANG_DEFAULT')] = $this->l('Newsletter2Go');
        $tab->class_name = 'Newsletter2GoTab';
		// Set parent tab id
        $parent_id = (_PS_VERSION_ >= '1.7.0.0' ? (int)Tab::getIdFromClassName('CONFIGURE') : 0);
		$tab->id_parent = $parent_id;
        $tab->module = $this->name;
        $tab->add();

        // Set icon image when menu is collapsed
        if (_PS_VERSION_ >= '1.7') {
            $db = Db::getInstance();
            $db->update('tab', array('icon' => 'sms'), 'id_tab = ' . $tab->id);
        }

        return parent::install() && $this->registerUrls() && $this->registerHook('backOfficeHeader');
    }

    public function uninstall()
    {
        // Deactivate the previous API key
        $account_id = Configuration::get('NEWSLETTER2GO_API_ACCOUNT');
        $db_instance = Db::getInstance();
        $db_instance->update('webservice_account', array('active' => '0'), 'id_webservice_account = ' . $account_id);

        // Remove values from configuration
        Configuration::deleteByName('NEWSLETTER2GO_API_KEY');
        Configuration::deleteByName('NEWSLETTER2GO_API_ACCOUNT');

        $tab = new Tab((int)Tab::getIdFromClassName('Newsletter2GoTab'));
        $tab->delete();

        return parent::uninstall();
    }

    public function hookBackOfficeHeader()
    {
        $param = md5(time());
        $this->context->controller->addJS($this->_path . 'views/js/nl2go_script.js?param=' . $param, false);
        $this->context->controller->addCSS($this->_path . 'views/css/menuTabIcon.css?param=' . $param, 'all', null, false);
    }

    /**
     * Registers rewrite urls for frontend controller
     * @return bool
     */
    public function registerUrls()
    {
        try {
            foreach (Language::getLanguages() as $language) {
                $data = Meta::getMetaByPage('module-newsletter2go-Export', $language['id_lang']);
                $meta = new Meta($data['id_meta']);
                if ($meta && $meta->id) {
                    $meta->url_rewrite = 'n2go-export';
                    $meta->save();
                }
            }
        } catch (Exception $e) {
            return false;
        }

        return true;
    }
	
}
