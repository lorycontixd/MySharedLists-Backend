<?php
require_once('../jsonutils.php');

class ListItem implements \JsonSerializable{
    public $id;
    public $name;
    public $description;
    public $quantity;
    public $listid;
    public $ischecked;
    public $creatorid;
    public $creationdate;

    public function __construct(
        int $id,
        string $name,
        string $description,
        int $quantity,
        int $listid,
        bool $ischecked,
        int $creatorid,
        DateTime $creationdate
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->quantity = $quantity;
        $this->listid = $listid;
        $this->ischecked = $ischecked;
        $this->creatorid = $creatorid;
        $this->creationdate = $creationdate;
    }

    public function jsonSerialize()
    {
        return json_encode(JsonUtils::toArray($this));
    }

    public function textSerialize()
    {
        return 
        $this->id . "\t" .
        $this->name . "\t" . 
        $this->description . "\t" .
        $this->quantity . "\t" . 
        $this->listid . "\t" . 
        $this->ischecked . "\t" .
        $this->creatorid . "\t" .
        $this->creationdate;
    }
}
?>