<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Inscripciones;
use  yii\web\Session;

/**
 * InscripcionSearch represents the model behind the search form about `app\models\Inscripciones`.
 */
class InscripcionSearch extends Inscripciones
{
    /**
     * @inheritdoc
     */
     public $empresa_nombre,$persona_nombre,$asistente;
    public function rules()
    {
        return [
            [['id', 'id_empresa', 'id_producto', 'id_tipo_asistente', 'estado', 'id_persona', 'deleted'], 'integer'],
            [['created_at', 'modified_at','persona_nombre','empresa_nombre','asistente'], 'safe'],
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
        $query = Inscripciones::find()
                 ->leftJoin('productos', 'productos.id=inscripciones.id_producto')
                ->where(['=','productos.id_evento',$event_id]);
        $query->joinWith('idEmpresa');
        $query->joinWith('idPersona');
        $query->joinWith(['idPersona.idTipoAsistente']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $dataProvider->sort->attributes["persona_nombre"]["asc"] = ["personas.nombre" => SORT_ASC];
        $dataProvider->sort->attributes["persona_nombre"]["desc"] = ["personas.nombre" => SORT_DESC];
        $dataProvider->sort->attributes["persona_nombre"]["label"] = "Persona";
        
        $dataProvider->sort->attributes["empresa_nombre"]["asc"] = ["empresas.nombre" => SORT_ASC];
        $dataProvider->sort->attributes["empresa_nombre"]["desc"] = ["empresas.nombre" => SORT_DESC];
        $dataProvider->sort->attributes["empresa_nombre"]["label"] = "Empresa";
        
        $dataProvider->sort->attributes["asistente"]["asc"] = ["tipo_asistentes.nombre" => SORT_ASC];
        $dataProvider->sort->attributes["asistente"]["desc"] = ["tipo_asistentes.nombre" => SORT_DESC];
        $dataProvider->sort->attributes["asistente"]["label"] = "Asistente";
        
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        $this->addCondition($query, 'empresa_nombre',true);
        $this->addCondition($query, 'asistente');
       
        $query->andFilterWhere([
            'or',
            ['like', 'personas.nombre', $this->persona_nombre],
            ['like', 'personas.apellido', $this->persona_nombre],
        ]);    

        // grid filtering conditions
        $query->andFilterWhere([
            'inscripciones.estado' => $this->estado,
        ]);

        return $dataProvider;
    }
    
    protected function addCondition($query, $attribute, $partialMatch = false) {
        $value = $this->$attribute;
        if (trim($value) === '') {
            return;
        }
        
        if ($attribute == "asistente") {
            $attribute = "tipo_asistentes.id";
        }
        if ($attribute == "empresa_nombre") {
            $attribute = "empresas.nombre";//nombre de la tabla del join
        }
       
        if($partialMatch){
                $query->andWhere(['like', $attribute, $value]);
        } else {
                $query->andWhere([$attribute => $value]);
        }
        
    }
}
