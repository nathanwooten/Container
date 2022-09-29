<?php

namespace nathanwooten\Container;

use nathanwooten\{

  Container\ContainerInterface,

};

use Exception;

abstract class ContainerAbstract implements ContainerInterface
{

  protected $services = [];

  protected $directory;

  public function __construct( $directory = null )
  {

    if ( ! is_null( $directory ) ) {
      $this->config( $directory );
    }

  }

  public function set( $id, $service )
  {

    if ( $this->isA( $service, $id ) ) {
      $this->services[ $id ] = $service;
    }

  }

  public function get( $id, $args = null )
  {

    $args = (array) $args;

    if ( array_key_exists( $id, $this->services ) ) {
      $container = $this->services[ $id ];

    } else {
      $container = $this->create( $id );

      $this->set( $id, $container );
    }

    $service = $container->service( ...$args );

    return $service;

  }

  protected function create( $id, array $args = null )
  {

    $service = false;
    $args = (array) $args;

    $fn_args = func_get_args();
    if ( ! isset( $fn_args[2] ) ) {
        $directory = $this->config();
	} else {
        $directory = $fn_args[2];
    }
    $directory = rtrim( $directory, DS ) . DS;
    $name = $this->getName( $id );

	$directory = $directory . $name . DS;
	$readable = $directory . $name . 'service.php';

    if ( ! is_readable( $readable ) ) {
      throw new Exception( 'Unreadable: ' . (string) $readable );
    }
    $class = $this->getServiceClass( $id );

    $container = new $class( $this );
    return $container;

  }

  public function config( $directory = null )
  {

    if ( isset( $directory ) ) {
      $this->directory = $directory;
    }

    return $this->directory;

  }

  protected function isA( $is, $a )
  {

    if ( is_object( $a ) ) {
      if ( is_a( $a, $is ) ) {
        return true;
      }

      return false;
    }

    return true;

  }

  public function getServiceClass( $id )
  {

    $name = static::getName( $id );
    $class = static::getNamespace() . '\\' . 'Services' . '\\' . $name . '\\' . $name . 'service';
    return $class;

  }

  public function getName( $id )
  {

    return str_replace( '\\', '', strtolower( $id ) );

  }

  public static function getContainerClass()
  {

    $class = PROJECT_NAME . '\\' . 'Container' . '\\' . 'Container';
    return $class;

  }

}
