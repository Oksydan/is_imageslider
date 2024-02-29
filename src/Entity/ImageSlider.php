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
    private int $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private bool $active;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private int $position;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="display_from", type="datetime", nullable=true)
     */
    private \DateTime $display_from;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="display_to", type="datetime", nullable=true)
     */
    private \DateTime $display_to;

    /**
     * @var bool
     *
     * @ORM\Column(name="image_to_all_langs", type="boolean")
     */
    private bool $image_to_all_langs;

    /**
     * @ORM\OneToMany(targetEntity="Oksydan\IsImageslider\Entity\ImageSliderLang", mappedBy="imageSlider", cascade={"persist", "remove"})
     */
    private Collection $sliderLang;

    /**
     * @ORM\ManyToMany(targetEntity="PrestaShopBundle\Entity\Shop", cascade={"persist"})
     *
     * @ORM\JoinTable(
     *      joinColumns={@ORM\JoinColumn(name="id_slide", referencedColumnName="id_slide")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_shop", referencedColumnName="id_shop", onDelete="CASCADE")}
     * )
     */
    private Collection $shops;

    public function __construct()
    {
        $this->shops = new ArrayCollection();
        $this->sliderLang = new ArrayCollection();
        $this->active = false;
        $this->image_to_all_langs = false;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getDisplayFrom(): \DateTime
    {
        return $this->display_from;
    }

    public function setDisplayFrom(\DateTime $display_from): void
    {
        $this->display_from = $display_from;
    }

    public function getDisplayTo(): \DateTime
    {
        return $this->display_to;
    }

    public function setDisplayTo(\DateTime $display_to): void
    {
        $this->display_to = $display_to;
    }

    public function getImagesForAllLangs(): bool
    {
        return $this->image_to_all_langs;
    }

    public function setImagesForAllLangs(bool $image_to_all_langs): void
    {
        $this->image_to_all_langs = $image_to_all_langs;
    }

    public function addShopAssociation(Shop $shop): void
    {
        $this->shops[] = $shop;
    }

    public function removeShopAssociation(Shop $shop): void
    {
        $this->shops->removeElement($shop);
    }

    /**
     * @return Collection
     */
    public function getShopAssociation(): Collection
    {
        return $this->shops;
    }

    public function getSliderLangs(): Collection
    {
        return $this->sliderLang;
    }

    public function addSliderLang(ImageSliderLang $sliderLang): void
    {
        $sliderLang->setImageSlider($this);
        $this->sliderLang->add($sliderLang);
    }

    public function removeSliderLang(ImageSliderLang $sliderLang): void
    {
        $this->sliderLang->removeElement($sliderLang);
    }
}
