<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "eventos".
 *
 * @property integer $id
 * @property string $nombre
 * @property string $descripcion
 * @property string $fecha_hora_inicio
 * @property string $fecha_hora_fin
 * @property integer $id_ciudad
 * @property string $direccion
 * @property string $descripcion_sitio
 * @property string $tipo
 * @property integer $id_sector
 * @property string $encabezado
 * @property string $piedepagina
 * @property string $created_at
 * @property string $modified_at
 * @property integer $deleted
 *
 * @property SectoresEmpresas $idSector
 * @property Productos[] $productos
 */
class Eventos extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'eventos';
    }

    /**
     * @inheritdoc
     */
    public $pais,$copyEvent,$copyEventId;
    public function rules()
    {
        return [
            [['nombre', 'descripcion', 'fecha_hora_inicio', 'fecha_hora_fin',  'tipo'], 'required'],
            [['descripcion', 'descripcion_sitio'], 'string'],
            [['fecha_hora_inicio', 'fecha_hora_fin', 'created_at', 'descripcion_sitio', 'id_sector', 'id_ciudad', 'direccion','modified_at'], 'safe'],
            [['id_ciudad', 'id_sector', 'deleted'], 'integer'],
            [['nombre', 'direccion'], 'string', 'max' => 255],
            [['tipo'], 'string', 'max' => 20],
            [['pais','copyEvent','copyEventId'], 'safe'],
            ['nombre', 'unique'],
            ['fecha_hora_inicio','validateDates'],
            [['id_sector'], 'exist', 'skipOnError' => true, 'targetClass' => SectoresEmpresas::className(), 'targetAttribute' => ['id_sector' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'descripcion' => 'Descripcion',
            'fecha_hora_inicio' => 'Fecha Hora Inicio',
            'fecha_hora_fin' => 'Fecha Hora Fin',
            'id_ciudad' => 'Ciudad',
            'direccion' => 'Direccion',
            'descripcion_sitio' => 'Descripcion Sitio',
            'tipo' => 'Tipo Evento',
            'id_sector' => 'Sector',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
            'deleted' => 'Deleted',
            'copyEvent'=>'Copiar Tablas del evento Anterior',
            'copyEventId'=>'Eventos Anteriores'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdSector()
    {
        return $this->hasOne(SectoresEmpresas::className(), ['id' => 'id_sector']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdTipo()
    {
        return $this->hasOne(TipoEvento::className(), ['id' => 'id_sector']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductos()
    {
        return $this->hasMany(Productos::className(), ['id_evento' => 'id']);
    }


    public function getUser()
    {
        return $this->hasMany(User::className(), ['id_evento' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDescripcionProductos()
    {
        return $this->hasMany(DescripcionProductos::className(), ['evento_id' => 'id']);
    }
    
    public function getUsuarioEvento()
    {
        return $this->hasMany(UsuarioEvento::className(), ['evento' => 'id']);
    }

    /**
     * @return array
     */
    public static function toList()
    {
        $model= Eventos::find()->where(['=','deleted',0])->orderBy('id')->asArray()->all();
        return \yii\helpers\ArrayHelper::map($model, 'id', 'nombre');
    }

    /**
     *
     */
    public function validateDates(){
        if($this->fecha_hora_inicio > $this->fecha_hora_fin){
            $this->addError('fecha_hora_inicio','Ingrese una Fecha de Inicio menor al Fecha de Fin');
            $this->addError('fecha_hora_fin','Ingrese una Fecha de Inicio menor al Fecha de Fin');
        }
    }
}
