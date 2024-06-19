<?php

class Order {
    // Properties of the Order class
    public int $id; // Order ID
    public float $total; // Total amount of the order
    public string $date; // Date of the order
    public int $fk_customerId; // Foreign key for the customer ID
    public ?int $fk_couponId; // Foreign key for the coupon ID, nullable

    // Constructor to initialize the Order object
    public function __construct(int $id, float $total, string $date, int $fk_customerId, ?int $fk_couponId)
    {
        $this->id = $id;
        $this->total = $total;
        $this->date = $date;
        $this->fk_customerId = $fk_customerId;
        $this->fk_couponId = $fk_couponId;
    }
}
