<?php

namespace examples;

use Craft;
use craft\console\controllers\MigrateController;
use craft\services\ProjectConfig;
use lenz\craft\utils\elementCache\ElementCache;

/**
 * Class Module
 */
class Module extends \yii\base\Module
{
  /**
   * Initializes the module.
   */
  public function init() {
    Craft::setAlias('@examples', __DIR__);

    if (Craft::$app->getRequest()->getIsConsoleRequest()) {
      $this->controllerNamespace = 'examples\\console\\controllers';
    } else {
      $this->controllerNamespace = 'examples\\controllers';
    }

    Craft::$app->view->registerTwigExtension(new twig\Extension());

    $this->setComponents([
      'cache' => ElementCache::getInstance(),

    ]);

    parent::init();
  }
}
