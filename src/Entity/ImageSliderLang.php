<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Entity;

use Doctrine\ORM\Mapping as ORM;
use PrestaShopBundle\Entity\Lang;

/**
 * @ORM\Table()
 *
 * @ORM\Entity
 */
class ImageSliderLang
{
    /**
     * @var ImageSlider
     *
     * @ORM\Id
     *
     * @ORM\ManyToOne(targetEntity="Oksydan\IsImageslider\Entity\ImageSlider", inversedBy="imageSlideLang")
     *
     * @ORM\JoinColumn(name="id_slide", referencedColumnName="id_slide", nullable=false)
     */
    private $imageSlide;

    /**
     * @var Lang
     *
     * @ORM\Id
     *
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\Lang")
     *
     * @ORM\JoinColumn(name="id_lang", referencedColumnName="id_lang", nullable=false, onDelete="CASCADE")
     */
    private $lang;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="legend", type="text")
     */
    private $legend;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="text")
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="text")
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="image_mobile", type="text")
     */
    private $imageMobile;

    /**
     * @return ImageSlider
     */
    public function getImageSlider(): ImageSlider
    {
        return $this->imageSlide;
    }

    /**
     * @param ImageSlider $imageSlide
     *
     * @return ImageSliderLang $this
     */
    public function setImageSlider(ImageSlider $imageSlide): ImageSliderLang
    {
        $this->imageSlide = $imageSlide;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return ImageSliderLang $this
     */
    public function setTitle(string $title): ImageSliderLang
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getLegend(): string
    {
        return $this->legend;
    }

    /**
     * @param string $legend
     *
     * @return ImageSliderLang $this
     */
    public function setLegend(string $legend): ImageSliderLang
    {
        $this->legend = $legend;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return ImageSliderLang $this
     */
    public function setUrl(string $url): ImageSliderLang
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return ImageSliderLang $this
     */
    public function setDescription(string $description): ImageSliderLang
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param string $image
     *
     * @return ImageSliderLang $this
     */
    public function setImage(string $image): ImageSliderLang
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getImageMobile(): ?string
    {
        return $this->imageMobile;
    }

    /**
     * @param string $imageMobile
     *
     * @return ImageSliderLang $this
     */
    public function setImageMobile(string $imageMobile): ImageSliderLang
    {
        $this->imageMobile = $imageMobile;

        return $this;
    }

    /**
     * @return Lang
     */
    public function getLang(): Lang
    {
        return $this->lang;
    }

    /**
     * @param Lang $lang
     *
     * @return ImageSliderLang $this
     */
    public function setLang(Lang $lang): ImageSliderLang
    {
        $this->lang = $lang;

        return $this;
    }
}
