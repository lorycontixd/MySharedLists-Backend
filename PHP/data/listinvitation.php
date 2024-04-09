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
    }
?>