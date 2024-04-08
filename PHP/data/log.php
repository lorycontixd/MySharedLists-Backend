<?php
    require_once('../jsonutils.php');

    class Log implements \JsonSerializable{
        public $id;
        public $code;
        public $level;
        public $title;
        public $description;
        public $userid;
        public $source;
        public $stacktrace;
        public $devicename;
        public $devicemodel;
        public $devicetype;
        public $deviceos;
        public $creationdate;

        public function __construct__(
            $id,
            $code,
            $level,
            $title,
            $description,
            $userid,
            $source,
            $stacktrace,
            $devicename,
            $devicemodel,
            $devicetype,
            $deviceos,
            $creationdate
        ){
            $this->id = $id;
            $this->code = $code;
            $this->level = $level;
            $this->title = $title;
            $this->description = $description;
            $this->userid = $userid;
            $this->source = $source;
            $this->stacktrace = $stacktrace;
            $this->devicename = $devicename;
            $this->devicemodel = $devicemodel;
            $this->devicetype = $devicetype;
            $this->deviceos = $deviceos;
            $this->creationdate = $creationdate;
        }

        public function jsonSerialize(){
            return json_encode(JsonUtils::toArray($this));
        }

        public function newJsonSerialize(){
            return json_encode(get_object_vars($this));
        }

        public function textSerialize(){
            return 
            $this->id . "\t" .
            $this->code . "\t" . 
            $this->level . "\t" .
            $this->title . "\t" . 
            $this->description . "\t" . 
            $this->userid . "\t" . 
            $this->source . "\t" . 
            $this->stacktrace . "\t" . 
            $this->devicename . "\t" . 
            $this->devicemodel . "\t" . 
            $this->devicetype . "\t" . 
            $this->deviceos . "\t" . 
            $this->creationdate;
        }
    }
?>