<?php
// Item Model
class Item {
    // Properties
    public $id = "";
    public $sku = "";
    public $name = "";
    public $description = "";
    public $category_id = 0;
    public $supplier_id = 0;
    public $stock_quantity = 0;
    public $purchase_price = 0.0;
    public $selling_price = 0.0;
    public $created_at = "";
    // For display purposes (joined data)
    public $category_name = "";
    public $supplier_name = "";

    function __construct($sku, $name, $description = "", $category_id = 0, $supplier_id = 0,
                        $stock_quantity = 0, $purchase_price = 0.0, $selling_price = 0.0,
                        $created_at = "", $id = "", $category_name = "", $supplier_name = "")
    {
        $this->id = $id;
        $this->sku = $sku;
        $this->name = $name;
        $this->description = $description;
        $this->category_id = $category_id;
        $this->supplier_id = $supplier_id;
        $this->stock_quantity = $stock_quantity;
        $this->purchase_price = $purchase_price;
        $this->selling_price = $selling_price;
        $this->created_at = $created_at;
        $this->category_name = $category_name;
        $this->supplier_name = $supplier_name;
    }
}
?>