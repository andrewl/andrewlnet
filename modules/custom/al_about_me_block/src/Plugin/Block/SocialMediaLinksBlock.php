<?php
/**
 * @file
 * Contains \Drupal\al_about_me_block\Plugin\Block\SocialMediaLinksBlock.
 */

namespace Drupal\al_about_me_block\Plugin\Block;
use Drupal\Core\Block\BlockBase;
/**
 * Provides my custom block.
 *
 * @Block(
 *   id = "al_social_media_links_block",
 *   admin_label = @Translation("Social Media Links"),
 *   category = @Translation("Blocks")
 * )
 */
class SocialMediaLinksBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return array(
      '#markup' => '<a href="http://twitter.com/andrewl"><span class="socicon">a</span> @andrewl</a>',
    );
  }

}
