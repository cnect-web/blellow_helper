<?php

declare(strict_types = 1);

namespace Drupal\blellow_helper\Plugin\Block;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the login widget as a block.
 *
 * @Block(
 *   id = "cnect_join_futurium",
 *   admin_label = @Translation("Join Futurium block"),
 *   category = @Translation("Cnect Corporate blocks"),
 * )
 */
class JoinFuturiumBlock extends BlockBase  implements ContainerFactoryPluginInterface{

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Construct the footer block object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $cache = new CacheableMetadata();
    $cache->addCacheContexts(['languages:language_interface']);
    $config = $this->configFactory->get('blellow_helper.data.welcome');

    $cache->addCacheableDependency($config);
    $build['#theme'] = 'cnect_corporate_blocks_join_futurium';

    $build['#attached'] = [
      'library' => [
        'blellow_helper/join_futurium',
      ],
    ];

    NestedArray::setValue($build, ['#corporate_welcome', 'join', 'title'], $config->get('join_title'));
    NestedArray::setValue($build, ['#corporate_welcome', 'join', 'body'], $config->get('join_body'));
    NestedArray::setValue($build, ['#corporate_welcome', 'join', 'call'], $config->get('join_call'));
    NestedArray::setValue($build, ['#corporate_welcome', 'join', 'target'], $config->get('join_target'));

    return $build;
  }

}
