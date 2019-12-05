<?php
namespace app\modules\api\controllers;

use yii\filters\auth\HttpBearerAuth;


/**
 * Всё для водителей
 */
class DriverController extends \yii\rest\ActiveController
{
    public $modelClass = '';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // remove authentication filter
        $auth = $behaviors['authenticator'];
        unset($behaviors['authenticator']);

        // add CORS filter
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
            ],
        ];


        // re-add authentication filter
        $behaviors['authenticator'] = $auth;
        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)


        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
        ];
        // исключим временно из проверки авторизованности для данного запроса...
        $behaviors['authenticator']['except'] = [
            'savephotofile',
            'upload'
        ];


        return $behaviors;
    }

    public function actionUpload()
    {
        return [
            'files' => $_FILES,
            'get' => $_GET,
            'post' => $_POST,
        ];

        // echo "FILES:<pre>"; print_r($_FILES); echo "</pre>";
        // echo "GET:<pre>"; print_r($_GET); echo "</pre>";
        // echo "POST:<pre>"; print_r($_POST); echo "</pre>";

        /*
        $postdata = fopen( $_FILES[ 'data' ][ 'tmp_name' ], "r" );
        // Get file extension
        $extension = substr( $_FILES[ 'data' ][ 'name' ], strrpos( $_FILES[ 'data' ][ 'name' ], '.' ) );

        // Generate unique name
        $filename = $this->documentPath . uniqid() . $extension;

        // Open a file for writing
        $fp = fopen( $filename, "w" );

        // Read the data 1 KB at a time and write to the file
        while( $data = fread( $postdata, 1024 ) )
            fwrite( $fp, $data );

        // Close the streams
        fclose( $fp );
        fclose( $postdata );

        // the result object that is sent to client
        $result = new UploadResult;
        $result->filename = $filename;
        $result->document = $_FILES[ 'data' ][ 'name' ];
        $result->create_time = date( "Y-m-d H:i:s" );

        return $result;

        */
    }


    public function actions()
    {
        $actions = parent::actions();
        unset($actions['view'], $actions['delete'], $actions['index'], $actions['create'], $actions['update']);

        $actions['setphotosdata']['class'] = 'app\modules\api\actions\driver\SetPhotosDataAction';
        $actions['setmessagetooperator']['class'] = 'app\modules\api\actions\driver\SetMessageToOperatorAction';
        $actions['savephotofile']['class'] = 'app\modules\api\actions\driver\SavePhotoFileAction';

        return $actions;
    }

    protected function verbs(){
        return [
            'setphotosdata' => ['GET', 'POST'],
            'setmessagetooperator' => ['GET', 'POST'],
            'savephotofile' => ['GET', 'POST', 'PUT'],
            'upload' => ['POST'],
        ];
    }

}
