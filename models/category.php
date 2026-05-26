<?php
// Category Model
class Category {
    // Properties
    public $id = "";
    public $name = "";
    public $description = "";
    public $created_at = "";

    function __construct($name, $description = "", $created_at = "", $id = "")
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->created_at = $created_at;
    }
}
?>