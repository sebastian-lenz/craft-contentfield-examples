<?php

namespace examples\models;

use Craft;
use craft\elements\Entry;
use yii\base\Model;

/**
 * Class SearchModel
 */
class SearchModel extends Model
{
  /**
   * @var string
   */
  public $query = '';


  /**
   * SearchModel constructor.
   *
   * @param array $config
   */
  public function __construct($config = []) {
    parent::__construct($config);
    $this->query = Craft::$app->getRequest()->getParam('query', '');
  }

  /**
   * @return array
   */
  public function getResults() {
    return Entry::findAll([
      'search'  => $this->query,
      'orderBy' => 'title',
    ]);
  }
}
