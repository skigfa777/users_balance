<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>

<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>
<div class="groumedia__wrap">
    <table class="groumedia__table">
<?
$header = false;
foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
    if (!$header) {
	?>
    <tr>
        <th><?echo $arItem["NAME"]?></th>
        <?foreach($arItem["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
            <th>
            <?=$arProperty["NAME"]?>
            </th>
            <?
        endforeach;?> 
        <?if($arResult['CAN_ACCOUNT_REFILL'] == 1) {?>
            <th>Действие</th> 
        <?}?> 
    </tr>
    <?
    $header = true;
    }?>
	<tr id="<?=$this->GetEditAreaId($arItem['ID']);?>">
		<td><?echo $arItem["NAME"]?></td>

		<?foreach($arItem["FIELDS"] as $code=>$value):?>
			<td>
			<?=GetMessage("IBLOCK_FIELD_".$code)?>:&nbsp;<?=$value;?>
			</td>
		  <?
        endforeach;?>
		<?foreach($arItem["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
			<td>
			<?if(is_array($arProperty["DISPLAY_VALUE"])):?>
				<?=implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);?>
			<?else:?>
				<?=$arProperty["DISPLAY_VALUE"];?>
			<?endif?>
			</td>
		<?endforeach;?>
        <?if($arResult['CAN_ACCOUNT_REFILL'] == 1) {?>
            <td>
                <form method="POST">
                    <input type="hidden" name="id" value="<?=$arItem['ID']?>">
                    <input type="hidden" name="currency" value="<?=$arItem['PROPERTIES']['CURRENCY']['VALUE']?>">
                    <button name="action" value="change_balance" type="submit">+</button>
                </form>
            </td>
        <?}?>
	</tr>
<?endforeach;?>
    </table>
</div>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>

