<?php
    require_once('../jsonutils.php');

    enum ListInvitationStatus: int{
        case Pending = 0;
        case Accepted = 1;
        case Rejected = 2;
    }

    class ListInvitation implements \JsonSerializable{
        public $id;
        public $creatorid;
        public $invitedid;
        public $creatorusername;
        public $listid;
        public $listname;
        public $viewed;
        public $status;
        public $dayduration;
        public $creationdate;

        public function __construct($id, $creatorid, $invitedid, $creatorusername, $listid, $listname, $viewed, $status, $dayduration, $creationdate){
            $this->id = $id;
            $this->creatorid = $creatorid;
            $this->invitedid = $invitedid;
            $this->creatorusername = $creatorusername;
            $this->listid = $listid;
            $this->listname = $listname;
            $this->viewed = $viewed;
            $this->status = $status;
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
            $this->creatorusername . "\t" .
            $this->listid . "\t" . 
            $this->listname . "\t" .
            $this->viewed . "\t" .
            $this->status . "\t" .
            $this->dayduration . "\t" .
            $this->creationdate;
        }
    }
?>