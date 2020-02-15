<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\DetalleFactura;

/**
 * DetalleFacturaSearch represents the model behind the search form about `app\models\DetalleFactura`.
 */
class DetalleFacturaSearch extends DetalleFactura
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_factura', 'id_inscripcion', 'cantidad', 'id_moneda', 'id_estado_factura', 'deleted'], 'integer'],
            [['valor', 'descuento', 'valorTotal', 'subtotal', 'iva'], 'number'],
            [['observacion', 'created_at', 'modified_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = DetalleFactura::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'id_factura' => $this->id_factura,
            'id_inscripcion' => $this->id_inscripcion,
            'cantidad' => $this->cantidad,
            'valor' => $this->valor,
            'descuento' => $this->descuento,
            'valorTotal' => $this->valorTotal,
            'subtotal' => $this->subtotal,
            'iva' => $this->iva,
            'id_moneda' => $this->id_moneda,
            'id_estado_factura' => $this->id_estado_factura,
            'created_at' => $this->created_at,
            'modified_at' => $this->modified_at,
            'deleted' => $this->deleted,
        ]);

        $query->andFilterWhere(['like', 'observacion', $this->observacion]);

        return $dataProvider;
    }
}
