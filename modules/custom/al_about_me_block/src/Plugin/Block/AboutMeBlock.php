<?php
/**
 * @file
 * Contains \Drupal\al_about_me_block\Plugin\Block\AboutMeBlock.
 */
namespace Drupal\al_about_me_block\Plugin\Block;
use Drupal\Core\Block\BlockBase;
/**
 * Provides my custom block.
 *
 * @Block(
 *   id = "al_about_me_block",
 *   admin_label = @Translation("About Me"),
 *   category = @Translation("Blocks")
 * )
 */
class AboutMeBlock extends BlockBase {
    
/**
 * {@inheritdoc}
 */
  public function build() {
    return array('#markup' => 'Software delivery professional, certified scrum master');
  }
}
