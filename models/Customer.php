<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customer".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property string|null $name
 * @property string|null $numeroDocumento
 * @property string|null $phone
 * @property string|null $numberPhone2
 * @property int|null $iduser
 * @property int|null $iddocumentNumberType
 * @property int|null $type
 * @property int|null $idcustomer
 * @property string|null $email
 * @property int|null $idjobPosition
 * @property int|null $code
 * @property int|null $idcity
 * @property string|null $address
 * @property int|null $codigoTipoDocumentoIdentidad
 * @property int|null $complemento
 * @property string|null $razonSocial
 * @property bool|null $allowedCredit
 * @property string|null $note
 */
class Customer extends \yii\db\ActiveRecord
{
    private static $customDb;
    
    public static function tableName()
    {
        return 'customer';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    // public static function getDb()
    // {
    //     return Yii::$app->get('empresa8');
    // }
    public static function getDb()
    {
        return self::$customDb ?: Yii::$app->db;
    }

    public static function setCustomDb($db)
    {
        self::$customDb = $db;
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dateCreate'], 'safe'],
            [['recycleBin', 'allowedCredit'], 'boolean'],
            [['name', 'numeroDocumento', 'phone', 'email', 'address', 'razonSocial', 'note'], 'string'],
            [['iduser', 'iddocumentNumberType', 'type', 'idcustomer', 'idjobPosition', 'code', 'idcity', 'codigoTipoDocumentoIdentidad', 'complemento'], 'default', 'value' => null],
            [['iduser', 'iddocumentNumberType', 'type', 'idcustomer', 'idjobPosition', 'code', 'idcity', 'codigoTipoDocumentoIdentidad', 'complemento'], 'integer'],
            [['numberPhone2'], 'string', 'max' => 30],
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
            'numeroDocumento' => 'Numero Documento',
            'phone' => 'Phone',
            'numberPhone2' => 'Number Phone2',
            'iduser' => 'Iduser',
            'iddocumentNumberType' => 'Iddocument Number Type',
            'type' => 'Type',
            'idcustomer' => 'Idcustomer',
            'email' => 'Email',
            'idjobPosition' => 'Idjob Position',
            'code' => 'Code',
            'idcity' => 'Idcity',
            'address' => 'Address',
            'codigoTipoDocumentoIdentidad' => 'Codigo Tipo Documento Identidad',
            'complemento' => 'Complemento',
            'razonSocial' => 'Razon Social',
            'allowedCredit' => 'Allowed Credit',
            'note' => 'Note',
        ];
    }

    /**
     * {@inheritdoc}
     * @return CustomerQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CustomerQuery(get_called_class());
    }
}
