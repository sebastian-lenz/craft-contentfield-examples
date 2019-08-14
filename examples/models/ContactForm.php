<?php

namespace examples\models;

use lenz\contentfield\events\BeforeActionEvent;
use lenz\contentfield\models\forms\AbstractForm;

/**
 * Class ContactForm
 */
class ContactForm extends AbstractForm
{
  /**
   * @var string
   */
  public $name = '';

  /**
   * @var string
   */
  public $mail = '';

  /**
   * @var string
   */
  public $message = '';

  /**
   * The name of the namespace used by the template to send the form data,
   */
  const PARAM_NAME = 'contactForm';


  /**
   * Returns the validation rules for attributes.
   *
   * Validation rules are used by [[validate()]] to check if attribute values are valid.
   * Child classes may override this method to declare different validation rules.
   *
   * @return array
   */
  public function rules() {
    return [
      [['name', 'mail', 'message'], 'string'],
      [['name', 'mail', 'message'], 'required'],
      ['mail', 'email'],
    ];
  }

  /**
   * Callback invoked when this form is being submitted.
   *
   * This is only invoked if
   * - The current request is a post request.
   * - The form data is present in the submitted post data.
   * - The posted data could be validated.
   *
   * @param BeforeActionEvent $event
   * @return bool
   */
  protected function submit(BeforeActionEvent $event) {
    // TODO: Add submit handler
    return true;
  }
}
