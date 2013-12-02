<?php
namespace Main\Widgets;
use Core\Widget;

class News extends Widget
{
    public function init()
    {
        $this->render('index', array(
            'items' => array(
                array(
                    'id' => 1,
                    'date' => date('Y-m-d'),
                    'title' => 'News title',
                    'desc' => 'News content',
                    'pic' => '/img/nopic.png'
                )
            )
        ));
    }
}