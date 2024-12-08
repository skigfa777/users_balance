<?php

use \Bitrix\Main\Context;
use \Bitrix\Main\UserTable;

function updateBalance($currency, $ufProperty, $sum, $USER) {
    $userId = $USER->GetID();
    $balanceField = 'UF_BALANCE_' . $currency;

    if (!isset($ufProperty[$balanceField]) || $ufProperty[$balanceField] == 0) {
        return 'Недостаточно денег для совершения операции';
    }

    $fields = array($balanceField => $ufProperty[$balanceField] - $sum);
    $result = $USER->update($userId, $fields);

    if (!$result) {
        return $USER->LAST_ERROR;
    }

    return true;
}

AddEventHandler("main", "OnBeforeProlog", "updateUserBalance");

function updateUserBalance() {
    global $USER;

    $sum = 1; // на сколько списывать
    $iblockId = 3;

    $request = Context::getCurrent()->getRequest();

    $action = (string) $request->getPost('action');
    $elementId = (int) $request->getPost('id'); //id аккаунта
    $currency = (string) $request->getPost('currency'); // USD|EUR
    $userId = $USER->GetID();

    if (CModule::IncludeModule("iblock") 
        && $action == 'change_balance'
        && $USER->IsAuthorized()
    ) {
        // получить текущий баланс пользователя (EUR, USD)
        $ufProperty = UserTable::getList(array(
            'select' => array('UF_BALANCE_USD', 'UF_BALANCE_EUR', 'UF_ACCOUNT_REFILL'),
            'filter' => array('ID' => $userId)
        ))->Fetch();

        // проверить, что пользователь может пополнять аккаунты
        if ($ufProperty['UF_ACCOUNT_REFILL'] != 1) {
            ShowMessage("Вы не можете пополнять баланс");
            return;
        }

        // списать с пользователя (EUR, USD в зависимости от типа валюты на аккаунте)
        $result = true;

        if (in_array($currency, array('EUR', 'USD'))) {
            $result = updateBalance($currency, $ufProperty, $sum, $USER);
            if (is_string($result) && $result !== 'true') {
                ShowMessage($result);
                return;
            }
        }

        // и пополнить баланс аккаунта на +$sum
        $propertyCode = 'BALANCE';
        $propertyValue = 0;
        $db_props = CIBlockElement::GetProperty($iblockId, $elementId, "sort", "asc", array("CODE"=>$propertyCode));
        if ($balance = $db_props->Fetch()) {
            $propertyValue = $balance['VALUE'] + $sum;
        }

        CIBlockElement::SetPropertyValueCode($elementId, $propertyCode, $propertyValue); 
    }
}
