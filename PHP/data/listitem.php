<?php
require_once('../jsonutils.php');

class ListItem implements \JsonSerializable{
    public $id;
    public $listorder;
    public $name;
    public $description;
    public $quantity;
    public $price;
    public $brand;
    public $listid;
    public $ischecked;
    public $creatorid;
    public $creationdate;

    public function __construct(
        int $id,
        int $listorder,
        string $name,
        string $description,
        int $quantity,
        float $price,
        string $brand,
        int $listid,
        bool $ischecked,
        int $creatorid,
        DateTime $creationdate
    )
    {
        $this->id = $id;
        $this->listorder = $listorder;
        $this->name = $name;
        $this->description = $description;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->brand = $brand;
        $this->listid = $listid;
        $this->ischecked = $ischecked;
        $this->creatorid = $creatorid;
        $this->creationdate = $creationdate;
    }

    public function jsonSerialize()
    {
        //return json_encode(JsonUtils::toArray($this), JSON_UNESCAPED_SLASHES);
        //return json_encode(get_object_vars($this));
        return JsonUtils::toArray($this);
    }

    public function newJsonSerialize(){
        return json_encode(get_object_vars($this));
    }

    public function textSerialize()
    {
        return 
        $this->id . "\t" .
        $this->listorder . "\t" .
        $this->name . "\t" . 
        $this->description . "\t" .
        $this->price . "\t" .
        $this->brand . "\t" .
        $this->quantity . "\t" . 
        $this->listid . "\t" . 
        $this->ischecked . "\t" .
        $this->creatorid . "\t" .
        $this->creationdate;
    }
}
?>