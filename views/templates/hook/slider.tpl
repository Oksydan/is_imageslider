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

{if $homeslider.slides}
  {$sliderConfig = [
    "loop" => 1,
    "preloadImages" => false,
    "lazy" => true,
    "autoplay" => [
      "delay" => $homeslider.speed
    ]
  ]}

  <div class="homeslider swiper-container" {if $homeslider.slides|count > 1} data-swiper='{$sliderConfig|json_encode}'{/if}>
    <ul class="swiper-wrapper homeslider__list">
      {foreach from=$homeslider.slides item=slide}
        <li class="swiper-slide homeslider__slide">
          <a href="{$slide.url}">
            {if $slide@first}
              {if $slide.image_url && $slide.image_mobile_url}
                <img
                  class="img-fluid d-block d-lg-none"
                  src="{$slide.image_mobile_url}"
                  alt="{$slide.title}"
                  {$slide.size_mobile nofilter}>
                <img
                  class="img-fluid d-none d-lg-block lazyload"
                  src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='{$slide.sizes[0]}' height='{$slide.sizes[1]}' viewBox='0 0 1 1'%3E%3C/svg%3E"
                  data-src="{$slide.image_url}"
                  alt="{$slide.title}"
                  {$slide.size nofilter}>
              {/if}
            {else}
              {if $slide.image_url && $slide.image_mobile_url}
                <picture>
                    <source
                      data-srcset="{$slide.image_url}"
                      media="(min-width: 768px)">
                  <img
                    class="img-fluid homeslider__img swiper-lazy"
                    src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' {$slide.size|replace:'"':"'"} viewBox='0 0 1 1'%3E%3C/svg%3E"
                    data-src="{$slide.image_mobile_url}"
                    alt="{$slide.title}"
                    loading="lazy"
                    {$slide.size nofilter}
                    >
                </picture>
              {/if}
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
