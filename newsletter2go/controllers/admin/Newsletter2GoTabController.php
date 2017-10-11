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

class Newsletter2GoTabController extends AdminController
{

    const INTEGRATION_CREATED = 'Newsletter2Go integration for Prestashop created successfully!';

    public function __construct()
    {
        $this->bootstrap = true;
        $this->className = 'Configuration';
        $this->table = 'configuration';

        parent::__construct();
    }

    public function initContent()
    {
        parent::initContent();
        $this->initTabModuleList();
        $this->initToolbar();
        $this->initPageHeaderToolbar();
        $this->addToolBarModulesListButton();
        unset($this->toolbar_btn['save']);
        $back = $this->context->link->getAdminLink('AdminDashboard');
        $this->toolbar_btn['back'] = array(
            'href' => $back,
            'desc' => $this->l('Back to the dashboard'),
        );

        $api_key = Configuration::get('NEWSLETTER2GO_API_KEY');
        if (!$api_key) {
            $api_key = $this->createNewServiceAccount();
        }

        $version = $this->getPluginVersion();

        $this->context->smarty->assign(array(
            'web_services_api_key' => $api_key,
            'plugin_version' => $version,
            'url_post' => self::$currentIndex . '&token=' . $this->token,
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn,
        ));

        $this->setTemplate('newsletter2go.tpl');
    }

    public function createTemplate($tpl_name)
    {
        $path = dirname(__FILE__) . DS . '..' . DS . '..' . DS . 'views' . DS . 'templates' . DS . 'admin' . DS;

        return $this->context->smarty->createTemplate($path . $tpl_name, $this->context->smarty);
    }

    public function checkAccess()
    {
        return true;
    }

    public function viewAccess()
    {
        return true;
    }

    private function createNewServiceAccount()
    {
        $api_key = Tools::strtoupper(md5(time()));
        $resources = WebserviceRequest::getResources();
        $db_instance = Db::getInstance();

        $db_instance->insert('webservice_account', array(
            'key' => $api_key,
            'active' => '1',
        ));
        $account_id = $db_instance->Insert_ID();

        $shop_id = (int)Context::getContext()->shop->id;
        $db_instance->insert('webservice_account_shop', array(
            'id_webservice_account' => $account_id,
            'id_shop' => $shop_id,
        ));

        $values = array(
            array(
                'resource' => 'customers',
                'method' => 'PUT',
                'id_webservice_account' => $account_id,
            ),
        );
        foreach (array_keys($resources) as $resource) {
            $values[] = array(
                'resource' => $resource,
                'method' => 'GET',
                'id_webservice_account' => $account_id,
            );
        }

        $db_instance->insert('webservice_permission', $values);
        Configuration::updateValue('NEWSLETTER2GO_API_KEY', $api_key);
        Configuration::updateValue('NEWSLETTER2GO_API_ACCOUNT', $account_id);
        Configuration::updateValue('PS_WEBSERVICE', 1);

        //enables fast-CGI option if it is supported by the server
        $sapi = php_sapi_name();
        if (strpos($sapi, 'cgi') !== false) {
            Configuration::updateValue('PS_WEBSERVICE_CGI_HOST', 1);
        }

        return $api_key;
    }

    public function ajaxProcessGenerateNewApiKey()
    {
        //delete previous settings
        $account_id = Configuration::get('NEWSLETTER2GO_API_ACCOUNT');
        $db_instance = Db::getInstance();
        $where = 'id_webservice_account = ' . $account_id;
        $db_instance->delete('webservice_account', $where);
        $db_instance->delete('webservice_account_shop', $where);
        $db_instance->delete('webservice_permission', $where);

        //apply new settings
        $api_key = $this->createNewServiceAccount();

        die($api_key);
    }

    /**
     * Retrieves version of the installed module
     * @return string
     */
    protected function getPluginVersion()
    {
        $module = Module::getInstanceByName('newsletter2go');

        $version =  str_replace('.', '', $module->version);

        return $version;
    }
}
