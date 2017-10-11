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

/**
 * Workaround class because PrestaShop API can't be extended in a module
 * Class Newsletter2GoExportModuleFrontController
 */
class Newsletter2GoExportModuleFrontController extends ModuleFrontController
{

    public function init()
    {
        $apiKey = Tools::getValue('apiKey');
        $limit = Tools::getValue('limit');
        $action = Tools::getValue('action');
        $emails = Tools::getValue('emails');
        $subscribed = Tools::getValue('subscribed');

        try {
            header('Content-Type: application/xml');
            if (!$this->checkApiKey($apiKey)) {
                echo $this->errorMessage('Invalid authentication key format');
                die;
            }

            if (!$this->tableExists()) {
                echo $this->errorMessage('Newsletter module is not installed');
                die;
            }


            $xml = null;
            switch ($action) {
                case 'getSubscribers':
                    $xml = $this->getSubscribers($subscribed, $limit, $emails);
                    break;
                case 'getPluginVersion':
                    $xml = $this->getPluginVersion();
                    break;
                default:
                    $xml = $this->errorMessage('Action not found', 404);
                    break;
            }
        } catch (Exception $e) {
            $xml = $this->errorMessage($e->getMessage());
        }

        echo $xml->asXML();
        die;
    }

    /**
     * @param string $subscribed
     * @param string $limit
     * @param $emails
     * @return SimpleXMLElement
     */
    protected function getSubscribers($subscribed, $limit, $emails)
    {
        $sqlLimit = '';
        $sqlWhere = '';
        if ($limit) {
            $sqlLimit = 'LIMIT ' . pSQL($limit);
        }

        if ($subscribed) {
            $sqlWhere .= ' AND main.active = 1';
        }

        if (is_array($emails) && !empty($emails)) {
            foreach ($emails as &$email) {
                $email = pSQL($email);
            }

            $sqlWhere .= ' AND email IN (\'' . implode("','", $emails) . '\'';
        }

        require_once dirname(__FILE__) . DS . '..' . DS . '..' . DS . 'classes' . DS . 'NewsletterSubscriber.php';
        $subscriber = new NewsletterSubscriberCore();
        $subscribers = $subscriber->getWebserviceObjectList('', $sqlWhere, '', $sqlLimit);

        $xml = new SimpleXMLElement('<prestashop/>');
        $xml->addAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
        $subsNode = $xml->addChild('subscribers');

        foreach ($subscribers as $subscriber) {
            $subscriberNode = $subsNode->addChild('subscriber');
            foreach ($subscriber as $key => $value) {
                $subscriberNode->addChild($key, $value);
            }
        }

        return $xml;
    }

    /**
     * Retrieves version of the installed module
     * @return SimpleXMLElement
     */
    protected function getPluginVersion()
    {
        $module = Module::getInstanceByName('newsletter2go');

        $xml = new SimpleXMLElement('<prestashop/>');
        $xml->addAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
        $xml->addChild('version', str_replace('.', '', $module->version));

        return $xml;
    }

    /**
     * Check if newsletter module is installed
     * @return bool
     */
    protected function tableExists()
    {
        $tableName = _DB_PREFIX_;
        $tableName .=  _PS_VERSION_ >= '1.7.0.0' ? 'emailsubscription' : 'newsletter';
        $db = Db::getInstance();
        $tableInfo = $db->getRow("SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = '$tableName'");

        return is_array($tableInfo);
    }

    /**
     * Checks if api key is valid
     * @param string $apiKey
     * @return bool
     */
    protected function checkApiKey($apiKey)
    {
        $apiKey = pSQL($apiKey);
        $tableName = _DB_PREFIX_ . 'webservice_account';
        $db = Db::getInstance();
        $row = $db->getRow("SELECT * FROM $tableName s WHERE s.key = '$apiKey' AND active = 1");

        return is_array($row);
    }

    /**
     * Creates error message response
     * @param string $message
     * @return SimpleXMLElement
     */
    protected function errorMessage($message = '', $code = 18)
    {
        $xml = new SimpleXMLElement('<prestashop/>');
        $xml->addAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
        $errorsNode = $xml->addChild('errors');
        $errorNode = $errorsNode->addChild('error');
        $errorNode->addChild('code', $code);
        $errorNode->addChild('message', $message);

        return $xml->asXML();
    }
}
