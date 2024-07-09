<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cfg.ioSystem".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property string|null $dbidentifier data base identifier
 * @property string|null $v0_nameCompany
 * @property int|null $iduseradmin
 * @property string|null $v0_codigoSistema ref SIAT
 * @property int|null $iduser
 * @property int|null $idstatus
 * @property string|null $numberPhone
 * @property string|null $codeCountry
 * @property string|null $descriptionCompany
 * @property string|null $fullNameCompany
 * @property string|null $activadoCodigoModalidad
 * @property string|null $v0_nit
 * @property string|null $v0_razonSocial
 * @property bool|null $v0_restaurant
 * @property bool|null $updateSiat
 * @property bool|null $activatedSaleMetodoPago
 * @property bool|null $v0_allowStore
 * @property bool|null $discountProductSale
 * @property bool|null $v0_factory
 * @property int|null $v0_idtype
 * @property int|null $v0_codigoModalidad
 * @property bool|null $v0_masivaFactura
 * @property bool $menuVertical
 * @property string|null $email
 * @property int|null $v0_idtypeEnterprise
 * @property bool|null $productCodeAuto
 * @property string|null $buttons
 * @property string|null $siatUser
 * @property string|null $siatPassword
 */
class IoSystem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cfg.ioSystem';
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
            [['dateCreate'], 'safe'],
            [['recycleBin', 'v0_restaurant', 'updateSiat', 'activatedSaleMetodoPago', 'v0_allowStore', 'discountProductSale', 'v0_factory', 'v0_masivaFactura', 'menuVertical', 'productCodeAuto'], 'boolean'],
            [['v0_nameCompany', 'v0_codigoSistema', 'descriptionCompany', 'fullNameCompany', 'v0_razonSocial', 'email', 'buttons', 'siatUser', 'siatPassword'], 'string'],
            [['iduseradmin', 'iduser', 'idstatus', 'v0_idtype', 'v0_codigoModalidad', 'v0_idtypeEnterprise'], 'default', 'value' => null],
            [['iduseradmin', 'iduser', 'idstatus', 'v0_idtype', 'v0_codigoModalidad', 'v0_idtypeEnterprise'], 'integer'],
            [['dbidentifier'], 'string', 'max' => 50],
            [['numberPhone'], 'string', 'max' => 18],
            [['codeCountry'], 'string', 'max' => 3],
            [['activadoCodigoModalidad'], 'string', 'max' => 10],
            [['v0_nit'], 'string', 'max' => 20],
            [['v0_idtype'], 'exist', 'skipOnError' => true, 'targetClass' => CfgType::class, 'targetAttribute' => ['v0_idtype' => 'id']],
            [['v0_codigoModalidad'], 'exist', 'skipOnError' => true, 'targetClass' => SiatSiatModalidad::class, 'targetAttribute' => ['v0_codigoModalidad' => 'id']],
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
            'dbidentifier' => 'Dbidentifier',
            'v0_nameCompany' => 'V0 Name Company',
            'iduseradmin' => 'Iduseradmin',
            'v0_codigoSistema' => 'V0 Codigo Sistema',
            'iduser' => 'Iduser',
            'idstatus' => 'Idstatus',
            'numberPhone' => 'Number Phone',
            'codeCountry' => 'Code Country',
            'descriptionCompany' => 'Description Company',
            'fullNameCompany' => 'Full Name Company',
            'activadoCodigoModalidad' => 'Activado Codigo Modalidad',
            'v0_nit' => 'V0 Nit',
            'v0_razonSocial' => 'V0 Razon Social',
            'v0_restaurant' => 'V0 Restaurant',
            'updateSiat' => 'Update Siat',
            'activatedSaleMetodoPago' => 'Activated Sale Metodo Pago',
            'v0_allowStore' => 'V0 Allow Store',
            'discountProductSale' => 'Discount Product Sale',
            'v0_factory' => 'V0 Factory',
            'v0_idtype' => 'V0 Idtype',
            'v0_codigoModalidad' => 'V0 Codigo Modalidad',
            'v0_masivaFactura' => 'V0 Masiva Factura',
            'menuVertical' => 'Menu Vertical',
            'email' => 'Email',
            'v0_idtypeEnterprise' => 'V0 Idtype Enterprise',
            'productCodeAuto' => 'Product Code Auto',
            'buttons' => 'Buttons',
            'siatUser' => 'Siat User',
            'siatPassword' => 'Siat Password',
        ];
    }

    /**
     * {@inheritdoc}
     * @return CfgIoSystemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new IoSystemQuery(get_called_class());
    }
}
