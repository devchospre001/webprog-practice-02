<?php

class ExamDao
{

  private $conn;

  /**
   * constructor of dao class
   */
  public function __construct()
  {
    try {
      /** TODO
       * List parameters such as servername, username, password, schema. Make sure to use appropriate port
       */
      $hostname = "localhost";
      $username = "root";
      $password = "K3rim123";
      $schema = "classicmodels";
      $port = 3306;

      /** TODO
       * Create new connection
       */
      $this->conn = new PDO(
        "mysql:host=$hostname;port=$port;dbname=$schema",
        $username,
        $password
      );
    } catch (PDOException $e) {
      echo "Connemployees_performance_reportection failed: " . $e->getMessage();
    }
  }

  /** TODO
   * Implement DAO method used to get employees performance report
   */
  public function employees_performance_report()
  {
    $sql = <<<SQL
      SELECT e.employeeNumber AS id,
      CONCAT(e.firstName, ' ', e.lastName) as full_name, 
      e.email,
      COALESCE(SUM(p.amount), 0) AS total
      FROM employees e
      LEFT JOIN customers c ON e.employeeNumber = c.salesRepEmployeeNumber
      LEFT JOIN payments p ON c.customerNumber = p.customerNumber
      GROUP BY e.employeeNumber, e.firstName, e.lastName, e.email
      ORDER BY total DESC
    SQL;

    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /** TODO
   * Implement DAO method used to delete employee by id
   */
  public function delete_employee($employee_id)
  {
    $sql = "DELETE FROM employees WHERE employeeNumber = :employee_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(":employee_id", $employee_id);
    $stmt->execute();
  }

  public function find_employee($employee_id)
  {
    $sql = "SELECT * FROM employees WHERE employeeNumber = :employee_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(":employee_id", $employee_id);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  /** TODO
   * Implement DAO method used to edit employee data
   */
  public function edit_employee($employee_id, $data)
  {
    $sql = <<<SQL
      UPDATE employees
      SET firstName = :firstName,
          lastName = :lastName,
          email = :email
      WHERE employeeNumber = :employee_id
    SQL;

    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(":firstName", $data['firstName']);
    $stmt->bindParam(":lastName", $data['lastName']);
    $stmt->bindParam(":email", $data['email']);
    $stmt->bindParam(":employee_id", $employee_id);
    $stmt->execute();

    $query = "SELECT * FROM employees WHERE employeeNumber = :employee_id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":employee_id", $employee_id);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  /** TODO
   * Implement DAO method used to get orders report
   */
  public function get_orders_report()
  {
    $sql = <<<SQL
    SELECT o.orderNumber AS order_number,
      SUM(od.quantityOrdered * od.priceEach) AS total_amount,
      CONCAT(
      '<tr><td>', o.orderNumber, '</td>',
      '<td>', SUM(od.quantityOrdered * od.priceEach), '</td></tr>'
      ) AS details
      FROM orders o
      JOIN orderdetails od ON o.orderNumber = od.orderNumber
      GROUP BY o.orderNumber
      ORDER BY o.orderNumber DESC;
   SQL;

    $stmt = $this->conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /** TODO
   * Implement DAO method used to get all products in a single order
   */
  public function get_order_details($order_id)
  {
    $sql = <<<SQL
      SELECT
        p.productName AS product_name,
        od.quantityOrdered AS quantity,
        od.priceEach AS price_each
      FROM orderdetails od
      JOIN products p ON od.productCode = p.productCode
      WHERE od.orderNumber = :orderNumber
    SQL;

    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(":orderNumber", $order_id);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
