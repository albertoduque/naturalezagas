<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;

function FormatoFechas($fecha){
        $dia = substr($fecha, 0, 2);
        $mes  = substr($fecha, 3, 2);
        $ano = substr($fecha, -4);
        // fechal final realizada el cambio de formato a las fechas europeas5
        $fecha = $mes.'/'.$dia.'/'.$ano;
   
    return $fecha;
}

/**
 * UserSearch represents the model behind the search form about `app\models\User`.
 */
class UserSearch extends User
{
    /**
     * @inheritdoc
     */
    
    public $rol_nombre;
    
    public function rules()
    {
        return [
            [['id', 'status',  'updated_at'], 'integer'],
            [['username', 'auth_key', 'password_hash', 'password_reset_token', 'email','created_at','rol_nombre','nombre','cedula','telefono'], 'safe'],
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
        $query = User::find();
        $query->joinWith('rol');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $dataProvider->sort->attributes["rol_nombre"]["asc"] = ["rol.nombre" => SORT_ASC];
        $dataProvider->sort->attributes["rol_nombre"]["desc"] = ["rol.nombre" => SORT_DESC];
        $dataProvider->sort->attributes["rol_nombre"]["label"] = "Role";
        
        
        $dataProvider->pagination->pageSize=10;
       
       //  var_dump($query);
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        
        //------------------ Filtro para la Fecha ------------------------------
        $date = (new \DateTime($this->created_at));
        if(!empty($this->created_at))
        {
            $date = (new \DateTime(FormatoFechas($this->created_at)));
            $date->setTime(0,0,0);
            // set lowest date value
            $unixDateStart = $date->getTimeStamp();
            // add 1 day and subtract 1 second
            $date->add(new \DateInterval('P1D'));
            $date->sub(new \DateInterval('PT1S'));
            // set highest date value
            $unixDateEnd = $date->getTimeStamp();
        }
        //------------------ Filtro para la Fecha ------------------------------ 
        $this->addCondition($query, 'rol_nombre');
       
    
        $query->andFilterWhere([
            'id' => $this->id,
            'user.status' => $this->status,
            'updated_at' => $this->updated_at,
        ]);
        $query->andFilterWhere(['>', 'user.status', 0]);
        if(!empty($this->created_at))
        {
            $query->andFilterWhere(['>=', 'created_at', $unixDateStart])
                  ->andFilterWhere(['<=', 'created_at', $unixDateEnd]);
        }
        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'user.nombre', $this->nombre]) 
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
    
    protected function addCondition($query, $attribute) {
        $value = $this->$attribute;
        if (trim($value) === '') {
            return;
        }
       
        if ($attribute == "rol_nombre") {
            $attribute = "rol.idRol";
        }
       
        $query->andWhere([$attribute => $value]); 
    }
   
}
