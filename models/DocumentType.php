<?php

namespace app\models;

use Yii;

// para los tipos de movimientos, compras ventas, etc
class DocumentType extends \yii\db\ActiveRecord {

    public static $idTypeINPUT = 1;
    public static $idTypeOUTPUT = 2;
    public static $idTypeSALE = 3;
    public static $idTypeSALE_ANNUL = 4;
    public static $idTypePURCHASE = 5;
    public static $idTypePURCHASE_ANNUL = 6;
    public static $idTypeADJUSTMENT_INPUT = 7;
    public static $idTypeADJUSTMENT_INPUT_ANNUL = 8;
    public static $idTypeADJUSTMENT_OUTPUT = 9;
    public static $idTypeADJUSTMENT_OUTPUT_ANNUL = 10;
    public static $idTypeINPUT_ANNUL = 11;
    public static $idTypeOUTPUT_ANNUL = 12;
    public static $idTypeORDER = 13;
    public static $idTypeORDER_ANNUL = 14;
    public static $idTypeORDEN_PRODUCCION_INICIADA = 15;
    public static $idTypeORDEN_PRODUCCION_INICIADA_ANULADA = 16;
    public static $idTypeORDEN_PRODUCCION_FINALIZADA_ENTRADA = 17;
    public static $idTypeORDEN_PRODUCCION_FINALIZADA_ENTRADA_CORRECCION = 18;
    public static $idTypeORDEN_PRODUCCION_FINALIZADA_SALIDA_CORRECCION = 19;
    public static $idTypeORDEN_PRODUCCION_ANULADA = 20;

    public static function tableName() {
        return 'documentType';
    }

    public static function getDb() {
        return Yii::$app->get('iooxs_io');
    }

    public function rules() {
        return [
            [['dateCreate'], 'safe'],
            [['recycleBin'], 'boolean'],
            [['type', 'description'], 'string'],
            [['action', 'iduser'], 'default', 'value' => null],
            [['action', 'iduser'], 'integer'],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'dateCreate' => 'Date Create',
            'recycleBin' => 'Recycle Bin',
            'type' => 'Type',
            'action' => 'Action',
            'description' => 'Description',
            'iduser' => 'Iduser',
        ];
    }

    public static function find() {
        return new DocumentTypeQuery(get_called_class());
    }
}
