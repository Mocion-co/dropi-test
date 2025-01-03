<?php

namespace Drupal\dropi\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\dropi\service\DropiService;
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
   * Gets the inbox content.
   *
   * @return array
   *   The renderable array.
   */
  public function display(): array {
    $data = $this->apiService->getProducts();

    if ($data && isset($data['objects'])) {
      // print_r($data['objects']);
      return [
        '#theme' => 'dropi_product_list',
        '#products' => $data['objects'],
        '#title' => $this->t('Dropi Products'),
      ];
    }
    else {
      return [
        '#markup' => $this->t('Unable to fetch data from the API.'),
      ];
    }
  }

}
