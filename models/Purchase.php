<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "purchase".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property int|null $idvendor
 * @property string|null $nameVendor
 * @property string|null $numeroDocumento
 * @property int|null $idstatus
 * @property string|null $comment
 * @property int|null $number
 * @property int|null $iddocument
 * @property float|null $discountpercentage
 * @property float|null $discountamount
 * @property float|null $montoTotal
 * @property int|null $iduser
 * @property float|null $subTotal
 * @property int|null $idinvoice
 * @property int|null $numeroFactura
 * @property string|null $attachedDocument
 * @property string|null $broadcastDateDocument
 * @property string|null $cuf
 * @property int|null $idstore
 *
 * @property Productstock[] $productstocks
 */
class Purchase extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'purchase';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('iooxsBranch');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dateCreate', 'broadcastDateDocument'], 'safe'],
            [['recycleBin'], 'boolean'],
            [['idvendor', 'idstatus', 'number', 'iddocument', 'iduser', 'idinvoice', 'numeroFactura', 'idstore'], 'default', 'value' => null],
            [['idvendor', 'idstatus', 'number', 'iddocument', 'iduser', 'idinvoice', 'numeroFactura', 'idstore'], 'integer'],
            [['nameVendor', 'comment', 'cuf'], 'string'],
            [['discountpercentage', 'discountamount', 'montoTotal', 'subTotal'], 'number'],
            [['numeroDocumento'], 'string', 'max' => 30],
            [['attachedDocument'], 'string', 'max' => 70],
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
            'idvendor' => 'Idvendor',
            'nameVendor' => 'Name Vendor',
            'numeroDocumento' => 'Numero Documento',
            'idstatus' => 'Idstatus',
            'comment' => 'Comment',
            'number' => 'Number',
            'iddocument' => 'Iddocument',
            'discountpercentage' => 'Discountpercentage',
            'discountamount' => 'Discountamount',
            'montoTotal' => 'Monto Total',
            'iduser' => 'Iduser',
            'subTotal' => 'Sub Total',
            'idinvoice' => 'Idinvoice',
            'numeroFactura' => 'Numero Factura',
            'attachedDocument' => 'Attached Document',
            'broadcastDateDocument' => 'Broadcast Date Document',
            'cuf' => 'Cuf',
            'idstore' => 'Idstore',
        ];
    }

    /**
     * Gets query for [[Productstocks]].
     *
     * @return \yii\db\ActiveQuery|ProductstockQuery
     */
    public function getProductstocks()
    {
        return $this->hasMany(Productstock::class, ['idpurchase' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return PurchaseQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PurchaseQuery(get_called_class());
    }

    public function beforeSave($insert) {

        if ($this->scenario == 'default') {
            $this->iduser = Yii::$app->user->getId();
            $this->setNumber();
        }

        return true; // Permitir que la acción continúe
    }

    public function setNumber() {
        $q = 'select max(number) from purchase where "recycleBin"=false';
        $command = Yii::$app->iooxsBranch->createCommand($q);
        $number = $command->queryScalar();
        $number = $number == null ? 1 : $number + 1;

        $this->number = $number;
    }
}
