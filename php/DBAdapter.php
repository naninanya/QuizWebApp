<?php

class DBAdapter
{
    private $dsn = ''; //your dsn;
    private $user = ''; //your userID;
    private $pass = ''; //your password;
    protected $pdo;

    public function __construct()
    {
        $this->Open();
    }

    public function Open()
    {
        $this->pdo = new PDO($this->dsn, $this->user, $this->pass);
    }

    public function GetPDO()
    {
        return $this->pdo;
    }
}
