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
        code = document.getElementById('code'),
        codeLoader = document.getElementById('codeLoader'),
        connect = document.getElementById('nl2goConnectButton'),
        orderTrackingOn = document.getElementById('nl2goOrderTracking_on'),
        orderTrackingOff = document.getElementById('nl2goOrderTracking_off');

    connect.addEventListener('click', function () {
        var baseUrl = 'https://ui.newsletter2go.com/integrations/connect/PS17/',
            params = {
                //ignore version to create latest version of connector
                //version: document.getElementById("version").value,
                apiKey: code.value,
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
                    code.value = xmlHttp.responseText;
                } else {
                    alert('An error occurred while generating new API key, http code 200 expected.');
                }

                codeLoader.style.display = 'none';
                code.style.display = 'block';
            }
        };

        xmlHttp.open('POST', 'index.php', true);
        xmlHttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xmlHttp.send(parameters);
    });

    orderTrackingOn.addEventListener('click', function ajax() {
        var xmlHttp = new XMLHttpRequest(),
            parameters = 'token=token&ajax=a&tab=Newsletter2GoTab&action=trackingOrder&enable=1';

        xmlHttp.open('POST', 'index.php', true);
        xmlHttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xmlHttp.send(parameters);
    });

    orderTrackingOff.addEventListener('click', function ajax() {
        var xmlHttp = new XMLHttpRequest(),
            parameters = 'token=token&ajax=a&tab=Newsletter2GoTab&action=trackingOrder&enable=0';

        xmlHttp.open('POST', 'index.php', true);
        xmlHttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xmlHttp.send(parameters);
    });
});