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

class NewsletterSubscriberCore extends ObjectModel
{
    public $id_shop;

    public $id_shop_group;

    public $email;

    public $newsletter_date_add;

    public $ip_registration_newsletter;

    public $http_referer;

    public $active;

    public static $definition = array(
        'table' => 'newsletter',
        'primary' => 'id',
        'fields' => array(
            'id_shop' => array('type' => self::TYPE_INT, 'required' => true),
            'id_shop_group' => array('type' => self::TYPE_INT, 'required' => true),
            'email' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 255),
            'newsletter_date_add' => array('type' => self::TYPE_DATE, 'required' => false, 'size' => 255),
            'ip_registration_newsletter' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 15),
            'http_referer' => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 255),
            'active' => array('type' => self::TYPE_BOOL, 'required' => true, 'default' => 0),
        ),
    );

    protected $webserviceParameters = array();

    public function getWebserviceObjectList($sql_join, $sql_filter, $sql_sort, $sql_limit)
    {
        if (_PS_VERSION_ >= '1.7.0.0') {
            $this->def['table'] = 'emailsubscription';
        }

        $query = 'SELECT main.* FROM `' . _DB_PREFIX_ . bqSQL($this->def['table']) . '` AS main
		' . $sql_join . '
		WHERE 1 ' . $sql_filter . '
		' . ($sql_sort != '' ? $sql_sort : '') . '
		' . ($sql_limit != '' ? $sql_limit : '');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    public function getWebserviceObjectListCount($sql_filter)
    {
        if (_PS_VERSION_ >= '1.7.0.0') {
            $this->def['table'] = 'emailsubscription';
        }
        
        $query = 'SELECT COUNT(main.id) as total FROM `' . _DB_PREFIX_ . bqSQL($this->def['table']) . '` AS main
		WHERE 1 ' . $sql_filter;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
    }
}
