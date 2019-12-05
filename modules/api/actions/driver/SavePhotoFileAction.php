<?php

namespace app\modules\api\actions\driver;


use Yii;
use yii\web\ForbiddenHttpException;

/*
 * Получение от водителей файла фото
 * - изначально сохранение файлов фото было на одном сервере, а данные по сохраненным файлам сохранялись
 *      на другом сервере. Сейчас все на одном сервере, но по старой логике запрос текущий сохраняющий файл
 *      отделен от запроса сохраняющего историю сохранений (ибо неизвестно придеться ли разделять сервера...)
 *
 *
 */
class SavePhotoFileAction extends \yii\rest\Action
{
    public $modelClass = '';


    public function run()
    {
//        echo "GET:<pre>"; print_r($_GET); echo "</pre>";
//        echo "POST:<pre>"; print_r($_POST); echo "</pre>";
//        echo "FILES:<pre>"; print_r($_FILES); echo "</pre>";
//
//        POST:<pre>Array(
//            [photo_access_code] => de71a5b12e6837a10d1c2dca8f7ec9e4
//            [token] => rDc9x2pDzyrIpqwb3cMqE1QLJ-UVm1RW
//            [transport_car_reg] => 838
//            [transport_sh_model] => ПЖ
//            [driver_name] => Рекичинский Сергей Иванович
//            [foto1_created_date] => 1526344628
//        )</pre>
//
//        FILES:<pre>Array(
//            [foto1] => Array(
//                [name] => Screenshot_20180515-033708.png
//                [type] => image/png
//                [tmp_name] => /tmp/php8ANbA8
//                [error] => 0
//                [size] => 84902
//            )
//        )



        //throw new ForbiddenHttpException('test');

//        $uploads = \yii\web\UploadedFile::getInstancesByName("imageFile");
//        if (empty($uploads)){
//            //return "Must upload at least 1 file in upfile form-data POST";
//            throw new ForbiddenHttpException('Must upload at least 1 file in upfile form-data POST');
//        }
//
//        $savedfiles = [];
//        foreach ($uploads as $file){
//            //$path = //Generate your save file path here;
//            //    $file->saveAs($path); //Your uploaded file is saved, you can process it further from here
//        }


        $site_url = 'http://'.$_SERVER['HTTP_HOST'];


        $photo_access_code = Yii::$app->request->post('photo_access_code');
        if(empty($photo_access_code)) {
            throw new ForbiddenHttpException('Не передан код доступа к фото-серверу');
        }

        $token = Yii::$app->request->post('token');
        if(empty($token)) {
            throw new ForbiddenHttpException('Не передан token');
        }
        if(md5($token.'slktRic9i_akre') != $photo_access_code) {
            throw new ForbiddenHttpException('Неправильный код доступа к фото-серверу');
        }


        $transport_car_reg = Yii::$app->request->post('transport_car_reg');
        if(empty($transport_car_reg)) {
            throw new ForbiddenHttpException('Не передан регистрационный номер машины');
        }

        $transport_sh_model = Yii::$app->request->post('transport_sh_model');
        if(empty($transport_sh_model)) {
            throw new ForbiddenHttpException('Не передан сокращенное наименование модели машины');
        }

        $driver_name = Yii::$app->request->post('driver_name');
        if(empty($driver_name)) {
            throw new ForbiddenHttpException('Не передано имя водителя');
        }



        $dir_name = $transport_car_reg.' '.$transport_sh_model;
        $inner_dir = '/home/ftp_app/reports/'.$dir_name.'/'.date("d_m_Y");
        //$dir_path = $_SERVER['DOCUMENT_ROOT'].$inner_dir;
        $dir_path = $inner_dir;
        if(!file_exists($dir_path)) {
            if(!mkdir($dir_path, 0777, true)) {
                throw new ForbiddenHttpException('Не удалось создать директорию '.$dir_path);
            }
        }

        $allowedExts = ["gif", "jpeg", "jpg", "png", "GIF", "JPEG", "JPG", "PNG"];
        $i = 1;
        $aSavedFiles = [];

        foreach($_FILES as $key => $aFile) {

            // проверяем что в директории текущего дня не больше 6 файлов
            $dir = opendir($dir_path);
            $count = 0;
            while($file = readdir($dir)){
                if($file == '.' || $file == '..'){
                    continue;
                }
                $count++;
            }

            if($count >= 6 && $i == 1) {
                throw new ForbiddenHttpException('За сегодня загружено 6 файлов, больше загрузить сегодня не получиться');
            }elseif($count >= 6 && $i == 2) {
                // пропускаем загрузку 7-й фотографии
                return [
                    'status' => "success",
                    'foto1_link' => (isset($aSavedFiles[0]) ? $site_url.$inner_dir.'/'.$aSavedFiles[0] : ''),
                    'foto2_link' => (isset($aSavedFiles[1]) ? $site_url.$inner_dir.'/'.$aSavedFiles[1] : ''),
                ];
            }



            $temp = explode(".", $aFile['name']);
            $extension = end($temp);
            if (!in_array($extension, $allowedExts)) {
                throw new ForbiddenHttpException('Запрещенный формат файла '.$aFile['name']);
            }

            if ($aFile["error"] > 0) {
                throw new ForbiddenHttpException('ERROR Code: '. $aFile["error"]);
            }

            if(!isset($_POST['foto'.$i.'_created_date'])) {
                throw new ForbiddenHttpException('Отсутствует время создания фото '.$key);
            }

            $filename = $_POST['transport_car_reg'].'_'.$_POST['foto'.$i.'_created_date'].'.'.$extension;
            if(!move_uploaded_file($aFile["tmp_name"], $dir_path.'/'.$filename)) {
                throw new ForbiddenHttpException('Не удалось сохранить файл '.$filename);
            }



            // На самой фотографии средствами PHP нужно проставлять:
            // + краткое название т/с,
            // + номер т/с,
            // + ФИО водителя,
            // - время выгрузки,
            // + время фотографирования = time().
            $myCurl = curl_init();
            curl_setopt_array($myCurl, [
                CURLOPT_URL => $site_url.'/gas/drawtext.php',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query([
                    'filename' => 'screenshorts/'.$dir_name.'/'.date("d_m_Y").'/'.$filename,
                    'transport_sh_model' => $_POST['transport_sh_model'],
                    'transport_car_reg' => $_POST['transport_car_reg'],
                    'driver_name' => $_POST['driver_name'],
                    'created_date' => $_POST['foto'.$i.'_created_date'],
                ])
            ]);
            $response = curl_exec($myCurl);
            curl_close($myCurl);


            $aSavedFiles[] = $filename;
            $i++;
        }


        return [
            'status' => "success",
            'foto1_link' => (isset($aSavedFiles[0]) ? $site_url.$inner_dir.'/'.$aSavedFiles[0] : ''),
            'foto2_link' => (isset($aSavedFiles[1]) ? $site_url.$inner_dir.'/'.$aSavedFiles[1] : ''),
        ];
    }
}
