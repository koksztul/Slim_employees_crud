<?php
namespace App\controllers;

use App\classes\Employee;
use App\traits\PeselTrait;
use PDO;
use Slim\Views\Twig as View;

class EmployeeController extends BaseController
{
    use PeselTrait;
    protected $view;
    protected $db;
    private $data = [];
    protected $flash;

    public function __construct(View $view, PDO $db, \Slim\Flash\Messages $flash)
    {
        $this->view = $view;
        $this->db = $db;
        $this->flash = $flash;
    }

    public function index($request, $response)
    {
        $this->addData("pageTitle", "Slim Employees");
        $this->addData("successMessage", $this->getFlashMessage("success")[0] ?? null);
        $this->view->render($response, 'home.twig', $this->getData());
        return $response;
    }

    public function list($request, $response)
    {
        $employees['data'] = $this->getEmployees();

        return json_encode($employees);
    }

    public function create($request, $response, $args)
    {
        $this->addData("pageTitle", "Slim Employees - New Employee");
        $this->addData("errorMsg", $this->getFlashMessage("error")[0] ?? null);
        $data = $this->getData();
        $this->view->render($response, 'create.twig', $data);
        return $response;
    }

    public function store($request, $response, $args)
    {
        if ($request->isPost()) {
            $postValues = $request->getParsedBody();
            $name = $postValues["name"];
            $surname = $postValues["surname"];
            $address = $postValues["address"];
            $pesel = $postValues["pesel"];

            if ($this->isPeselValid($pesel)) {
                $this->createEmployee($name, $surname, $address, $pesel);
                $this->addFlashMessage("success", "Employee Created");
                return $response->withRedirect($request->getUri()->getBasePath(), 301);
            } else {
                $this->addFlashMessage("error", "Invalid PESEL: " . $pesel);
                return $response->withRedirect($request->getUri()->getBasePath() . '/employees/create', 302);
            }
        }
    }

    public function edit($request, $response, $args)
    {
        $employee = $this->getEmployee($args['id']);
        $this->addData("pageTitle", "Slim Employees - Edit Employee");
        $this->addData("errorMsg", $this->getFlashMessage("error")[0] ?? null);
        $this->addData("employee", $employee);
        $this->addData("successMessage", $this->getFlashMessage("success")[0] ?? null);
        $this->view->render($response, 'edit.twig', $this->getData());
        return $response;
    }

    public function update($request, $response, $args)
    {
        if ($request->isPut()) {
            $postValues = $request->getParsedBody();
            $name = $postValues["name"];
            $surname = $postValues["surname"];
            $address = $postValues["address"];
            $pesel = $postValues["pesel"];

            if ($this->isPeselValid($pesel)) {
                $this->updateEmployee($name, $surname, $address, $pesel, $args["id"]);
                $this->addFlashMessage("success", "Employee Updated");
            } else {
                $this->addFlashMessage("error", "Invalid PESEL: " . $pesel);
            }
            return $response->withRedirect($request->getUri()->getBasePath() . "/employees/edit/{$args['id']}", 301);
        }
    }

    public function view($request, $response, $args)
    {
        $this->addData("pageTitle", "Slim Employee - View Employee");
        $employee = $this->getEmployee($args['id']);
        $this->addData("employee", $employee);
        $this->view->render($response, 'view.twig', $this->getData());
        return $response;
    }

    public function delete($request, $response, $args)
    {
        $query = $this->db->prepare("DELETE FROM employees WHERE id=:id");
        $query->execute([
            "id" => $args['id'],
        ]);
        return $response->withStatus(204);
    }

    /**
     * @param $name
     * @param $surname
     * @param $address
     * @param $pesel
     * @return void
     */
    private function createEmployee($name, $surname, $address, $pesel): void
    {
        $query = $this->db->prepare("INSERT INTO employees(name, surname, address, pesel)
          VALUES(:name, :surname, :address, :pesel)");
        $query->execute([
            "name" => $name,
            "surname" => $surname,
            "address" => $address,
            "pesel" => $pesel
        ]);
    }

    /**
     * @param $name
     * @param $surname
     * @param $address
     * @param $pesel
     * @param $id
     * @return void
     */
    private function updateEmployee($name, $surname, $address, $pesel, $id): void
    {
        $query = $this->db->prepare("UPDATE employees SET `name`=:name, `surname`=:surname, `address`=:address, `pesel`=:pesel WHERE id=" . $id);
        $query->execute([
            "name" => $name,
            "surname" => $surname,
            "address" => $address,
            "pesel" => $pesel,
        ]);
    }
    /**
     * @param $key
     * @return array
     */
    private function addData($key, $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * @return array
     */
    private function getEmployees(): array
    {
        $query = $this->db->query('SELECT * FROM employees');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as $row) {
            $employee = new Employee();
            $employees[] = $employee->createAsArray($row);
        }
        return $employees;
    }

    /**
     * @param $id
     * @return Employee
     */
    private function getEmployee($id): Employee
    {
        $query = $this->db->query('SELECT * FROM employees WHERE id=' . $id);
        $query->execute();
        $result = $query->fetch();
        $employee = new Employee();
        return $employee->create($result);
    }

    /**
     * @return array
     */
    private function getData(): array
    {
        return $this->data;
    }

    /**
     * @param $key
     * @param $message
     * @return void
     */
    private function addFlashMessage($key, $message): void
    {
        $this->flash->addMessage($key, $message);
    }

    /**
     * @param $key
     * @return mixed
     */
    private function getFlashMessage($key): mixed
    {
        return $this->flash->getMessage($key);
    }
    /**
     * @param $pesel
     * @param $format
     * @return bool
     */
    private function isPeselValid($pesel): bool
    {
        if (strlen($pesel) !== 11) {
            return false;
        }
        if (!preg_match('/^\d+$/', $pesel)) {
            return false;
        }

        $weights = [1, 3, 7, 9, 1, 3, 7, 9, 1, 3, 1];
        $digits = str_split($pesel);
        $checksum = 0;
        for ($i = 0; $i < count($digits); $i++) {
            $checksum += $weights[$i] * intval($digits[$i]);
        }

        if ($checksum % 10 !== 0) {
            return false;
        }

        $fullYear = $this->getFormattedDateFromPesel($pesel, "Y");
        $month = $this->getFormattedDateFromPesel($pesel, "m");
        $day = $this->getFormattedDateFromPesel($pesel, "d");

        if ($fullYear < 1800 || $fullYear > 2299) {
            return false;
        }

        if (intval($month) < 1 || intval($month) > 12) {
            return false;
        }

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, intval($month), intval($fullYear));
        if (intval($day) < 1 || intval($day) > $daysInMonth) {
            return false;
        }

        return true;
    }
}


?>