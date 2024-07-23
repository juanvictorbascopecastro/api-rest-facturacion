<?php

namespace app\models;

use app\models\City; 

use Yii;

/**
 * This is the model class for table "cfg.ioSystemBranch".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property string|null $name
 * @property string|null $codigoSucursal codigo SIAT
 * @property int|null $idioSystem
 * @property int|null $iduser
 * @property int|null $idstatus
 * @property string|null $dbidentifier
 * @property string|null $address
 * @property string|null $numberPhone
 * @property string|null $numberPhone2
 * @property string|null $codeCountry
 * @property string|null $nameCity
 * @property string|null $fullName
 * @property string|null $v0_activadoCodigoModalidad
 * @property int|null $v0_codigoModalidadw
 * @property int|null $idkey
 * @property string|null $key
 * @property bool|null $printTicket
 * @property bool|null $onlyInvoice
 * @property bool|null $enabledSecondPrinter
 * @property string|null $ipPrinter
 * @property bool|null $printDirectly
 * @property bool|null $allowCredit
 * @property bool|null $allowPriceSheet
 * @property string|null $paperPrinter
 * @property bool|null $allowInvoice
 * @property bool|null $enableCopyDescriptionOrderSale
 * @property bool|null $allowDevolution
 * @property int|null $decimalsStock
 * @property bool|null $allowFileExcelSale
 * @property bool|null $allowPanelProduct
 * @property bool|null $allowCommentProductOrder
 * @property string|null $dateLicense
 * @property bool|null $onButtonCancel
 * @property int|null $idtypeEnterprise
 * @property string|null $readOnly_codigoSistema
 * @property string|null $readOnly_nit
 * @property int|null $readOnly_codigoSucursal
 * @property string|null $readOnly_razonSocial
 * @property bool|null $allowStores
 * @property int|null $idstoreProduction
 * @property int|null $idcity
 * @property bool|null $allowControlInventory
 * @property bool|null $allowCost
 * @property bool|null $activatedSaleMetodoPago
 * @property bool|null $allowPrinterSilence
 * @property string|null $userPrinterSilence
 * @property int|null $numPrintPrinterSilence numero de impresiones silenciosas
 * @property bool|null $batchs ORES
 */
class IoSystemBranch extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cfg.ioSystemBranch';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('iooxs_access');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dateCreate', 'dateLicense'], 'safe'],
            [['recycleBin', 'printTicket', 'onlyInvoice', 'enabledSecondPrinter', 'printDirectly', 'allowCredit', 'allowPriceSheet', 'allowInvoice', 'enableCopyDescriptionOrderSale', 'allowDevolution', 'allowFileExcelSale', 'allowPanelProduct', 'allowCommentProductOrder', 'onButtonCancel', 'allowStores', 'allowControlInventory', 'allowCost', 'activatedSaleMetodoPago', 'allowPrinterSilence', 'batchs'], 'boolean'],
            [['idioSystem', 'iduser', 'idstatus', 'v0_codigoModalidadw', 'idkey', 'decimalsStock', 'idtypeEnterprise', 'readOnly_codigoSucursal', 'idstoreProduction', 'idcity', 'numPrintPrinterSilence'], 'default', 'value' => null],
            [['idioSystem', 'iduser', 'idstatus', 'v0_codigoModalidadw', 'idkey', 'decimalsStock', 'idtypeEnterprise', 'readOnly_codigoSucursal', 'idstoreProduction', 'idcity', 'numPrintPrinterSilence'], 'integer'],
            [['address', 'numberPhone', 'fullName', 'paperPrinter', 'readOnly_codigoSistema', 'readOnly_nit', 'readOnly_razonSocial', 'userPrinterSilence'], 'string'],
            [['name'], 'string', 'max' => 25],
            [['codigoSucursal', 'v0_activadoCodigoModalidad', 'key'], 'string', 'max' => 10],
            [['dbidentifier', 'nameCity'], 'string', 'max' => 50],
            [['numberPhone2'], 'string', 'max' => 18],
            [['codeCountry'], 'string', 'max' => 3],
            [['ipPrinter'], 'string', 'max' => 27],
            [['idioSystem'], 'exist', 'skipOnError' => true, 'targetClass' => IoSystem::class, 'targetAttribute' => ['idioSystem' => 'id']],
            [['idstatus'], 'exist', 'skipOnError' => true, 'targetClass' => CfgStatus::class, 'targetAttribute' => ['idstatus' => 'id']],
            [['idstatus'], 'exist', 'skipOnError' => true, 'targetClass' => CfgStatus::class, 'targetAttribute' => ['idstatus' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dateCreate' => 'Date Create',
            'recycleBin' => 'Recycle Bin',
            'name' => 'Name',
            'codigoSucursal' => 'Codigo Sucursal',
            'idioSystem' => 'Idio System',
            'iduser' => 'Iduser',
            'idstatus' => 'Idstatus',
            'dbidentifier' => 'Dbidentifier',
            'address' => 'Address',
            'numberPhone' => 'Number Phone',
            'numberPhone2' => 'Number Phone2',
            'codeCountry' => 'Code Country',
            'nameCity' => 'Name City',
            'fullName' => 'Full Name',
            'v0_activadoCodigoModalidad' => 'V0 Activado Codigo Modalidad',
            'v0_codigoModalidadw' => 'V0 Codigo Modalidadw',
            'idkey' => 'Idkey',
            'key' => 'Key',
            'printTicket' => 'Print Ticket',
            'onlyInvoice' => 'Only Invoice',
            'enabledSecondPrinter' => 'Enabled Second Printer',
            'ipPrinter' => 'Ip Printer',
            'printDirectly' => 'Print Directly',
            'allowCredit' => 'Allow Credit',
            'allowPriceSheet' => 'Allow Price Sheet',
            'paperPrinter' => 'Paper Printer',
            'allowInvoice' => 'Allow Invoice',
            'enableCopyDescriptionOrderSale' => 'Enable Copy Description Order Sale',
            'allowDevolution' => 'Allow Devolution',
            'decimalsStock' => 'Decimals Stock',
            'allowFileExcelSale' => 'Allow File Excel Sale',
            'allowPanelProduct' => 'Allow Panel Product',
            'allowCommentProductOrder' => 'Allow Comment Product Order',
            'dateLicense' => 'Date License',
            'onButtonCancel' => 'On Button Cancel',
            'idtypeEnterprise' => 'Idtype Enterprise',
            'readOnly_codigoSistema' => 'Read Only Codigo Sistema',
            'readOnly_nit' => 'Read Only Nit',
            'readOnly_codigoSucursal' => 'Read Only Codigo Sucursal',
            'readOnly_razonSocial' => 'Read Only Razon Social',
            'allowStores' => 'Allow Stores',
            'idstoreProduction' => 'Idstore Production',
            'idcity' => 'Idcity',
            'allowControlInventory' => 'Allow Control Inventory',
            'allowCost' => 'Allow Cost',
            'activatedSaleMetodoPago' => 'Activated Sale Metodo Pago',
            'allowPrinterSilence' => 'Allow Printer Silence',
            'userPrinterSilence' => 'User Printer Silence',
            'numPrintPrinterSilence' => 'Num Print Printer Silence',
            'batchs' => 'Batchs',
        ];
    }

    /**
     * {@inheritdoc}
     * @return CfgIoSystemBranchQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new IoSystemBranchQuery(get_called_class());
    }

    public function getCfgIoSystem()
    {
        return $this->hasOne(IoSystem::class, ['id' => 'idioSystem']);
    }
    
     public function getIdcity0() {
        return $this->hasOne(City::class, ['id' => 'idcity']);
    }
}
