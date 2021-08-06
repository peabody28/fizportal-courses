<?php
require_once __DIR__ . "/Theme.php";
require_once __DIR__ . "/Courses_table.php";
require_once __DIR__ . "/Themes_table.php";
require_once __DIR__ . "/Render.php";


class Course
{
    public $id, $title, $text, $complexity=0, $price=null, $img_url=null, $themes=null;

    public function __construct($id=null)
    {
        if($id !== null)
        {
            $courses_table = new Courses_table();
            $tmp_course = $courses_table->read($id);
            $this->id = (int)$tmp_course["id"];
            $this->title = $tmp_course["title"];
            $this->text = $tmp_course["text"];
            $this->complexity = (int)$tmp_course["complexity"];
            $this->price = $tmp_course["price"];
            $this->img_url = $tmp_course["img_url"];
        }

    }

    public function get_themes()
    {
       $themes_table = new Themes_table();
       $themes_list = $themes_table->get_courses_themes($this->id);
       foreach ($themes_list as $item)
       {
           $theme = new Theme($item["id"], $item);
           $this->themes[] = $theme;
       }
       return $this->themes;
    }

    public function get_themes_ids()
    {
        $list = [];
        if(!$this->themes)
            $this->get_themes();
        foreach ($this->themes as $theme)
            $list[] = $theme->id;
        return $list;
    }

    public function get_html($data)
    {
        $render = new Render();
        return $render->render_course($this, $data["status"]);
    }
}
