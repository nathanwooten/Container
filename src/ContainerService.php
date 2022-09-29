<?php

namespace nathanwooten\Container;

use nathanwooten\{

  Autoloader

};

use function orDefault;

use Exception;

abstract class ContainerService
{

  protected ContainerInterface $container;

  protected $id;
  protected $args = [];

  protected $load = [];
  protected $property = [];

  public function __construct( ContainerInterface $container )
  {

    $this->container = $container;

    if ( ! empty( $this->load ) ) {
      $this->load( ...$this->load );
    }

  }

  public function service( ...$args )
  {

    if ( ! isset( $this->service ) ) {

      $service = $this->id;
      $args = $this->args( $args );

      if ( ! method_exists( $this, $this->getName() ) ) {
		$service = new $service( ...$args );

      } else {
        $this->{$this->getName()}();

      }

      if ( $this->isFactory() ) {
        return $service;
      }

      $this->service = $service;
    }

    return $this->service;

  }

  public function args( $args )
  {

    if ( ! isset( $args ) ) {
      if ( ! isset( $this->args ) ) {
        $args = [];
      } else {
        $args = $this->args;
      }
    }

    return $this->args = $args;

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

  public function getName()
  {

    return getName( $this->id );

  }

}
