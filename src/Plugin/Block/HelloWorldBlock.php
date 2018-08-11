<?php

namespace Drupal\hello_world\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Provides a block with a list of article titles tagged with Sections vocab.
 * 
 * @Block(
 *    id = "hello_world_article_block",
 *    admin_label = @Translation("Hello World Block"),
 *    category = @Translation("Custom"),
 * )
 */

class HelloWorldBlock extends BlockBase {
  
  public function build() {
    // Get Hello World Articles
    $nids = \Drupal::entityQuery('node')
      ->condition('status', 1)
        ->condition('type', 'hello_world_article')
          ->execute();
    $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
    
    // Filter Hello World Articles with Sections tags.
    if (!empty($nodes)) { // Check that we have articles to look at
      $content = '<ul>';
      foreach ($nodes as $node) {
        $title = $node->getTitle();
        $nid = $node->id();
        $options = array(
          'attributes' => ['class' => ['node-link']],
          'absolute' => TRUE,
        );
        $link_info = Url::fromUri('internal:/node/' . $nid, $options);
        $link = Link::fromTextAndUrl($title, $link_info)->toString();
        $section = $node->get('field_sections')->getValue();
        // Add node to list if it has been tagged with Sections vocab
        if (!empty($section)) {
          $content .= '<li>' . $link . '</li>';
        }
      }
      $content .= '</ul>';
    } else {
      $content = '<p>No Articles</p>';
    }
    
    return array(
      '#title' => $this->t('Hello World!'),
      '#markup' => $content,
    );
  }
  
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }
  
  public function blockForm($form, FormStateInterface $form_state) {
    $config = $this->getConfiguration();
    
    return $form;
  }
  
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['hello_world_block_settings'] = 
      $form_state->getValue('hello_world_block_settings');
  }
}