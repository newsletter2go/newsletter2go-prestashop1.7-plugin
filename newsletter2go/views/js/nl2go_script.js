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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2016 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

window.addEventListener('load', function () {
    var generate = document.getElementById('nl2goGenerateButton'),
        apiKey = document.getElementById('apiKey'),
        apiKeyLoader = document.getElementById('apiKeyLoader'),
        connect = document.getElementById('nl2goConnectButton'),
        abandonedShoppingCartOn = document.getElementById('nl2goAbandonedShoppingCart_on'),
        abandonedShoppingCartOff = document.getElementById('nl2goAbandonedShoppingCart_off'),
        saveSettings = document.getElementById('nl2goSaveSettingsButton');

    connect.addEventListener('click', function () {
        var baseUrl = 'https://ui.newsletter2go.com/integrations/connect/PS17/',
            params = {
                //ignore version to create latest version of connector
                //version: document.getElementById("version").value,
                apiKey: apiKey.value,
                language: document.getElementById('language').value,
                url: document.getElementById('base_url').value,
                callback: document.getElementById("callback_url").value
            };

        window.open(baseUrl + '?' + $.param(params), '_blank');
    });

    generate.addEventListener('click', function ajax() {
        var xmlHttp = new XMLHttpRequest(),
            parameters = 'token=token&ajax=a&tab=Newsletter2GoTab&action=generateNewApiKey';

        xmlHttp.onreadystatechange = function () {
            if (xmlHttp.readyState == XMLHttpRequest.DONE) {
                if (xmlHttp.status == 200) {
                    apiKey.value = xmlHttp.responseText;
                } else {
                    alert('An error occurred while generating new API key, http code 200 expected.');
                }

                apiKeyLoader.style.display = 'none';
                apiKey.style.display = 'block';
            }
        };

        xmlHttp.open('POST', 'index.php', true);
        xmlHttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xmlHttp.send(parameters);
    });

    abandonedShoppingCartOn.addEventListener('click', function ajax() {
        $('#nl2goAbandonedShoppingCartSettings').show();
    });

    abandonedShoppingCartOff.addEventListener('click', function ajax() {
        $('#nl2goAbandonedShoppingCartSettings').hide();
    });

    saveSettings.addEventListener('click', function ajax() {
        var orderTrackingElement = document.getElementById('nl2goOrderTracking_on').checked;
        var shoppingCartElement = document.getElementById('nl2goAbandonedShoppingCart_on').checked;
        var orderTracking = 0;
        var shoppingCart = 0;
        var xmlHttp = new XMLHttpRequest(),
            parameters = 'token=token&ajax=a&tab=Newsletter2GoTab&action=saveSettings';
        var transactionalMailings = document.getElementById("nl2goTransactionMailing");
        var transactionalMailingHandleTimes = document.getElementById("nl2goTransactionMailingHandleTime");

        if(orderTrackingElement){
            orderTracking = 1;
        }
        parameters = parameters.concat('&conversionTracking=' + orderTracking);

        if(shoppingCartElement){
            shoppingCart = 1;
        }

        parameters = parameters.concat('&shoppingCart=' + shoppingCart);

        transactionalMailingId = transactionalMailings.options[transactionalMailings.selectedIndex].value;
        parameters = parameters.concat('&transactionalMailingId=' + transactionalMailingId);

        transactionalMailingHandleTime = transactionalMailingHandleTimes.options[transactionalMailingHandleTimes.selectedIndex].value;
        parameters = parameters.concat('&transactionalMailingHandleTime=' + transactionalMailingHandleTime);

        xmlHttp.open('POST', 'index.php', true);
        xmlHttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xmlHttp.send(parameters);
    });
});