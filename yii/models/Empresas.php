<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "empresas".
 *
 * @property int $id
 * @property int $id_tipo_identificacion
 * @property string $nombre
 * @property string $identificacion
 * @property string $direccion
 * @property string $telefono
 * @property string $telefono_extension
 * @property string $movil
 * @property int $id_ciudad
 * @property string $afiliado_gremio
 * @property string $estado
 * @property int $id_sector_empresa
 * @property string $created_at
 * @property string $modified_at
 * @property int $deleted
 * @property int $id_evento
 * @property int $id_proveedor_tecnologico
 * @property string $correo_facturacion_electronica
 *
 * @property Contactos[] $contactos
 * @property Ciudad $ciudad
 * @property ProveedorTecnologico $proveedorTecnologico
 * @property SectoresEmpresas $sectorEmpresa
 * @property Facturas[] $facturas
 * @property Inscripciones[] $inscripciones
 */
class Empresas extends \yii\db\ActiveRecord
{
    public $pais,$id_padre,$redirectEmpresa,$is_patrocinios;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'empresas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'identificacion', 'direccion', 'telefono', 'movil', 'id_ciudad', 'afiliado_gremio', 'estado', 'id_sector_empresa', 'id_evento','id_tipo_identificacion'], 'required'],
            [['id_ciudad', 'id_sector_empresa', 'deleted', 'id_evento', 'id_proveedor_tecnologico','id_tipo_identificacion'], 'integer'],
            [['created_at', 'modified_at','pais','id_padre','verificacion','redirectEmpresa','id_proveedor_tecnologico','correo_facturacion_electronica','is_patrocinios'], 'safe'],
            [['nombre', 'direccion'], 'string', 'max' => 255],
            [['identificacion', 'telefono', 'telefono_extension', 'movil', 'estado'], 'string', 'max' => 20],
            [['afiliado_gremio'], 'string', 'max' => 10],
            [['correo_facturacion_electronica'], 'string', 'max' => 250],
            [['id_ciudad'], 'exist', 'skipOnError' => true, 'targetClass' => Ciudad::className(), 'targetAttribute' => ['id_ciudad' => 'id']],
            [['id_proveedor_tecnologico'], 'exist', 'skipOnError' => true, 'targetClass' => ProveedorTecnologico::className(), 'targetAttribute' => ['id_proveedor_tecnologico' => 'id']],
            [['id_tipo_identificacion'], 'exist', 'skipOnError' => true, 'targetClass' => TipoIdentificacion::className(), 'targetAttribute' => ['id_tipo_identificacion' => 'id']],
            [['id_sector_empresa'], 'exist', 'skipOnError' => true, 'targetClass' => SectoresEmpresas::className(), 'targetAttribute' => ['id_sector_empresa' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Razón Social',
            'identificacion' => 'RUT / NIT',
            'direccion' => 'Dirección',
            'telefono' => 'Telefono',
            'telefono_extension' => 'Telefono Extension',
            'movil' => 'Movil',
            'id_ciudad' => 'Ciudad',
            'verificacion'=> 'Digito de Verificación',
            'afiliado_gremio' => 'Afiliado Gremio',
            'estado' => 'Estado',
            'id_evento'=>'Evento',
            'id_sector_empresa' => 'Sector Empresa',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
            'deleted' => 'Deleted',
            'id_proveedor_tecnologico' => 'Proveedor Tecnologico',
            'id_tipo_identificacion' => 'Tipo Identificación',
            'correo_facturacion_electronica' => 'Correo Facturación Electrónica',
            'is_patrocinios'=>'Patrocinador',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContactos()
    {
        return $this->hasMany(Contactos::className(), ['id_empresa' => 'id']);
    }
	
	/**
     * @return \yii\db\ActiveQuery
     */
    public function getIdDepartamento()
    {
        //return $this->hasOne(Departamento::className(), ['id' => 'id_ciudad']);
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
    public function getProveedorTecnologico()
    {
        return $this->hasOne(ProveedorTecnologico::className(), ['id' => 'id_proveedor_tecnologico']);
    }
	
	/**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoIdentificacion()
    {
        return $this->hasOne(TipoIdentificacion::className(), ['id' => 'id_tipo_identificacion']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSectorEmpresa()
    {
        return $this->hasOne(SectoresEmpresas::className(), ['id' => 'id_sector_empresa']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFacturas()
    {
        return $this->hasMany(Facturas::className(), ['id_empresa' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInscripciones()
    {
        return $this->hasMany(Inscripciones::className(), ['id_empresa' => 'id']);
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
