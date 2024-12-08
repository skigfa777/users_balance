<?php
$userID = $USER->GetID();
$dbUser = $USER->GetByID($userID);
$arrUser = $dbUser->fetch();
$arResult['USER_ID'] = $userID;

// показывать ли кнопку +полнения баланса
$arResult['CAN_ACCOUNT_REFILL'] = $arrUser['UF_ACCOUNT_REFILL'];
