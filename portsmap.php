<?php 
/**
 * @package   portsmap.php
 * @author    Samy Naqwada <naqwada@pm.me>
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link      https://github.com/Naqwada/Ports-Scanner-PHP-CLI
 * Scan a range of ports from an IP address to check if ports are close or open
 * How to use:
 * php portsmap.php $ip_address $port_start-$port_end
 * Example:
 * php portsmap.php 192.168.1.1 21-3306
 */
set_time_limit(0);
error_reporting(0);

class Portmap {
  public function __construct($argv) {
    if(empty($argv[1]) || empty($argv[2])) {
      echo "\033[31m Error ... \r\n";
      echo "\033[00m How to use: \r\n";
      echo "\033[00m php portsmap.php 192.168.1.1 21-3306 \r\n";
      exit;
    }
    
    if(strpos($argv[2], "-")) {
      $ports = explode("-", $argv[2]);
      $portStart = $ports[0];
      $portEnd = $ports[1];
    } else {
      $portStart = $argv[2];
      $portEnd = null;
    } 

    $host = $argv[1];

    if(!filter_var($host, FILTER_VALIDATE_IP)){
      echo "\033[31m IP address $host is not valid. \r\n";
      exit;
    }

    return $this->scanPort($host,$portStart,$portEnd);
  }

  public function scanPort($host,$portStart,$portEnd) {
    if($portEnd == null) {
      if(!$portStart < 1 && !$portStart > 65535) {
        echo "\033[31m port $portStart is not valid. \r\n";
        exit;
      }
      $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
      if(socket_connect($sock, $host, $portStart)) {
        echo "\033[32m port $portStart is open. \r\n";
        socket_close($sock);
      } else {
        echo "\033[31m port $portStart is closed. \r\n";
        socket_close($sock);
      }
    } else {
      if(!$portStart < 1 && !$portEnd > 65535) {
        echo "\033[31m The range port $portStart-$portEnd is not valid \r\n";
        exit;
      }

      for ($i=$portStart; $i < $portEnd+1; $i++) { 
        $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if(socket_connect($sock, $host, $i)) {
          echo "\033[32m port $i is open.\r\n";
          socket_close($sock);
        } else {
          echo "\033[31m port $i is closed.\r\n";
          socket_close($sock);
        } 
      }
    }
  }
}

new Portmap($argv);
?>
