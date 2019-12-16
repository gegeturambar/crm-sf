<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CustomerRepository")
 * @ApiResource(
 *     attributes={
 *     "pagination_items_per_page"=4
 *     },
 *     normalizationContext={
 *      "groups"={"customers_read"}
 *     },
 *     collectionOperations={"GET"={"path"="/customers"},"POST"},
 *     itemOperations={"GET","PUT","DELETE"},
 *     subresourceOperations={"invoices_get_subresource"={"path"="/customers/{id}/invoices"}}
 * )
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={"firstName","lastName","company"}
 * )
 */
class Customer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"customers_read","invoices_read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"customers_read","invoices_read"})
     * @Assert\NotBlank(
     *     message="Vous devez saisir votre nom"
     * )
     * @Assert\Length(min=3,minMessage="Le prénom doit faire entre 3 et 255 caractères", max=255,maxMessage="Le prénom doit faire entre 3 et 255 caractères")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"customers_read","invoices_read"})
     * @Assert\NotBlank(
     *     message="Vous devez saisir votre prenom"
     * )
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"customers_read","invoices_read"})
     * @Assert\NotBlank(
     *     message="Vous devez saisir votre compagnie"
     * )
     */
    private $company;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"customers_read","invoices_read"})
     * @Assert\NotBlank(
     *     message="Vous devez saisir votre email"
     * )
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Invoice", mappedBy="customer")
     * @Groups({"customers_read"})
     * @ApiSubresource()
     */
    private $invoices;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="customers")
     * @Groups({"customers_read"})
     */
    private $user;

    public function __construct()
    {
        $this->invoices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param mixed $company
     * @return Customer
     */
    public function setCompany($company)
    {
        $this->company = $company;
        return $this;
    }


    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection|Invoice[]
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): self
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices[] = $invoice;
            $invoice->setCustomer($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): self
    {
        if ($this->invoices->contains($invoice)) {
            $this->invoices->removeElement($invoice);
            // set the owning side to null (unless already changed)
            if ($invoice->getCustomer() === $this) {
                $invoice->setCustomer(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * retourne le total des invoices
     * @return float
     * @Groups({"customers_read"})
     */
    public function getTotalAmount(): float
    {
        return array_reduce($this->invoices->toArray(),function ($total,Invoice $invoice){
           return $total + $invoice->getAmount();
        },0);
    }

    /**
     * retourne le total des invoices non payés
     * @return float
     * @Groups({"customers_read"})
     */
    public function getUnpaidAmount(): float
    {
        return array_reduce($this->invoices->toArray(),function ($total,Invoice $invoice){
            return $total +  in_array($invoice->getStatus() ,array('PAID','CANCELLED')) ? 0 : $invoice->getAmount();
        },0);
    }
}
