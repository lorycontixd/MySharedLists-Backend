<?php
require_once('../jsonutils.php');

class User implements \JsonSerializable{
    public $id;
    public $username;
    public $firstname;
    public $lastname;
    public $password;
    public $creationdate;
    public $lastupdated;

    // Currencies
    public $currencies;
    // Scores
    public $scores;


    public function __construct(
        int $id,
        string $uname,
        string $fname,
        string $lname,
        string $pwd,
        DateTime $cdate,
        DateTime $ldate,
    )
    {
        $this->id = $id;
        $this->username = $uname;
        $this->firstname = $fname;
        $this->lastname = $lname;
        $this->password = $pwd;
        $this->creationdate = $cdate->format('Y-m-d H:i:s'); // Date
        $this->lastupdated = $ldate->format('Y-m-d H:i:s'); // Date
    }

    public function jsonSerialize()
    {
        return json_encode(JsonUtils::toArray($this));
    }

    public function textSerialize()
    {
        return 
        $this->id . "\t" .
        $this->username . "\t" . 
        $this->firstname . "\t" .
        $this->lastname . "\t" . 
        $this->password . "\t" . 
        $this->creationdate . "\t" . 
        $this->lastupdated;
    }
}
?>