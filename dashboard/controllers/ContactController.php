<?php

require_once "models/Contact.php";

class ContactController
{
    public $contactModel;

    public function __construct()
    {
        $this->contactModel = new Contact();
    }

    public function index()
    {
        $contacts = $this->contactModel->getAllContacts();
        include 'views/contact/index.php';
    }
}
?>