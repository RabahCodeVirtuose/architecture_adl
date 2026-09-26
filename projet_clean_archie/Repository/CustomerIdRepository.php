<?php

interface IGetCustomerByEmailRepository
{
    public function getCustomerByEmail($email) : Customer;
}

class GetCustomerByEmailRepository implements IGetCustomerByEmailRepository
{
    public function getCustomerByEmail($email): Customer
    {
        $customer = new Customer('test@gmail.com', "test123", "Pierre", "Dupond");
        return $customer;
    }
}
