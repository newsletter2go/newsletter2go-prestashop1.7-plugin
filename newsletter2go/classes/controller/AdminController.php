<?php

class AdminControllerextends extends ModuleFrontControllerCore
{

    public function hookActionCartSave()
    {
        Tools::getIsset('product');
        var_dump($_SERVER);
    }

    public function hookActionObjectProductInCartDeleteAfter()
    {
        var_dump($_SERVER);
    }
}
