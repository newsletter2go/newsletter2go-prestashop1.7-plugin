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

class Newsletter2GoCallbackModuleFrontController extends ModuleFrontController
{

    public function initContent()
    {
        $authKey = Tools::getValue('auth_key', 0);
        $accessToken = Tools::getValue('access_token', 0);
        $refreshToken = Tools::getValue('refresh_token', 0);
        $companyId = Tools::getValue('company_id', 0);

        if (!empty($authKey)) {
            Configuration::updateValue('NEWSLETTER2GO_AUTH_KEY', $authKey);
        }

        if (!empty($accessToken)) {
            Configuration::updateValue('NEWSLETTER2GO_ACCESS_TOKEN', $accessToken);
        }

        if (!empty($refreshToken)) {
            Configuration::updateValue('NEWSLETTER2GO_REFRESH_TOKEN', $refreshToken);
        }

        if (!empty($companyId)) {
            Configuration::updateValue('NEWSLETTER2GO_COMPANY_ID', $companyId);
        }

        die(json_encode(
            array(
                'success' => true
            )
        ));
    }
}
