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
    private ImageSlider $imageSlide;

    /**
     * @var Lang
     *
     * @ORM\Id
     *
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\Lang")
     *
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
     * @ORM\OneToOne(targetEntity="Oksydan\IsImageslider\Entity\ImageSliderImage", cascade={"persist", "remove"}, mappedBy="imageSlideLang", fetch="EAGER")
     *
     * @ORM\JoinColumn(name="image", referencedColumnName="id_image", nullable=true)
     */
    private ImageSliderImage $image;

    /**
     * @ORM\OneToOne(targetEntity="Oksydan\IsImageslider\Entity\ImageSliderImage", cascade={"persist", "remove"}, mappedBy="imageSlideLang", fetch="EAGER")
     *
     * @ORM\JoinColumn(name="image_mobile", referencedColumnName="id_image", nullable=true)
     */
    private ImageSliderImage $imageMobile;

    public function getImageSlider(): ImageSlider
    {
        return $this->imageSlide;
    }

    public function setImageSlider(ImageSlider $imageSlide): void
    {
        $this->imageSlide = $imageSlide;
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

    public function getImage(): ImageSliderImage
    {
        return $this->image;
    }

    public function setImage(ImageSliderImage $image): void
    {
        $this->image = $image;
    }

    public function getImageMobile(): ImageSliderImage
    {
        return $this->imageMobile;
    }

    public function setImageMobile(ImageSliderImage $imageMobile): void
    {
        $this->imageMobile = $imageMobile;
    }

    public function getLang(): Lang
    {
        return $this->lang;
    }

    public function setLang(Lang $lang): void
    {
        $this->lang = $lang;
    }
}
