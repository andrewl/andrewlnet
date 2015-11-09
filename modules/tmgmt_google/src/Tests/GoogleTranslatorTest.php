<?php

/**
 * @file
 * Test cases for the google translator module.
 */

namespace Drupal\tmgmt_google\Tests;

use Drupal\tmgmt\Tests\TMGMTTestBase;
use Drupal\tmgmt_google\Plugin\tmgmt\Translator\GoogleTranslator;
use Drupal\tmgmt\TranslatorInterface;
use Drupal\Core\Url;

/**
 * Basic tests for the google translator.
 *
 * @group tmgmt_google
 */
class GoogleTranslatorTest extends TMGMTTestBase {

  /**
   * A tmgmt_translator with a server mock.
   *
   * @var TranslatorInterface
   */
  protected $translator;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('tmgmt_google', 'tmgmt_google_test');

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->addLanguage('de');
    // Override plugin params to query tmgmt_google_test mock service instead
    // of Google Translate service.
    $url = Url::fromUri('base://tmgmt_google_test', array('absolute' => TRUE))->toString();
    $this->translator = $this->createTranslator([
      'plugin' => 'google',
      'settings' => ['url' => $url],
    ]);
  }

  /**
   * Tests basic API methods of the plugin.
   */
  protected function testGoogle() {
    $plugin = $this->translator->getPlugin();
    $this->assertTrue($plugin instanceof GoogleTranslator, 'Plugin is a GoogleTranslator');

    $job = $this->createJob();
    $job->translator = $this->translator->id();
    $item = $job->addItem('test_source', 'test', '1');
    $item->data = array(
      'wrapper' => array(
        '#text' => 'Hello world',
      ),
    );
    $item->save();

    $this->assertFalse($job->isTranslatable(), 'Check if the translator is not available at this point because we did not define the API parameters.');

    // Save a wrong api key.
    $this->translator->setSetting('api_key', 'wrong key');
    $this->translator->save();

    $languages = $this->translator->getSupportedTargetLanguages('en');
    $this->assertTrue(empty($languages), t('We can not get the languages using wrong api parameters.'));

    // Save a correct api key.
    $this->translator->setSetting('api_key', 'correct key');
    $this->translator->save();

    // Make sure the translator returns the correct supported target languages.
    $this->translator->clearLanguageCache();
    $languages = $this->translator->getSupportedTargetLanguages('en');

    $this->assertTrue(isset($languages['de']));
    $this->assertTrue(isset($languages['fr']));
    // As we requested source language english it should not be included.
    $this->assertTrue(!isset($languages['en']));

    $this->assertTrue($job->canRequestTranslation()->getSuccess());

    $job->requestTranslation();

    // Now it should be needs review.
    foreach ($job->getItems() as $item) {
      $this->assertTrue($item->isNeedsReview());
    }
    $items = $job->getItems();
    $item = end($items);
    $data = $item->getData();
    $this->assertEqual('Hallo Welt', $data['dummy']['deep_nesting']['#translation']['#text']);
  }

  /**
   * Tests the UI of the plugin.
   */
  protected function testMicrosoftUi() {
    $this->loginAsAdmin();
    $edit = [
      'settings[api_key]' => 'wrong key',
    ];
    $this->drupalPostForm('admin/config/regional/tmgmt_translator/manage/' . $this->translator->id(), $edit, t('Save'));
    $this->assertText(t('The "Google API key" is not correct.'));
    $edit = [
      'settings[api_key]' => 'correct key',
    ];
    $this->drupalPostForm('admin/config/regional/tmgmt_translator/manage/' . $this->translator->id(), $edit, t('Save'));
    $this->assertText(t('@label configuration has been updated.', ['@label' => $this->translator->label()]));
  }

}
