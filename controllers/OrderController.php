<?php
namespace app\controllers;

use Yii;
use app\models\Order;
use app\models\OrderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
{

    /**
     *
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => [
                        'POST'
                    ]
                ]
            ]
        ];
    }

    /**
     * Lists all Order models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Displays a single Order model.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id)
        ]);
    }

    /**
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $model = new Order();
        if ($model->load(Yii::$app->request->post())) {

            $response = $model->getCoordinates($model);
            if (! empty($response['message'])) {

                $model->addError('street_address', $response['message']);
                return $this->render('create', [
                    'model' => $model
                ]);
            }
            $model->latitude = (string) $response['lat'];
            $model->longitude = (string) $response['lng'];
            if ($model->save()) {
                return $this->redirect([
                    'view',
                    'id' => $model->id
                ]);
            } else {
                print_r($model->getErrors());
                exit();
            }
        }

        return $this->render('create', [
            'model' => $model,

            'dataProvider' => $dataProvider
        ]);
    }
    
    public function actionGetCoordinates(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $model=new Order();
        if(isset($_POST)){
            $model->street_address=$_POST['street'];
            $model->city=$_POST['city'];
            $model->state=$_POST['state'];
            $model->country=$_POST['country'];
            $response = $model->getCoordinates($model);
            return $response;
            
        }
    }

    public function actionEditable($id)
    {
        if (isset($_POST['hasEditable'])) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $model = Order::findOne($id);
            if (! empty($model)) {
                $status=array_values($_POST['Order']);
                $model->status = $status['0']['status'];
                if($model->save()){
                    return ['output'=>$model->getStatus($model->status), 'message'=>''];
                }
                
                print_r($_POST);
                exit();
            }
        }
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect([
                'view',
                'id' => $model->id
            ]);
        }

        return $this->render('update', [
            'model' => $model
        ]);
    }

    /**
     * Deletes an existing Order model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete()
    {
        $id=$_POST['id'];
        $this->findModel($id)->delete();

        return $this->redirect([
            'create'
        ]);
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
