<?php

/**
 * Contains \Drupal\payment\Plugin\Payment\Method\PaymentMethodManager.
 */

namespace Drupal\payment\Plugin\Payment\Method;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Language\LanguageManager;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Manages discovery and instantiation of payment method plugins.
 *
 * @see \Drupal\payment\Plugin\Payment\Method\PaymentMethodInterface
 */
class PaymentMethodManager extends DefaultPluginManager implements PaymentMethodManagerInterface {

  /**
   * Constructs a new class instance.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Language\LanguageManager $language_manager
   *   The language manager.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, LanguageManager $language_manager, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/Payment/Method', $namespaces, '\Drupal\payment\Annotations\PaymentMethod');
    $this->alterInfo($module_handler, 'payment_method');
    $this->setCacheBackend($cache_backend, $language_manager, 'payment_method');
  }

  /**
   * {@inheritdoc}
   */
  public function createInstance($plugin_id, array $configuration = array()) {
    // If a plugin is missing, use the default.
    try {
      return parent::createInstance($plugin_id, $configuration);
    }
    catch (PluginException $e) {
      return parent::createInstance('payment_unavailable', $configuration);
    }
  }

  /**
   * {@inheritdoc}
   */
  function options() {
    $options = array();
    $definitions = $this->getDefinitions();
    unset($definitions['payment_unavailable']);
    foreach ($definitions as $plugin_id => $definition) {
      $options[$plugin_id] = $definition['label'];
    }
    natcasesort($options);

    return $options;
  }
}
