<?php
    require_once('../jsonutils.php');

    class ListInvitation implements \JsonSerializable{
        public $id;
        public $creatorid;
        public $invitedid;
        public $listid;
        public $wasviewed;
        public $dayduration;
        public $creationdate;

        public function __construct(
            int $id,
            int $creatorid,
            int $invitedid,
            int $listid,
            bool $wasviewed,
            int $dayduration,
            DateTime $creationdate
        )
        {
            $this->id = $id;
            $this->creatorid = $creatorid;
            $this->invitedid = $invitedid;
            $this->listid = $listid;
            $this->wasviewed = $wasviewed;
            $this->dayduration = $dayduration;
            $this->creationdate = $creationdate;
        }

        public function jsonSerialize()
        {
            return JsonUtils::toArray($this);
        }

        public function newJsonSerialize(){
            return json_encode(get_object_vars($this));
        }

        public function textSerialize()
        {
            return 
            $this->id . "\t" .
            $this->creatorid . "\t" . 
            $this->invitedid . "\t" .
            $this->listid . "\t" . 
            $this->wasviewed . "\t" .
            $this->dayduration . "\t" .
            $this->creationdate;
        }
    }
?>