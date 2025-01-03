<?php

namespace Drupal\dropi\service;

use Drupal\commerce_price\Price;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use GuzzleHttp\ClientInterface;
use Symfony\Component\Serializer\Encoder\JsonEncode;

/**
 *
 */
class DropiService {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The logger factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * Constructs a new CustomService object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The HTTP client.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    ClientInterface $http_client,
    LoggerChannelFactoryInterface $logger_factory,
    EntityTypeManagerInterface $entity_type_manager,
  ) {
    $this->configFactory = $config_factory;
    $this->httpClient = $http_client;
    $this->entityTypeManager = $entity_type_manager;
    $this->loggerFactory = $logger_factory;
  }

  /**
   *
   */
  protected function getApiUrl($country_code = 'CO') {
    $config = $this->configFactory->get('dropi.settings');
    switch ($country_code) {
      case 'CO':
        return 'https://api.dropi.co/integrations';

      case 'PA':
        return 'https://api.dropi.pa/integrations';

      case 'MX':
        return 'https://api.dropi.mx/integrations';

      case 'EC':
        return 'https://api.dropi.ec/integrations';

      case 'CL':
        return 'https://api.dropi.cl/integrations';

      case 'PE':
        return 'https://api.dropi.pe/integrations';

      case 'ES':
        return 'https://api.dropi.com.es/integrations';

      case 'PY':
        return 'https://api.dropi.com.py/integrations';
    }
  }

  /**
   *
   */
  public function getApiImagePath($country_code = 'CO') {
    $config = $this->configFactory->get('dropi.settings');
    return 'https://d39ru7awumhhs2.cloudfront.net/';
  }

  /**
   *
   */
  public function getProducts($country_code = 'CO', $page_size = 20, $page_offset = 0) {
    $config = $this->configFactory->get('dropi.settings');
    $api_url = $this->getApiUrl();
    $api_key = $config->get('dropi_integration_keys.' . $country_code);
    $country_code = $config->get('country');

    try {
      $data = [
        // 'keywords' => $search,
        'startData' => $page_offset,
        'pageSize' => $page_size,
        'order_type' => 'asc',
        'order_by' => 'id',
        'active' => TRUE,
        'no_count' => TRUE,
        'integration' => TRUE,
        'type' => 'VARIABLE',
      ];

      if ($country_code == "ES") {
        unset($data['integration']);
      }

      if (in_array($country_code, ["CO", "PY", "PE", "PA"])) {
        $data['get_stock'] = FALSE;
      }

      $response = $this->httpClient->request(
            'POST', $api_url . '/products/index', [
              'headers' => [
                'dropi-integration-key' => $api_key,
                'Content-Type' => 'application/json;charset=UTF-8',
              ],
              'json' => $data,
            ]
        );

      return json_decode($response->getBody()->getContents(), TRUE);
    }
    catch (\Exception $e) {
      $this->loggerFactory->get('dropi')->error('Error fetching data from API: @message', ['@message' => $e->getMessage()]);
      return NULL;
    }
  }

  /**
   *
   */
  public function createProduct($data) {

    $created = \Drupal::time()->getRequestTime();

    /** @var \Drupal\commerce_product\Entity\ProductVariationInterface $productVariationEntity */
    $productVariationEntity = $this->entityTypeManager->getStorage('commerce_product_variation');

    $variations = [];

    if ($data['type'] == 'SIMPLE') {
      $variation = $productVariationEntity->create([
        'type' => 'default',
        'sku' => 'sku-' . $data['id'],
        'price' => new Price($data['sale_price'], 'COP'),
        'list_price' => new Price($data['suggested_price'], 'COP'),
        'status' => TRUE,
        'created' => $created,
        'changed' => $created,
      ]);
      $variation->save();
      $variations[] = $variation;
    }
    else if ($data['type'] == 'VARIABLE') {
      foreach ($data['variations'] as $variationItem) {
        $variation = $productVariationEntity->create([
          'type' => 'default',
          'sku' => 'sku-' . $variationItem['id'],
          'price' => new Price($variationItem['sale_price'], 'COP'),
          'list_price' => new Price($variationItem['suggested_price'], 'COP'),
          'status' => TRUE,
          'created' => $created,
          'changed' => $created,
        ]);
        $variation->save();
        $variations[] = $variation;
      }
    }

    /** @var \Drupal\commerce_product\Entity\ProductInterface $productEntity */
    $productEntity = $this->entityTypeManager->getStorage('commerce_product');

    $product = $productEntity->create([
      'status' => TRUE,
      'title' => $data['name'],
      'type' => 'default',
      'body' => [
        'value' => $data['description'],
        'summary' => '',
        'format' => 'basic_html',
      ],
      'created' => $created,
      'variations' => $variations,
      'field_data' => json_encode($data),
    ]);
    $product->save();
  }

}
