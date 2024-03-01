<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Entity;

use Doctrine\ORM\Mapping as ORM;
use PrestaShopBundle\Entity\Lang;

/**
 * @ORM\Table()
 * @ORM\Entity
 */
class ImageSliderLang
{
    /**
     * @var ImageSlider
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Oksydan\IsImageslider\Entity\ImageSlider", inversedBy="sliderLang")
     * @ORM\JoinColumn(name="id_slide", referencedColumnName="id_slide", nullable=false)
     */
    private ImageSlider $imageSlider;

    /**
     * @var Lang
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\Lang")
     * @ORM\JoinColumn(name="id_lang", referencedColumnName="id_lang", nullable=false, onDelete="CASCADE")
     */
    private Lang $lang;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text")
     */
    private string $title;

    /**
     * @var string
     *
     * @ORM\Column(name="legend", type="text")
     */
    private string $legend;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="text")
     */
    private string $url;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private string $description;

    /**
     * @ORM\ManyToOne(targetEntity="Oksydan\IsImageslider\Entity\ImageSliderImage", inversedBy="imageSliderLangs", cascade={"persist", "remove"}, fetch="EAGER")
     * @ORM\JoinColumn(name="image", referencedColumnName="id_image", nullable=true)
     */
    private ?ImageSliderImage $image = null;

    /**
     * @ORM\ManyToOne(targetEntity="Oksydan\IsImageslider\Entity\ImageSliderImage", inversedBy="imageMobileSliderLangs", cascade={"persist", "remove"}, fetch="EAGER")
     * @ORM\JoinColumn(name="image_mobile", referencedColumnName="id_image", nullable=true)
     */
    private ?ImageSliderImage $imageMobile = null;

    public function getImageSlider(): ImageSlider
    {
        return $this->imageSlider;
    }

    public function setImageSlider(ImageSlider $imageSlider): void
    {
        $this->imageSlider = $imageSlider;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getLegend(): string
    {
        return $this->legend;
    }

    public function setLegend(?string $legend): void
    {
        $this->legend = $legend;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getLang(): Lang
    {
        return $this->lang;
    }

    public function setLang(Lang $lang): void
    {
        $this->lang = $lang;
    }

    public function getImage(): ?ImageSliderImage
    {
        return $this->image;
    }

    public function setImage(?ImageSliderImage $image): void
    {
        $this->image = $image;
    }

    public function getImageMobile(): ?ImageSliderImage
    {
        return $this->imageMobile;
    }

    public function setImageMobile(?ImageSliderImage $imageMobile): void
    {
        $this->imageMobile = $imageMobile;
    }
}
