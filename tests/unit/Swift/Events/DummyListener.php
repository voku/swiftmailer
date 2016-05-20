<?php

class Swift_Events_DummyListener implements Swift_Events_EventListener
{
  public function sendPerformed(Swift_Events_SendEvent $evt)
  {
  }
}
