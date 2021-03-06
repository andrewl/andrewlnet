<?php

/**
 * @file
 * Watermark effect test case script.
 */

namespace Drupal\image_effects\Tests;

use Drupal\image\Entity\ImageStyle;

/**
 * Watermark effect test.
 *
 * @group Image Effects
 */
class ImageEffectsWatermarkTest extends ImageEffectsTestBase {

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->toolkits = ['gd', 'imagemagick'];
  }

  /**
   * Watermark effect test.
   */
  public function testColorShiftEffect() {
    // Test operations on toolkits.
    $this->executeTestOnToolkits([$this, 'doTestWatermarkOperations']);
  }

  /**
   * Watermark operations test.
   */
  public function doTestWatermarkOperations() {
    $image_factory = $this->container->get('image.factory');

    $test_file = drupal_get_path('module', 'simpletest') . '/files/image-1.png';
    $original_uri = file_unmanaged_copy($test_file, 'public://', FILE_EXISTS_RENAME);
    $generated_uri = 'public://styles/image_effects_test/public/' . \Drupal::service('file_system')->basename($original_uri);

    $watermark_file = drupal_get_path('module', 'simpletest') . '/files/image-test.png';
    $watermark_uri = file_unmanaged_copy($watermark_file, 'public://', FILE_EXISTS_RENAME);

    $effect = [
      'id' => 'image_effects_watermark',
      'data' => [
        'placement' => 'left-top',
        'x_offset' => 1,
        'y_offset' => 1,
        'opacity' => 100,
        'watermark_image' => $watermark_uri
      ],
    ];
    $uuid = $this->addEffectToTestStyle($effect);

    // Load Image Style.
    $image_style = ImageStyle::load('image_effects_test');

    // Check that ::applyEffect generates image with expected watermark.
    $image_style->createDerivative($original_uri, $image_style->buildUri($original_uri));
    $image = $image_factory->get($generated_uri, 'gd');
    $watermark = $image_factory->get($watermark_uri, 'gd');
    $this->assertFalse($this->colorsAreEqual($this->getPixelColor($watermark, 0, 0), $this->getPixelColor($image, 0, 0)));
    $this->assertTrue($this->colorsAreEqual($this->getPixelColor($watermark, 0, 0), $this->getPixelColor($image, 1, 1)));
    $this->assertTrue($this->colorsAreEqual($this->getPixelColor($watermark, 0, 1), $this->getPixelColor($image, 1, 2)));
    $this->assertTrue($this->colorsAreEqual($this->getPixelColor($watermark, 0, 3), $this->getPixelColor($image, 1, 4)));

    // Remove effect.
    $this->removeEffectFromTestStyle($uuid);

    // Test for watermark PNG image with full transparency set, 100% opacity
    // watermark.
    $test_file = drupal_get_path('module', 'image_effects') . '/tests/images/fuchsia.png';
    $original_uri = file_unmanaged_copy($test_file, 'public://', FILE_EXISTS_RENAME);
    $generated_uri = 'public://styles/image_effects_test/public/' . \Drupal::service('file_system')->basename($original_uri);
    $watermark_file = drupal_get_path('module', 'simpletest') . '/files/image-test.png';
    $watermark_uri = file_unmanaged_copy($watermark_file, 'public://', FILE_EXISTS_RENAME);

    $effect = [
      'id' => 'image_effects_watermark',
      'data' => [
        'placement' => 'left-top',
        'x_offset' => 0,
        'y_offset' => 0,
        'opacity' => 100,
        'watermark_image' => $watermark_uri
      ],
    ];
    $uuid = $this->addEffectToTestStyle($effect);

    // Load Image Style.
    $image_style = ImageStyle::load('image_effects_test');

    // Check that ::applyEffect generates image with expected transparency.
    $image_style->createDerivative($original_uri, $image_style->buildUri($original_uri));
    $image = $image_factory->get($generated_uri, 'gd');
    $this->assertTrue($this->colorsAreEqual($this->getPixelColor($image, 0, 19), $this->fuchsia));

    // Remove effect.
    $this->removeEffectFromTestStyle($uuid);

    // Test for watermark PNG image with full transparency set, 50% opacity
    // watermark.
    $test_file = drupal_get_path('module', 'image_effects') . '/tests/images/fuchsia.png';
    $original_uri = file_unmanaged_copy($test_file, 'public://', FILE_EXISTS_RENAME);
    $generated_uri = 'public://styles/image_effects_test/public/' . \Drupal::service('file_system')->basename($original_uri);
    $watermark_file = drupal_get_path('module', 'simpletest') . '/files/image-test.png';
    $watermark_uri = file_unmanaged_copy($watermark_file, 'public://', FILE_EXISTS_RENAME);

    $effect = [
      'id' => 'image_effects_watermark',
      'data' => [
        'placement' => 'left-top',
        'x_offset' => 0,
        'y_offset' => 0,
        'opacity' => 50,
        'watermark_image' => $watermark_uri
      ],
    ];
    $uuid = $this->addEffectToTestStyle($effect);

    // Load Image Style.
    $image_style = ImageStyle::load('image_effects_test');

    // Check that ::applyEffect generates image with expected alpha.
    $image_style->createDerivative($original_uri, $image_style->buildUri($original_uri));
    $image = $image_factory->get($generated_uri, 'gd');
    $this->assertTrue($this->colorsAreEqual($this->getPixelColor($image, 0, 19), $this->fuchsia));
    // GD and ImageMagick return slightly different colors, use the
    // ::colorsAreClose method.
    $this->assertTrue($this->colorsAreClose($this->getPixelColor($image, 39, 0), [127, 127, 127, 0]));

    // Remove effect.
    $this->removeEffectFromTestStyle($uuid);
  }

}
