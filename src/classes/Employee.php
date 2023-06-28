<?php
namespace App\classes;

use App\traits\PeselTrait;

class Employee
{
    use PeselTrait;
    private $id;
    private $name;
    private $surname;
    private $address;
    private $birthdate;
    private $sex;
    private $pesel;

    /**
     * Employee constructor.
     */
    public function __construct($name = null, $surname = null, $address = null, $pesel = null)
    {
        $this->name = $name;
        $this->surname = $surname;
        $this->address = $address;
        $this->pesel = $pesel;
    }

    /**
     * @param $row
     * @return Employee
     */
    public function create($row): Employee
    {
        $this->id = $row["id"];
        $this->name = $row["name"];
        $this->surname = $row["surname"];
        $this->address = $row["address"];
        $this->pesel = $row["pesel"];
        $this->birthdate = $this->getFormattedDateFromPesel($row['pesel']);
        $this->sex = $this->getSexFromPesel($row['pesel']);

        return $this;
    }

    /**
     * @param $row
     * @return array
     */
    public function createAsArray($row): array
    {
        return [
            'id' => $row["id"],
            'name' => $row["name"],
            'surname' => $row["surname"],
            'address' => $row["address"],
            'pesel' => $row["pesel"],
            'birthdate' => $this->getFormattedDateFromPesel($row['pesel']),
            'sex' => $this->getSexFromPesel($row['pesel'])
        ];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSex(): string
    {
        return $this->sex;
    }

    /**
     * @return string
     */
    public function getBirthdate(): string
    {
        return $this->birthdate;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * return void
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * return string
     */
    public function getSurname(): string
    {
        return $this->surname;
    }

    /**
     * return void
     */
    public function setSurname($surname): void
    {
        $this->surname = $surname;
    }

    /**
     * return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * return void
     */
    public function setAddress($address): void
    {
        $this->address = $address;
    }

    /**
     * return string
     */
    public function getPesel(): string
    {
        return $this->pesel;
    }

    /**
     * return void
     */
    public function setPesel($pesel): void
    {
        $this->pesel = $pesel;
    }

    /**
     * @param $pesel
     * @return string
     */
    private function getSexFromPesel($pesel): string
    {
        $sex = (int) substr($pesel, 9, 1);
        return $sex % 2 === 0 ? "F" : "M";
    }
}