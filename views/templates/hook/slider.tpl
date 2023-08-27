{*
 * 2007-2020 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

{if !empty($homeslider.slides)}
  {$sliderConfig = [
    "loop" => 1,
    "autoplay" => [
      "delay" => $homeslider.speed
    ]
  ]}

  <div class="homeslider swiper bg-light" {if $homeslider.slides|count > 1} data-swiper='{$sliderConfig|json_encode}'{/if}>
    <ul class="swiper-wrapper homeslider__list">
      {images_block}
        {foreach from=$homeslider.slides item=slide}
          <li class="swiper-slide homeslider__slide">
            <a href="{$slide.url}">
              {if $slide@first}
                <img
                  class="img-fluid"
                  src="{$slide.image_url}"
                  alt="{$slide.title}"
                  {if !empty($slide.sizes)}
                    width="{$slide.sizes.0}"
                    height="{$slide.sizes.1}"
                  {/if}
                  >
              {else}
                <img
                  class="img-fluid homeslider__img"
                  src="{$slide.image_url}"
                  alt="{$slide.title}"
                  {if !empty($slide.sizes)}
                    width="{$slide.sizes.0}"
                    height="{$slide.sizes.1}"
                  {/if}
                  loading="lazy"
                  >
              {/if}

              {if $slide.title || $slide.description }
                <span class="homeslider__caption">
                  <h2 class="homeslider__title">{$slide.title}</h2>
                  <div class="homeslider__desc">{$slide.description nofilter}</div>
                </span>
              {/if}
            </a>
          </li>
        {/foreach}
      {/images_block}
    </ul>
    {if $homeslider.slides|count > 1}
      <div class="swiper-button-prev swiper-button-custom homeslider__arrow homeslider__arrow--prev">
        <i class="material-icons">&#xE314;</i>
      </div>
      <div class="swiper-button-next swiper-button-custom homeslider__arrow homeslider__arrow--next">
        <i class="material-icons">&#xE315;</i>
      </div>
    {/if}
  </div>
{/if}
