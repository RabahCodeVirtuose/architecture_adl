<?php

interface IGetCustomerByEmailRepository
{
    public function getCustomerByEmail(string $email): ?Customer;
}

class GetCustomerByEmailRepository implements IGetCustomerByEmailRepository
{
    /** @param Customer[] $customers */
    public function __construct(private array $customers)
    {
    }

    public function getCustomerByEmail(string $email): ?Customer
    {
        foreach ($this->customers as $customer) {
            if ($customer->getEmail() === $email) {
                return $customer;
            }
        }

        return null;
    }
}
