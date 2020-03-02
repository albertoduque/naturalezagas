<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Empresas;

/**
 * EmpresaSearch represents the model behind the search form about `app\models\Empresas`.
 */
class EmpresaSearch extends Empresas
{
    public $is_patrocinios;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_ciudad', 'id_sector_empresa', 'deleted'], 'integer'],
            [['nombre', 'identificacion', 'direccion', 'telefono', 'telefono_extension', 'movil', 'afiliado_gremio', 'estado', 'created_at', 'modified_at','is_patrocinios'], 'safe'],
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
        $session = Yii::$app->session;
        $event_id = $session->get('event_id');
        $evento = \app\models\Eventos::findOne($session->get('event_id'));
        if($evento['tipo']==2)
          $query = Empresas::find()->where(['=','empresas.id_evento',$event_id]);
        else {
          $query = Empresas::find()
          ->select(['empresas.*', 'facturas.is_patrocinios'])
          ->leftJoin('facturas', 'facturas.id_empresa = empresas.id')
          ->where(['=','empresas.id_evento',$event_id]);
        }
        
        
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
              'pageSize' => 15,
          ],
        ]);
        
        $dataProvider->sort->attributes["is_patrocinios"]["asc"] = ["facturas.is_patrocinios" => SORT_ASC];
        $dataProvider->sort->attributes["is_patrocinios"]["desc"] = ["facturas.is_patrocinios" => SORT_DESC];
        $dataProvider->sort->attributes["is_patrocinios"]["label"] = "Patrocinios";

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        $this->addCondition($query, 'is_patrocinios');
         // echo $query->createCommand()->sql;die;
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'id_ciudad' => $this->id_ciudad,
            'id_sector_empresa' => $this->id_sector_empresa,
            'created_at' => $this->created_at,
            'modified_at' => $this->modified_at,
            'deleted' => $this->deleted,
        ]);

        $query->andFilterWhere(['like', 'nombre', $this->nombre])
            ->andFilterWhere(['like', 'identificacion', $this->identificacion])
            ->andFilterWhere(['like', 'direccion', $this->direccion])
            ->andFilterWhere(['like', 'telefono', $this->telefono])
            ->andFilterWhere(['like', 'telefono_extension', $this->telefono_extension])
            ->andFilterWhere(['like', 'movil', $this->movil])
            ->andFilterWhere(['like', 'afiliado_gremio', $this->afiliado_gremio])
            ->andFilterWhere(['like', 'estado', $this->estado]);

        return $dataProvider;
    }
    protected function addCondition($query, $attribute, $partialMatch = false) {
        $value = $this->$attribute;
        if (trim($value) === '') {
            return;
        }
        
        if ($attribute == "is_patrocinios") {
            $attribute = "facturas.is_patrocinios";
        }
       
        if($partialMatch){
                $query->andWhere(['like', $attribute, $value]);
        } else {
                $query->andWhere([$attribute => $value]);
        }
        
    }
}
