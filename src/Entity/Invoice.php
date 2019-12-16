<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Symfony\Component\Serializer\Annotation\Groups;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InvoiceRepository")
 * @ApiResource(
 *     attributes={
 *     "order": {"amount":"desc"}
 *     },
 *     normalizationContext={"groups"={"invoices_read"}},
 *     subresourceOperations=
 *     {
 *          "api_customers_invoices_get_subresource"=
 *          {
 *              "normalization_context"={"groups"={"invoices_subresource"}}
 *          },
 *     },
 *     itemOperations=
 *     {
 *          "GET","PUT","DELETE","increment"=
 *          {
 *              "method"="post",
 *              "path"="/invoices/{id}/increment",
 *              "controller"="App\Controller\InvoiceIncrementationController",
 *              "openapi_context"=
 *              {
 *                  "summary"="increment a given invoice",
 *                  "description"="triloui",
 *              },
 *          }
 *     }
 * )
 *
 * @ApiFilter(OrderFilter::class)
 */
class Invoice
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"invoices_read","customers_read","invoices_subresource"})
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     * @Groups({"invoices_read","customers_read","invoices_subresource"})
     * @Assert\NotBlank(message="Le montant de la facture est obligatoire")
     * @Assert\Type(type="numeric",message="le montant de la facture doit être un numérique")
     */
    private $amount;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"invoices_read","customers_read","invoices_subresource"})
     * @Assert\NotBlank(message="la date d'envoi doit être renseigné")
     * @Assert\DateTime(message="la date doit être au format YYYY-MM-DD")
     */
    private $sentAt;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"invoices_read","customers_read","invoices_subresource"})
     * @Assert\NotBlank(message="le statut de la facture est obligatoire")
     * @Assert\Choice(choices={"SENT","PAID","CANCELLED"}, message="le statut doit être soit SENT, soit PAID, soit CANCELLED")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer", inversedBy="invoices")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"invoices_read"})
     * @Assert\NotBlank(message="Il faut un customer")
     */
    private $customer;

    /**
     * @return User
     * @Groups({"invoices_read","customers_read","invoices_subresource"})
     */
    public function getUser(): User
    {
        return $this->customer->getUser();
    }

    /**
     * @ORM\Column(type="integer")
     * @Groups({"invoices_read","customers_read"})
     * @Assert\NotBlank(message="il faut un chrono pour la facture")
     * @Assert\Type(message="Le chrono doit être un chiffre",type="integer")
     */
    private $chrono;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt(\DateTimeInterface $sentAt): self
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getChrono(): ?int
    {
        return $this->chrono;
    }

    public function setChrono(int $chrono): self
    {
        $this->chrono = $chrono;

        return $this;
    }
}
