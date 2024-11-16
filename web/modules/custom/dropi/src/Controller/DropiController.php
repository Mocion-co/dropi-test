<?php

namespace Drupal\dropi\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\dropi\DropiService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 *
 */
class DropiController extends ControllerBase {

  protected $apiService;

  public function __construct(DropiService $api_service) {
    $this->apiService = $api_service;
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
   *
   */
  public function display() {
    $data = $this->apiService->fetchData();

    if ($data) {
      $output = [
        '#theme' => 'item_list',
        '#items' => $data,
        '#title' => $this->t('API Data'),
      ];
    }
    else {
      $output = [
        '#markup' => $this->t('Unable to fetch data from the API.'),
      ];
    }

    return $output;
  }

}
