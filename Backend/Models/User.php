<?php

class User {
    // Properties of the User class
    public int $id; // User ID
    public string $username; // Username of the user
    public string $email; // Email of the user
    public string $firstname; // First name of the user
    public string $lastname; // Last name of the user
    public string $gender; // Gender of the user
    public string $adress; // Address of the user (note: should be spelled 'address')
    public string $postcode; // Postcode of the user's address
    public string $city; // City of the user's address
    public string $paymentMethod; // Payment method used by the user
    public bool $isEnabled; // Status indicating if the user is enabled
    public string $role; // Role of the user (e.g., admin, user)
    private string $password; // Password of the user (private for security reasons)

    // Constructor to initialize the User object
    public function __construct(
        int $id, 
        string $username, 
        string $email, 
        string $firstname, 
        string $lastname, 
        string $gender, 
        string $adress, 
        string $postcode, 
        string $city, 
        string $paymentMethod, 
        bool $isEnabled, 
        string $role,
        string $password
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->gender = $gender;
        $this->adress = $adress; // Note: should be spelled 'address'
        $this->postcode = $postcode;
        $this->city = $city;
        $this->paymentMethod = $paymentMethod;
        $this->isEnabled = $isEnabled;
        $this->role = $role;
        $this->password = $password;
    }

    // Getter methods
    public function getId(): int {
        return $this->id; // Return the user ID
    }

    public function getUsername(): string {
        return $this->username; // Return the username
    }

    public function getEmail(): string {
        return $this->email; // Return the email
    }

    public function getFirstname(): string {
        return $this->firstname; // Return the first name
    }

    public function getLastname(): string {
        return $this->lastname; // Return the last name
    }

    public function getGender(): string {
        return $this->gender; // Return the gender
    }

    public function getAddress(): string {
        return $this->adress; // Return the address (note: should be spelled 'address')
    }

    public function getPostcode(): string {
        return $this->postcode; // Return the postcode
    }

    public function getCity(): string {
        return $this->city; // Return the city
    }

    public function getPaymentMethod(): string {
        return $this->paymentMethod; // Return the payment method
    }

    public function isEnabled(): bool {
        return $this->isEnabled; // Return the enabled status
    }

    public function getRole(): string {
        return $this->role; // Return the role
    }

    public function getPassword(): string {
        return $this->password; // Return the password
    }
}

?>
