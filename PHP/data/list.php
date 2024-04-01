<?php
require_once('../jsonutils.php');

    class MyList implements \JsonSerializable{
        public $id;
        public $name;
        public $description;
        public $creatorid;
        public $color;
        public $code;
        public $iconid;
        public $memberids;
        public $adminids;
        public $lastupdated;
        public $creationdate;

        public function __construct(
            int $id,
            string $name,
            string $description,
            int $creatorid,
            int $color,
            int $iconid,
            string $code,
            array $memberids = array(),
            array $adminids = array(),
            DateTime $cdate = null,
            DateTime $ldate = null,
        )
        {
            $this->id = $id;
            $this->name = $name;
            $this->description = $description;
            $this->creatorid = $creatorid;
            $this->color = $color;
            $this->iconid = $iconid;
            $this->code = $code;
            $this->memberids = $memberids;
            $this->adminids = $adminids;
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
            $this->iconid . "\t" .
            $this->code . "\t" . 
            $this->memberids . "\t" .
            $this->adminids . "\t" .
            $this->creationdate . "\t" . 
            $this->lastupdated;
        }
    }
?>