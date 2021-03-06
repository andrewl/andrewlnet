<?php

/**
 * @file
 * Contains \Drupal\image_effects\Plugin\ImageEffectsPluginManager.
 */

namespace Drupal\image_effects\Plugin;

use Drupal\Component\Utility\SafeMarkup;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Language\LanguageManager;
use Drupal\Core\Plugin\DefaultPluginManager;
use Symfony\Component\DependencyInjection\Container;

/**
 * Plugin manager for image_effects plugins.
 */
class ImageEffectsPluginManager extends DefaultPluginManager {

  /**
   * The configuration object.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * {@inheritdoc}
   *
   * @param string $type
   *   The plugin type, for example 'color_selector'.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler, $type, ConfigFactoryInterface $config_factory) {
    $path = Container::camelize($type);
    $this->config = $config_factory->get('image_effects.settings');
    parent::__construct("Plugin/image_effects/{$path}", $namespaces, $module_handler);
    $this->alterInfo("image_effects_{$type}_plugin_info");
    $this->setCacheBackend($cache_backend, "image_effects_{$type}_plugins");
    $this->defaults += [
      'plugin_type' => $type,
    ];
  }

  public function getType() {
    return $this->defaults['plugin_type'];
  }

  public function getPlugin($plugin_id = NULL) {
    $plugin_id = $plugin_id ?: $this->config->get($this->getType() . '.plugin_id');
    $plugins = $this->getAvailablePlugins();

    // Check if plugin is available.
    if (!isset($plugins[$plugin_id]) || !class_exists($plugins[$plugin_id]['class'])) {
      trigger_error("image_effects " . $this->getType() . " handling plugin '$plugin_id' is no longer available.", E_USER_ERROR);
      $plugin_id = NULL;
    }

    return $this->createInstance($plugin_id, array('plugin_type' => $this->getType()));
  }

  /**
   * Gets a list of available plugins.
   *
   * @return array
   *   An array with the plugin ids as keys and the definitions as values.
   */
  public function getAvailablePlugins() {
    $plugins = $this->getDefinitions();
    $output = array();
    foreach ($plugins as $id => $definition) {
      // Only allow plugins that are available.
      if (call_user_func($definition['class'] . '::isAvailable')) {
        $output[$id] = $definition;
      }
    }
    return $output;
  }

  /**
   * Gets a formatted list of available plugins.
   *
   * @return array
   *   An array with the plugin ids as keys and the descriptions as values.
   */
  public function getPluginOptions() {
    $options = array();
    foreach ($this->getAvailablePlugins() as $plugin) {
      $options[$plugin['id']] = SafeMarkup::format('<b>@title</b> - @description', ['@title' => $plugin['short_title'], '@description' => $plugin['help']]);
    }
    return $options;
  }

}
