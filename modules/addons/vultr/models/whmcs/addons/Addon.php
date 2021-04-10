<?php
namespace MGModule\vultr\models\whmcs\addons;

use MGModule\vultr as main;

/**
 * Description of Addon
 *
 * @Table(name=tbladdons,preventUpdate,prefixed=false)
 */
class Addon extends main\mgLibs\models\Orm
{
    /**
     * @Column()
     * @var int
     */
    protected $id;

    /**
     * @Column(name=packages)
     * @var string
     */
    protected $packages;

    /**
     * @Column(name=name)
     * @var string
     */
    protected $name;

    /**
     * @Column(name=description)
     * @var string
     */
    protected $description;

    /**
     * @Column(name=billingcycle,as=billingCycle)
     * @var string
     */
    protected $billingCycle;

    /**
     * @Column(name=tax)
     * @var string
     */
    protected $tax;

    /**
     * @Column(name=showorder)
     * @var string
     */
    protected $showorder;

    /**
     * @Column(name=downloads)
     * @var string
     */
    protected $downloads;

    /**
     * @Column(name=autoactivate,as=autoActivate)
     * @var string
     */
    protected $autoActivate;

    /**
     * @Column(name=suspendproduct,as=suspendProduct)
     * @var string
     */
    protected $suspendProduct;

    /**
     * @Column(name=welcomeemail,as=welcomeEmail)
     * @var int
     */
    protected $welcomeEmail;

    /**
     * @Column(name=weight)
     * @var int
     */
    protected $weight;

    private $_pricing;

    public function getId()
    {
        return $this->id;
    }

    public function getPackages()
    {
        return $this->packages;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getBillingCycle()
    {
        return $this->billingCycle;
    }

    public function getTax()
    {
        return $this->tax;
    }

    public function getShoworder()
    {
        return $this->showorder;
    }

    public function getDownloads()
    {
        return $this->downloads;
    }

    public function getAutoActivate()
    {
        return $this->autoActivate;
    }

    public function getSuspendProduct()
    {
        return $this->suspendProduct;
    }

    public function getWelcomeEmail()
    {
        return $this->welcomeEmail;
    }

    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Get Pricing
     * @return main\models\whmcs\pricing\Price[]
     */
    public function getPricing()
    {
        if (!empty($this->_pricing)) {
            return $this->_pricing;
        }

        $repository = new main\models\whmcs\pricing\Repository();
        $repository->onlyAddon();
        $repository->withRelation($this->id);
        $repository->withBillingCycle();
        $this->_pricing = array();
        foreach ($repository->get() as $price) {
            $this->_pricing[] = $price;
        }
        unset($repository);

        return $this->_pricing;
    }

    public function getPrice($currencyId, $billingCycle)
    {
        $repository = new main\models\whmcs\pricing\Repository();
        $repository->onlyAddon();
        $repository->withRelation($this->id);
        $repository->withBillingCycle();
        $repository->onlyCurrency($currencyId);
        foreach ($repository->get() as $price) {
            return $price->getPrice($billingCycle);
        }

        return null;
    }
}
