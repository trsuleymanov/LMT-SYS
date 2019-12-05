<?php

namespace app\models\core;
use PDO;
use Symfony\Component\Yaml\Tests\YamlTest;
use yii\base\ErrorException;

/**
 * Запросы формируемые с помощью этого класса с результатами запросов сохраняются до следующей перезагрузки страницы
 */

class QueryWithSave extends \yii\db\Query {

    public function one($db = null) {

        if ($this->emulateExecution) {
            return false;
        }

        $command = $this->createCommand($db);
        //$command->fetchMode = \PDO::FETCH_OBJ;
        $sql = $command->getRawSql();

        if(!isset(\Yii::$app->params['queriesWithCache_one'][$sql])) {
            \Yii::$app->params['queriesWithCache_one'][$sql] = $command->queryOne(\PDO::FETCH_OBJ);
        }

        return \Yii::$app->params['queriesWithCache_one'][$sql];
    }


    public function all($db = null) {

        if ($this->emulateExecution) {
            return [];
        }

        $command = $this->createCommand($db);
        //$command->fetchMode = \PDO::FETCH_OBJ;
        //$command->fetchMode=[\PDO::FETCH_CLASS,__CLASS__];
        //$command->setFetchMode(PDO::FETCH_OBJ);
        $sql = $command->getRawSql();

        if(!isset(\Yii::$app->params['queriesWithCache_all'][$sql])) {
            $rows = $command->queryAll(\PDO::FETCH_OBJ);
            //\Yii::$app->params['queriesWithCache_all'][$sql] = $this->populate($rows);
            \Yii::$app->params['queriesWithCache_all'][$sql] = $rows;
        }

        return \Yii::$app->params['queriesWithCache_all'][$sql];
    }


}