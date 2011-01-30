<?php
class MemberConfigNotificationForm extends MemberConfigForm
{
  protected $category = 'notification';

  public function setMemberConfigWidget($name)
  {
    $result = parent::setMemberConfigWidget($name);

    return $result;
  }

  public function validate($validator,$value)
  {
    return $value;
  }
}

