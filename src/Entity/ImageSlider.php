<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use PrestaShopBundle\Entity\Shop;

/**
 * @ORM\Entity(repositoryClass="Oksydan\IsImageslider\Repository\ImageSliderRepository")
 *
 * @ORM\Table()
 */
class ImageSlider
{
    /**
     * @var int
     *
     * @ORM\Id
     *
     * @ORM\Column(name="id_slide", type="integer")
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="display_from", type="datetime", nullable=true)
     */
    private $display_from;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="display_to", type="datetime", nullable=true)
     */
    private $display_to;

    /**
     * @ORM\OneToMany(targetEntity="Oksydan\IsImageslider\Entity\ImageSliderLang", cascade={"persist", "remove"}, mappedBy="imageSlide")
     */
    private $sliderLangs;

    /**
     * @ORM\ManyToMany(targetEntity="PrestaShopBundle\Entity\Shop", cascade={"persist"})
     *
     * @ORM\JoinTable(
     *      joinColumns={@ORM\JoinColumn(name="id_slide", referencedColumnName="id_slide")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_shop", referencedColumnName="id_shop", onDelete="CASCADE")}
     * )
     */
    private $shops;

    public function __construct()
    {
        $this->shops = new ArrayCollection();
        $this->sliderLangs = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return ImageSlider $this
     */
    public function setActive(bool $active): ImageSlider
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     *
     * @return ImageSlider $this
     */
    public function setPosition(int $position): ImageSlider
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDisplayFrom(): \DateTime
    {
        return $this->display_from;
    }

    /**
     * @param \DateTime $display_from
     *
     * @return ImageSlider $this
     */
    public function setDisplayFrom(\DateTime $display_from): ImageSlider
    {
        $this->display_from = $display_from;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDisplayTo(): \DateTime
    {
        return $this->display_to;
    }

    /**
     * @param \DateTime $display_to
     *
     * @return ImageSlider $this
     */
    public function setDisplayTo(\DateTime $display_to): ImageSlider
    {
        $this->display_to = $display_to;

        return $this;
    }

    /**
     * @param Shop $shop
     *
     * @return ImageSlider $this
     */
    public function addShop(Shop $shop): ImageSlider
    {
        $this->shops[] = $shop;

        return $this;
    }

    /**
     * @param Shop $shop
     *
     * @return ImageSlider $this
     */
    public function removeShop(Shop $shop): ImageSlider
    {
        $this->shops->removeElement($shop);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getShops(): Collection
    {
        return $this->shops;
    }

    /**
     * @return ImageSlider $this
     */
    public function clearShops(): ImageSlider
    {
        $this->shops->clear();

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSliderLangs()
    {
        return $this->sliderLangs;
    }

    /**
     * @param int $langId
     *
     * @return ImageSliderLang|null
     */
    public function getImageSliderLangByLangId(int $langId)
    {
        foreach ($this->sliderLangs as $sliderLang) {
            if ($langId === $sliderLang->getLang()->getId()) {
                return $sliderLang;
            }
        }

        return null;
    }

    /**
     * @param ImageSliderLang $sliderLang
     *
     * @return ImageSlider $this
     */
    public function addImageSliderLang(ImageSliderLang $sliderLang): ImageSlider
    {
        $sliderLang->setImageSlider($this);
        $this->sliderLangs->add($sliderLang);

        return $this;
    }
}
