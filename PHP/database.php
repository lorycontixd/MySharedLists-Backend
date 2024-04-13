<?php
  class Database{
    // Endpoint variables
    public $name = "mysharedlists";
    public $uid = "lorenzo.conti";
    public $password = "Loriemichi19!";
    public $servername = "tcp:mysharedlists.database.windows.net,1433";
    public $conn;

    // Date & time
    public $timezone = "Europe/Rome";

    private $isConnected;

    function __construct() {
        $connectionInfo = array("UID" => $this->uid, "pwd" => $this->password, "Database" => $this->name, "LoginTimeout" => 30, "Encrypt" => 1, "TrustServerCertificate" => 0);
        $this->conn = sqlsrv_connect($this->servername, $connectionInfo);
    }

    // Connection - Methods
    function get_connection(){
        return $this->conn;
    }
    function get_connection_info(){
      return array("UID" => $this->uid, "pwd" => $this->password, "Database" => $this->name, "LoginTimeout" => 30, "Encrypt" => 1, "TrustServerCertificate" => 0);
    }
    // Date & time - Methods
    function get_timezone(){
        return $this->timezone;
    }
    function get_server_date(){
      date_default_timezone_set($this->timezone);
      $date = date('Y-m-d H:i:s');
      return $date;
    }
    function get_server_date_iso(){
      date_default_timezone_set($this->timezone);
      $date = date('Y-m-d\TH:i:s');
      return $date;
    }
    function get_date(){
        date_default_timezone_set($this->timezone);
        return date("d-m-Y H:i:s");
    }

    function generate_code( $type = 'alnum', $length = 8 )
    {
      switch ( $type )
      {
        case 'alnum':
          $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
          break;
        case 'alpha':
          $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
          break;
        case 'hexdec':
          $pool = '0123456789abcdef';
          break;
        case 'numeric':
          $pool = '0123456789';
          break;
        case 'nozero':
          $pool = '123456789';
          break;
        case 'distinct':
          $pool = '2345679ACDEFHJKLMNPRSTUVWXYZ';
          break;
        default:
          $pool = (string) $type;
          break;
      }


      $crypto_rand_secure = function ( $min, $max ) {
        $range = $max - $min;
        if ( $range < 0 ) return $min; // not so random...
        $log    = log( $range, 2 );
        $bytes  = (int) ( $log / 8 ) + 1; // length in bytes
        $bits   = (int) $log + 1; // length in bits
        $filter = (int) ( 1 << $bits ) - 1; // set all lower bits to 1
        do {
          $rnd = hexdec( bin2hex( openssl_random_pseudo_bytes( $bytes ) ) );
          $rnd = $rnd & $filter; // discard irrelevant bits
        } while ( $rnd >= $range );
        return $min + $rnd;
      };

      $token = "";
      $max   = strlen( $pool );
      for ( $i = 0; $i < $length; $i++ ) {
        $token .= $pool[$crypto_rand_secure( 0, $max )];
      }
      return $token;
    }
  }
?>