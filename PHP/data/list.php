<?php
    require_once('../jsonutils.php');

    class MyList implements \JsonSerializable{
        public $id;
        public $name;
        public $description;
        public $creatorid;
        public $colorcode;
        public $iconid;
        public $currencyid;
        public $code;
        public $memberids;
        public $adminids;
        public $lastupdated;
        public $creationdate;
        public $listitems;

        public function __construct(
            int $id,
            string $name,
            string $description,
            int $creatorid,
            int $colorcode,
            int $iconid,
            int $currencyid,
            string $code,
            array $memberids = array(),
            array $adminids = array(),
            DateTime $ldate = null,
            DateTime $cdate = null,
            array $listitems = array()
        )
        {
            $this->id = $id;
            $this->name = $name;
            $this->description = $description;
            $this->creatorid = $creatorid;
            $this->colorcode = $colorcode;
            $this->iconid = $iconid;
            $this->currencyid = $currencyid;
            $this->code = $code;
            $this->memberids = $memberids;
            $this->adminids = $adminids;
            $this->creationdate = $cdate->format('Y-m-d H:i:s'); // Date
            $this->lastupdated = $ldate->format('Y-m-d H:i:s'); // Date
            $this->listitems = $listitems;
        }

        public function jsonSerialize()
        {
            return json_encode(get_object_vars($this), JSON_UNESCAPED_SLASHES);
        }

        public function textSerialize()
        {
            return 
            $this->id . "\t" .
            $this->name . "\t" . 
            $this->description . "\t" .
            $this->creatorid . "\t" . 
            $this->colorcode . "\t" . 
            $this->iconid . "\t" .
            $this->currencyid . "\t" .
            $this->code . "\t" . 
            $this->memberids . "\t" .
            $this->adminids . "\t" .
            $this->creationdate . "\t" . 
            $this->lastupdated . "\t" .
            $this->listitems;
        }
    }
?>