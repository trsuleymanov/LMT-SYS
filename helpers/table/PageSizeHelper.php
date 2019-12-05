<?php

namespace app\helpers\table;

use Yii;
use yii\helpers\Html;

/**
 * Class PageSizeHelper
 *
 * @package backend\helpers\table
 */
class PageSizeHelper
{
    /**
     * @var array $buttons
     */
    private $buttons;

    /**
     * PageSizeHelper constructor.
     * @param array $buttons
     */
    public function __construct($buttons = [20, 50, 100, 200, 500])
    {
        $this->buttons = $buttons;

        if (!in_array($this->getRows(), $this->buttons)) {
            Yii::$app->session->set('table-rows', $this->buttons[0]);
        }
    }

    /**
     * @return mixed
     */
    public function getRows()
    {
        //echo 'buttons='.$this->buttons[0].'<br />';
        //$xz = Yii::$app->session->get('table-rows');
        //echo "table-rows:<pre>"; print_r($xz); echo '</pre>';

        return Yii::$app->session->get('table-rows', $this->buttons[0]);
    }

    /**
     * @return string
     */
    public function getButtons()
    {
        //echo "buttons:<pre>"; print_r($this->buttons); echo "</pre>";

        $return = '<div class="btn-group btn-group-xs pull-right">';
        foreach ($this->buttons as $button_count) {
            $return .= Html::a($button_count ? $button_count : 'все', ['/admin/setting/table-rows', 'rows' => $button_count], [
                'class' => 'btn btn-' . ($this->getRows() == $button_count ? 'github active' : 'default')
            ]);
        }
        $return .= '</div>';

        //echo "return=".$return."<br />";

        return $return;
    }
}
