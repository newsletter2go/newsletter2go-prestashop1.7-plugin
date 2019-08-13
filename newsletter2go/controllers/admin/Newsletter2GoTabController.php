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

include_once(dirname(__FILE__) . '/../../Service/Newsletter2goApiService.php');

class Newsletter2GoTabController extends AdminController
{

    const INTEGRATION_CREATED = 'Newsletter2Go integration for Prestashop created successfully!';

    public function __construct()
    {
        $this->bootstrap = true;
        $this->className = 'Configuration';
        $this->table = 'configuration';
        $this->name = 'newsletter2go';

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
        $listId = null;
        $newsletterId = null;
        $connectedCompany = null;
        $handleCartAfter = null;
        $transactionalMailings = [];
        $apiClient = new Newsletter2goApiService;
        $company = $apiClient->testConnection();
        $testConnection = false;
        if ($company['status'] === 200) {
            $testConnection = true;
            $connectedCompany = $company['company_name'] . ', ' . $company['company_bill_address'];
        }

        $userIntegration = $apiClient->getUserIntegration(Configuration::get('NEWSLETTER2GO_USER_INTEGRATION_ID'));

        if (isset($userIntegration) && $apiClient->getLastStatusCode() === 200) {
            $listId = $userIntegration['list_id'];
            $newsletterId = $userIntegration['newsletter_id'];
            $handleCartAfter = $userIntegration['handle_cart_as_abandoned_after'];

            if (isset($listId)) {
                $transactionalMailingsResponse = $apiClient->getTransactionalMailings($listId);

                foreach ($transactionalMailingsResponse as $transactionalMailing) {
                    $transactionalMailings[$transactionalMailing['id']] = $transactionalMailing['name'];
                }
            }
        }

        $version = $this->getPluginVersion();

        $enableTracking = Configuration::get('NEWSLETTER2GO_TRACKING_ORDER');
        $enableAbandonedShoppingCart = Configuration::get('NEWSLETTER2GO_ABANDONED_SHOPPING_CART');

        $this->context->smarty->assign(
            array(
                'test_connection' => $testConnection,
                'company' => $connectedCompany,
                'list_id' => $listId,
                'newsletter_id' => $newsletterId,
                'transactionalMailings' => $transactionalMailings,
                'handleCartAfter' => $handleCartAfter,
                'web_services_api_key' => $api_key,
                'plugin_version' => $version,
                'url_post' => self::$currentIndex . '&token=' . $this->token,
                'show_page_header_toolbar' => $this->show_page_header_toolbar,
                'page_header_toolbar_title' => $this->page_header_toolbar_title,
                'page_header_toolbar_btn' => $this->page_header_toolbar_btn,
                'callback_url' => $this->context->link->getModuleLink('newsletter2go', 'Callback'),
                'enable_tracking' => isset($enableTracking) && $enableTracking === '1',
                'enable_abandoned_shopping_cart' => isset($enableAbandonedShoppingCart) && $enableAbandonedShoppingCart === '1'
            )
        );

        $this->setTemplate('newsletter2go.tpl');
    }

    public function createTemplate($tpl_name)
    {
        return $this->context->smarty->createTemplate(
            _PS_MODULE_DIR_ . $this->name . '/views/templates/admin/' . $tpl_name,
            $this->context->smarty
        );
    }

    public function checkAccess()
    {
        return true;
    }

    public function viewAccess($disable = false)
    {
        return true;
    }

    private function createNewServiceAccount()
    {
        $api_key = Tools::strtoupper(md5(time()));
        $resources = WebserviceRequest::getResources();
        $db_instance = Db::getInstance();

        $db_instance->insert(
            'webservice_account',
            array(
                'key' => $api_key,
                'active' => '1',
            )
        );
        $account_id = $db_instance->Insert_ID();

        $shop_id = (int)Context::getContext()->shop->id;
        $db_instance->insert(
            'webservice_account_shop',
            array(
                'id_webservice_account' => $account_id,
                'id_shop' => $shop_id,
            )
        );

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

    public function ajaxProcessSaveSettings()
    {
        Configuration::updatevalue('NEWSLETTER2GO_TRACKING_ORDER', Tools::getValue('conversionTracking'));
        Configuration::updatevalue('NEWSLETTER2GO_ABANDONED_SHOPPING_CART', Tools::getValue('shoppingCart'));
        $transactionalMailingId = Tools::getValue('transactionalMailingId');
        $transactionalMailingHandleTime = Tools::getValue('transactionalMailingHandleTime');
        $apiClient = new Newsletter2goApiService;
        $apiClient->addTransactionMailingToUserIntegration(
            Configuration::get('NEWSLETTER2GO_USER_INTEGRATION_ID'),
            $transactionalMailingId,
            $transactionalMailingHandleTime
        );

        die();
    }

    public function ajaxProcessDisconnect()
    {
        Configuration::updatevalue('NEWSLETTER2GO_TRACKING_ORDER', 0);
        Configuration::updatevalue('NEWSLETTER2GO_ABANDONED_SHOPPING_CART', 0);
        Configuration::updatevalue('NEWSLETTER2GO_ACCESS_TOKEN', '');
        Configuration::updatevalue('NEWSLETTER2GO_REFRESH_TOKEN', '');
        Configuration::updatevalue('NEWSLETTER2GO_COMPANY_ID', '');
        Configuration::updatevalue('NEWSLETTER2GO_USER_INTEGRATION_ID', '');

        die();
    }

    /**
     * Retrieves version of the installed module
     * @return string
     */
    protected function getPluginVersion()
    {
        $module = Module::getInstanceByName('newsletter2go');

        $version = str_replace('.', '', $module->version);

        return $version;
    }
}
