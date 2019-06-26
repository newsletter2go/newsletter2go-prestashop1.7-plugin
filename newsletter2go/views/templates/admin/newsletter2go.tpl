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
        <div class="col-lg-9" style="text-align: center; color: red;"><h2>{l s='Error: ' mod='newsletter2go'}{$newsletter2go_error|escape:'htmlall':'UTF-8'}</h2></div>
    </div>
{/if}
{if isset($newsletter2go_success_user)}
    <div class="row">
        <div class="col-lg-9" style="text-align: center; color: #8bc954;"><h2>{$newsletter2go_success_user|escape:'htmlall':'UTF-8'}</h2></div>
    </div>
{/if}
{if isset($newsletter2go_success_integration)}
    <div class="row">
        <div class="col-lg-9" style="text-align: center; color: #8bc954;"><h2>{$newsletter2go_success_integration|escape:'htmlall':'UTF-8'}</h2></div>
    </div>
{/if}
<div class="form-horizontal">
    <div class="panel">
        <div class="panel-heading"><i class="icon-cogs" style="margin-right: 10px"></i>{l s='Webservice Accounts' mod='newsletter2go'}</div>
        <div class="form-wrapper">
            <div class="form-group">
                <label class="control-label col-lg-3 required" style="text-align: right;">
                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="">
                        {l s='Key' mod='newsletter2go'}
                    </span>
                </label>
                <div class="col-lg-9 ">
                    <div class="row">
                        <div class="col-lg-5">
                            <img src="../img/admin/ajax-loader.gif" alt="" id="codeLoader" style="display: none;" />
                            <input type="text" name="key" id="code" value="{$web_services_api_key|escape:'htmlall':'UTF-8'}" readonly="true">
                        </div>
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-default" id="nl2goGenerateButton">
                                {l s='Generate!' mod='newsletter2go'}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-horizontal">
    <div class="panel">
        <div class="panel-heading"><i class="icon-cogs" style="margin-right: 10px"></i>{l s='Connect' mod='newsletter2go'}</div>
        <div class="form-wrapper">
            <div class="form-group">
                <label class="control-label col-lg-3" style="text-align: right;">
                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="">
                        {l s='Connect to Newsletter2Go' mod='newsletter2go'}
                    </span>
                </label>
                <div class="col-lg-9">
                    <input type="hidden" id="language" value="{$lang_iso|escape:'htmlall':'UTF-8'}">
                    <input type="hidden" id="base_url" value="{$base_url|escape:'htmlall':'UTF-8'}">
                    <input type="hidden" id="callback_url" value="{$callback_url|escape:'htmlall':'UTF-8'}">
                    <input type="hidden" id="version" value="{$plugin_version|escape:'htmlall':'UTF-8'}">
                    <button type="button" class="btn btn-default" id="nl2goConnectButton">
                        {l s='connect' mod='newsletter2go'}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-horizontal">
    <div class="panel">
        <div class="panel-heading"><i class="icon-cogs" style="margin-right: 10px"></i>{l s='Conversion Tracking' mod='newsletter2go'}</div>
        <div class="form-wrapper">
            <div class="form-group">
                <label class="control-label col-lg-3" style="text-align: right;">
                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="">
                        {l s='Enable order tracking' mod='newsletter2go'}
                    </span>
                </label>
                <div class="col-lg-9">
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio" name="nl2goOrderTracking" id="nl2goOrderTracking_on" value="1" {if $enable_tracking}checked="checked"{/if}>
                        <label for="nl2goOrderTracking_on" class="radioCheck">{l s='Yes'}</label>
                        <input type="radio" name="nl2goOrderTracking" id="nl2goOrderTracking_off" value="0" {if !$enable_tracking}checked="checked"{/if}>
                        <label for="nl2goOrderTracking_off" class="radioCheck">{l s='No'}</label>
                        <a class="slide-button btn"></a>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-horizontal">
    <div class="panel">
        <div class="panel-heading"><i class="icon-cogs" style="margin-right: 10px"></i>{l s='Abandoned Shopping Cart' mod='newsletter2go'}</div>
        <div class="form-wrapper">
            <div class="form-group">
                <label class="control-label col-lg-3" style="text-align: right;">
                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="">
                        {l s='Enable Abandoned Shopping Cart' mod='newsletter2go'}
                    </span>
                </label>
                <div class="col-lg-9">
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio" name="nl2goAbandonedShoppingCart" id="nl2goAbandonedShoppingCart_on" value="1" {if $enable_abandoned_shopping_cart}checked="checked"{/if}>
                        <label for="nl2goAbandonedShoppingCart_on" class="radioCheck">{l s='Yes'}</label>
                        <input type="radio" name="nl2goAbandonedShoppingCart" id="nl2goAbandonedShoppingCart_off" value="0" {if !$enable_abandoned_shopping_cart}checked="checked"{/if}>
                        <label for="nl2goAbandonedShoppingCart_off" class="radioCheck">{l s='No'}</label>
                        <a class="slide-button btn"></a>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-horizontal">
    <div class="panel">
        <div class="panel-heading"><i class="icon-cogs" style="margin-right: 10px"></i>{l s='testConnection' mod='newsletter2go'}</div>
        <div class="form-wrapper">
            <div class="form-group">
                <label class="control-label col-lg-3" style="text-align: right;">
                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="">
                        {l s='Test connection to Newsletter2Go' mod='newsletter2go'}
                    </span>
                </label>
                <div class="col-lg-9">
                    <button type="button" class="btn btn-default" id="nl2goTestConnectionButton">
                        {l s='testConnection' mod='newsletter2go'}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>