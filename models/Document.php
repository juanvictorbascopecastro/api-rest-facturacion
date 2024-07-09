<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "document".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property int|null $idcliente
 * @property int|null $idstatus
 * @property string|null $comment
 * @property int|null $number
 * @property int|null $iddocumentType
 * @property int|null $idsale
 * @property int|null $idpurchase
 * @property int|null $iduser
 * @property int|null $iddocument
 * @property int|null $idorder
 * @property int|null $idstore
 * @property int|null $idproductionOrder
 * @property int|null $idadjustment
 */
class Document extends \yii\db\ActiveRecord
{
    public static $customDb;

    public static function tableName()
    {
        return 'document';
    }

    public static function getDb()
    {
        return Yii::$app->iooxsBranch;
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
            [['recycleBin'], 'boolean'],
            [['idcliente', 'idstatus', 'number', 'iddocumentType', 'idsale', 'idpurchase', 'iduser', 'iddocument', 'idorder', 'idstore', 'idproductionOrder', 'idadjustment'], 'default', 'value' => null],
            [['idcliente', 'idstatus', 'number', 'iddocumentType', 'idsale', 'idpurchase', 'iduser', 'iddocument', 'idorder', 'idstore', 'idproductionOrder', 'idadjustment'], 'integer'],
            [['comment'], 'string'],
            [['idsale'], 'exist', 'skipOnError' => true, 'targetClass' => Sale::class, 'targetAttribute' => ['idsale' => 'id']],
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
            'idcliente' => 'Idcliente',
            'idstatus' => 'Idstatus',
            'comment' => 'Comment',
            'number' => 'Number',
            'iddocumentType' => 'Iddocument Type',
            'idsale' => 'Idsale',
            'idpurchase' => 'Idpurchase',
            'iduser' => 'Iduser',
            'iddocument' => 'Iddocument',
            'idorder' => 'Idorder',
            'idstore' => 'Idstore',
            'idproductionOrder' => 'Idproduction Order',
            'idadjustment' => 'Idadjustment',
        ];
    }

    /**
     * {@inheritdoc}
     * @return DocumentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DocumentQuery(get_called_class());
    }
}
