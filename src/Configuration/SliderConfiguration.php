<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Configuration;

class SliderConfiguration
{
    public const HOMESLIDER_SPEED = 'HOMESLIDER_SPEED';
    public const HOMESLIDER_PAUSE_ON_HOVER = 'HOMESLIDER_PAUSE_ON_HOVER';
    public const HOMESLIDER_WRAP = 'HOMESLIDER_WRAP';

    public function getSliderSpeed()
    {
        return \Configuration::get(SliderConfiguration::HOMESLIDER_SPEED);
    }

    public function getSliderPauseOnHover()
    {
        return \Configuration::get(SliderConfiguration::HOMESLIDER_PAUSE_ON_HOVER);
    }

    public function getSliderWrap()
    {
        return \Configuration::get(SliderConfiguration::HOMESLIDER_WRAP);
    }
}
