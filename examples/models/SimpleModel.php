<?php

namespace examples\models;

use Craft;
use craft\helpers\Json;
use Exception;
use yii\base\Model;

/**
 * Class SimpleModel
 */
class SimpleModel extends Model
{
  /**
   * @var string
   */
  public $myConstProperty = 'Hello world!';

  /**
   * The sample api endpoint used by SimpleModel::fetch
   */
  const API_ENDPOINT = 'https://api.nasa.gov/planetary/apod?api_key=NNKOjkoul8n1CH18TWA9gwngW1s1SmjESPjNoUFo';


  /**
   * @return array
   */
  public function fetch() {
    try {
      return Craft::$app->cache->getOrSet(__METHOD__, function() {
        return Json::decode(
          file_get_contents(self::API_ENDPOINT)
        );
      }, 3600);
    } catch (Exception $error) {
      return [
        'url' => '<Unavailable>'
      ];
    }
  }
}
