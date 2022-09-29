<?php

namespace nathanwooten\Container;

use nathanwooten\{

  Autoloader,

  Standard\StandardRun

};

use Exception;

abstract class ContainerService
{

  use StandardRun;

  protected ContainerInterface $container;

  protected $id = IdClass::class;
  protected $args = [];

  protected $load = [];
  protected $property = [];

  public function __construct( ContainerInterface $container )
  {

    $this->container = $container;

    $this->load( ...$this->load );
  }

  public function service( ...$args )
  {

    if ( ! isset( $this->service ) ) {

      $service = $this->id;
      $args = $this->args( $args );

      $service = new $service( ...$args );

      if ( $this->isFactory() ) {
        return $service;
      }

      $this->service = $service;
    }

    return $this->service;

  }

  public function args( $args )
  {

	$this->args = $this->orDefault( 'args', $args );
    return $this->args;

  }

  public function load( ...$load )
  {

    if ( ! empty( $load ) ) {
      $index = Autoloader::add( ...$load );
      if ( is_integer( $index ) ) {
        $package = Autoloader::get( $index );
        return $package;
      }
    }
  }

  public function isFactory()
  {

    if ( isset( $this->property[ 'factory' ] ) && $this->property[ 'factory' ] ) {
      return true;
    }

    return false;

  }

  public function orDefault( $property, $value = null )
  {

    return $this->standardRun( __FUNCTION__, [ $this, $property, $value ] );    

  }

}
