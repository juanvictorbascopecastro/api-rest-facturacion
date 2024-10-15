<?php

namespace app\models;

use app\models\SiatCuis;
use app\models\SiatCufd; 
use app\models\SiatBranch; 
use Yii;

/**
 * This is the model class for table "siat.systemPoint".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property int|null $codigoPuntoVenta CIAT wsdl [registroPuntoVenta]
 * @property int|null $idsiatBranch
 * @property int|null $codigoAmbiente CIAT wsdl [registroPuntoVenta]
 * @property int|null $codigoTipoPuntoVenta CIAT wsdl [registroPuntoVenta]
 * @property string|null $descripcion CIAT wsdl [registroPuntoVenta]
 * @property string|null $nombrePuntoVenta CIAT wsdl [registroPuntoVenta]
 * @property int|null $idsiatCuis para solicitar wsdl punto de venta[codigoPuntoVenta]
 * @property int $idstatus ref MAINdb status table
 * @property string|null $name identificador de punto de venta ,como ref 
 * @property int|null $iduser
 * @property int|null $siatTransaccion
 * @property string|null $siatResponse
 * @property int|null $codigoModalidad
 * @property string|null $respSiat
 */
class SystemPoint extends \yii\db\ActiveRecord {

    public $statusACTIVO = 10;
    public $statusINACTIVO = 60;
    public $statusFINALIZADO = 100;
    public $modelSiatCuis = null;
    public $modelSiatCufd = null;
    public static $modelUser = null;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'siat.systemPoint';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb() {
        return Yii::$app->get('iooxsBranch');
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['dateCreate'], 'safe'],
            [['recycleBin'], 'boolean'],
            [['codigoPuntoVenta', 'idsiatBranch', 'codigoAmbiente', 'codigoTipoPuntoVenta', 'idsiatCuis', 'idstatus', 'iduser', 'siatTransaccion', 'codigoModalidad'], 'default', 'value' => null],
            [['codigoPuntoVenta', 'idsiatBranch', 'codigoAmbiente', 'codigoTipoPuntoVenta', 'idsiatCuis', 'idstatus', 'iduser', 'siatTransaccion', 'codigoModalidad'], 'integer'],
            [['descripcion', 'nombrePuntoVenta', 'siatResponse', 'respSiat'], 'string'],
            [['idstatus'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['idsiatBranch'], 'exist', 'skipOnError' => true, 'targetClass' => SiatSiatBranch::class, 'targetAttribute' => ['idsiatBranch' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'dateCreate' => 'Date Create',
            'recycleBin' => 'Recycle Bin',
            'codigoPuntoVenta' => 'Codigo Punto Venta',
            'idsiatBranch' => 'Idsiat Branch',
            'codigoAmbiente' => 'Codigo Ambiente',
            'codigoTipoPuntoVenta' => 'Codigo Tipo Punto Venta',
            'descripcion' => 'Descripcion',
            'nombrePuntoVenta' => 'Nombre Punto Venta',
            'idsiatCuis' => 'Idsiat Cuis',
            'idstatus' => 'Idstatus',
            'name' => 'Name',
            'iduser' => 'Iduser',
            'siatTransaccion' => 'Siat Transaccion',
            'siatResponse' => 'Siat Response',
            'codigoModalidad' => 'Codigo Modalidad',
            'respSiat' => 'Resp Siat',
        ];
    }

    public function getIdsiatBranch0() {
        return $this->hasOne(SiatBranch::class, ['id' => 'idsiatBranch']);
    }

    public static function getModelCurrent() {

        if (self::$modelUser == null) {
            self::$modelUser = UserSystemPoint::find()
                    ->where(['iduserEnabled' => Yii::$app->user->getId()])
                    ->andWhere(['idstatus' => UserSystemPoint::$statusACTIVO])
                    ->one();
        }

        $model = null;
        if (self::$modelUser != null) {
            $model = self::$modelUser->idsystemPoint0;
        }

        return $model;
    }

    public static function getActiveWithUser() {
        $criteria = new CDbCriteria;
        $criteria->with = array('users');
        $criteria->addCondition("users.idstatus= " . UserSystemPoint::$statusACTIVO);
        $criteria->addCondition("users.iduser =" . Yii::app()->user->getId());

        $criteria->addCondition("t.idstatus= " . SystemPoint::$statusACTIVO);
        $criteria->order = '';

        return SystemPoint::model()->find($criteria);
    }

    public function SiatCuisActive() {
        //    echo "[".SystemPoint::getModelCurrent()->idsiatBranch0->codigoModalidad."]";
        //    echo "[".SORT_DESC."]";
        //    return;
        if ($this->modelSiatCuis == null) {

            $this->modelSiatCuis = SiatCuis::find()
                    ->where(['idsystemPoint' => $this->id])
                    ->andWhere(['idstatus' => SiatCuis::$statusACTIVO])
                    ->andWhere(['"codigoModalidad"' => SystemPoint::getModelCurrent()->idsiatBranch0->codigoModalidad])
                    ->andWhere(['"codigoAmbiente"' => SystemPoint::getModelCurrent()->idsiatBranch0->codigoAmbiente])
                    ->andWhere('NOW() <= "fechaVigencia"')
                    ->orderBy(['id' => SORT_DESC])
                    ->one();

            if ($this->modelSiatCuis == null) {
                $this->modelSiatCuis = SiatCuis::getSWDL($this);
            }
        }
        return $this->modelSiatCuis;
    }

    public function SiatCufdActive($new = false) {
        if ($this->modelSiatCuis == null)
            $this->SiatCuisActive();

        if ($new == false && $this->modelSiatCufd == null) {
            
            $this->modelSiatCufd = SiatCufd::find()
                    ->where(['"idsiatCuis"' => $this->modelSiatCuis->id])
                    ->andWhere(['idstatus' => SiatCufd::$statusACTIVO])
                    ->andWhere(['"codigoModalidad"' => SystemPoint::getModelCurrent()->idsiatBranch0->codigoModalidad])
                    ->andWhere(['"codigoAmbiente"' => SystemPoint::getModelCurrent()->idsiatBranch0->codigoAmbiente])
                    ->andWhere('NOW() <= "fechaVigencia"')
                    ->orderBy(['id' => SORT_DESC])
                    ->one();

            //$this->modelSiatCufd==null get SWDL

            if ($this->modelSiatCufd == null) {
                $this->modelSiatCufd = SiatCufd::getSWDL($this);
            }
        } else {
            if ($new == true) {

                $this->modelSiatCufd = SiatCufd::getSWDL($this);
            }
        }
        return $this->modelSiatCufd;
    }

    public function SiatCufdActive_masivaFactura($new = false) {
        if ($this->modelSiatCuis == null)
            $this->SiatCuisActive();

        if ($new == false && $this->modelSiatCufd == null) {
            $criteria = new CDbCriteria;
            $criteria->addCondition('t."idsiatCuis"= ' . $this->modelSiatCuis->id);
            $criteria->addCondition("t.idstatus= " . SiatCufd::$statusACTIVO);
            $criteria->addCondition('now() <= t."fechaVigencia"');

            $criteria->addCondition('t."masivaFactura"=true');
            $criteria->order = 't."id" desc';

            $this->modelSiatCufd = SiatCufd::model()->find($criteria);

            //$this->modelSiatCufd==null get SWDL

            if ($this->modelSiatCufd == null) {
                $this->modelSiatCufd = SiatCufd::getSWDL_masivaFactura($this);
            }
        } else {
            if ($new == true) {

                $this->modelSiatCufd = SiatCufd::getSWDL_masivaFactura($this);
            }
        }
        return $this->modelSiatCufd;
    }

    public function SiatCufdCafc($date = null, $dateEnd = null) {
        $idcontingencia = 0;
        if ($date != null)
            $date = str_replace('T', ' ', $date);
        if ($dateEnd != null)
            $dateEnd = str_replace('T', ' ', $dateEnd);
        if ($this->modelSiatCuis == null)
            $this->SiatCuisActive();


        $criteria = new CDbCriteria;
        $criteria->addCondition('t."idsiatCuis"= ' . $this->modelSiatCuis->id);
        $criteria->addCondition("t.idstatus= " . SiatCufd::$statusACTIVO);
        if ($date != null && $dateEnd == null) {
            $criteriaSiatContingencia = new CDbCriteria;
            $criteriaSiatContingencia->addCondition('"automaticExecute"=false ');
            $criteriaSiatContingencia->addCondition('"executed"=false ');
            $criteriaSiatContingencia->addCondition("" . "'" . $date . "'" . "::timestamp BETWEEN " . '"' . "fechaHoraInicioEvento" . '" ' . "  AND " . '"' . "fechaHoraFinEvento" . '"' . " ");
            $criteriaSiatContingencia->order = "id desc";
            $modelSiatContingencia = Contingencia::model()->find($criteriaSiatContingencia);

            if ($modelSiatContingencia != null) {
                $criteria->addCondition("t.cufd= '" . $modelSiatContingencia->cufdEvento . "'");
                $idcontingencia = $modelSiatContingencia->id;
            } else {
                $criteria->addCondition("" . "'" . $date . "'" . "::timestamp BETWEEN " . '"' . "fechaVigencia" . '"' . " - CAST('1 days' AS INTERVAL)  AND " . '"' . "fechaVigencia" . '"' . " ");
            }
        }

        if ($date != null && $dateEnd != null) {
            $criteria->addCondition("" . "'" . $date . "'" . "::timestamp >= " . '("' . "fechaVigencia" . '"' . " - CAST('1 days' AS INTERVAL))  ");
            $criteria->addCondition("" . "'" . $dateEnd . "'" . "::timestamp <= " . '("' . "fechaVigencia" . '"' . " )  ");
        }
        $criteria->order = 't."id" desc';
        $modelCufd = SiatCufd::model()->find($criteria);
        if ($modelCufd != null) {
            $modelCufd->idcontingencia = $idcontingencia;
        }
        return $modelCufd;
    }
}
