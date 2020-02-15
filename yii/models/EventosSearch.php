<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Eventos;

/**
 * EventosSearch represents the model behind the search form about `app\models\Eventos`.
 */
class EventosSearch extends Eventos
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_ciudad', 'deleted'], 'integer'],
            [['nombre', 'descripcion', 'fecha_hora_inicio', 'fecha_hora_fin', 'direccion', 'descripcion_sitio', 'tipo', 'sector',  'created_at', 'modified_at'], 'safe'],
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
        $query = Eventos::find()
            ->innerJoinWith('usuarioEvento', 'eventos.id = usuarioEvento.evento')
            ->andWhere(['usuario_evento.usuario' => Yii::$app->user->getId()])
            ->andWhere(['<>','eventos.id','109']);
        
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
            'fecha_hora_inicio' => $this->fecha_hora_inicio,
            'fecha_hora_fin' => $this->fecha_hora_fin,
            'id_ciudad' => $this->id_ciudad,
            'created_at' => $this->created_at,
            'modified_at' => $this->modified_at,
            'deleted' => $this->deleted,
        ]);

        $query->andFilterWhere(['like', 'nombre', $this->nombre])
            ->andFilterWhere(['like', 'descripcion', $this->descripcion])
            ->andFilterWhere(['like', 'direccion', $this->direccion])
            ->andFilterWhere(['like', 'descripcion_sitio', $this->descripcion_sitio])
            ->andFilterWhere(['like', 'tipo', $this->tipo])
            ->andFilterWhere(['like', 'id_sector', $this->id_sector]);

        return $dataProvider;
    }
}
