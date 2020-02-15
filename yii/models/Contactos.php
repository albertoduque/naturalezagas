<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contactos".
 *
 * @property integer $id
 * @property string $nombre
 * @property string $telefono
 * @property string $telefono_extension
 * @property integer $diamax_facturacion
 * @property resource $direccion
 * @property integer $id_ciudad
 * @property resource $observaciones
 * @property string $movil
 * @property string $correo
 * @property integer $id_cargo
 * @property string $created_at
 * @property string $modified_at
 * @property integer $deleted
 *
 * @property DetalleContacto[] $detalleContactos
 * @property Inscripciones[] $idDetalleProductos
 */
class Contactos extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
     public $pais,$id_padre,$inscripcion,$active;
    public static function tableName()
    {
        return 'contactos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[ 'id_ciudad','id_empresa','nombre', 'movil'], 'required'],
            [['diamax_facturacion', 'id_ciudad', 'id_cargo', 'deleted'], 'integer'],
            [['direccion', 'observaciones'], 'string'],
            [['created_at', 'modified_at','pais','id_padre','inscripcion','active'], 'safe'],
            [['correo'], 'email'],
            [['nombre', 'telefono', 'movil', 'correo'], 'string', 'max' => 255],
            [['telefono_extension'], 'string', 'max' => 20],
            [['id_empresa'], 'exist', 'skipOnError' => true, 'targetClass' => Empresas::className(), 'targetAttribute' => ['id_empresa' => 'id']],
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
            'telefono' => 'Teléfono',
            'telefono_extension' => 'Extensión',
            'diamax_facturacion' => 'Dia del mes limite de Facturación',
            'direccion' => 'Dirección',
            'id_ciudad' => 'Ciudad',
            'observaciones' => 'Observaciones',
            'movil' => 'Movil',
            'correo' => 'Correo',
            'id_cargo' => 'Cargo',
            'id_empresa' => 'Empresa',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetalleContactos()
    {
        return $this->hasMany(DetalleContacto::className(), ['id_contacto' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdDetalleProductos()
    {
        return $this->hasMany(Inscripciones::className(), ['id' => 'id_detalle_producto'])->viaTable('detalle_contacto', ['id_contacto' => 'id']);
    }
    
    public function getIdEmpresa()
    {
        return $this->hasOne(Empresas::className(), ['id' => 'id_empresa']);
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
	
    public static function toList($id)
    {
        $model=  Contactos::find()->where(['=','id_empresa',$id])->orderBy('id')->asArray()->all();
        return \yii\helpers\ArrayHelper::map($model, 'id', 'nombre');
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->nombre=strtoupper($this->nombre);
        $this->direccion=strtoupper($this->direccion);

        return true;
    }
}
