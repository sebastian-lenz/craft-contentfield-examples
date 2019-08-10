<?php

namespace examples;

use Craft;
use craft\controllers\InstallController;
use craft\console\controllers\InstallController as ConsoleInstallController;
use craft\errors\MigrationException;
use lenz\craft\utils\elementCache\ElementCache;
use yii\base\ActionEvent;
use yii\base\Application;
use yii\base\Event;

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

    Event::on(Application::class, Application::EVENT_AFTER_ACTION, [$this, 'onAfterAction']);

    parent::init();
  }

  /**
   * @param ActionEvent $event
   * @throws MigrationException
   */
  public function onAfterAction(ActionEvent $event) {
    $controller = $event->action->controller;

    if (
      $controller instanceof ConsoleInstallController &&
      $event->action->id == 'craft'
    ) {

      $controller->stdout('*** installing example data' . PHP_EOL);
      $migrator = Craft::$app->getContentMigrator();
      $migrator->up();
      $controller->stdout('done' . PHP_EOL);
    } elseif (
      $controller instanceof InstallController &&
      $event->action->id == 'install'
    ) {
      $migrator = Craft::$app->getContentMigrator();
      $migrator->up();
    }
  }
}
