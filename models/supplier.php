<?php
// Supplier Model
class Supplier {
    // Properties
    public $id = "";
    public $name = "";
    public $contact_person = "";
    public $phone = "";
    public $email = "";
    public $address = "";
    public $created_at = "";

    function __construct($name, $contact_person = "", $phone = "", $email = "", $address = "", $created_at = "", $id = "")
    {
        $this->id = $id;
        $this->name = $name;
        $this->contact_person = $contact_person;
        $this->phone = $phone;
        $this->email = $email;
        $this->address = $address;
        $this->created_at = $created_at;
    }
}
?>