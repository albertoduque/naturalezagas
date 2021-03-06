<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "personas".
 *
 * @property integer $id
 * @property int $id_tipo_identificacion
 * @property string $nombre
 * @property string $apellido
 * @property string $identificacion
 * @property string $tipo_documento
 * @property string $telefono
 * @property string $movil
 * @property string $direccion
 * @property integer $id_ciudad
 * @property string $email
 * @property string $estado
 * @property integer $id_cargo
 * @property string $created_at
 * @property string $modified_at
 * @property integer $deleted
 *
 * @property Inscripciones[] $inscripciones
 * @property Cargos $idCargo
 * @property Ciudad $idCiudad
 */
class Personas extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $pais,$id_padre,$idEmpresa;
    public static function tableName()
    {
        return 'personas';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre', 'apellido', 'identificacion', 'movil', 'direccion', 'id_ciudad', 'email', 'estado','id_tipo_asistente','id_tipo_identificacion'], 'required'],
            [['id_ciudad', 'id_cargo', 'deleted','id_tipo_identificacion'], 'integer'],
            [['created_at', 'modified_at','pais','id_padre','idEmpresa','id_evento'], 'safe'],
            [['nombre', 'direccion', 'email'], 'string', 'max' => 255],
            [['apellido'], 'string', 'max' => 50],
            [['email'], 'email'],
           /* [['identificacion'],
                'unique',
                'when' => function ($model, $attribute) {
                    return $model->{$attribute} !== $model->getOldAttribute($attribute);
                }
            ],  */
            ['identificacion', 'unique', 'targetAttribute' => ['identificacion', 'id_evento'] ,'message' => 'La identificacion de la persona sido utilizado para este evento'],
            [['identificacion', 'telefono', 'movil', 'estado'], 'string', 'max' => 20],
            [['id_tipo_asistente'], 'exist', 'skipOnError' => true, 'targetClass' => TipoAsistentes::className(), 'targetAttribute' => ['id_tipo_asistente' => 'id']],
            [['id_cargo'], 'exist', 'skipOnError' => true, 'targetClass' => Cargos::className(), 'targetAttribute' => ['id_cargo' => 'id']],
            [['id_ciudad'], 'exist', 'skipOnError' => true, 'targetClass' => Ciudad::className(), 'targetAttribute' => ['id_ciudad' => 'id']],
			[['id_tipo_identificacion'], 'exist', 'skipOnError' => true, 'targetClass' => TipoIdentificacion::className(), 'targetAttribute' => ['id_tipo_identificacion' => 'id']],
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
            'apellido' => 'Apellido',
            'identificacion' => 'Identificacion',
            //'tipo_documento' => 'Tipo Documento',
            'telefono' => 'Telefono',
            'movil' => 'Movil',
            'direccion' => 'Direccion',
            'id_ciudad' => 'Ciudad',
            'email' => 'Email',
            'estado' => 'Estado',
            'id_cargo' => 'Cargo',
            'id_evento'=>'Evento',
            'id_tipo_asistente' => 'Tipo Asistente',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
            'deleted' => 'Deleted',
			'id_tipo_identificacion' => 'Tipo Identificación',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInscripciones()
    {
        return $this->hasMany(Inscripciones::className(), ['id_persona' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdCargo()
    {
        return $this->hasOne(Cargos::className(), ['id' => 'id_cargo']);
    }
	
	/**
     * @return \yii\db\ActiveQuery
     */
    public function getIdDepartamento()
    {
        return $this->hasOne(Departamento::className(), ['id' => 'id_padre']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCiudad()
    {
        return $this->hasOne(Ciudad::className(), ['id' => 'id_ciudad']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdTipoAsistente()
    {
        return $this->hasOne(TipoAsistentes::className(), ['id' => 'id_tipo_asistente']);
    }
	
	/**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoIdentificacion()
    {
        return $this->hasOne(TipoIdentificacion::className(), ['id' => 'id_tipo_identificacion']);
    }
}
