{*
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
*}
{if isset($newsletter2go_error)}
    <div class="row">
        <div class="col-lg-9" style="text-align: center; color: red;">
            <h2>{l s='Error: ' mod='newsletter2go'}{$newsletter2go_error|escape:'htmlall':'UTF-8'}</h2></div>
    </div>
{/if}
{if isset($newsletter2go_success_user)}
    <div class="row">
        <div class="col-lg-9" style="text-align: center; color: #8bc954;">
            <h2>{$newsletter2go_success_user|escape:'htmlall':'UTF-8'}</h2></div>
    </div>
{/if}
{if isset($newsletter2go_success_integration)}
    <div class="row">
        <div class="col-lg-9" style="text-align: center; color: #8bc954;">
            <h2>{$newsletter2go_success_integration|escape:'htmlall':'UTF-8'}</h2></div>
    </div>
{/if}
<div class="form-horizontal">
    <div class="panel">
        <div class="panel-heading"><i class="icon-cogs"
                                      style="margin-right: 10px"></i>{l s='Connection' mod='newsletter2go'}</div>
        <div class="form-wrapper">
            <div class="form-group" {if $test_connection}style="display: none"{/if}>
                <label class="control-label col-lg-3" style="text-align: right;">
                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="">
                        {l s='Connect to Newsletter2Go' mod='newsletter2go'}
                    </span>
                </label>
                <div class="col-lg-9">
                    <span style="border: 1px solid #c05c67; color: #c05c67; padding: 4px; text-transform: uppercase; margin-left:4%">
                        <span><i class="icon-circle"
                                 style="margin-right: 1%"></i>{l s='Not Connected' mod='newsletter2go'}</span>
                    </span>
                    <input type="hidden" id="language" value="{$lang_iso|escape:'htmlall':'UTF-8'}">
                    <input type="hidden" id="base_url" value="{$base_url|escape:'htmlall':'UTF-8'}">
                    <input type="hidden" id="callback_url" value="{$callback_url|escape:'htmlall':'UTF-8'}">
                    <input type="hidden" id="version" value="{$plugin_version|escape:'htmlall':'UTF-8'}">
                    <button type="button" class="btn btn-primary" id="nl2goConnectButton"
                            style="margin-left: 2%; background-color: #2eacce;">
                        {l s='Connect' mod='newsletter2go'}
                    </button>
                </div>
            </div>
            <div class="form-group" {if !$test_connection}style="display: none"{/if}>
                <label class="control-label col-lg-3" style="text-align: right;">
                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="">
                        {l s='Connected to' mod='newsletter2go'}
                    </span>
                </label>
                <div class="col-lg-9 ">
                    <span style="border: 1px solid #19d76e; color: #19d76e; padding: 4px; text-transform: uppercase; margin-left:4%">
                        <span name="company" id="company"><i class="icon-circle"
                                                             style="margin-right: 1%"></i>{$company|escape:'htmlall':'UTF-8'}</span>
                    </span>
                    <button type="button" class="btn btn-primary" id="nl2goDisconnectButton"
                            style="margin-left: 2%; background-color: #2eacce;">
                        {l s='Disconnect' mod='newsletter2go'}
                    </button>
                </div>
            </div>
        </div>
        <div class="form-wrapper">
            <div class="form-group">
                <label class="control-label col-lg-3" style="text-align: right;">
                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="">
                        {l s='Key' mod='newsletter2go'}
                    </span>
                    <span class="nl2go-help-box"
                          title="API Key for setting up a connection between Prestashop and Newsletter2go">
                            </span>
                </label>
                <div class="col-lg-9 ">
                    <div class="row">
                        <div class="col-lg-8" style="margin-left: 3.7%">
                            <img src="../img/admin/ajax-loader.gif" alt="" id="apiKeyLoader"
                                 style="display: none;"/>
                            <input type="text" name="key" id="apiKey"
                                   value="{$web_services_api_key|escape:'htmlall':'UTF-8'}" readonly="true">
                        </div>
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-primary" id="nl2goGenerateButton"
                                    style="background-color: #2eacce;">
                                {l s='Generate!' mod='newsletter2go'}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-horizontal" {if !$test_connection}style="display: none"{/if}>
    <div class="panel">
        <div class="panel-heading"><i class="icon-cogs"
                                      style="margin-right: 10px"></i>{l s='Features' mod='newsletter2go'}
        </div>
        <div class="form-wrapper">
            <div class="form-group">
                <label class="control-label col-lg-3" style="text-align: right;">
                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="">
                        {l s='Enable conversion tracking' mod='newsletter2go'}
                    </span>
                </label>
                <div class="col-lg-9">
                    <span class="nl2go-switch" style="margin-left: 3.7%;">
                        <input class="nl2go-switch" type="radio" name="nl2goOrderTracking" id="nl2goOrderTracking_off"
                               value="0"
                               {if !$enable_tracking}checked="checked"{/if}>
                        <label for="nl2goOrderTracking_off" class="radioCheck">{l s='No'}</label>
                        <input class="nl2go-switch" type="radio" name="nl2goOrderTracking" id="nl2goOrderTracking_on"
                               value="1"
                               {if $enable_tracking}checked="checked"{/if}>
                        <label for="nl2goOrderTracking_on" class="radioCheck">{l s='Yes'}</label>
                        <a class="slide-button btn"></a>
                    </span>
                </div>
            </div>
        </div>
        <div class="panel-heading"></div>
        <div class="form-wrapper">
            <div class="form-group">
                <label class="control-label col-lg-3" style="text-align: right;">
                        <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                              data-original-title="">
                            {l s='Enable Abandoned Shopping Cart' mod='newsletter2go'}
                        </span>
                </label>
                <div class="col-lg-9">
                    <span class="nl2go-switch" style="margin-left: 3.7%; margin-bottom: 1%;">
                        <input class="nl2go-switch" type="radio" name="nl2goAbandonedShoppingCart"
                               id="nl2goAbandonedShoppingCart_off"
                               value="0" {if !$enable_abandoned_shopping_cart}checked="checked"{/if}>
                        <label for="nl2goAbandonedShoppingCart_off" class="radioCheck">{l s='No'}</label>
                          <input class="nl2go-switch" type="radio" name="nl2goAbandonedShoppingCart"
                                 id="nl2goAbandonedShoppingCart_on"
                                 value="1" {if $enable_abandoned_shopping_cart}checked="checked"{/if}>
                        <label for="nl2goAbandonedShoppingCart_on" class="radioCheck">{l s='Yes'}</label>
                        <a class="slide-button btn"></a>
                    </span>
                </div>
                <label class="nl2goAbandonedShoppingCartSettings control-label col-lg-3"
                       style="{if !$enable_abandoned_shopping_cart}display: none;{/if} text-align: right;">
                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                  data-original-title="">
                                {l s='Transactional Mailing' mod='newsletter2go'}
                            </span>
                    <span class="nl2go-help-box" title="Transactionalmailing which will be filled with product data">
                            </span>
                </label>
                <select id="nl2goTransactionMailing" class="nl2goAbandonedShoppingCartSettings col-lg-6"
                        style="{if !$enable_abandoned_shopping_cart}display: none;{/if} margin-left: 3%; margin-bottom: 1%;">
                    {foreach from=$transactionalMailings item="label" key="key"}
                        <option value="{$key}"
                                {if $key == $newsletter_id}selected="selected"{/if}>{$label}</option>
                    {/foreach}
                </select>
                <label class="nl2goAbandonedShoppingCartSettings control-label col-lg-3"
                       style="{if !$enable_abandoned_shopping_cart}display: none;{/if} text-align: right;">
                                <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                      data-original-title="">
                                {l s='Send Mailing after X Hours' mod='newsletter2go'}
                                </span>
                    <span class="nl2go-help-box" title="Mailing will be sent after selected hours">
                            </span>
                </label>
                <select id="nl2goTransactionMailingHandleTime" class="nl2goAbandonedShoppingCartSettings col-lg-6"
                        style="{if !$enable_abandoned_shopping_cart}display: none;{/if} margin-left: 3%;">
                    <option value="1">1 Hour</option>
                    <option value="2">2 Hours</option>
                    <option value="3">3 Hours</option>
                    <option value="4">4 Hours</option>
                    <option value="5">5 Hours</option>
                    <option value="6">6 Hours</option>
                    <option value="7">7 Hours</option>
                    <option value="8">8 Hours</option>
                    <option value="9">9 Hours</option>
                    <option value="10">10 Hours</option>
                    <option value="11">11 Hours</option>
                    <option value="12">12 Hours</option>
                    <option value="13">13 Hour</option>
                    <option value="14">14 Hours</option>
                    <option value="15">15 Hours</option>
                    <option value="16">16 Hours</option>
                    <option value="17">17 Hours</option>
                    <option value="18">18 Hours</option>
                    <option value="19">19 Hours</option>
                    <option value="20">20 Hours</option>
                    <option value="21">21 Hours</option>
                    <option value="22">22 Hours</option>
                    <option value="23">23 Hours</option>
                    <option value="24">24 Hours</option>
                </select>
            </div>
        </div>
        <div class="form-wrapper">
            <div class="form-group">
                <div class="panel-heading"></div>
                <button type="button" class="btn btn-primary" id="nl2goSaveSettingsButton"
                        style="float: right; margin-right: 2%; background-color: #2eacce;">
                    <span><i class="icon-save" style="margin-right:1%"></i>{l s=' Save' mod='newsletter2go'}</span>
                </button>
            </div>
        </div>
    </div>