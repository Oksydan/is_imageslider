<?php

declare(strict_types=1);

namespace Oksydan\IsImageslider\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
class ImageSliderImage
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id_image", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\OneToMany(targetEntity="Oksydan\IsImageslider\Entity\ImageSliderLang", mappedBy="image", fetch="EAGER", cascade={"persist", "remove"})
     */
    private Collection $imageSliderLangs;

    /**
     * @ORM\OneToMany(targetEntity="Oksydan\IsImageslider\Entity\ImageSliderLang", mappedBy="imageMobile", fetch="EAGER", cascade={"persist", "remove"})
     */
    private Collection $imageMobileSliderLangs;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text")
     */
    private string $name;

    public function __construct()
    {
        $this->imageSliderLangs = new ArrayCollection();
        $this->imageMobileSliderLangs = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name ?? '';
    }


    /**
     * @return Collection|ImageSliderLang[]
     */
    public function getImageSliderLangs(): Collection
    {
        return $this->imageSliderLangs;
    }

    public function addImageSliderLang(ImageSliderLang $imageSliderLang): self
    {
        if (!$this->imageSliderLangs->contains($imageSliderLang)) {
            $this->imageSliderLangs[] = $imageSliderLang;
            $imageSliderLang->setImage($this);
        }

        return $this;
    }

    public function removeImageSliderLang(ImageSliderLang $imageSliderLang): self
    {
        if ($this->imageSliderLangs->removeElement($imageSliderLang)) {
            // set the owning side to null (unless already changed)
            if ($imageSliderLang->getImage() === $this) {
                $imageSliderLang->setImage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ImageSliderLang[]
     */
    public function getImageMobileSliderLangs(): Collection
    {
        return $this->imageMobileSliderLangs;
    }

    public function addImageMobileSliderLang(ImageSliderLang $imageMobileSliderLang): self
    {
        if (!$this->imageMobileSliderLangs->contains($imageMobileSliderLang)) {
            $this->imageMobileSliderLangs[] = $imageMobileSliderLang;
            $imageMobileSliderLang->setImageMobile($this);
        }

        return $this;
    }

    public function removeImageMobileSliderLang(ImageSliderLang $imageMobileSliderLang): self
    {
        if ($this->imageMobileSliderLangs->removeElement($imageMobileSliderLang)) {
            // set the owning side to null (unless already changed)
            if ($imageMobileSliderLang->getImageMobile() === $this) {
                $imageMobileSliderLang->setImageMobile(null);
            }
        }

        return $this;
    }
}
