<?php

namespace Drupal\dropi\Commands;

use Drupal\dropi\Service\DropiService;
use Drush\Commands\DrushCommands;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 *
 */
class DropiCommands extends DrushCommands {

  protected $dropiService;

  public function __construct(DropiService $dropi_service) {
    parent::__construct();
    $this->dropiService = $dropi_service;
  }

  /**
   *
   */
  public static function create(ContainerInterface $container) {
    return new static(
          $container->get('dropi.service')
      );
  }

  /**
   * Fetch data from the Dropi API.
   *
   * @command dropi:fetch
   * @aliases dropi-fetch
   * @usage dropi:fetch
   *   Fetches data from the configured Dropi API.
   */
  public function fetch() {
    $data = $this->dropiService->getProducts();

    if ($data) {
      // $this->output()->writeln(print_r($data, TRUE));
      $this->logger()->success(dt('Data fetched successfully.'));

      foreach ($data['objects'] as $item) {
        $this->dropiService->createProduct($item);
      }
    }
    else {
      $this->logger()->error(dt('Failed to fetch data from the API.'));
    }
  }

}
