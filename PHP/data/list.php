<?php
require_once('../jsonutils.php');

    class MyList implements \JsonSerializable{
        public $id;
        public $name;
        public $description;
        public $creatorid;
        public $color;
        public $code;
        public $lastupdated;
        public $creationdate;

        public function __construct(
            int $id,
            string $name,
            string $description,
            int $creatorid,
            int $color,
            string $code,
            DateTime $cdate,
            DateTime $ldate,
        )
        {
            $this->id = $id;
            $this->name = $name;
            $this->description = $description;
            $this->creatorid = $creatorid;
            $this->color = $color;
            $this->code = $code;
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
            $this->name . "\t" . 
            $this->description . "\t" .
            $this->creatorid . "\t" . 
            $this->color . "\t" . 
            $this->code . "\t" . 
            $this->creationdate . "\t" . 
            $this->lastupdated;
        }
    }
?>