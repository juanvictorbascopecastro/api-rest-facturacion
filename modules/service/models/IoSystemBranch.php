<?php

namespace app\modules\service\models;

use app\models\CfgIoSystem;

class IoSystemBranch extends \app\models\IoSystemBranch {
    public function fields()
    {
        return [
            'id',
            'dateCreate',
            'recycleBin',
            'name',
            'codigoSucursal',
            'idioSystem',
            'iduser',
            'idstatus',
            'dbidentifier',
            'address',
            'numberPhone',
            'numberPhone2',
            'codeCountry',
            'nameCity',
            'fullName',
            'idkey',
            'key',
            'printTicket',
            'onlyInvoice',
            'enabledSecondPrinter',
            'ipPrinter',
            'printDirectly',
            'allowCredit',
            'allowPriceSheet',
            'paperPrinter',
            'allowInvoice',
            'enableCopyDescriptionOrderSale',
            'allowDevolution',
            'decimalsStock',
            'allowFileExcelSale',
            'allowPanelProduct',
            'allowCommentProductOrder',
            'dateLicense',
            'onButtonCancel',
            'idtypeEnterprise',
            'readOnly_codigoSistema',
            'readOnly_nit',
            'readOnly_codigoSucursal',
            'readOnly_razonSocial',
            'allowStores',
            'idstoreProduction',
            'idcity',
            'allowControlInventory',
            'allowCost',
            'activatedSaleMetodoPago',
            'allowPrinterSilence',
            'userPrinterSilence',
            'numPrintPrinterSilence',
            'batchs',
            'cfgIoSystem'
        ];
    }
}
