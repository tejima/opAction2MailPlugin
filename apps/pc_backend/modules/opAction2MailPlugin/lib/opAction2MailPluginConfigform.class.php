<?php
class opAction2MailPluginConfigForm extends sfForm
{
  protected $configs = array(
    'notify_from' => 'ZUNIV_US_NOTIFYFROM',
  );
  public function configure()
  {
    $this->setWidgets(array(
      'notify_from' => new sfWidgetFormInput(),
    ));
    $this->setValidators(array(
      'notify_from' => new sfValidatorString(array(),array()),
    ));

    $this->widgetSchema->setHelp('notify_from', '通知メールのFROMアドレス（空欄の場合は管理者メールアドレスを使う）');

    foreach($this->configs as $k => $v)
    {
      $config = Doctrine::getTable('SnsConfig')->retrieveByName($v);
      if($config)
      {
        $this->getWidgetSchema()->setDefault($k,$config->getValue());
      }
    }
    $this->getWidgetSchema()->setNameFormat('action2mail[%s]');
  }
  public function save()
  {
    foreach($this->getValues() as $k => $v)
    {
      if(!isset($this->configs[$k]))
      {
        continue;
      }
      $config = Doctrine::getTable('SnsConfig')->retrieveByName($this->configs[$k]);
      if(!$config)
      {
        $config = new SnsConfig();
        $config->setName($this->configs[$k]);
      }
      $config->setValue($v);
      $config->save();
    }
  }
  public function validate($validator,$value,$arguments = array())
  {
    return $value;
  }
}


