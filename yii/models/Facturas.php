<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "facturas".
 *
 * @property integer $id
 * @property string $numero
 * @property integer $subtotal
 * @property integer $iva
 * @property integer $total
 * @property integer $id_estado_factura
 * @property string $observaciones
 * @property integer $id_moneda
 * @property integer $id_contacto
 * @property integer $id_empresa
 * @property integer $id_persona
 * @property integer $descuento
 * @property string $created_at
 * @property string $modified_at
 * @property integer $deleted
 * @property string $tipo_factura
 * @property DetalleFactura[] $detalleFacturas
 * @property Empresas $idEmpresa
 * @property Personas $idPersona
 * @property Contactos $idContacto
 * @property EstadosFactura $idEstadoFactura
 * @property Monedas $idMoneda
 */
class Facturas extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $serie,$direccion,$telefonoContacto,$tipoidentificacion
		,$ciudad,$ciudadNombre
		,$departamento,$departamentoNombre
		,$pais
		,$porcentajeIva,$clientes,$relacion_nc,$serie_nc,$id_nc,$identificacion, $verificacion,$facturaNC,$facturas,$identificacionFormat,$tipoDocumento
		,$cantidadLineas
		,$tipoNota
		,$tipoDocumentoFacturaModificada
		,$facturaNumero
		,$fecha_factura
		;
    public static function tableName()
    {
        return 'facturas';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['numero', 'id_estado_factura', 'id_moneda','tipo_compra','clientes'], 'required'],
            ['periodo_pago', 'required', 'when' => function($model) {
                return $model->tipo_compra == 'CREDITO' && $model->tipoDocumento == 1;
            }],
            ['fecha_vencimiento', 'required', 'when' => function($model) {
                return $model->tipo_compra == 'CREDITO' && $model->tipoDocumento == 1;
            }],
             ['id_tipo_nota', 'required', 'when' => function($model) {
                return $model->tipoDocumento == 2 || $model->tipoDocumento == 3;
            }],
            ['fecha', 'compare', 'compareAttribute' => 'fecha_factura','operator' => '>='],
            //[['numero', 'id_estado_factura', 'id_moneda','fecha'], 'required'],
            [[ 'id_estado_factura', 'id_moneda', 'id_empresa', 'id_persona', 'descuento','is_patrocinios', 'deleted','id_evento','id_serie','tipo_compra','id_impuesto','id_tipo_nota'], 'integer'],
            [['observaciones','cufe','respuesta','orden_compra','numeroaceptacioninterno'], 'string'],
            [['subtotal', 'iva', 'total','tipo_factura','send_xml'], 'string'],
            [['created_at','send_xml','cufe', 'modified_at','fecha_transmision','direccion','serie_nc','id_nc','tipoDocumento','relacion_nc','telefonoContacto','tipoidentificacion'
				,'pais'
				,'ciudad','ciudadNombre'
				,'departamento','departamentoNombre'
				,'porcentajeIva','subtotal', 'iva', 'total','is_patrocinios','clientes'
				,'identificacion', 'verificacion','facturaNC','facturas','serie','respuesta','id_serie','trm',
				'fecha_vencimiento','orden_compra','numeroaceptacioninterno','fechaemisionordencompra',
				'cantidadLineas','fecha_factura'], 'safe'],
            [['numero','facturaNumero'], 'string', 'max' => 20],
            //[['numero'], 'unique'],
            //['numero', 'unique', 'targetAttribute' => ['id_evento','numero'=>'tipo_factura']],
            [['id_empresa'], 'exist', 'skipOnError' => true, 'targetClass' => Empresas::className(), 'targetAttribute' => ['id_empresa' => 'id']],
            [['id_persona'], 'exist', 'skipOnError' => true, 'targetClass' => Personas::className(), 'targetAttribute' => ['id_persona' => 'id']],
            [['id_contacto'], 'exist', 'skipOnError' => true, 'targetClass' => Contactos::className(), 'targetAttribute' => ['id_contacto' => 'id']],
            [['id_estado_factura'], 'exist', 'skipOnError' => true, 'targetClass' => EstadosFactura::className(), 'targetAttribute' => ['id_estado_factura' => 'id']],
            [['id_moneda'], 'exist', 'skipOnError' => true, 'targetClass' => Monedas::className(), 'targetAttribute' => ['id_moneda' => 'id']],
			[['id_impuesto'], 'exist', 'skipOnError' => true, 'targetClass' => Impuestos::className(), 'targetAttribute' => ['id_impuesto' => 'id']],
			[['id_medio_pago'], 'exist', 'skipOnError' => true, 'targetClass' => MedioPago::className(), 'targetAttribute' => ['id_medio_pago' => 'id']],
			[['id_tipo_nota'], 'exist', 'skipOnError' => true, 'targetClass' => TipoNota::className(), 'targetAttribute' => ['id_tipo_nota' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Id',
            'numero' => 'Numero',
            'subtotal' => 'Subtotal',
            'iva' => 'Iva',
            'total' => 'Total',
            'id_estado_factura' => 'Id Estado Factura',
            'observaciones' => 'Observaciones',
            'id_moneda' => 'Id Moneda',
            'id_contacto' => 'Id Contacto',
            'id_empresa' => 'Id Empresa',
            'id_persona' => 'Id Persona',
            'descuento' => 'Descuento',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
            'deleted' => 'Deleted',
            'fecha'=>'Fecha Factura',
            'is_patrocinios'=>'Patrocinios',
            'id_evento'=>'Evento',
            'cufe' => 'Cufe',
            'tipo_factura'=> 'Tipo Documento',
			'trm'=> 'Tasa de cambio',						
			'tipo_compra'=>'Tipo Compra',						
			'periodo_pago'=>'Periodo Pago',						
			'fecha_vencimiento'=>'Fecha Vencimiento',						
			'id_impuesto'=>'Impuesto Retención',			
			'orden_compra'=>'Orden de Compra',			
			'fechaemisionordencompra'=>'Fecha Orden de Compra',			
			'numeroaceptacioninterno'=>'Número Aceptación Interno',
			'id_tipo_nota'=>'Tipo Nota'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetalleFacturas()
    {
        return $this->hasMany(DetalleFactura::className(), ['id_factura' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdEmpresa()
    {
        return $this->hasOne(Empresas::className(), ['id' => 'id_empresa']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdPersona()
    {
        return $this->hasOne(Personas::className(), ['id' => 'id_persona']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdContacto()
    {
        return $this->hasOne(Contactos::className(), ['id' => 'id_contacto']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdEstadoFactura()
    {
        return $this->hasOne(EstadosFactura::className(), ['id' => 'id_estado_factura']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdMoneda()
    {
        return $this->hasOne(Monedas::className(), ['id' => 'id_moneda']);
    }		
	/**     * @return \yii\db\ActiveQuery     */    
	public function getIdImpuesto()    {        
		return $this->hasOne(Impuestos::className(), ['id' => 'id_impuesto']);    
	}
	
	/**     * @return \yii\db\ActiveQuery     */    
	public function getIdMedioPago()    {        
		return $this->hasOne(MedioPago::className(), ['id' => 'id_medio_pago']);    
	}
    public static function toListTipoFacturas(){
        return ['FA'=>'FACTURA','NC'=>'NOTA CREDITO','ND'=>'NOTA DEBITO'];
    }
    
    public function validateInvoiceDate($attribute, $params,$validator){
        $validator->addError('fecha', 'Your salary is not enough for children.');
        return false;
    }
}