<?php
    require_once('../jsonutils.php');

    class User implements \JsonSerializable{
        public $id; // int: The unique identifier of the user
        public $roleid; // int: The index of the role (0 = Admin, 1 = User, ..)
        public $username; // string: The username of the user
        public $email; // string: The email of the user
        public $password; // string: The password of the user (hashed)
        public $firstname; // string: The first name of the user
        public $lastname; // string: The last name of the user
        public $iconurl; // string: The url of the user's icon
        public $subscriptionplan; // int: The index of the subscription plan (0 = Free, 1 = Basic, 2 = Premium)
        public $subscriptiondate; // string: The date of the user's subscription
        public $subscriptionenddate; // string: The date of the user's subscription end
        public $subscriptionstatus; // int: The index of the subscription status (0 = Active, 1 = Inactive)
        public $isonline; // bit: The online status of the user (0 = Offline, 1 = Online)
        public $isdeleted; // bit: The deletion status of the user (0 = Not deleted, 1 = Deleted)
        public $isvalidated; // bit: The validation status of the user (0 = Not validated, 1 = Validated)
        public $validationdate; // string: The date of the user's validation
        public $validationcode; // string: The code used during the user validation
        public $validationcodeexpiration; // string: The expiration date of the validation code
        public $lastlogin; // string: The date of the user's last login
        public $creationdate; // string: The date of the user's creation
        public $lastupdated; // string: The date of the user's last update

        public function __construct(
            int $id,
            int $roleid,
            string $username,
            string $email,
            string $password,
            string $firstname,
            string $lastname,
            string $iconurl,
            int $subscriptionplan,
            DateTime $subscriptiondate,
            DateTime $subscriptionenddate,
            bool $subscriptionstatus,
            bool $isonline,
            bool $isdeleted,
            bool $isvalidated,
            DateTime $validationdate,
            string $validationcode,
            DateTime $validationcodeexpiration,
            DateTime $lastlogin,
            DateTime $lastupdated,
            DateTime $creationdate
        )
        {
            $this->id = $id;
            $this->roleid = $roleid;
            $this->username = $username;
            $this->email = $email;
            $this->password = $password;
            $this->firstname = $firstname;
            $this->lastname = $lastname;
            $this->iconurl = $iconurl;
            $this->subscriptionplan = $subscriptionplan;
            $this->subscriptiondate = $subscriptiondate->format('Y-m-d H:i:s');
            $this->subscriptionenddate= $subscriptionenddate->format('Y-m-d H:i:s');
            $this->subscriptionstatus = $subscriptionstatus;
            $this->isonline = $isonline;
            $this->isdeleted = $isdeleted;
            $this->isvalidated = $isvalidated;
            $this->validationdate = $validationdate->format('Y-m-d H:i:s');
            $this->validationcode = $validationcode;
            $this->validationcodeexpiration = $validationcodeexpiration->format('Y-m-d H:i:s');
            $this->lastlogin = $lastlogin->format('Y-m-d H:i:s');
            $this->lastupdated = $lastupdated->format('Y-m-d H:i:s');
            $this->creationdate = $creationdate->format('Y-m-d H:i:s');
        }

        public function jsonSerialize()
        {
            return json_encode(JsonUtils::toArray($this));
        }

        public function newJsonSerialize(){
            return json_encode(get_object_vars($this));
        }

        public function textSerialize()
        {
            return 
                'id: ' . $this->id . ', ' .
                'roleid: ' . $this->roleid . ', ' .
                'username: ' . $this->username . ', ' .
                'email: ' . $this->email . ', ' .
                'password: ' . $this->password . ', ' .
                'firstname: ' . $this->firstname . ', ' .
                'lastname: ' . $this->lastname . ', ' .
                'iconurl: ' . $this->iconurl . ', ' .
                'subscriptionplan: ' . $this->subscriptionplan . ', ' .
                'subscriptiondate: ' . $this->subscriptiondate . ', ' .
                'subscriptionenddate: ' . $this->subscriptionenddate . ', ' .
                'subscriptionstatus: ' . $this->subscriptionstatus . ', ' .
                'isonline: ' . $this->isonline . ', ' .
                'isdeleted: ' . $this->isdeleted . ', ' .
                'isvalidated: ' . $this->isvalidated . ', ' .
                'validationdate: ' . $this->validationdate . ', ' .
                'validationcode: ' . $this->validationcode . ', ' .
                'validationcodeexpiration: ' . $this->validationcodeexpiration . ', ' .
                'lastlogin: ' . $this->lastlogin . ', ' .
                'creationdate: ' . $this->creationdate . ', ' .
                'lastupdated: ' . $this->lastupdated;
        }
    }
?>